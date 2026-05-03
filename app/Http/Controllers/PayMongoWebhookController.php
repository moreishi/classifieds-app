<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handle PayMongo webhooks for GCash verification.
 *
 * After the user pays the ₱5 verification fee via GCash,
 * PayMongo sends a payment.paid webhook with the payer's
 * mobile number in billing.phone. We match it against
 * pending verifications and mark the user verified.
 *
 * @see https://developers.paymongo.com/docs/webhooks
 */
class PayMongoWebhookController extends Controller
{
    public function __invoke(Request $request): \Illuminate\Http\Response
    {
        $payload = $request->json()->all();
        $eventType = $payload['data']['attributes']['type'] ?? '';
        $livemode = $payload['data']['attributes']['livemode'] ?? false;

        Log::info('[PayMongo] Webhook received', [
            'type' => $eventType,
            'livemode' => $livemode,
        ]);

        // We only care about successful payments
        if ($eventType !== 'payment.paid') {
            return response('ignored', 200);
        }

        $paymentData = $payload['data']['attributes']['data'] ?? [];
        $billing = $paymentData['attributes']['billing'] ?? [];
        $paymentIntentId = $paymentData['attributes']['payment_intent_id'] ?? '';
        $amount = $paymentData['attributes']['amount'] ?? 0;
        $metadata = $paymentData['attributes']['metadata'] ?? [];

        // Extract the phone number from billing info
        $phone = $billing['phone'] ?? '';

        if (empty($phone)) {
            Log::warning('[PayMongo] payment.paid webhook missing billing.phone', [
                'payment_intent_id' => $paymentIntentId,
            ]);
            return response('missing phone', 200);
        }

        // Normalize: PayMongo returns +639171234567, we store 09171234567
        $phone = $this->normalizePhone($phone);

        if (empty($phone)) {
            Log::warning('[PayMongo] Could not normalize phone', [
                'raw' => $billing['phone'] ?? '',
            ]);
            return response('invalid phone', 200);
        }

        // Check if this is our verification flow
        $checkMethod = 'billing_phone';

        // We also check metadata for user_id (set when creating the Payment Intent)
        $userIdFromMetadata = $metadata['user_id'] ?? null;

        if ($userIdFromMetadata) {
            // Direct match via metadata — most reliable
            $user = User::find($userIdFromMetadata);
            if ($user && !$user->gcash_verified_at) {
                $user->update(['gcash_verified_at' => now()]);
                Log::info('[PayMongo] User verified via metadata', [
                    'user_id' => $userIdFromMetadata,
                    'phone' => $phone,
                ]);

                // Clear pending verification
                cache()->forget("verification:{$userIdFromMetadata}");

                return response('verified via metadata', 200);
            }
        }

        // Fallback: match by phone number (in case metadata wasn't set)
        $user = User::where('gcash_number', $phone)
            ->whereNull('gcash_verified_at')
            ->first();

        if ($user) {
            $user->update(['gcash_verified_at' => now()]);
            Log::info('[PayMongo] User verified via phone match', [
                'user_id' => $user->id,
                'phone' => $phone,
            ]);

            cache()->forget("verification:{$user->id}");

            return response('verified via phone', 200);
        }

        Log::info('[PayMongo] No matching pending verification', [
            'phone' => $phone,
            'payment_intent_id' => $paymentIntentId,
        ]);

        return response('no match', 200);
    }

    /**
     * Normalize Philippine mobile numbers.
     * +639171234567 → 09171234567
     * 639171234567  → 09171234567
     * 09171234567   → 09171234567
     */
    private function normalizePhone(string $phone): string
    {
        // Remove non-digits
        $digits = preg_replace('/[^0-9]/', '', $phone);

        // +63 -> 0
        if (strlen($digits) === 12 && str_starts_with($digits, '63')) {
            return '0' . substr($digits, 2);
        }

        // Already 09 format
        if (strlen($digits) === 11 && str_starts_with($digits, '09')) {
            return $digits;
        }

        return '';
    }
}

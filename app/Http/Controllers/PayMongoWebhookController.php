<?php

namespace App\Http\Controllers;

use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayMongoWebhookController extends Controller
{
    public function __invoke(Request $request): \Illuminate\Http\Response
    {
        $payload = $request->json()->all();
        $eventType = $payload['data']['attributes']['type'] ?? '';

        Log::info('[PayMongo] Webhook received', ['type' => $eventType]);

        // We only care about successful payments
        if ($eventType !== 'payment.paid') {
            return response('ignored', 200);
        }

        $paymentData = $payload['data']['attributes']['data'] ?? [];
        $billing = $paymentData['attributes']['billing'] ?? [];
        $paymentIntentId = $paymentData['attributes']['payment_intent_id'] ?? '';
        $metadata = $paymentData['attributes']['metadata'] ?? [];
        $phone = $billing['phone'] ?? '';

        $phone = $phone ? $this->normalizePhone($phone) : '';
        $userId = $metadata['user_id'] ?? null;

        // === Buy Credits Flow ===
        if (isset($metadata['type']) && $metadata['type'] === 'buy_credits') {
            return $this->handleBuyCredits((int) $userId, $metadata, $paymentIntentId);
        }

        // === Verification Flow ===
        if (empty($phone)) {
            Log::warning('[PayMongo] payment.paid webhook missing billing.phone', [
                'payment_intent_id' => $paymentIntentId,
            ]);
            return response('missing phone', 200);
        }

        // Direct match via metadata — most reliable
        if ($userId) {
            $user = User::find($userId);
            if ($user && !$user->gcash_verified_at) {
                $user->update(['gcash_verified_at' => now()]);
                Log::info('[PayMongo] User verified via metadata', [
                    'user_id' => $userId,
                    'phone' => $phone,
                ]);
                cache()->forget("verification:{$userId}");
                return response('verified via metadata', 200);
            }
        }

        // Fallback: match by phone number
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
     * Handle a buy_credits payment confirmation.
     */
    private function handleBuyCredits(int $userId, array $metadata, string $paymentIntentId): \Illuminate\Http\Response
    {
        $user = User::find($userId);
        if (!$user) {
            Log::warning('[PayMongo] buy_credits: user not found', ['user_id' => $userId]);
            return response('user not found', 200);
        }

        $pending = cache()->pull("purchase:{$userId}");

        if (!$pending) {
            Log::warning('[PayMongo] buy_credits: no pending purchase in cache', [
                'user_id' => $userId,
                'payment_intent_id' => $paymentIntentId,
            ]);
            return response('no pending purchase', 200);
        }

        // Deposit credits
        $credits = $pending['credits'];
        $bonus = $pending['bonus'] ?? 0;
        $total = $credits + $bonus;

        $user->increment('credit_balance', $total);

        CreditTransaction::create([
            'user_id' => $userId,
            'amount' => $total,
            'type' => 'top_up',
            'notes' => "Purchased {$credits} credits" . ($bonus ? " + {$bonus} bonus" : '') . " via GCash",
        ]);

        // If this is their first purchase and they were referred, credit the referrer
        $referralAwarded = app(\App\Services\CreditService::class)->creditReferrer($user);

        Log::info('[PayMongo] Credits deposited', [
            'user_id' => $userId,
            'credits' => $credits,
            'bonus' => $bonus,
            'total' => $total,
            'referral_bonus_awarded' => $referralAwarded,
        ]);

        return response('credits deposited', 200);
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($digits) === 12 && str_starts_with($digits, '63')) {
            return '0' . substr($digits, 2);
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '09')) {
            return $digits;
        }

        return '';
    }
}

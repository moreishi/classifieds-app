<?php

namespace App\Http\Controllers;

use App\Models\TransactionReceipt;

class ReceiptController extends Controller
{
    public function download(TransactionReceipt $receipt)
    {
        if ($receipt->seller_id !== auth()->id()) {
            abort(403);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.receipt', [
            'receipt' => $receipt,
        ]);

        return $pdf->download('receipt-' . $receipt->reference_number . '.pdf');
    }
}

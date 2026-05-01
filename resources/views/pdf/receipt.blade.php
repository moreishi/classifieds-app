<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $receipt->reference_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .header p { color: #666; }
        .ref { font-size: 18px; font-weight: bold; text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; text-align: left; }
        .bordered th, .bordered td { border: 1px solid #ddd; }
        .total { font-weight: bold; font-size: 16px; }
        .footer { margin-top: 40px; text-align: center; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Iskina.ph</h1>
        <p>Cebu's Classifieds Marketplace</p>
    </div>

    <div class="ref">
        Reference: {{ $receipt->reference_number }}
    </div>

    <table class="bordered">
        <tr>
            <th style="width: 40%">Seller</th>
            <td>{{ $receipt->seller->name }}</td>
        </tr>
        <tr>
            <th>Buyer</th>
            <td>{{ $receipt->buyer_name ?: $receipt->buyer_email }}</td>
        </tr>
        <tr>
            <th>Buyer Email</th>
            <td>{{ $receipt->buyer_email }}</td>
        </tr>
        <tr>
            <th>Listing</th>
            <td>{{ $receipt->listing->title }}</td>
        </tr>
        <tr>
            <th>Date</th>
            <td>{{ $receipt->created_at->format('F d, Y h:i A') }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($receipt->status) }}</td>
        </tr>
        <tr class="total">
            <th>Amount</th>
            <td>₱{{ number_format($receipt->amount / 100, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>This is a computer-generated receipt. No signature required.</p>
        <p>Iskina.ph · Commission-free marketplace</p>
    </div>
</body>
</html>

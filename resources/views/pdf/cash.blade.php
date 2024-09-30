<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cash Transaction #{{ $cash->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header,
        .footer {
            text-align: center;
            font-size: 12px;
        }

        .header h2,
        .footer h2 {
            margin: 5px;
        }

        .container {
            margin: 20px;
        }

        .details {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .details th,
        .details td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .details th {
            background-color: #f2f2f2;
        }

        .summary-table {
            margin-top: 20px;
            width: 100%;
        }

        .summary-table th,
        .summary-table td {
            padding: 8px;
            border: none;
        }

        .summary-table th {
            text-align: right;
        }

        .summary-table td {
            text-align: left;
        }

        .summary-table .bold {
            font-weight: bold;
        }

        .bank-details {
            margin-top: 30px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>CREDIT INVOICE</h2>
        <p>KARA Traders LTD. www.karatraders.co.tz</p>
        <p>PO Box 993, Corner of India/Zanaki Street, Near Sabodo Parking, Dar es Salaam, Tanzania</p>
        <p>Whatsapp: +255 682 140 991 | TIN NUMBER: 100 - 113 - 716 | VRN NUMBER: 10 - 001096 - I</p>
        <p>Date: {{ $cash->creation_date }} | Invoice # {{ $cash->id }}</p>
    </div>

    <div class="container">
        <p><strong>Company Name:</strong> {{ $cash->customer->name }}</p>
        <p><strong>TIN Number:</strong> {{ $cash->customer->tin_number }}</p>
        <p><strong>VRN Number:</strong> {{ $cash->customer->vrn_number }}</p>

        <table class="details">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>Unit</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cash->details as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->item->item_name }}</td>
                        <th>{{ $detail->unit->unit_name }}</th>
                        <td>{{ $detail->quantity }}</td>
                        <td>{{ number_format($detail->price, 2) }}</td>
                        <td>{{ number_format($detail->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <th>Subtotal:</th>
                <td>{{ number_format($cash->details->sum('total'), 2) }} TZS</td>
            </tr>
            <tr>
                <th>VAT (18%):</th>
                <td>{{ number_format($cash->details->sum('total') * 0.18, 2) }} TZS</td>
            </tr>
            <tr class="bold">
                <th>Total (TZS):</th>
                <td>{{ number_format($cash->total_amount, 2) }} TZS</td>
            </tr>
        </table>

        <div class="bank-details">
            <p><strong>BANK DETAILS</strong></p>
            <p>KARA TRADERS LIMITED</p>
            <p>EXIM BANK (T) LIMITED, DAR ES SALAAM</p>
            <p>SWIFT CODE: EXTNTZTXXX</p>
            <p>USD A/C NO.0300514000 | TZS A/C NO.0300514006</p>
        </div>
    </div>

    <div class="footer">
        <p><strong>Terms and Conditions:</strong></p>
        <p>1. Our invoices are net 30 days payable monthly. Bank interest will be charged on all overdue accounts.</p>
        <p>2. Goods once sold, delivered, and accepted by the customer will not be returnable.</p>
        <p>3. We are not responsible for breakage or shortage of goods in transit.</p>
    </div>
</body>

</html>

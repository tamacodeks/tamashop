<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->username }} Invoice No {{ $invoice->invoice_ref }}</title>
    <base href=""/>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    {{--<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">--}}
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 90%;
            margin: auto;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            padding: 10px;
            vertical-align: top;
        }

        .header-logo {
            width: 60%;
        }

        .header-details {
            width: 40%;
            text-align: right;
        }

        .header-details strong {
            display: block;
            font-size: 14px;
        }

        .invoice-details {
            margin-bottom: 20px;
        }

        .invoice-details td {
            padding: 5px;
        }

        h4 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .separator {
            border-bottom: 5px solid #ff0000;
            margin: 20px 0;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .payment-table th, .payment-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .payment-table th {
            background-color: #f2f2f2;
            text-transform: uppercase;
        }

        .total-row {
            background-color: #f2f2f2;
            font-weight: bold;
            border-top: 2px solid #000;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #555;
        }

        .footer strong {
            font-size: 15px;
        }

        @media print {
            body {
                font-size: 14pt;
            }
        }
    </style>
</head>
<body id="print-content">

<div class="container">

    <!-- Header Section -->
    <table class="header-table">
        <tbody>
        <tr>
            <!-- Logo Column -->
            <td class="header-logo">
                <img src="{{ secure_url('images/logo.png') }}" alt="Logo">
            </td>
            <!-- Invoice Details Column -->
            <td class="header-details">
                <strong>Date:</strong> {{ $invoice->date }}
                <strong>Période:</strong> {{ $invoice->period }}
                <strong>Numéro de facture:</strong> {{ $invoice->invoice_ref }}
                <strong>BANQUE: LCL</strong>
                <strong>IBAN: FR91 3000 2016 3700 0007 1620 S65</strong>
                <strong>BIC: CRLYFRPP</strong>
            </td>
        </tr>
        </tbody>
    </table>

    <!-- Client Details Section -->
    <table class="invoice-details">
        <tbody>
        <tr>
            <td>
                <strong>{{ isset($invoice) ? $invoice->first_name ." ".$invoice->last_name : "" }}</strong><br>
                {!! nl2br($invoice->address) !!}<br>
                France<br>
                <strong>Customer ID:</strong> {{ $invoice->cust_id }}<br>
                <strong>TVA intracom:</strong> {{ $invoice->tva_no }}
            </td>
        </tr>
        </tbody>
    </table>

    <div class="separator"></div>

    <!-- Payment Details Section -->
    <h4>Details des payments</h4>

    <table class="payment-table">
        <thead>
        <tr>
            <th>Payment Date</th>
            <th>Amount Paid</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $invoice->date }}</td>
            <td>{{ number_format($invoice->amount, 2) }} &euro;</td>
        </tr>
        <tr class="total-row">
            <td><strong>Total des commissions TTC</strong></td>
            <td><strong>{{ number_format($invoice->amount, 2) }} &euro;</strong></td>
        </tr>
        </tbody>
    </table>

    <!-- Footer Section -->
    <div class="footer">
        Facture établie par <strong>TAMA GROUPE</strong> au nom et pour le compte
        de <strong>{{ isset($invoice) ? $invoice->first_name ." ".$invoice->last_name : "" }}</strong>
    </div>

</div>

</body>
</html>

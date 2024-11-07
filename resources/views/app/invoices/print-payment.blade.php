<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->username }} Invoice No {{ $invoice->invoice_ref }}</title>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    {{--<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">--}}
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table td {
            padding: 10px;
        }

        .table img {
            width: 150px;
        }

        .invoice-info {
            text-align: right;
            line-height: 1.5;
        }

        .invoice-info strong {
            display: block;
            margin-bottom: 5px;
        }

        h4 {
            text-align: center;
            font-size: 18px;
            text-transform: uppercase;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .cdr-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .cdr-table th, .cdr-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .cdr-table th {
            background-color: #f2f2f2;
            text-transform: uppercase;
        }

        .total-row td {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
        }

        .footer strong {
            font-size: 15px;
        }

        .separator {
            border-bottom: 4px solid #ff0000;
            margin: 20px 0;
        }

        @media print {
            body {
                font-size: 14pt;
            }
        }
    </style>
</head>
<body>

<div class="page">

    <!-- Header Section -->
    <table class="table">
        <tbody>
        <tr>
            <!-- Logo Column -->
            <td style="width: 60%;">
                <img src="{{ public_path('images/logo.png') }}" alt="TamaShop Logo">
            </td>

            <!-- Invoice Details Column -->
            <td class="invoice-info" style="width: 40%;">
                <strong>Date:</strong> {{ \Illuminate\Support\Carbon::parse($invoice->year . "-" . $invoice->month)->startOfMonth()->addMonth()->toDateString() }}
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
    <table class="table">
        <tbody>
        <tr>
            <td>
                <strong>{{ $invoice->username }}</strong><br>
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
    <h4>Détails des paiements</h4>

    <table class="cdr-table">
        <thead>
        <tr>
            <th>Payment Date</th>
            <th>Description</th>
            <th>Previous Balance</th>
            <th>New Balance</th>
            <th>Amount</th>

        </tr>
        </thead>
        <tbody>
        @forelse($servicePrintData as $srvData)
            <tr>
                <td>{{ \Illuminate\Support\Carbon::parse($srvData->updated_at)->format('d-m-Y') }}</td>
                <td>{{ $srvData->description }}</td>
                <td>{{ number_format($srvData->prev_bal, 2) }} &euro;</td>
                <td>{{ number_format($srvData->balance, 2) }} &euro;</td>
                <td>{{ number_format($srvData->amount, 2) }} &euro;</td>

            </tr>
        @empty
            <tr>
                <td colspan="5">No Payment Data Available</td>
            </tr>
        @endforelse
        <tr class="total-row">
            <td colspan="4">Total des commissions TTC</td>
            <td>{{ number_format($invoice->total_amount, 2) }} &euro;</td>
        </tr>
        </tbody>
    </table>

    <!-- Footer Section -->
    <div class="footer">
        Facture établie par <strong>TAMA GROUPE</strong> au nom et pour le compte de
        <strong>{{ $invoice->first_name }} {{ $invoice->last_name }}</strong>.
    </div>
</div>

</body>
</html>

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
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        .table td {
            padding: 10px;
            vertical-align: top;
        }

        .table thead tr {
            background-color: #f2f2f2;
        }

        .table thead tr td {
            font-weight: bold;
            text-align: center;
            padding: 15px;
            border-bottom: 2px solid #ccc;
        }

        .table tbody tr td {
            border-bottom: 1px solid #e6e6e6;
        }

        .invoice-header {
            text-align: center;
            /*margin-bottom: 30px;*/
        }

        .invoice-header img {
            max-width: 150px;
        }

        p {
            margin: 0;
        }

        .cdr-table td, th {
            border: 1px solid black;
            padding: 8px;
        }

        .page {
            page-break-after: always;
        }

        .lastpage {
            white-space: normal;
        }

        .section-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #ff0000;
            text-align: left;
            border-bottom: 2px solid #ff0000;
            padding-bottom: 5px;
        }

        .table-invoice td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        .bank-details {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ccc;
            margin-top: 300px;
        }

        .bank-details strong {
            display: block;
            margin-bottom: 5px;
        }

        .total {
            font-size: 16px;
            font-weight: bold;
            background-color: #f2f2f2;
        }

        @media print {
            body {
                font-size: 10pt;
            }

            .table td {
                padding: 5px;
            }

            .page {
                page-break-after: always;
            }
        }
        /* Basic styling for the table */
        .table-invoice {
            width: 100%;
            border-collapse: separate; /* Needed for border-spacing to take effect */
        }

        .table-invoice td {
            padding: 10px; /* Adjust padding as needed for table cell spacing */
            border: 1px solid #ddd; /* Optional border for each cell */
        }

        /* Styling for the spacer row */
        .spacer-row {
            height: 150px; /* Adjust height for the space between rows */
        }

        /* Styling for the total row */
        .table-invoice .total td {
            font-weight: bold;
            padding-top: 10px; /* Optional additional spacing for clarity */
            border-top: 2px solid #000; /* Optional border to distinguish total row */
        }

    </style>
</head>
<body id="print-content">

<!-- Invoice Header -->
<div class="invoice-header">
    <table class="table">
        <tbody>
        <tr>
            <!-- Left Column with Company Details -->
            <td style="width: 30%; text-align: left;">
                <strong>TAMA GROUPE SASU</strong>
                <br>131 Rue de Crequi
                <br>69006 Lyon
                <br>France
                <br>+33176660340
                <br>billing@tamaexpress.com
                <br>TVA intracom: FR 41823939285
            </td>

            <!-- Center Column with Logo -->
            <td style="width: 40%; text-align: center;">
                <img src="{{ public_path('images/tama_logo.png') }}" alt="Tama Logo">
            </td>

            <!-- Right Column with Customer Details -->
            <?php
            $invoiceDate = \Illuminate\Support\Carbon::parse($invoice->year . "-" . $invoice->month)->startOfMonth()->addMonth()->toDateString();
            ?>
            <td style="width: 30%; text-align: right;">
                <strong>{{ isset($invoice) ? $invoice->first_name ." ".$invoice->last_name : "" }}</strong>
                <br>{!! $invoice->address !!}
                <br>France
                <br>Customer ID: {{ $invoice->cust_id }}
                <br>TVA intracom: {{ $invoice->tva_no }}
            </td>
        </tr>
        </tbody>
    </table>
</div>

<!-- Invoice Section Header -->
<div class="section-header"></div>

<?php
$invoiceDate = \Illuminate\Support\Carbon::parse($invoice->year . "-" . $invoice->month)->startOfMonth()->addMonth()->toDateString();
?>
<table class="table">
    <tbody>
<tr>
<td style="width: 10%;">
    <strong>Date:</strong> {{ $invoiceDate }}
    <br><strong>Numéro de facture:</strong> {{ $invoice->invoice_ref }}
</td>
</tr>
</tbody>
</table>
<!-- Invoice Details -->
<table class="table table-invoice" style="border-spacing: 0 10px;">
    <thead>
    <tr>
        <td>DÉSIGNATION</td>
        <td>Montant</td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Rechargement du compte Tama Service</td>
        <td>{{ number_format($invoice->total_amount, 2) }} &euro;</td>
    </tr>
    <!-- Spacer row for additional space between data rows and total row -->
    <tr class="spacer-row"></tr>
    <tr class="total">
        <td><strong>Montant HT</strong></td>
        <td><strong>{{ number_format($invoice->grand_total, 2) }} &euro;</strong></td>
    </tr>
    </tbody>
</table>
<div style="text-align: center;margin-top:10px;margin-bottom: 50px">
    Autoliquidation de la TVA art 283-2 octies du CGI. TVA due par le
    preneur
</div>

<!-- Bank Details -->
<div class="bank-details">
    <strong>BANQUE: LCL</strong>
    <strong>IBAN: FR91 3000 2016 3700 0007 1620 S65</strong>
    <strong>BIC: CRLYFRPP</strong>
</div>

</body>
</html>

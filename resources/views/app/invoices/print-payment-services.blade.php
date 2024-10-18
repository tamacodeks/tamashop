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
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        .table {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
            white-space: nowrap;
            /*border: 1px solid #000;*/
        }

        .table td {
            /*border: 1px solid black;*/
            width: 50%;
        }

        .table tr:first-child td {
            border-top: 0;
        }

        .table tr td:first-child {
            border-left: 0;
        }

        .table tr:last-child td {
            border-bottom: 0;
        }

        .table tr td:last-child {
            border-right: 0;
        }

        .none {
            display: none;
        }

        #goods thead tr td {
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
        }

        #goods tr td {
            border-right: 1px solid #fff;
            border-bottom: 1px solid #fff;
        }

        #goods tr:last-child td {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        p {
            margin: 0;
        }

        .cdr-table td, th {
            border: 1px solid black;
        }

        .page {
            page-break-after: always;
            page-break-inside: avoid;
        }

        .lastpage{
            overflow-wrap: normal !important;
            white-space: normal !important;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        @media print {
            body {
                font-size: 14pt;
            }
        }
    </style>
</head>
<body id="print-content">

<div class="page">
    <table class="table">
        <tbody>
        <tr>
            <!-- First column with logo -->
            <td style="width: 90%;">
                <img src="{{ public_path('images/tama_logo.png') }}" style="width: 130px">
            </td>

            {{--<!-- Second column with customer information -->--}}
            {{--<td style="width: 40%;">--}}
                {{--<strong>{{ isset($invoice) ? $invoice->first_name ." ". $invoice->last_name : "" }}</strong>--}}
                {{--<br>{!! $invoice->address !!}--}}
                {{--<br>France--}}
                {{--<br>Customer ID: {{ $invoice->cust_id }}--}}
                {{--<br>TVA intracom: {{ $invoice->tva_no }}--}}
            {{--</td>--}}

            <!-- Third column with invoice details -->
            <?php
            $invoiceDate = \Illuminate\Support\Carbon::parse($invoice->year . "-" . $invoice->month)->startOfMonth()->addMonth()->toDateString();
            ?>
            <td style="width: 50%;">
                <strong>Date:</strong> {{ $invoiceDate }}
                <br><strong>Période:</strong> {{ $invoice->period }}
                <br><strong>Numéro de facture:</strong> {{ $invoice->invoice_ref }}
            </td>
        </tr>
        </tbody>
    </table>

    <br>

    <p style="border-bottom: 5px solid #ff0000"></p>
    <br><br>

    {{--<table class="table">--}}
        {{--<tbody>--}}
        {{--<tr>--}}

        {{--</tr>--}}
        {{--</tbody>--}}
    {{--</table>--}}
    {{--<br>--}}
    {{--<div style="border-bottom:1px solid #000;">--}}
        {{--<div style="display:inline-block"><h4>FACTURATION</h4></div>--}}
        {{--<div style="display:inline-block; float:right"><h4>MONTANT</h4></div>--}}
    {{--</div>--}}
    {{--<table class="table" id="goods" style="border: 1px solid #fff;">--}}
        {{--<tbody>--}}
        {{--<tr>--}}
            {{--<td style="border-top: 1px #000 solid"></td>--}}
            {{--<td style="border-top: 1px #000 solid"></td>--}}
            {{--<td style="border-top: 1px #000 solid"></td>--}}
        {{--</tr>--}}
        {{--<tr style="border-bottom: 1px #000 solid">--}}
            {{--<td style="height: 30px;"></td>--}}
            {{--<td style="text-align: center"></td>--}}
            {{--<td style="text-align: right"></td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td style="height: 30px;">Ventes services Tama collectées TTC</td>--}}
            {{--<td style="text-align: center"></td>--}}
            {{--<td style="text-align: right">{{ number_format($invoice->total_amount,2) }} &euro;</td>--}}
        {{--</tr>--}}
    {{--</table>--}}
    {{--<div style="text-align: center;margin-top:10px;margin-bottom: 100px">--}}
        {{--Facture etablie par TAMA GROUPE au nom et pour le compte--}}
        {{--de {{ isset($invoice) ? $invoice->first_name ." ".$invoice->last_name : "" }}--}}
    {{--</div>--}}
{{--</div>--}}
<div class="page">
    <h4 style="text-align: center">Details des payments</h4>
    <br><br><br>
    <table class="cdr-table" style="font-size: 13px;border-collapse: collapse;width: 100%;">
        <thead>
            <tr style="background: #f2f2f2">
                <td style="text-align: center">Payment Date</td>
                <td style="text-align: center">Amount Paid</td>
                <td style="text-align: center">Previous Balace</td>
                <td style="text-align: center">New Balance</td>
                <td style="text-align: center">Description</td>
            </tr>
        </thead>
        <tbody>
        @forelse($servicePrintData as $srvData)
            <tr>
                <td style="text-align: center">({{ $srvData->updated_at }})</td>
                <td style="text-align: center">{{ $srvData->amount }} &euro;</td>
                <td style="text-align: center">{{ $srvData->prev_bal }} &euro;</td>
                <td style="text-align: center">{{ $srvData->balance }}  &euro;</td>
                <td style="height: 30px;">{{$srvData->description }}</td>
            </tr>
        @empty
        @endforelse
        <tr>
            <td style="border-bottom: 1px solid #000; background: #f2f2f2">
                <strong>Total des commissions TTC</strong>
            </td>
            <td style="text-align: center; border-bottom: 1px solid #000; background: #f2f2f2"></td>
            <td style="text-align: center; border-bottom: 1px solid #000; background: #f2f2f2"></td>
            <td style="background: #f2f2f2">&nbsp;</td>
            <td style="text-align: right; border-bottom: 1px solid #000; background: #f2f2f2">
                <strong>{{$invoice->total_amount }}  &euro;</strong>
            </td>
        </tr>
        </tbody>
    </table>
    <div style="text-align: center;margin-top:10px;margin-bottom: 100px">
    Facture etablie par TAMA GROUPE au nom et pour le compte
    de {{ isset($invoice) ? $invoice->first_name ." ".$invoice->last_name : "" }}
    </div>
</div>
</body>
</html>

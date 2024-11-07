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
            margin-top: 20px;
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

        #goods {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        #goods td {
            padding: 10px;
            border-top: 1px solid #000;
            text-align: right;
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

        .cdr-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 20px;
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

        .total-row {
            background-color: #f2f2f2;
            font-weight: bold;
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
    </style>
</head>
<body id="print-content">

<!-- Invoice Header -->
<div class="invoice-header">
    <table class="table">
        <tbody>
        <tr>
           <!-- Center Column with Logo -->
            <td style="width: 40%; text-align: left;">
                <img src="{{ public_path('images/tama_logo.png') }}" alt="Tama Logo">
            </td>

            <!-- Right Column with Customer Details -->
            <?php
            $invoiceDate = \Illuminate\Support\Carbon::parse($invoice->year . "-" . $invoice->month)->startOfMonth()->addMonth()->toDateString();
            ?>
            <td style="width: 30%; text-align: right;">
                <strong>{{ $invoice->username }}</strong><br>
                {!! nl2br($invoice->address) !!}<br>
                France<br>
                <strong>Customer ID:</strong> {{ $invoice->cust_id }}<br>
                <strong>TVA intracom:</strong> {{ $invoice->tva_no }}
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
            <br><strong>Période:</strong> {{ $invoice->period }}
            {{--<br><strong>Numéro de facture:</strong> {{ $invoice->invoice_ref }}--}}
        </td>
    </tr>
    </tbody>
</table>

<!-- Invoice Details -->
<table class="table table-invoice">
    <thead>
    <tr>
        <td>Détails de la transaction</td>
        <td>Montant</td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Ventes services Tama collectées HT</td>
        <td style="text-align: center">{{ number_format($invoice->total_amount, 2) }} &euro;</td>
    </tr>
    <?php
    $vatInfo = \app\Library\ServiceHelper::vat($invoice->commission_amount, 20);
    ?>
    <tr>
        <td>Commissions HT</td>
        <td style="text-align: center">{{ number_format($vatInfo['price_before_vat'], 2) }} &euro;</td>
    </tr>
    <tr>
        <td>TVA Commission 20%</td>
        <td style="text-align: center">{{ number_format($vatInfo['vat_amount'], 2) }} &euro;</td>
    </tr>
    <tr class="total-row">
        <td>Commission sur vente TTC</td>
        <td style="text-align: center">{{ number_format($invoice->commission_amount, 2) }} &euro;</td>
    </tr>
    <tr class="total-row">
        <td><strong>Montant total du HT</strong></td>
        <td style="text-align: center"><strong>{{ number_format($invoice->grand_total, 2) }} &euro;</strong></td>
    </tr>
    </tbody>
</table>
    {{--<div class="footer">--}}
        {{--Facture établie par <strong>TAMA GROUPE</strong> au nom et pour le compte de--}}
        {{--<strong>{{ $invoice->first_name }} {{ $invoice->last_name }}</strong>.--}}
    {{--</div>--}}
</div>
<!-- Bank Details -->
{{--<div class="bank-details">--}}
    {{--<strong>BANQUE: LCL</strong>--}}
    {{--<strong>IBAN: FR91 3000 2016 3700 0007 1620 S65</strong>--}}
    {{--<strong>BIC: CRLYFRPP</strong>--}}
{{--</div>--}}
<div class="page">
    <h4 style="text-align: center">Détails des commissions</h4>

    <table class="cdr-table">
        <thead>
        <tr>
            <th>Service Tama</th>
            <th>Montant HT</th>
            <th>Commission TTC</th>
            <th>Total Commission</th>
        </tr>
        </thead>
        <tbody>
        @forelse($servicePrintData as $srvData)
            <tr>
                <td>{{ $srvData->service_name }}</td>
                <td>{{ number_format($srvData->total_amount, 2) }} &euro;</td>
                <td>{{ $srvData->commission }}% ttc</td>
                <td>{{ number_format($srvData->commission_amount, 2) }} &euro;</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Aucune donnée disponible</td>
            </tr>
        @endforelse
        <tr class="total-row">
            <td>Total des commissions TTC</td>
            <td colspan="2"></td>
            <td>{{ number_format($invoice->commission_amount, 2) }} &euro;</td>
        </tr>
        </tbody>
    </table>
</div>

<div class="lastpage">
    @forelse($servicePrintData as $srvData)
        <?php
        $invoiceUser = \App\User::find($srvData->user_id);
        $invoiceData = \App\Models\Invoice::find($srvData->invoice_id);
        $fromtodate = str_replace("  au ", "_", $invoiceData->period);
        $period = explode("_", trim($fromtodate));
        $filterDate = [$period[0] . " 00:00:00", $period[1] . " 23:59:59"];
        $cdrs = \app\Library\ServiceHelper::getCDR($srvData->service_id, $invoiceUser->group_id, $invoiceUser->id, $filterDate);
        ?>
        <h4><u>{{ $srvData->service_name }}</u></h4>
        <table class="cdr-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Transaction ID</th>
                <th>Produit</th>
                <th>Prix Public</th>
                <th>Prix Rés</th>
                <th>Marge de Vente</th>
                <th>Mobile</th>
            </tr>
            </thead>
            <tbody>
            @php($sl = 1)
            @forelse($cdrs as $cdr)
                <tr>
                    <td>{{ $sl }}</td>
                    <td>{{ $cdr->date }}</td>
                    <td>{{ $cdr->txn_id }}</td>
                    <td>{!! $cdr->service_id == 1 ? optional(\App\Models\Product::find($cdr->product_id))->name : $cdr->service_name !!}</td>
                    <td>{{ $cdr->public_price }} &euro;</td>
                    <td>{{ $cdr->grand_total }} &euro;</td>
                    <td>{{ $cdr->sale_margin }} &euro;</td>
                    <td>{{ $cdr->service_id == 2 ? $cdr->tt_mobile : $cdr->app_mobile }}</td>
                </tr>
                @php($sl++)
            @empty
                <tr>
                    <td colspan="8">Aucune donnée disponible</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    @empty
    @endforelse
</div>

</body>
</html>

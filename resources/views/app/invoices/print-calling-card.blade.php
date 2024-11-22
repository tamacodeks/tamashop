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
            margin-bottom: 30px;
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
    <tr>
        <td style="height: 60px;">Recharges Téléphoniques HT</td>
        <td style="text-align: center">{{ number_format($invoice->total_amount,2) }} &euro;</td>
    </tr>
    <tr>
        <td style="height: 60px;">TVA</td>
        <td style="text-align: center">0 &euro;</td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #000; background: #f2f2f2">
            <strong>Montant total TTC</strong>
        </td>
        <td style="text-align: center;border-bottom: 1px solid #000; background: #f2f2f2 ">
            <strong >{{ number_format($invoice->grand_total,2) }} &euro;</strong>
        </td>
    </tr>
    </thead>
    <tbody>
</table>
<div style="text-align: center;margin-top:10px;margin-bottom: 50px">
    Autoliquidation de la TVA art 283-2 octies du CGI. TVA due par le
    preneur
</div>
<!-- Bank Details -->
{{--<div class="bank-details">--}}
    {{--<strong>BANQUE: LCL</strong>--}}
    {{--<strong>IBAN: FR91 3000 2016 3700 0007 1620 S65</strong>--}}
    {{--<strong>BIC: CRLYFRPP</strong>--}}
{{--</div>--}}
</div>
<div class="lastpage">
    <h4><u>Cdr's</u></h4>
    <?php
    $invoiceUser = \App\User::find($invoice->user_id);
    $invoiceData = \App\Models\Invoice::find($invoice->id);
    //            dd($invoiceData);
    $fromtodate = str_replace("  au ", "_", $invoice->period);
    $period = explode("_", trim($fromtodate));
    $filterDate = [$period[0] . " 00:00:00", $period[1] . " 23:59:59"];
    $cdrs = \app\Library\ServiceHelper::getCDR(7, $invoiceUser->group_id, $invoiceUser->id, $filterDate);
        //dd($cdrs);
    ?>
    <table class="cdr-table" style="font-size: 11.5px;border-collapse: collapse;width: 100%">
        <thead>
        <tr>
            <th style="text-align: center">#</th>
            <th style="text-align: center">{{ trans('common.lbl_date') }}</th>
            <th style="text-align: center">{{ trans('common.transaction_tbl_trans_id') }}</th>
            <th style="text-align: center">{{ trans('common.lbl_product') }}</th>
            <th style="text-align: center">{{ trans('sale.pin') }}</th>
            <th style="text-align: center">{{ trans('sale.serial') }}</th>
            <th style="text-align: center">{{ trans('common.transaction_tbl_pub_price') }}</th>
            <th style="text-align: center">{{ trans('common.transaction_tbl_res_price') }}</th>
            <th style="text-align: center">{{ trans('common.transaction_tbl_sale_margin') }}</th>
        </tr>
        </thead>
        <tbody>
        @php($sl=1)
        @forelse($cdrs as $cdr)
            <?php
            if($cdr->service_id == 1){
                $product_name = optional(\App\Models\Product::find($cdr->product_id))->name;
            }elseif($cdr->service_id == 5){
                $product_name = $cdr->service_name.' '.\app\Library\AppHelper::formatAmount($cdr->app_currency,$cdr->app_amount_topup);
            }elseif($cdr->service_id == 2 || $cdr->service_id == 7){
                $tt_op = \App\Models\OrderItem::find($cdr->order_item_id);
                $product_name = $cdr->tt_operator == null ? optional($tt_op)->tt_operator :  str_replace("€","&euro;",$cdr->tt_operator);
            }else{
                $iso_code = optional(\App\User::find($cdr->user_id))->currency;
                $price = $cdr->public_price == "0.00" ? $cdr->grand_total : $cdr->public_price;
                $product_name = $cdr->service_name.' '.\app\Library\AppHelper::formatAmount($iso_code,$price);
                $mobile = $cdr->app_mobile;
            }
            $mobile = $cdr->service_id == 2 ? $cdr->tt_mobile : $cdr->app_mobile;
            $pinDetail = \App\Models\PinHistory::where("used_by",$cdr->user_id)->where('date',$cdr->date)->first();
            ?>
            <tr>
                <td style="text-align: center">{{ $sl }}</td>
                <td style="text-align: center">{{ $cdr->date }}</td>
                <td style="text-align: center">{{ $cdr->txn_id }}</td>
                <td style="text-align: center">{!! $product_name !!}</td>
                <td style="text-align: center">{{ optional($pinDetail)->pin }}</td>
                <td style="text-align: center">{{ optional($pinDetail)->serial }}</td>
                <td style="text-align: center">{{ $cdr->public_price }}</td>
                <td style="text-align: center">{{ $cdr->grand_total }}</td>
                <td style="text-align: center">{{ $cdr->sale_margin }}</td>
            </tr>
            @php($sl++)
        @empty
        @endforelse
        </tbody>
    </table>
</div>
</body>
</html>

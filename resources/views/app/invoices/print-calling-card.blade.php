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

        #goods tr:nth-last-child(2) td {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        p {
            margin: 0;
        }
        .cdr-table td,  th {
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
                font-size: 10pt;
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
                <img src="{{ public_path('images/logo.png') }}" alt="TamaShop Logo">
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
            <td style="width: 10%;">
                <strong>Date:</strong> {{ $invoiceDate }}
                <br><strong>Période:</strong> {{ $invoice->period }}
                <br><strong>Numéro de facture:</strong> {{ $invoice->invoice_ref }}
                <br><strong>BANQUE: LCL</strong>
                <br><strong> IBAN :FR91 3000 2016 3700 0007 1620 S65 </strong>
                <br><strong>BIC : CRLYFRPP</strong>
            </td>
            <td style="width: 80%"></td>
            <td><strong>{{ isset($invoice) ? $invoice->first_name ." ".$invoice->last_name : "" }}</strong>
                <br>{!! $invoice->address !!}
                <br>France
                <br>Customer ID: {{ $invoice->cust_id }}
                <br>TVA intracom: {{ $invoice->tva_no }}
            </td>
        </tr>
        </tbody>
    </table>

    <br><br><br>

<p style="border-bottom: 5px solid #ff0000"></p>


<h4>FACTURATION</h4>
<table class="table">
    <tbody>
    <tr>
        <td>
            <table class="table" id="goods" style="border: 1px solid #fff;">
                <thead>
                <tr style="border-bottom: 1px solid; background: #f2f2f2">
                    <td style="width: 85%"></td>
                    <td style="text-align: center;width: 15%;white-space: pre-wrap; font-weight: bold">MONTANT</td>

                </tr>
                </thead>
                <tbody>
                <tr>
                    <td style="height: 60px;">Recharges Téléphoniques HT</td>
                    <td style="text-align: right">{{ number_format($invoice->total_amount,2) }} &euro;</td>
                </tr>
                <tr>
                    <td style="height: 60px;">TVA</td>
                    <td style="text-align: right">0 &euro;</td>
                </tr>


                <tr>
                    <td style="border-bottom: 1px solid #000; background: #f2f2f2">
                        <strong>Montant total TTC</strong>
                    </td>
                    <td style="text-align: right; border-bottom: 1px solid #000; background: #f2f2f2">
                        <strong>{{ number_format($invoice->grand_total,2) }} &euro;</strong>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<div style="text-align: center;margin-top:10px;margin-bottom: 50px">
    Autoliquidation de la TVA art 283-2 octies du CGI. TVA due par le
    preneur
</div>
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

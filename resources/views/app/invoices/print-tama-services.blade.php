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
            <td style="width: 35%"><strong>TAMA GROUPE SASU</strong>
                <br>131 Rue de Crequi
                <br>69006 Lyon
                <br>France
                <br>+33176660340
                <br>billing@tamaexpress.com
                <br>TVA intracom: FR 41823939285
            </td>
            <td><img src="{{ public_path('images/tama_logo.png') }}" style="width: 130px"></td>
        </tr>
        </tbody>
    </table>
    <br><br><br>

    <table class="table">
        <tbody>
        <tr>
            <td style="width: 100%"></td>
            <td><strong>{{ isset($invoice) ? $invoice->first_name ." ".$invoice->last_name : "" }}</strong>
                <br>{!! $invoice->address !!}
                <br>France
                <br>Customer ID: {{ $invoice->cust_id }}
                <br>TVA intracom: {{ $invoice->tva_no }}
            </td>
        </tr>
        </tbody>
    </table>
    <br>

    <p style="border-bottom: 5px solid #ff0000"></p>
    <br><br>

    <table class="table">
        <tbody>
        <tr>
            <?php
            $invoiceDate = \Illuminate\Support\Carbon::parse($invoice->year."-".$invoice->month)->startOfMonth()->addMonth()->toDateString();
            ?>
            <td style="width: 35%"><strong>Date:</strong> {{ $invoiceDate }}
                <br><strong>Période:</strong> {{ $invoice->period }}
                <br><strong>Numéro de facture:</strong> {{ $invoice->invoice_ref }}</td>
            <td></td>
        </tr>
        </tbody>
    </table>
    <br>
    <div style="border-bottom:1px solid #000;">
        <div style="display:inline-block"><h4>FACTURATION</h4></div>
        <div style="display:inline-block; float:right"><h4>MONTANT</h4></div>
    </div>
    <table class="table" id="goods" style="border: 1px solid #fff;">
        <tbody>
        <tr>
            <td style="border-top: 1px #000 solid"></td>
            <td style="border-top: 1px #000 solid"></td>
            <td style="border-top: 1px #000 solid"></td>
        </tr>
        <tr style="border-bottom: 1px #000 solid">
            <td style="height: 30px;"></td>
            <td style="text-align: center"></td>
            <td style="text-align: right"></td>
        </tr>
        <tr>
            <td style="height: 30px;">Ventes services Tama collectées TTC</td>
            <td style="text-align: center"></td>
            <td style="text-align: right">{{ number_format($invoice->total_amount,2) }} &euro;</td>
        </tr>
        <tr>
            <td style="height: 60px;"></td>
            <td></td>
            <td></td>
        </tr>
        <?php
        $vatInfo = \app\Library\ServiceHelper::vat($invoice->commission_amount, 20);
        ?>
        <tr>
            <td>Commissions HT</td>
            <td style="text-align: center">&nbsp;</td>
            <td style="text-align: right">{{ $vatInfo['price_before_vat'] }}  &euro;</td>
        </tr>
        <tr>
            <td style="height: 40px;">TVA Commission 20%</td>
            <td style="text-align: center">&nbsp;</td>
            <td style="text-align: right">{{ $vatInfo['vat_amount']  }}  &euro;</td>
        </tr>
        <tr>
            <td style="height: 30px;"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px #000 solid">Commission sur vente TTC</td>
            <td style="text-align: center;border-bottom: 1px #000 solid">&nbsp;</td>
            <td style="text-align: right;border-bottom: 1px #000 solid">{{ $invoice->commission_amount }}  &euro;</td>
        </tr>
        <tr>
            <td style="height: 60px;"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #000; background: #f2f2f2">
                <strong>Montant total du TTC</strong>
            </td>
            <td style="text-align: center; border-bottom: 1px solid #000; background: #f2f2f2"></td>
            <td style="text-align: right; border-bottom: 1px solid #000; background: #f2f2f2">
                <strong>{{ $invoice->grand_total }} &euro;</strong>
            </td>
        </tr>
        </tbody>
    </table>
    <div style="text-align: center;margin-top:10px;margin-bottom: 100px">
        Facture etablie par TAMA GROUPE au nom et pour le compte
        de {{ isset($invoice) ? $invoice->first_name ." ".$invoice->last_name : "" }}
    </div>
</div>
<div class="page">
    <h4 style="text-align: center">Details des commissions</h4>
    <br><br><br>
    <table class="cdr-table" style="font-size: 13px;border-collapse: collapse;width: 100%;">
        <thead>
            <tr style="background: #f2f2f2">
                <td style="text-align: center">Service Tama</td>
                <td style="text-align: center">Montant TTC</td>
                <td style="text-align: center">Commission TTC</td>
                <td style="text-align: center">Total Commission</td>
            </tr>
        </thead>
        <tbody>
        @forelse($servicePrintData as $srvData)
            <tr>
                <td style="height: 30px;">Services({{ $srvData->service_name }})</td>
                <td style="text-align: center">{{ $srvData->total_amount }} &euro;</td>
                <td style="text-align: center">{{ $srvData->commission }}% ttc</td>
                <td style="text-align: right">{{$srvData->commission_amount }}  &euro;</td>
            </tr>
        @empty
        @endforelse
        <tr>
            <td style="border-bottom: 1px solid #000; background: #f2f2f2">
                <strong>Total des commissions TTC</strong>
            </td>
            <td style="text-align: center; border-bottom: 1px solid #000; background: #f2f2f2"></td>
            <td style="background: #f2f2f2">&nbsp;</td>
            <td style="text-align: right; border-bottom: 1px solid #000; background: #f2f2f2">
                <strong>{{$invoice->commission_amount }}  &euro;</strong>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<div class="lastpage">
    @forelse($servicePrintData as $srvData)
        <?php
        $invoiceUser = \App\User::find($srvData->user_id);
        $invoiceData = \App\Invoice::find($srvData->invoice_id);
        //        dd($invoiceData->period);
        $fromtodate = str_replace("  au ", "_", $invoiceData->period);
        $period = explode("_", trim($fromtodate));
        $filterDate = [$period[0] . " 00:00:00", $period[1] . " 23:59:59"];
        $cdrs = \app\Library\ServiceHelper::getCDR($srvData->service_id, $invoiceUser->group_id, $invoiceUser->id, $filterDate);
        ?>
        <h4><u>{{ $srvData->service_name }}</u></h4>
        <table class="cdr-table" style="font-size: 13px;border-collapse: collapse;width: 100%;">
            <thead>
            <tr>
                <th style="text-align: center">#</th>
                <th style="text-align: center">{{ trans('common.lbl_date') }}</th>
                <th style="text-align: center">{{ trans('common.transaction_tbl_trans_id') }}</th>
                <th style="text-align: center">{{ trans('common.lbl_product') }}</th>
                <th style="text-align: center">{{ trans('common.transaction_tbl_pub_price') }}</th>
                <th style="text-align: center">{{ trans('common.transaction_tbl_res_price') }}</th>
                <th style="text-align: center">{{ trans('common.transaction_tbl_sale_margin') }}</th>
                <th style="text-align: center">{{ trans('tamatopup.mobile') }}</th>
            </tr>
            </thead>
            <tbody>
            @php($sl=1)
            @forelse($cdrs as $cdr)
                <?php
                if ($cdr->service_id == 1) {
                    $product_name = str_replace("€", "&euro;", optional(\App\Models\Product::find($cdr->product_id))->name);
                } elseif ($cdr->service_id == 5) {
                    $product_name = $cdr->service_name . ' ' . "&euro; ".$cdr->app_amount_topup;
                } elseif ($cdr->service_id == 2 || $cdr->service_id == 7) {
                    $tt_op = \App\Models\OrderItem::find($cdr->order_item_id);
                    $product_name = $cdr->tt_operator == null ? optional($tt_op)->tt_operator : $cdr->tt_operator;
                } else {
                    $price = $cdr->public_price == "0.00" ? $cdr->grand_total : $cdr->public_price;
                    $product_name = $cdr->service_name . ' ' . "&euro; ".$price;
                    $mobile = $cdr->app_mobile;
                }
                $mobile = $cdr->service_id == 2 ? $cdr->tt_mobile : $cdr->app_mobile;
                ?>
                <tr>
                    <td>{{ $sl }}</td>
                    <td style="text-align: center">{{ $cdr->date }}</td>
                    <td style="text-align: center">{{ $cdr->txn_id }}</td>
                    <td style="text-align: center">{!! $product_name !!}</td>
                    <td style="text-align: center">{{ $cdr->public_price }}</td>
                    <td style="text-align: center">{{ $cdr->grand_total }}</td>
                    <td style="text-align: center">{{ $cdr->sale_margin }}</td>
                    <td style="text-align: center">{{ $mobile }}</td>
                </tr>
                @php($sl++)
            @empty
            @endforelse
            </tbody>
        </table>
    @empty
    @endforelse
</div>
</body>
</html>

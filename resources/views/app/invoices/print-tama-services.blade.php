<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->username }} Invoice No {{ $invoice->invoice_ref }}</title>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .table {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
            margin-bottom: 20px;
        }

        .table td {
            padding: 10px;
        }

        .table img {
            width: 130px;
        }

        .invoice-details {
            width: 40%;
            text-align: right;
            line-height: 1.5;
        }

        .invoice-details strong {
            display: block;
            margin-bottom: 5px;
        }

        h4 {
            text-align: center;
            font-size: 18px;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .separator {
            border-bottom: 4px solid #ff0000;
            margin: 20px 0;
        }

        #goods {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        #goods td {
            padding: 10px;
            border-top: 1px solid #000;
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
<body>

<div class="page">

    <!-- Header Section -->
    <table class="table">
        <tbody>
        <tr>
            <!-- Logo Column -->
            <td style="width: 60%;">
                <img src="{{ public_path('images/tama_logo.png') }}" alt="TAMA Group Logo">
            </td>

            <!-- Invoice Details Column -->
            <td class="invoice-details">
                <?php
                $invoiceDate = \Illuminate\Support\Carbon::parse($invoice->year . "-" . $invoice->month)->startOfMonth()->addMonth()->toDateString();
                ?>
                <strong>Date:</strong> {{ $invoiceDate }}
                <strong>Période:</strong> {{ $invoice->period }}
                <strong>Numéro de facture:</strong> {{ $invoice->invoice_ref }}
                <strong>BANQUE: LCL</strong>
                <strong>IBAN: FR91 3000 2016 3700 0007 1620 S65</strong>
                <strong>BIC: CRLYFRPP</strong>
            </td>
        </tr>
        </tbody>
    </table>

    <!-- Customer Information Section -->
    <table class="table">
        <tbody>
        <tr>
            <td>
                <strong>{{ $invoice->first_name }} {{ $invoice->last_name }}</strong><br>
                {!! nl2br($invoice->address) !!}<br>
                France<br>
                <strong>Customer ID:</strong> {{ $invoice->cust_id }}<br>
                <strong>TVA intracom:</strong> {{ $invoice->tva_no }}
            </td>
        </tr>
        </tbody>
    </table>

    <div class="separator"></div>

    <!-- Facturation Section -->
    <div style="border-bottom:1px solid #000; padding-bottom: 5px;">
        <h4 style="display:inline-block;">FACTURATION</h4>
        <h4 style="display:inline-block; float:right;">MONTANT</h4>
    </div>

    <table id="goods">
        <tbody>
        <tr>
            <td>Ventes services Tama collectées TTC</td>
            <td style="text-align: right">{{ number_format($invoice->total_amount, 2) }} &euro;</td>
        </tr>
        <?php
        $vatInfo = \app\Library\ServiceHelper::vat($invoice->commission_amount, 20);
        ?>
        <tr>
            <td>Commissions HT</td>
            <td style="text-align: right">{{ number_format($vatInfo['price_before_vat'], 2) }} &euro;</td>
        </tr>
        <tr>
            <td>TVA Commission 20%</td>
            <td style="text-align: right">{{ number_format($vatInfo['vat_amount'], 2) }} &euro;</td>
        </tr>
        <tr class="total-row">
            <td>Commission sur vente TTC</td>
            <td style="text-align: right">{{ number_format($invoice->commission_amount, 2) }} &euro;</td>
        </tr>
        <tr class="total-row">
            <td><strong>Montant total du TTC</strong></td>
            <td style="text-align: right"><strong>{{ number_format($invoice->grand_total, 2) }} &euro;</strong></td>
        </tr>
        </tbody>
    </table>

    <!-- Footer Section -->
    <div class="footer">
        Facture établie par <strong>TAMA GROUPE</strong> au nom et pour le compte de
        <strong>{{ $invoice->first_name }} {{ $invoice->last_name }}</strong>.
    </div>

</div>

<div class="page">
    <h4 style="text-align: center">Détails des commissions</h4>

    <table class="cdr-table">
        <thead>
        <tr>
            <th>Service Tama</th>
            <th>Montant TTC</th>
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

@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => trans('common.menu_my_services'),'url'=> "",'active' => 'no'],
        ['name' => "Aleda",'url'=> secure_url('aleda/manage'),'active' => 'no'],
        ['name' => trans('myservice.vw_statistics'),'url'=> '','active' => 'yes'],
    ]])
    <link href="{{ secure_asset('vendor/date-picker/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ trans('common.dashboard_view_orders') }}</h3>
                    </div>
                    <div class="panel-body">
                        <form method="post" action="{{secure_url('trans')}}" enctype="multipart/form-data">
                            {{ csrf_field() }} {{ method_field('POST') }}
                            <div class="row">
                                <div class="form-group col-md-5">
                                    <div class="controls">
                                        <input type="text" class="form-control date" name="from_date" id="from_date" >
                                    </div>
                                </div>
                                <div class="form-group col-md-5">
                                    <div class="controls">
                                        <input type="text" class="form-control date" name="to_date" id="to_date" >
                                    </div>
                                </div>
                                <div class="controls">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-users fa-4x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">{{ isset($aleda_trans) ? $aleda_trans : 0 }}</div>
                                    <div class="huge-next">{{ trans('common.dashboard_total_resellers') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--<div class="col-md-3">--}}
                {{--<div class="panel panel-default">--}}
                {{--<div class="panel-heading">--}}
                {{--<div class="row">--}}
                {{--<div class="col-xs-3">--}}
                {{--<i class="fa fa-users fa-4x"></i>--}}
                {{--</div>--}}
                {{--<div class="col-xs-9 text-right">--}}
                {{--<div class="huge">{{ isset($total_trans) ? $total_trans : 0 }}</div>--}}
                {{--<div class="huge-next">{{ trans('common.dashboard_total_resellers') }}</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                <div class="panel" style="margin-top: -20px">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="orders-table"  class="table table-striped">
                                <thead>
                                <tr>
                                    <th width="30">No</th>
                                    <th>Card Name</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $total =0;
                                $total1 =0;
                                $total2 =0;
                                ?>
                                @if($total_orders)
                                    @forelse($total_orders as $refal_code)
                                        <?php   $t= $refal_code->buying_price * $refal_code->total;
                                        $total += $refal_code->total;
                                        $total1 += $refal_code->buying_price;
                                        $total2 += $t;
                                        ?>
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$refal_code->tt_operator}}</td>
                                            <td>{{$refal_code->total}}</td>
                                            <td>{{$refal_code->buying_price}}</td>
                                            <td>{{$t}}</td>
                                        </tr>
                                @endforeach
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th class="text-left">{{ trans('common.lbl_total') }}</th>
                                    <th>{{$total}}</th>
                                    <th>{{$total1}}</th>
                                    <th>{{$total2}}</th>
                                </tr>
                                </tfoot>
                                @else
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th class="text-left">Nothing To Show</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    @endif
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <link href="{{ secure_asset('vendor/datatables/datatables.css') }}" rel="stylesheet">
    <script src="{{ secure_asset('vendor/datatables/datatables.js') }}"></script>
    <script src="{{ secure_asset('vendor/datatables/app.js') }}"></script>
    <script src="{{ secure_asset('vendor/date-picker/jquery-ui.js') }}"></script>
    <script src="{{ secure_asset('vendor/select-picker/js/bootstrap-select.js') }}"></script>
    <script>
        $( function() {
            $( ".date" ).datepicker({
                showButtonPanel: true,
                changeMonth: true,
                changeYear: true,
                dateFormat : "yy-mm-dd",
                showAnim : "slideDown"
            });
        } );
        function filter_offers()
        {
            var from = $('#from_date').val();
            var to = $('#to_date').val();
            if(from=='')
            {
                $.toast({
                    heading: 'Please Select From Date',
                    position: 'top-right',
                    loaderBg:'#ff6849',
                    icon: 'error',
                    hideAfter: 3500,
                    stack: 6
                });
            }
            $.ajax({
                type: "POST",
                url: '{{secure_url('filter_offer1123')}}',
                data: {from:from,to:to},
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                dataType: "json",
                success: function(res) {
                    $.toast({
                        heading: 'Status Updated Sucessfully',
                        position: 'top-right',
                        loaderBg:'#ff6849',
                        icon: 'success',
                        hideAfter: 3500,
                        stack: 6
                    }),
                        location.reload();

                },
                error: function (jqXHR, exception) {
                    $.toast({
                        heading: 'Something went Wrong',
                        position: 'top-right',
                        loaderBg:'#ff6849',
                        icon: 'error',
                        hideAfter: 3500,
                        stack: 6
                    });
                }
            });
        }
    </script>
@endsection
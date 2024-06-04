@extends('layout.payment_app')
@section('content')
    @include('layout.breadcrumb',['data' => [
        ['name' => trans('common.payments'),'url'=> '','active' => 'yes']
    ]
    ])
    <link href="{{ secure_asset('css/payment.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('vendor/date-picker/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ trans('common.payments') }}</h3>
                        <div class="pull-right" style="margin-top: -23px;">
                            @if(auth()->user()->group_id != 4)
                                <a href="{{ secure_url('payment/add') }}" class="btn btn-theme btn-sm"><i class="fa fa-plus-circle"></i>&nbsp;{{ trans('common.payment_btn_add_payment') }}</a>
                            @endif
                        </div>
                    </div>
                    @if(auth()->user()->group_id == 4)
                        <div class="panel-body">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="design-process-content">
                                        <form id="payment-form" class="sr-payment-form">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th>Types</th>
                                                                <th>Overall Limit</th>
                                                                <th>Reminaing Limit</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td>Daily Limit</td>
                                                                <td>{{ \app\Library\AppHelper::formatAmount('EUR',auth()->user()->daily) }}</td>
                                                                <td>{{ \app\Library\AppHelper::formatAmount('EUR',$remaining_daily) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Weekly Limit</td>
                                                                <td>{{ \app\Library\AppHelper::formatAmount('EUR',auth()->user()->weekly) }}</td>
                                                                <td>{{ \app\Library\AppHelper::formatAmount('EUR',$remaining_weekly) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Monthly Limit</td>
                                                                <td>{{ \app\Library\AppHelper::formatAmount('EUR',auth()->user()->monthly) }}</td>
                                                                <td>{{ \app\Library\AppHelper::formatAmount('EUR',$remaining_monthly) }}</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="panel panel-success">
                                                        <div class="panel-heading">
                                                            <h4 class="text-center">Your Balance is {{ \app\Library\AppHelper::getBalance(auth()->user()->id,auth()->user()->currency,true) }}</h4>
                                                        </div>
                                                        <div class="panel-body">
                                                            <div class="input-group">
                                                                <span class="input-group-addon">&euro;</span>
                                                                <input class="form-control amount" type="text" id="amount" name="amount" placeholder="{{ min }} to {{ max }}" max="3" min="1">
                                                            </div>


                                                            <span id="span" class="hide" ><h5 class="text-center" style="color: red;">Please Enter From {{ min }} to {{ max }} </h5></span>

                                                            <span id="limit" class="hide" ><h5 class="text-center" style="color: red;">Daily Limit Exceed Please Contact Manager </h5></span>

                                                            <span id="weeklyLimit" class="hide" ><h5 class="text-center" style="color: red;">Weekly Limit Exceed Please Contact Manager </h5></span>

                                                            <span id="monthlyLimit" class="hide" ><h5 class="text-center" style="color: red;">Monthly Limit Exceed Please Contact Manager </h5></span>

                                                            <input class="form-control" type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}">

                                                            <div class="col-xs-12 col-sm-12 emphasis" id="nonstripe">
                                                                <a href="#" id="proceed" class="btn btn-theme btn-block" ><i class="fa fa-money"></i>&nbsp;Proceed To Pay</a>
                                                            </div>

                                                            <div class="col-xs-12 col-sm-12 emphasis hide"  id="stripe">
                                                                <div class="sr-combo-inputs-row">
                                                                    <div class="sr-input sr-card-element" id="card-element"></div>
                                                                </div>
                                                                <div class="sr-field-error" id="card-errors" role="alert" style="color: red;"></div>
                                                                <button id="submit" class="btn btn-primary btn-block emphasis">
                                                                    <div class="spinner hidden" id="spinner"></div>
                                                                    <span id="button-text">Pay</span><span id="order-amount"></span>
                                                                </button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2"></div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="panel-body">
                        <form method="POST" id="search-form" class="form-inline" role="form">
                            @if(auth()->user()->group_id != 4)
                                <div class="form-group">
                                    <label for="retailer_id">{{ trans('common.order_tbl_retailer') }}</label>
                                    <select data-actions-box="true" data-select-all-text="{{ trans('common.lbl_select_all') }}"  data-deselect-all-text="{{ trans('common.lbl_deselect_all') }}" data-none-results-text="{{ trans('common.no_result_matched') }}" title="{{ trans('common.lbl_please_choose') }}"  data-size="8" data-live-search="true" name="retailer_id" id="retailer_id" class="select-picker" multiple>
                                        @foreach($retailers as $retailer)
                                            <option value="{{ $retailer->id }}" @if(request()->get('user') == $retailer->username) selected @endif>{{ $retailer->username }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="from_date">{{ trans('common.filter_lbl_from') }}</label>
                                <input type="text" class="form-control date" name="from_date" id="from_date" >
                            </div>
                            <div class="form-group">
                                <label for="to_date">{{ trans('common.filter_lbl_to') }}</label>
                                <input type="text" class="form-control date" name="to_date" id="to_date" >
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i>&nbsp;{{ trans('myservice.btn_search') }}</button>
                        </form>
                    </div>
                </div>
                <div class="panel" style="margin-top: -20px">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="payments-table" class="table table-condensed">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ trans('common.order_tbl_sl') }}</th>
                                    <th>{{ trans('common.lbl_intiated_date') }}</th>
                                    <th>{{ trans('common.lbl_updated_date') }}</th>
                                    <th>{{ trans('common.payment_tbl_cust_id') }}</th>
                                    <th>{{ trans('common.order_tbl_retailer') }}</th>
                                    <th>{{ trans('common.payment_tbl_paid_amount') }}</th>
                                    <th>{{ trans('common.payment_tbl_prev_bal') }}</th>
                                    <th>{{ trans('common.payment_tbl_cur_bal') }}</th>
                                    <th>{{ trans('common.payment_tbl_comment') }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <script id="details-template" type="text/x-handlebars-template">
                            <table class="table table-bordered">
                                <tbody>
                                </tbody>
                            </table>
                        </script>
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
    <script src="{{ secure_asset('vendor/common/handlebars-v4.0.11.js') }}"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the input element
            var amountInput = document.getElementById('amount');

            // Attach an event listener to the input element
            amountInput.addEventListener('input', function(event) {
                // Get the current value of the input
                var inputValue = event.target.value;

                // Remove any non-numeric characters from the input
                inputValue = inputValue.replace(/\D/g, '');

                // Limit the input to 3 characters
                inputValue = inputValue.slice(0, 3);

                // Update the input value with the sanitized value
                event.target.value = inputValue;
            });
        });
    </script>
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
        $(document).ready(function () {
            var template = Handlebars.compile($("#details-template").html());
            $(".select-picker").selectpicker();
            var oTable = $('#payments-table').DataTable({
                "autoWidth": false,
                searching: false,
                "pageLength": "10",
                processing: "<span class='loader'></span>",
                language: {
                    "processing": "{{ trans('common.processing') }}"
                },
                serverSide: true,
                ajax: {
                    url: '{{ secure_url('fetch/payments') }}',
                    data: function (d) {
                        d.retailer_id = $('#retailer_id').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    }
                },
                columns: [
                    {
                        "className":      '',
                        orderable:      false,
                        searchable:     false,
                        "data":           null,
                        "defaultContent": ''
                    },
                    {
                        "className":      '',
                        "orderable":      false,
                        "searchable":     false,
                        "data":           null,
                        "defaultContent": ''
                    },
                    {data: 'date', name: 'payments.date'},
                    {data: 'payment_date', name: 'transactions.date'},
                    {data: 'cust_id', name: 'users.cust_id',searchable : false,orderable : false},
                    {data: 'username', name: 'users.username',searchable : false,orderable : false},
                    {data: 'amount', name: 'payments.amount',searchable : false,orderable : false},
                    {data: 'prev_bal', name: 'transactions.prev_bal',searchable : false,orderable : false},
                    {data: 'balance', name: 'transactions.balance',searchable : false,orderable : false},
                    { data:'prev_bal',
                        render: function(data, type, full) {
                            var ret = '';
                            if (!full.prev_bal) {
                                ret += '{{ trans('common.payment') }} '+ full.amount +'  € {{ trans('common.initiated') }} '+ full.username +' {{ trans('common.account_login') }}';
                            } else {
                                ret += full.description;
                            }
                            return ret;
                        }
                    }
                ],
                dom: 'Bfrtip',
                // Configure the drop down options.
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 {{ trans('users.records') }}', '25 {{ trans('users.records') }}', '50 {{ trans('users.records') }}', '{{ trans('users.show_all') }}' ]
                ],
                // Add to buttons the pageLength option.
                buttons: [
                    'pageLength',
                    {
                        extend:    'excel',
                        text:      '<i class="fa fa-file-excel"></i>',
                        titleAttr: '{{ trans('common.download_as_excel') }}'
                    },
                    {
                        extend:    'reload',
                        text:      '<i class="fa fa-sync"></i>',
                        titleAttr: '{{ trans('common.refresh') }}'
                    }
                ]
            });

            oTable.on('order.dt search.dt', function () {
                oTable.column(1, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();
            $('#search-form').on('submit', function(e) {
                oTable.draw();
                e.preventDefault();
            });

            // Add event listener for opening and closing details
            $('#payments-table tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = oTable.row( tr );

                if ( row.child.isShown() ) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    // Open this row
                    row.child( template(row.data()) ).show();
                    tr.addClass('shown');
                }
            });
            $('#amount').keyup(function () {
                $("#script_data").attr({
                    "data-amount": $('#amount').val(),
                });

                var span = document.getElementById("span");
                var limit = document.getElementById("limit");
                var weeklyLimit = document.getElementById("weeklyLimit");
                var monthlyLimit = document.getElementById("monthlyLimit");

                span.classList.add("hide");
                limit.classList.add("hide");
                weeklyLimit.classList.add("hide");
                monthlyLimit.classList.add("hide");

                var element = document.getElementById("stripe");
                var element1 = document.getElementById("nonstripe");

                var min = {{ min }};
                var max = {{ max }};
                var amn = $('#amount').val();
                var daily = {{ $remaining_daily }};
                var weekly = {{ $remaining_weekly }};
                var monthly = {{ $remaining_monthly }};

                if (!amn) {
                    element.classList.add("hide");
                    element1.classList.remove("hide");
                    span.classList.add("hide");
                } else {
                    if (amn < min || amn > max) {
                        element.classList.add("hide");
                        element1.classList.remove("hide");
                        span.classList.remove("hide");
                    } else {
                        if (amn > daily) {
                            element.classList.add("hide");
                            limit.classList.remove("hide");
                            element1.classList.remove("hide");
                        } else if (amn > weekly) {
                            element.classList.add("hide");
                            weeklyLimit.classList.remove("hide");
                            element1.classList.remove("hide");
                        } else if (amn > monthly) {
                            element.classList.add("hide");
                            monthlyLimit.classList.remove("hide");
                            element1.classList.remove("hide");
                        } else {
                            element.classList.remove("hide");
                            span.classList.add("hide");
                            element1.classList.add("hide");
                        }
                    }
                }
            });


            $('#proceed').click(function () {
                var amn =  $('#amount').val();
                if(!amn)
                {
                    span.classList.remove("hide");
                }
            });
        });
    </script>
    <script>
        var stripe = Stripe("{{STRIPE_KEY}}");
        var elements = stripe.elements();
        var card = elements.create('card');
        card.mount('#card-element');

        card.addEventListener('change', function(event) {
            var displayError = document.getElementById('card-errors');
            displayError.textContent = event.error ? event.error.message : '';
        });

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            $("#payment-form").LoadingOverlay('show');
            event.preventDefault();

            stripe.createPaymentMethod({
                type: 'card',
                card: card,
                billing_details: {
                    // Include any additional collected billing details.
                    name: '{{auth()->user()->username}}',
                },
            }).then(function(result) {
                if (result.error) {
                    showResponse('Failed',result.error);
                } else {
                    var amount_send = $("#amount").val();
                    handlePayment(result.paymentMethod.id, amount_send);
                }
            });
        });

        function handlePayment(paymentMethodId, amount) {
            var URL = "{{ secure_url('/api/payment/create') }}";
            fetch(URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    amount: amount,
                    user_id: "{{ auth()->user()->id }}",
                    payment_method_id: paymentMethodId,
                })
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                if (data.status === 'succeeded' ) {
                    savePaymentDetails();
                }else if(data.payment_intent_client_secret){
                    confirmPayment(data.payment_intent_client_secret, paymentMethodId);
                }else {
                    showResponse('Failed2',data.error);
                }
            }).catch(function(error) {
                showResponse('Failed1',error);
            });
        }

        function confirmPayment(clientSecret, paymentMethodId) {
            stripe.confirmCardPayment(clientSecret, {
                payment_method: paymentMethodId
            }).then(function(confirmResult) {
                if (confirmResult.error) {
                    showResponse('Failed',confirmResult.error.message);
                } else {
                    retrievePaymentDetails(confirmResult.paymentIntent.id);
                }
            });
        }

        function retrievePaymentDetails(paymentIntentId) {
            var URL = "{{ secure_url('/api/payment/retrieve') }}";
            fetch(URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    payment_intent_id: paymentIntentId
                })
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                if (data.status === 'success') {
                    savePaymentDetails();
                } else {
                    showResponse(data.error,data.error.message);
                }
            }).catch(function(error) {
                showResponse('Failed',error);

            });
        }

        function savePaymentDetails() {
            var amount_send = $("#amount").val();
            var URL = "{{ secure_url('/api/payment/save') }}";
            fetch(URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    amount: amount_send,
                    user_id: "{{ auth()->user()->id }}",
                })
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                showResponse('Success','Payment Completed');
            }).catch(function(error) {
                showResponse('Failed',error);
            });
        }

        function showResponse(Status,Message) {
            $("#payment-form").LoadingOverlay('hide');
            $.confirm({
                theme: 'material',
                title: Status,
                content: Message,
                autoClose: 'redirection|5000',
                buttons: {
                    OK: function() {
                        location.reload();
                    },
                    redirection: {
                        action: function() {
                            location.reload();
                        }
                    },
                }
            });
        }
    </script>

@endsection
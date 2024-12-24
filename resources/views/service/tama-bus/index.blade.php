@extends('layout.app')
@section('content')
    @include('layout.breadcrumb',['data' => [
    ['name' => "FlixBus",'url'=> '','active' => 'yes']
    ]
    ])
    <link href="{{ secure_asset('css/bus-booking.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ secure_asset('vendor/date-picker/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ secure_asset('css/topup.css') }}" rel="stylesheet">
    <div id="loadergif" data-text="Chargement, veuillez patienter"></div>
    <div class="container-fluid" id="bookContainer">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <img src="{{ secure_asset('images/logo-big.png') }}" class="home-logo">
                                </div>
                                <div class="col-md-8">
                                    <div class="booking-fields">
                                        <form id="frmBusBook" class="form-horizontal">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="inputWithIcon">
                                                        <input type="text" class="booking-input" placeholder="Lyon"
                                                               name="cityFrom"  id="cityFrom" required>
                                                        <input type="hidden"  name="cityFromHid"
                                                               id="cityFromHid">
                                                        <i class="fas fa-map-marker-alt fa-lg fa-fw" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="inputWithIcon">
                                                        <input type="text" class="booking-input" placeholder="Paris"
                                                               name="cityTo" id="cityTo">
                                                        <input type="hidden"  name="cityToHid" id="cityToHid" >
                                                        <i class="fas fa-map-marker-alt fa-lg fa-fw" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="inputWithIcon">
                                                        <input type="text" class="date booking-input"
                                                               name="departureDate" id="departureDate"
                                                               placeholder="Departure" value="{{ date("Y-m-d") }}" readonly>
                                                        <i class="far fa-calendar-alt fa-lg fa-fw"
                                                           aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="inputWithIcon">
                                                        <div class="popover-markup">
                                                            <div class="trigger  form-group-icon-left">
                                                                <input type="text" name="passengers" id="passengers" placeholder="{{ trans('service.passengers') }}"
                                                                       class="booking-input" readonly>
                                                                <i class="far fa-user fa-lg fa-fw"
                                                                   aria-hidden="true"></i>
                                                            </div>
                                                            <div class="content hide">
                                                                <div class="form-group row">
                                                                    <label class="control-label col-md-8"><strong>{{ trans('service.adult') }}</strong><br>
                                                                        <span class="text-muted sub-text"> from 15 years</span></label>
                                                                    <div class="input-group number-spinner col-md-4">
                        <span class="input-group-btn">
                            <a class="btn btn-default" data-choser="adult" data-dir="dwn"><span
                                        class="glyphicon glyphicon-minus"></span></a>
                        </span>
                                                                        <input type="text" name="adult" id="adult"
                                                                               class="form-control text-center"
                                                                               max=9 min=0>
                                                                        <span class="input-group-btn">
                            <a class="btn btn-default" data-choser="adult" data-dir="up"><span
                                        class="glyphicon glyphicon-plus"></span></a>
                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="control-label col-md-8"><strong>{{ trans('service.children') }}</strong><br>
                                                                        <span class="text-muted sub-text"> 0 to 14 years
                                                                    </span></label>
                                                                    <div class="input-group number-spinner col-md-4">
                        <span class="input-group-btn">
                            <a class="btn btn-default" data-choser="child" data-dir="dwn"><span
                                        class="glyphicon glyphicon-minus"></span></a>
                        </span>
                                                                        <input type="text" name="child" id="child"
                                                                               class="form-control text-center"  max=9
                                                                               min=0>
                                                                        <span class="input-group-btn">
                            <a class="btn btn-default" data-choser="child" data-dir="up"><span
                                        class="glyphicon glyphicon-plus"></span></a>
                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="pull-right">
                                                <div class="pull-right">
                                                    <a href="#" class="btn btn-primary"  id="frmBusBookbtn">search</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="margin-bottom col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class ="row margin-bot">
                            <div class="col-md-4">
                                <h4>{{trans('service.sort_by')}}</h4>
                                <select class="form-control" name="sort_by" id="sort_by">
                                    <option value="1">{{trans('service.departure_ear')}}</option>
                                    <option value="2">{{trans('service.price_low')}}</option>
                                    <option value="3">{{trans('service.durations_short')}} </option>
                                </select>
                            </div>
                            {{--<div class="col-md-4">--}}
                            {{--<h4>Departure From</h4>--}}
                            {{--<select class="form-control" name="departure_filter" id="departure_filter">--}}
                            {{--<option value="1">9:00 - 12:00</option>--}}
                            {{--<option value="2">12:00 - 3:00</option>--}}
                            {{--<option value="3">3:00 - 6:00</option>--}}
                            {{--</select>--}}
                            {{--</div>--}}
                            {{--<div class="col-md-4">--}}
                            {{--<h4>Departure From</h4>--}}
                            {{--<select class="form-control" name="departure_filter" id="departure_filter">--}}
                            {{--<option value="1">9:00 - 12:00</option>--}}
                            {{--<option value="2">12:00 - 3:00</option>--}}
                            {{--<option value="3">3:00 - 6:00</option>--}}
                            {{--</select>--}}
                            {{--</div>--}}
                        </div>
                        <div class="form-group" id="gridBus"></div>
                    </div>
                </div>
            </div>
            <div class="form-group hide" id="detectAnime">
                <div class="col-md-12">
                    <img src="{{ secure_asset('images/detectBuses.gif') }}" class="center-block img-responsive">
                </div>
            </div>
            <div class="row hide" id="detectBooking">
                <form id="frmBusBoo1k" class="form-horizontal">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-8 col-xs-12 passenger-information">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h4 style="font-weight:  bold;color: #ff0000;">Step 1: Passengers Details</h4>
                                                        <div class="col-md-12"><hr></div>
                                                        <div id="passenger_details"></div>
                                                    </div>
                                                    <div class="col-md-12"><hr></div>
                                                    <div class="col-md-12">
                                                        <h4 style="font-weight: bold;color: #ff0000;">Step 2 : Contact Info</h4>
                                                        <div class="col-md-12"><hr></div>
                                                        <div class="col-md-6">
                                                            <label>EmailID</label>
                                                            <input type="email" class="booking-input" placeholder="Email" name="email" id="email">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label style="color: #ff0000;">Phone Number - Add Country Code</label>
                                                            <input type="text" class="booking-input" placeholder="Phone Number" name="phone_number" id="phone_number" value="+33">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12"><hr></div>
                                                    <div class="col-md-12">
                                                        <h4 style="font-weight: bold;color: #ff0000;">Step 3 : Additional luggage</h4>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="extras-container">
                                                            <img class="luggages" src="{{ secure_asset('images/luggages.png') }}">
                                                            <h4>Additional luggage</h4>
                                                            <h6>Carry <strong>1 hand luggage</strong> and <strong>1 luggage</strong> (7 kg · 42×30×18 cm | 20 kg · 80×50×30 cm.) per person for <strong>free</strong>
                                                            </h6>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-12">
                                                <div class="booking-shopping-cart">
                                                    <h3>Your Order <span class="countdown"> </span></h3>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-10 col-xs-10">
                                                            <h4 style="text-align: left">
                                                                <span id="dep_time"></span>
                                                            </h4>
                                                        </div>
                                                        <div class="col-md-2 col-xs-2">
                                                            <i class="fas fa-times fa-2x" style="color: #FF0000"></i>
                                                        </div>

                                                        <div class="col-md-12 col-xs-12">
                                                            <h5><i class="fas fa-map-marker-alt"></i>
                                                                <span  id="des_from"></span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-md-12 col-xs-12">
                                                            <h5><i class="fas fa-map-marker-alt"></i>
                                                                <span id="des_to"></span>
                                                            </h5>
                                                        </div>
                                                        <div id="pas"></div>
                                                    </div>
                                                    <hr>
                                                    {{--<div class="row">--}}
                                                    {{--<div class="col-md-12 col-xs-12">--}}
                                                    {{--<h5>Redeem voucher</h5>--}}
                                                    {{--</div>--}}
                                                    {{--<div class="col-md-8 col-xs-8">--}}
                                                    {{--<input type="text">--}}
                                                    {{--</div>--}}
                                                    {{--<div class="col-md-4 col-xs-4 redeem-btn">--}}
                                                    {{--<button>Redeem</button>--}}
                                                    {{--</div>--}}
                                                    {{--</div>--}}
                                                    <h3>€ <span id="total_price"></span></h3>
                                                    <p>Your seats are reserved for the next 10 minutes.</p>
                                                    <a href="#" class="btn btn-primary booking-button" id="confirm_btn">Confirm Your Order</a>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="{{ secure_asset('vendor/date-picker/jquery-ui.js') }}"></script>
        <script src="{{ secure_asset('js/jquery.storageapi.min.js') }}"></script>
        <script>

            var api_base_url = "{{ secure_url('') }}";
            $(function () {
                var availableCities = [
                        @forelse($cities as $city)
                        @if($loop->last)

                    {
                        value: "{{ $city['id'] }}",
                        label: "{{ $city['name'] }}"
                    }
                        @else

                    {
                        value: "{{ $city['id'] }}",
                        label: "{{ $city['name'] }}"
                    },
                    @endif
                    @empty
                    @endforelse
                ];
                $("#cityFrom").autocomplete({
                    minLength: 2,
                    source: availableCities,
                    select: function (event, ui) {
                        // console.log(ui);
                        $("#cityFrom").val(ui.item.label); // display the selected text
                        $("#cityFromHid").val(ui.item.value); // save selected id to hidden input
                        return false;
                    }
                });
                $("#cityTo").autocomplete({
                    minLength: 2,
                    source: availableCities,
                    select: function (event, ui) {
                        // console.log(ui);
                        $("#cityTo").val(ui.item.label); // display the selected text
                        $("#cityToHid").val(ui.item.value); // save selected id to hidden input
                        return false;
                    }
                });
                $(".date").datepicker({
                    changeYear: true,
                    minDate: new Date(),
                    maxDate: '3M',
                    showButtonPanel: true,
                    dateFormat : "yy-mm-dd",
                    showAnim : "slideDown"
                });
            });
        </script>
<script>
    // JavaScript to prevent deleting the country code
    document.getElementById('phone_number').addEventListener('input', function(event) {
        var countryCode = "+33";
        var inputValue = this.value;
        
        if (!inputValue.startsWith(countryCode)) {
            this.value = countryCode;
            this.setSelectionRange(countryCode.length, countryCode.length);
        }
    });
</script>
        <script src="{{ secure_asset('js/bus.js') }}"></script>

@endsection
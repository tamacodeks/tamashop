@extends('layout.app')
@section('content')
    @include('layout.breadcrumb', ['data' => [
        ['name' => "Invoices", 'url' => '', 'active' => 'yes']
    ]])

    <link href="{{ asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ trans('common.gen_invoice') }}</h3>
                    </div>
                    <div class="panel-body">
                        <!-- Admin-only options -->
                        @if(auth()->user()->group_id == 1)
                            <div class="pull-right">
                                <a href="{{ secure_url('invoices/generate') }}" class="btn btn-primary">
                                    <i class="fa fa-plus-circle"></i>&nbsp;Generate Invoice
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="panel" style="margin-top: -20px">
                    <div class="panel-body">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                            <tr>
                                <td>Sl</td>
                                <td>Username</td>
                                <td>Action</td>
                            </tr>
                            </thead>
                            <tbody>
                            @php($sl = 1)
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>{{ $sl }}</td>
                                    <td>{{ $invoice->username }}</td>
                                    <td>
                                        <a href="{{ secure_url('invoices/viewed/'.$invoice->user_id."/".$invoice->id) }}"
                                           class="btn btn-primary btn-sm view-pdf">
                                            <i class="fa fa-eye"></i>&nbsp;{{ trans('common.lbl_view') }}
                                        </a>
                                    </td>
                                </tr>
                                @php($sl++)
                            @empty
                                <tr>
                                    <td colspan="3">{{ trans('common.no_records_found') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        {!! $invoices->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/select-picker/js/bootstrap-select.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(".select-picker").selectpicker();
        });
    </script>
@endsection

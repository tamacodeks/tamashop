<form  class="form-horizontal" id="frmCurrency" action="{{ secure_url('currency/update') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $row['id'] }}">
    <div class="form-group">
        <label class="control-label col-md-4" for="title">{{ trans('common.currency_tbl_title') }}</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="title" id="title" value="{{ $row['title'] }}">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="symbol_left">{{ trans('common.symbol_left') }}</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="symbol_left" id="symbol_left" value="{{ $row['symbol_left'] }}">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="symbol_right">{{ trans('common.symbol_right') }}</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="symbol_right" id="symbol_right" value="{{ $row['symbol_right'] }}">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="code">{{ trans('common.currency_tbl_code') }}</label>
        <div class="col-md-8">
            <input class="form-control"  type="text" name="code" id="code" value="{{ $row['code'] }}">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="decimal_place">{{ trans('common.currency_tbl_decimal_place') }}</label>
        <div class="col-md-8">
            <input class="form-control"  type="text" name="decimal_place" id="decimal_place" value="{{ $row['decimal_place'] }}">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="value">{{ trans('common.currency_tbl_value') }}</label>
        <div class="col-md-8">
            <input class="form-control money-input" type="text" name="value" id="value" value="{{ $row['value'] }}">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="decimal_point">{{ trans('common.currency_tbl_decimal_point') }}</label>
        <div class="col-md-8">
            <input class="form-control" type="text" name="decimal_point" id="decimal_point" value="{{ $row['decimal_point'] }}">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="thousand_point">{{ trans('common.currency_tbl_thousand_point') }}</label>
        <div class="col-md-8">
            <input class="form-control" type="text" name="thousand_point" id="thousand_point" value="{{ $row['thousand_point'] }}">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <button type="submit" id="btnSubmit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;{{ trans("common.btn_save") }}</button>
        </div>
        <div class="col-md-4"></div>
    </div>
</form>
<link href="{{ secure_asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
<script src="{{ secure_asset('vendor/select-picker/js/bootstrap-select.js') }}"></script>
<script>
    $(document).ready(function () {
        $(".select-picker").selectpicker();
        $('#frmCurrency').validate({
            // rules & options,
            rules: {
                title: "required",
                code: "required",
                value: "required"
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                // Add the `help-block` class to the error element
                error.addClass("help-block");

                if (element.prop("type") === "checkbox") {
                    error.insertAfter(element.parents("checkbox"));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("").removeClass("has-error");
            },
            submitHandler: function (form) {
                $.confirm({
                    title: '{{ trans('common.btn_save') }}',
                    content: '{{ trans('common.lbl_ask_proceed_form') }}',
                    buttons: {
                        "{{ trans('common.btn_save') }}": function () {
                            $("#frmCurrency").LoadingOverlay("show");
                            $("#btnSubmit").html("<i class='fa fa-spinner fa-pulse'></i>&nbsp;{{ trans('common.btn_save_changes') }}...").attr('disabled', 'disabled');
                            form.submit();
                        },
                        "{{ strtolower(trans('common.btn_cancel')) }}": function () {

                        }
                    }
                });
            }
        });
    });
</script>
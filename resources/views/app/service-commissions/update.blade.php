<form  class="form-horizontal" id="frmService" action="{{ secure_url('service-commission/update') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $row['id'] }}">
    <div class="form-group">
        <label class="control-label col-md-4" for="service_id">{{ trans('common.order_tbl_service') }}</label>
        <div class="col-md-8">
            <select class="form-control" name="service_id" id="service_id">
                <option value="">{{ trans('common.lbl_please_choose') }}</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}" @if($row['service_id'] ==  $service->id) selected @endif>{{ $service->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="commission" class="col-sm-4 control-label">{{ trans('service.current_commission') }}</label>
        <div class="col-sm-8">
            <input type="text" class="form-control money-input" id="commission" name="commission" value="{{ $row['commission'] }}">
        </div>
    </div>
    <div class="form-group">
        <label for="mgr_def_com" class="col-sm-4 control-label">{{ trans('service.def_mgr_com') }}</label>
        <div class="col-sm-8">
            <input type="text" value="{{ $row['mgr_def_com'] }}" class="form-control  money-input" id="mgr_def_com" name="mgr_def_com">
        </div>
    </div>
    <div class="form-group">
        <label for="retailer_def_com" class="col-sm-4 control-label">{{ trans('service.def_ret_com') }}</label>
        <div class="col-sm-8">
            <input type="text" value="{{ $row['retailer_def_com'] }}" class="form-control  money-input" id="retailer_def_com" name="retailer_def_com">
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
<script>
    $(document).ready(function () {
        $('.money-input').autoNumeric({
            aSep: ''
        });

        $('.money-input').blur(function () {
            if($(this).val() != ''){
                var striped_val = Math.abs($(this).val());
                $(this).val(striped_val);
            }
        })
        $('#frmService').validate({
            // rules & options,
            rules: {
                service_id: "required",
                commission: "required",
                user_def_commission: "required",
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
                            $("#frmService").LoadingOverlay("show");
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
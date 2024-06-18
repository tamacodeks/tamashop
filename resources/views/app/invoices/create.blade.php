<form class="form-horizontal" name="frmInvoiceSetting" id="frmInvoiceSetting" action="{{ secure_url('invoice-settings/store') }}" method="POST">
    {{ csrf_field() }}
    <div class="form-group">
        <label class="control-label col-md-4" for="name">{{ trans('tamatopup.country') }}</label>
        <div class="col-md-8">
            <select class="form-control select2" name="country_id" id="country_id">
                <option value="">{{ trans('common.lbl_please_choose') }}</option>
                @forelse($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->nice_name }}</option>
                @empty
                @endforelse
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="name">{{ trans('users.lbl_user_commission_commission') }}</label>
        <div class="col-md-8">
            <input type="text" name="commission" id="commission" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <button class="btn btn-primary" id="btnSubmit" type="submit"><i class="fa fa-save"></i>&nbsp;{{ trans('save') }} </button>
        </div>
        <div class="col-md-4"></div>
    </div>
</form>
<script>
    $(document).ready(function () {
        $('#frmInvoiceSetting').validate({
            // rules & options,
            rules: {
                country_id: "required",
                commission: "required"
            },
            errorElement: "span",
            errorPlacement: function (error, element) {
                // Add the `help-block` class to the error element
                error.addClass("help-block");

                if (element.prop("type") === "checkbox") {
                    error.insertAfter(element.parents("checkbox"));
                } else {
                    error.insertAfter(element.parents('.form-group'));
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("").removeClass("has-error");
            },
            submitHandler: function (form) {
                $("#btnSubmit").html("<i class='fa fa-refresh fa-spin'></i>&nbsp;{{ trans('common.btn_save_changes') }}...").attr('disabled', 'disabled');
                form.submit();
            }
        });
    });
</script>
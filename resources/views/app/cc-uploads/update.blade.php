<form class="form-horizontal" action="{{ secure_url('cc/reverse-transaction/rollback') }}" id="frmUpdate" method="post">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $row['id'] }}">
    <input type="hidden" name="cc_id" value="{{ $row['cc_id'] }}">
    <div class="form-group">
        <label for="rollback_note" class="col-sm-4 control-label">{{ trans('common.rollback_reason') }}</label>
        <div class="col-md-8">
            <textarea class="form-control" name="rollback_note" id="rollback_note" cols="8"></textarea>
        </div>
    </div>
    <div class="form-group m-t-20 m-b-20">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <button type="submit" id="btnSubmit" class="btn btn-primary"><i class="fa fa-check-circle"></i>&nbsp;{{ trans("common.rollback_trans") }}</button>
        </div>
        <div class="col-md-4"></div>
    </div>
</form>
<script>
    $(document).ready(function () {
        $('#frmUpdate').validate({
            // rules & options,
            rules: {
                rollback_note: "required"
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
                            $("#frmUpdate").LoadingOverlay("show");
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
<form class="form-horizontal" id="frmUpdate" action="{{ secure_url('cc-price-list/groups/update') }}" method="post">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $row['id'] }}">
    <div class="form-group">
        <label for="name" class="col-sm-4 control-label">{{ trans('myservice.lbl_rate_group_name') }}</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" id="name" name="name" value="{{ $row['name'] }}">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="status">{{ trans('myservice.lbl_status') }}</label>
        <div class="col-md-8">
            <div class="checkbox">
                <label><input name="status" type="checkbox"
                              value="1" @if($row['status'] == 1) checked @endif>{{ trans('common.lbl_enabled') }}</label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="rate_group_id" class="col-sm-4 control-label">{{ trans('myservice.lbl_rate_table_price_choose_exits') }}</label>
        <div class="col-md-8">
            <select class="form-control" id="rate_group_id" name="rate_group_id">
                <option value="">{{ trans('common.lbl_please_choose') }}</option>
                @if(isset($rate_groups))
                    @foreach($rate_groups as $rate_group)
                        <option value="{{ $rate_group->id }}">{{ $rate_group->name }}</option>
                    @endforeach
                @endif
            </select>
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
        $('#frmUpdate').validate({
            // rules & options,
            rules: {
                name: "required"
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
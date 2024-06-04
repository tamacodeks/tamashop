<link href="{{ secure_asset('vendor/multi-select/css/multi-select.dist.css') }}" rel="stylesheet">
<form class="form-horizontal" name="frmUserGroup" id="frmUserGroup" action="{{ secure_url('user-group/update') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $row['id'] }}">
    <div class="form-group">
        <label class="control-label col-md-4" for="name">{{ trans('common.trans_tbl_name') }}</label>
        <div class="col-md-8">
            <input type="text" name="name" id="name" class="form-control" value="{{ $row['name'] }}">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="description">{{ trans('common.lbl_desc') }}</label>
        <div class="col-md-8">
            <textarea class="form-control" name="description" id="description">{{ $row['description'] }}</textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="status">{{ trans('myservice.lbl_status') }}</label>
        <div class="col-md-8">
            <label class="radio-inline">
                <input type="checkbox" name="status" id="status" value="1" @if($row['status'] == 1) checked @endif>&nbsp;{{ trans('common.lbl_enabled') }}
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="level_access">{{ trans('users.level_access') }}</label>
        <div class="col-md-8">
            <select class="form-control" name="level_access[]" id="level_access" multiple>
                @foreach($user_groups as $user_group)
                    <option value="{{ $user_group->id }}" @if(in_array($user_group->id,$level_access)) selected @endif>{{ $user_group->name }}</option>
                @endforeach
            </select>
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
<script src="{{ secure_asset('vendor/multi-select/js/jquery.multi-select.js') }}"></script>
<script>
    $(document).ready(function () {
        $("#level_access").multiSelect();

        $('#frmUserGroup').validate({
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
                $("#btnSubmit").html("<i class='fa fa-spinner fa-pulse'></i>&nbsp;{{ trans('common.btn_save_changes') }}...").attr('disabled', 'disabled');
                form.submit();
            }
        });
    });
</script>
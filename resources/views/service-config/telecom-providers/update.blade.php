<link href="{{ secure_asset('vendor/select-picker/css/bootstrap-select.min.css') }}" rel="stylesheet">
<form  class="form-horizontal" id="frmService" action="{{ secure_url('tp-config/update') }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $row['id'] }}">
    <div class="form-group">
        <label class="control-label col-md-4" for="country_id">{{ trans('service.tp_country') }}</label>
        <div class="col-md-8">
            <select class="select-picker form-control" name="country_id" id="country_id" data-live-search="true" >
                <option value="">{{ trans('common.lbl_please_choose') }}</option>
                @if(isset($countries))
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" @if( $country->id == $row['country_id']) selected @endif>{{ $country->nice_name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="name">{{ trans('myservice.name') }}</label>
        <div class="col-md-8">
            <input type="text" class="form-control" name="name" id="name" value="{{ $row['name'] }}">
        </div>
    </div>
    <div class="form-group">
        <label for="image" class="col-sm-4 control-label">Image</label>
        <div class="col-sm-8">
            <?php
            if($row['id'] != ''){
                $tp_config =  \App\Models\TelecomProviderConfig::find($row['id']);
                $src_img = $tp_config->getMedia('telecom_providers')->first();
                $img = !empty($src_img) ? optional($src_img)->getUrl('thumb') : secure_asset('images/no_image.png');
            }else{
                $img = secure_asset('images/no_image.png');
            }
            ?>
            <img src="{{ $img }}" id="img_holder" style="width: 150px">
            <input type="file" name="image" class="form-control" id="image">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4" for="status">{{ trans('service.edit_telecom_provider') }}</label>
        <div class="col-md-8">
            <div class="checkbox">
                <label><input name="bimedia_card" type="checkbox"
                              value="1" @if($row['bimedia_card'] == 1) checked @endif>{{ trans('common.select_bimedia') }}</label>
            </div>
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
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <button type="submit" id="btnSubmit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;{{ trans("common.btn_save") }}</button>
        </div>
        <div class="col-md-4"></div>
    </div>
</form>
<script src="{{ secure_asset('vendor/select-picker/js/bootstrap-select.js') }}"></script>
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#img_holder').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(document).ready(function () {
        $("#image").change(function () {
            readURL(this);
        });
        $(".select-picker").selectpicker();
        $('#frmService').validate({
            // rules & options,
            rules: {
                country_id: "required",
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
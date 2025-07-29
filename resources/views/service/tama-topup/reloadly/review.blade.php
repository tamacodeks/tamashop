<form id="frmReviewTopup" action="{{ secure_url('tama-topup/reloadly/confirm/topup') }}" method="POST">
    @csrf
    <table class="table table-bordered">
        <tbody>
        <tr>
            <td>{{ trans('tamatopup.phone_number') }}</td>
            <td>{{ $phone_no ?? "" }}</td>
        </tr>
        <tr>
            <td>{{ trans('tamatopup.country') }}</td>
            <td>{{ $country ?? "" }}</td>
        </tr>
        <tr>
            <td>{{ trans('tamatopup.operator') }}</td>
            <td>{{ $operator ?? "" }}</td>
        </tr>
        <tr>
            <td>{{ trans('tamatopup.amount') }}</td>
            <td>{{ $SendValue ?? "" }}</td>
        </tr>
        <tr>
            <td>{{ trans('tamatopup.phone_will_receive') }}</td>
            <td>{{ $currency ?? "" }}  {{ $dest_amount ?? "" }}</td>a
        </tr>
        <tr>
            <td>{{ trans('tamatopup.total_amount_to_paid') }}</td>
            <td id="totalAmountCell">{{ $SendValue ?? "" }}</td>
        </tr>
        {{--<tr style="background-color: red">--}}
        {{--<td><label style="color:#ffffff;"  for="service_charge">{{ trans('tamatopup.service_charge') }}</label></td>--}}
        {{--<td>--}}
        <input type="hidden" class="form-control" name="service_charge" id="service_charge" value="0.00" placeholder="{{ trans('tamatopup.enter_service_charge') }}">
        {{--</td>--}}
        {{--</tr>--}}
        </tbody>
    </table>
    <input type="hidden" name="AccountNumber" value="{{ $phone_no ?? "" }}">
    <input type="hidden" name="SkuCode" value="{{ $skuCode ?? "" }}">
    <input type="hidden" name="SendValue" value="{{ $SendValue ?? "" }}">
    <input type="hidden" name="sendValueOriginal" value="{{ $sendValueOriginal ?? "" }}">
    <input type="hidden" name="local_amt" value="{{ $dest_amount ?? "" }}">
    <input type="hidden" name="countryCode" value="{{ $countryCode ?? "" }}">
    <input type="hidden" name="operator" value="{{ $operator ?? "" }}">
    <input type="hidden" name="currency" value="{{ $currency ?? "" }}">
    <input type="hidden" name="country" value="{{ $country ?? "" }}">
    <input type="hidden" name="ISO" value="{{ $ISO ?? "" }}">
    <div class="text-center">
        <span class="text-muted">{{ trans('tamatopup.any_local_taxes_text') }}</span>
        <br>
        <br>
        <button type="submit" id="btnSubmit" onclick="this.form.submit();this.disabled=true;" class="btn btn-primary">{{ trans('service.tamatopup_btn_confirm_topup') }}</button>
    </div>
</form>
<script>
    $(document).ready(function () {
        $('#service_charge').on('input', function () {
            // Get the service charge value
            var serviceCharge = parseFloat($("#service_charge").val()) || 0;

            // Update the SendValue with the service charge
            var totalAmount = parseFloat("{{ $SendValue or 0 }}") + serviceCharge;

            // Set the updated value to the total amount cell
            $("#totalAmountCell").text("{{ $currency ?? "" }} " + totalAmount.toFixed(3));
        });
        $("#btnSubmit").click(function () {
            $("#frmReviewTopup").LoadingOverlay('show');
            $("#frmReviewTopup").LoadingOverlay("text", "veuillez patienter et ne pas fermer le navigateur ni actualiser la page");
        });
    });
</script>
Hello Admin,<br><br>

Retailer {{ isset($reseller_name) ? $reseller_name : "" }} {{ trans('users.lbl_user_daily_current_credit_limit') }} {{ isset($amount) ? $amount : "" }} € {{ trans('users.was_updated_by') }} {{ isset($updater) ? $updater : "" }}.
<br><br>
{{ trans('users.lbl_user_daily_current_credit_limit') }} : {{ isset($current_balance) ? $current_balance : "" }}<br>
{{ trans('users.lbl_remaining_limit') }} : {{ isset($remaining_bal) ? $remaining_bal : "" }}<br>
<br><br>
{{ trans('common.payment_tbl_comment') }} : <br>
{{ isset($desc) ? $desc : "" }}

<br><br>
Regards.,<br>
{{ APP_NAME }}.
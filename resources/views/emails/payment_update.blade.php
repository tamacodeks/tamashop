Hello Admin,<br><br>

Retailer {{ isset($reseller_name) ? $reseller_name : "" }} {{ trans('users.balance') }} {{ isset($amount) ? $amount : "" }} {{ trans('users.was_updated_by') }} {{ isset($updater) ? $updater : "" }}.

<br><br>
{{ trans('users.lbl_user_old_balance') }} : {{ isset($oldBalance) ? $oldBalance : "" }}<br>
{{ trans('users.lbl_user_transaction_current_balance') }} : {{ isset($newBalance) ? $newBalance : "" }}<br>

<br><br>
{{ trans('common.payment_tbl_comment') }} : <br>
{{ isset($desc) ? $desc : "" }}

<br><br>
Regards.,<br>
{{ APP_NAME }}.



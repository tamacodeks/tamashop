<!-- Nav tabs -->
<ul class="nav nav-tabs process-model more-icon-preocess" role="tablist">
    <li role="presentation" class="@if($current == 'search') active @else  @endif"><a href="{{ secure_url('tama-topup') }}" aria-controls="discover"><i
                    class="fa fa-mobile" aria-hidden="true"></i>
            <p>{{ trans('common.filter_lbl_search') }}</p>
        </a></li>
    <li role="presentation" class="@if($current == 'choose') active @else disabled @endif"><a href="#content" aria-controls="strategy" role="tab"
                                                                                              data-toggle="tab"><i class="fas fa-th"
                                                                                                                   aria-hidden="true"></i>
            <p>{{ trans('tamatopup.choose') }}</p>
        </a></li>
    <li role="presentation" class="@if($current == 'review') active @else disabled @endif"><a href="#content" aria-controls="optimization"
                                                                                              role="tab" data-toggle="tab"><i
                    class="far fa-check-circle" aria-hidden="true"></i>
            <p>{{ trans('tamatopup.review') }}</p>
        </a></li>
    <li role="presentation" class="@if($current == 'topup') active @else disabled @endif"><a href="#content" aria-controls="optimization"
                                                                                             role="tab" data-toggle="tab"><i
                    class="far fa-paper-plane" aria-hidden="true"></i>
            <p>{{ trans('tamatopup.send_topup') }}</p>
        </a></li>
</ul>
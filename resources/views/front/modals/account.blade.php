@php
    $_user = wrap()->get('user');
@endphp
<div id="modal-account"
     class="modal-account"
     uk-modal>
    <div class="uk-modal-dialog"
         id="account-modal-box">
        @if($_user)
            @include('front.modals.account_info', compact('_user'))
        @else
            @include('front.forms.account_login')
        @endif
    </div>
</div>

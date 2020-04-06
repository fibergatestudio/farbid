@php
    $_profile_avatar_path = formalize_path('template/img/user-small.png');
    $_language = wrap()->get('locale');
    if($_profile_avatar = $_user->_profile->_avatar){
        $_profile_avatar_path = image_render($_profile_avatar, 'account_avatar_small', ['only_way' => TRUE]);
    }
@endphp
<div class="uk-modal-header uk-position-relative uk-flex uk-flex-middle">
    <div class="uk-margin-small-right">
        <img src="{{ $_profile_avatar_path }}"
             alt="{{ $_user->_profile->full_name }}"
             class="uk-display-inline-block">
    </div>
    <div class="uk-display-inline-block">
        <h2 class="uk-modal-title uk-text-uppercase">
            {{ $_user->_profile->full_name }}
        </h2>
    </div>
</div>
<div class="uk-modal-body uk-padding-remove uk-text-center">
    <div class="uk-child-width-1-2 uk-grid-collapse"
         uk-grid>
        <a href="{{ _u(($_language != DEFAULT_LANGUAGE) ? $_language . '/account' : 'account') }}"
           class="uk-button uk-button-default">
            @lang('forms.in_profile')
        </a>
        <form action="{{ _r('logout') }}"
              method="POST">
            {{ method_field('POST') }}
            {{ csrf_field() }}
            <button type="submit"
                    class="uk-button uk-button-danger uk-width-1-1">
                @lang('forms.exit')
            </button>
        </form>
    </div>
</div>

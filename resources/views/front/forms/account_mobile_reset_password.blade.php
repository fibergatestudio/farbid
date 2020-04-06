@php
    $_form = 'form-mobile-account-reset-password';
    $_box = 'account-mobile-box';
@endphp
<form method="POST"
      id="{{ $_form }}"
      action="{{ _r('password.email') }}">
    {{ csrf_field() }}
    {{ method_field('POST') }}
    <input type="hidden"
           value="{{ $_form }}"
           name="forms">
    <input type="hidden"
           value="{{ $_box }}"
           name="box">
    <input type="hidden"
           value="1"
           name="ajax">
    <div>
        {!!
            field_render('email', [
                'attributes' => [
                    'placeholder' => trans('forms.label_email'),
                                            ],
                'id' => "{$_form}-email",
                'class' => 'uk-margin-small-bottom'
            ])
        !!}
        <div
            class="uk-flex uk-flex-between uk-margin-small-bottom register">
            <a href="javascript:void(0);"
               class="use-ajax"
               data-form="login"
               data-box="{{ $_box }}"
               data-path="{{ _r('ajax.show_account_form') }}"
               rel="nofollow">
                @lang('forms.button_login')
            </a>
            <a href="javascript:void(0);"
               class="use-ajax"
               data-form="register"
               data-box="{{ $_box }}"
               data-path="{{ _r('ajax.show_account_form') }}"
               rel="nofollow">
                @lang('forms.check_in')
            </a>
        </div>
    </div>
    <div class="uk-child-width-1-2 uk-grid-collapse"
         uk-grid>
        <button class="uk-button uk-button-default use-ajax"
                data-form="login"
                data-box="{{ $_box }}"
                data-path="{{ _r('ajax.show_account_form') }}"
                type="button">
            @lang('forms.cancellation')
        </button>
        <button class="uk-button next use-ajax"
                type="submit">
            @lang('forms.restore')
        </button>
    </div>
</form>
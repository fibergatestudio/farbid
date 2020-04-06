@php
    $_form = 'form-mobile-account-login';
    $_box = 'account-mobile-box';
@endphp
<form method="POST"
      id="{{ $_form }}"
      action="{{ _r('login') }}">
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
            field_render('login_or_email', [
                'attributes' => [
                    'placeholder' => trans('forms.label_email'),
                                            ],
                'id' => "{$_form}-login_or_email",
                'class' => 'uk-margin-small-bottom'
            ])
        !!}
        {!!
            field_render('password', [
                'type' => 'password',
                'attributes' => [
                    'placeholder' => trans('forms.label_password')
                ],
                'id' => "{$_form}-password",
                'class' => 'uk-margin-small-bottom'
            ])
        !!}
        <div
            class="uk-flex uk-flex-between uk-margin-small-bottom register">
            <a href="javascript:void(0);"
               class="use-ajax"
               data-form="reset_password"
               data-box="{{ $_box }}"
               data-path="{{ _r('ajax.show_account_form') }}"
               rel="nofollow">
                @lang('forms.forgot_password')
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
        <button class="uk-button uk-button-default user-account form-account uk-width-1-1 use-ajax"
                type="submit">
				<i class="icon-account sprites-m uk-display-inline-block"></i>
             @lang('forms.log_your_account')
        </button>
    </div>
</form>
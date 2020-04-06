@php
    $_form = 'form-account-register';
@endphp
<div class="uk-animation-fade">
    <div class="uk-modal-header uk-position-relative">
        <i class="icon-account-modal sprites uk-display-inline-block"></i>
        <div class="uk-display-inline-block">
            <h2 class="uk-modal-title uk-text-uppercase">
                 @lang('forms.register')
            </h2>
            <div class="cabinet uk-text-uppercase">
                @lang('forms.new_account'):
            </div>
        </div>
    </div>
    <div class="uk-modal-body uk-padding-remove uk-text-center">
        <form method="POST"
              id="{{ $_form }}"
              action="{{ _r('register') }}">
            <input type="hidden"
                   value="{{ $_form }}"
                   name="forms">
            <input type="hidden"
                   value="1"
                   name="ajax">
            <div class="account-form uk-display-block">
                <div class="box-input uk-display-block">
                    {!!
                        field_render('email', [
                            'attributes' => [
                                'placeholder' => trans('forms.label_email'),
                                                        ],
                            'id' => "{$_form}-email",
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
                           data-path="{{ _r('ajax.show_account_form') }}"
                           rel="nofollow">
                             @lang('forms.forgot_password')
                        </a>
                        <a href="javascript:void(0);"
                           class="use-ajax"
                           data-form="login"
                           data-path="{{ _r('ajax.show_account_form') }}"
                           rel="nofollow">
                            @lang('forms.button_login')
                        </a>
                    </div>
                </div>
            </div>
            <div class="uk-child-width-1-2 uk-grid-collapse"
                 uk-grid>
                <button class="uk-modal-close-default uk-button uk-button-default use-ajax"
                        data-form="login"
                        data-path="{{ _r('ajax.show_account_form') }}"
                        type="button">
                     @lang('forms.cancellation')
                </button>
                <button class="uk-button use-ajax"
                        type="submit">
                    @lang('forms.check_in')
                </button>
            </div>
        </form>
    </div>
</div>
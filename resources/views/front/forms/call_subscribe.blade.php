@php
    $_form = 'forms-subscribe';
@endphp
<form method="POST"
      id="{{ $_form }}"
      action="{{ _r('ajax.subscribe.form') }}">
    <div class="newsletter-box">
        <input type="hidden"
               value="{{ $_form }}"
               name="forms">
        <div class="uk-form-controls">
            <div class="uk-form-row"
                 id="{{ $_form }}-email-object">
                <div class="uk-form-controls">
                    <div class="uk-inline uk-width-1-1 form-line">
                        <input type="text"
                               id="{{ $_form }}-email"
                               name="email"
                               value=""
                               autocomplete="off"
                               placeholder="{{ __('Введіть ваш E-mail') }}"
                               class="uk-input">
                        <button type="submit"
                                name="save"
                                value="1"
                                class="uk-button use-ajax uk-position-right">
                            @lang('forms.button_sent')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

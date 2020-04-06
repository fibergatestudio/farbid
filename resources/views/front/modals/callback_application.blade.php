@php($_form = 'forms-callback-application')
<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<div class="box-form">
    <form method="POST"
          id="{{ $_form }}"
          action="{{ _r('ajax.callback.form') }}">
        <input type="hidden"
               value="{{ $_form }}"
               name="forms">

        <input type="hidden"
               value="1"
               name="modal">
        <div class="top-form">

            <div class="uk-form-controls">
                <div class="uk-form-row"
                     id="{{ $_form }}-name-object">
                    <div class="uk-form-controls">
                        <div class="uk-inline uk-width-1-1 form-line">
                            <input type="text"
                                   id="{{ $_form }}-name"
                                   name="name"
                                   value=""
                                   autocomplete="off"
                                   placeholder="@lang('forms.label_modal_user_name')"
                                   class="uk-input uk-border">
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-form-controls">
                <div class="uk-form-row"
                     id="{{ $_form }}-phone-object">
                    <div class="uk-form-controls">
                        <div class="uk-inline uk-width-1-1 form-line">
                            <input type="text"
                                   id="{{ $_form }}-phone"
                                   name="phone"
                                   value=""
                                   autocomplete="off"
                                   placeholder="@lang('forms.label_modal_user_phone')"
                                   class="uk-input phone-mask">
                        </div>
                    </div>
                </div>
            </div>
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
                                   placeholder="email"
                                   class="uk-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="activate-order uk-flex uk-flex-between">
            <button type="submit"
                    name="save"
                    value="1"
                    class="uk-button use-ajax uk-width-1-1">
                @lang('forms.button_sent')
            </button>
        </div>
    </form>
</div>

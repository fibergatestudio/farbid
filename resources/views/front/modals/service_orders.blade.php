@php($_form = 'forms-service-order')
<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked"
      method="POST"
      id="{{ $_form }}"
      action="{{ _r('ajax.service_order.form') }}">
    <input type="hidden"
           value="{{ $_form }}"
           name="forms">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">@lang('forms.service_order')</h2>
    </div>
    <div class="uk-modal-body">
        <div class="uk-form-row"
             id="{{ $_form }}-name-object">
            <label for="{{ $_form }}-name"
                   class="uk-form-label">
                @lang('forms.label_first_name') <span class="uk-text-danger">*</span>
            </label>
            <div class="uk-form-controls">
                <div class="uk-inline uk-width-1-1">
                    <input type="text"
                           id="{{ $_form }}-name"
                           name="name"
                           value=""
                           autocomplete="off"
                           class="uk-input uk-border-rounded">
                </div>
            </div>
        </div>
        <div class="uk-form-row"
             id="{{ $_form }}-email-object">
            <label for="{{ $_form }}-email"
                   class="uk-form-label">
                E-mail
            </label>
            <div class="uk-form-controls">
                <div class="uk-inline uk-width-1-1">
                    <input type="email"
                           id="{{ $_form }}-email"
                           name="email"
                           value=""
                           autocomplete="off"
                           class="uk-input uk-border-rounded">
                </div>
            </div>
        </div>
        @isset($services)
            @if($services)
                <div class="uk-form-row"
                     id="{{ $_form }}-services-object">
                    <label for="{{ $_form }}-services"
                           class="uk-form-label">
                        @lang('forms.label_services')
                    </label>
                    <div class="uk-form-controls">
                        @foreach($services as $_key_service => $_name_service)
                            <label>
                                <div>
                                    <input name="services[{{ $_key_service }}]"
                                           type="checkbox"
                                           class="uk-checkbox"
                                           value="1">{!! $_name_service !!}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
        @endisset
        <div class="uk-form-row"
             id="{{ $_form }}-phone-object">
            <label for="{{ $_form }}-phone"
                   class="uk-form-label">
                @lang('forms.label_phone') <span class="uk-text-danger">*</span>
            </label>
            <div class="uk-form-controls">
                <div class="uk-inline uk-width-1-1">
                    <input type="text"
                           id="{{ $_form }}-phone"
                           name="phone"
                           value=""
                           autocomplete="off"
                           class="uk-input uk-border-rounded uk-phone-mask">
                </div>
            </div>
        </div>
        <div class="uk-form-row"
             id="{{ $_form }}-comment-object">
            <label for="{{ $_form }}-comment"
                   class="uk-form-label">
                @lang('forms.label_comment')
            </label>
            <div class="uk-form-controls">
                <div class="uk-inline uk-width-1-1">
                    <textarea id="{{ $_form }}-comment"
                              name="comment"
                              rows="5"
                              class="uk-textarea uk-border-rounded"></textarea>
                </div>
            </div>
        </div>
        <div class="uk-form-row"
             id="{{ $_form }}-urgently-object">
            <div class="uk-form-controls">
                <label class="uk-text-small">
                    <div>
                        <input name="urgently"
                               type="hidden"
                               value="0">
                        <input name="urgently"
                               type="checkbox"
                               id="{{ $_form }}-urgently"
                               class="uk-checkbox"
                               value="1"> @lang('forms.label_urgently')
                    </div>
                </label>
            </div>
        </div>
    </div>
    <div class="uk-modal-footer uk-text-right">
        <button type="submit"
                name="save"
                value="1"
                class="uk-button uk-button-secondary use-ajax uk-waves uk-border-rounded">
            @lang('forms.button_order')
        </button>
    </div>
</form>
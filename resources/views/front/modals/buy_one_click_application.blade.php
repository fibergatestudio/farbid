@php
    $_form = 'forms-shop-buy-one-click-application'
@endphp
<button class="uk-modal-close-default"
        type="button">
    <i class="icon-close uk-display-block sprites"></i>
</button>
<div class="box-form">
    <form method="POST"
          id="{{ $_form }}"
          action="{{ _r('ajax.shop.buy_one_click.form') }}">
        <input type="hidden"
               value="{{ $_form }}"
               name="forms">
        <input type="hidden"
               value="1"
               name="modal">
        <input type="hidden"
               value="{{ $product }}"
               name="product_id">
        <div class="uk-modal-header">
            <h2 class="uk-modal-title uk-text-uppercase uk-text-center">
                @lang('forms.buy_one_click')
            </h2>
        </div>
        <div class="uk-modal-body">
            <div class="uk-form-controls uk-margin">
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
            <div class="uk-form-controls uk-margin">
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
        </div>
        <div class="activate-order uk-text-center">
            <button type="submit"
                    name="save"
                    value="1"
                    class="uk-button use-ajax">
                @lang('forms.button_leave_a_application')
            </button>
        </div>
    </form>
</div>

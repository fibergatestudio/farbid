@php
    $_wrap = wrap()->get();
    $_delivery = $_wrap['variables']['shop']['deliveries'];
    $_payment = $_wrap['variables']['shop']['payments'];
    $_form = 'forms-shop-basket';
    $_user = $_wrap['user'];
@endphp
<div id="shop-basket-inside-card-items"
     class="uk-margin-large-bottom">
    @if($items->isNotEmpty())
        <div uk-grid>
            <div class="uk-width-2-3@l">
                <h2 class="heading">@lang('shop.form_title_order')</h2>
                @include('front.shop.items_basket', ['items' => $items])
            </div>
            <div class="uk-width-1-3@l box-form-order">
                <div class="address-box">
                    <form method="POST"
                          id="{{ $_form }}"
                          class="main-form full"
                          action="{{ _r('ajax.shop.order') }}">
                        <input type="hidden"
                               value="{{ $_form }}"
                               name="forms">
                        <div class="order-input">
                            <h3>@lang('shop.form_title_basket_form')</h3>
                            <div class="uk-form-controls uk-margin-bottom">
                                <input type="text"
                                       id="{{ $_form }}-email"
                                       name="email"
                                       value="{{ $_user->email ?? NULL }}"
                                       autocomplete="off"
                                       placeholder="Email"
                                       @if($_user) readonly
                                       @endif
                                       class="uk-input">
                            </div>
                            @if($_user)
                                <input type="hidden"
                                       id="{{ $_form }}-user_id"
                                       name="user_id"
                                       value="{{ $_user->id }}">
                            @else
                                <input type="hidden"
                                       id="{{ $_form }}-new_user"
                                       name="new_user"
                                       value="0">
                                <div class="form-action-user">
                                    <div class="uk-form-controls uk-margin">
                                        <input type="password"
                                               name="password"
                                               id="{{ $_form }}-password"
                                               class="uk-input"
                                               autocomplete="off"
                                               placeholder="@lang('forms.label_modal_user_password')">
                                        <div class="uk-help-block uk-text-small">
                                            @lang('forms.enter_password')
                                        </div>
                                    </div>
                                    <div class="uk-form-controls uk-form-button uk-margin">
                                        <button class="uk-button btn-account uk-width-1-1"
                                                data-form="{{ $_form }}"
                                                data-path="{{ _r('ajax.account_log_in') }}"
                                                id="form-basket-checkout-login-user"
                                                type="button">
                                            @lang('forms.login_account')
                                        </button>
                                    </div>
                                </div>
                                <div class="uk-flex uk-flex-between form-action-link uk-margin-bottom">
                                    <button data-class="log_in"
                                            type="button"
                                            rel="nofollow"
                                            class="uk-text-uppercase">
                                        <i class="icon-check uk-display-inline-block"></i>
                                        @lang('forms.regular_customer')
                                    </button>
                                    <button
                                        data-class="sing_in"
                                        type="button"
                                        rel="nofollow"
                                        class="uk-text-uppercase">
                                        <i class="icon-check uk-display-inline-block"></i>
                                        @lang('forms.new_customer')
                                    </button>
                                </div>
                            @endif
                            <div class="uk-form-controls uk-margin-bottom">
                                <input type="name"
                                       id="{{ $_form }}-name"
                                       name="name"
                                       class="uk-input"
                                       autocomplete="off"
                                       value="{{ $_user->_profile->full_name ?? NULL  }}"
                                       placeholder="@lang('forms.label_modal_user_name')">
                            </div>
                            <div class="uk-form-controls uk-margin-bottom">
                                <input type="phone"
                                       id="{{ $_form }}-phone"
                                       name="phone"
                                       class="uk-input phone-mask"
                                       autocomplete="off"
                                       value="{{ $_user->_profile->phone ?? NULL  }}"
                                       placeholder="@lang('forms.label_modal_user_phone')">
                            </div>
                            <div class="uk-form-controls input-box">
                                @php($delivery_placeholder = '')
                                @php($delivery_use_field = 1)
                                @foreach($_delivery as $_del_key=>$_del_data)
                                    @if($_del_data['use'])
                                        @if($loop->index == 0)
                                            @php($delivery_use_field = $_del_data['use_of_data'])
                                            @php($delivery_placeholder = $_del_data['placeholder'])
                                        @endif
                                        <div class="radio-box radio-flex">
                                            <label class="delivery-{{ $_del_key }}"
                                                   for="delivery-{{ $_del_key }}">
                                                <input type="radio"
                                                       id="delivery-{{ $_del_key }}"
                                                       value="{{ $_del_key }}"
                                                       autocomplete="off"
                                                       data-field="{{ $_del_data['use_of_data'] }}"
                                                       {{ $loop->index == 0 ? 'checked' : '' }}
                                                       name="delivery"
                                                       class="uk-radio">
                                                <span>
                                            {{ trans($_del_data['name']) }}
                                            </span>
                                            </label>

                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="uk-form-controls">
                                @foreach($_delivery as $_del_key=>$_del_data)
                                    @if($_del_data['use'])
                                        <div id="delivery-{{ $_del_key }}-address"
                                             data-index="{{ $_del_key }}"
                                             class="delivery-box uk-margin-top {{ $loop->index != 0 ? 'delivery-address' : '' }}">
                                            @if($_del_key == 1)
                                                <input type="text"
                                                       autocomplete="off"
                                                       class="uk-width-1-1 uk-input"
                                                       readonly
                                                       value="{{ trans($_del_data['placeholder']) }}">
                                            @elseif($_del_key == 2)
                                                <input type="text"
                                                       name="delivery_address_city"
                                                       autocomplete="off"
                                                       class="uk-width-1-1 uk-input uk-margin-bottom"
                                                       {{ $delivery_use_field ? '' : 'disabled' }}
                                                       id="{{ $_form }}-delivery_address_city"
                                                       placeholder="Город">
                                                <input type="text"
                                                       name="delivery_address_address"
                                                       autocomplete="off"
                                                       class="uk-width-1-1 uk-input"
                                                       id="{{ $_form }}-delivery_address_address"
                                                       {{ $delivery_use_field ? '' : 'disabled' }}
                                                       placeholder="Адрес">
                                            @elseif($_del_key == 3)
                                                <select name="delivery_area"
                                                        class="uk-select use-ajax"
                                                        data-href="{{ _r('np.ajax') }}"
                                                        data-type="area"

                                                        autocomplete="off"
                                                        id="{{ $_form }}-delivery_area">
                                                    <option value="0"
                                                            disabled
                                                            selected>Выберите область
                                                    </option>
                                                    @if(is_array($item->np_area))
                                                    @foreach($item->np_area as $_key_area => $_value_area)
                                                        <option value="{{ $_key_area }}">{{ $_value_area }}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                <select name="delivery_city"
                                                        class="uk-select uk-margin-top use-ajax"
                                                        data-href="{{ _r('np.ajax') }}"
                                                        data-type="city"
                                                        disabled
                                                        autocomplete="off"
                                                        id="{{ $_form }}-delivery_city">
                                                    <option value="0"
                                                            disabled
                                                            selected>Выберите Город
                                                    </option>
                                                </select>
                                                <select name="delivery_warehouses"
                                                        class="uk-select uk-margin-top"
                                                        disabled
                                                        autocomplete="off"
                                                        id="{{ $_form }}-delivery_warehouses">
                                                    <option value="0"
                                                            disabled
                                                            selected>Выберите отделение
                                                    </option>
                                                </select>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="uk-form-controls uk-margin-top">
                                @foreach($_payment as $_pay_key=>$_pay_data)
                                    @if($_pay_data['use'])
                                        <div class="radio-box radio-flex">
                                            <label for="payment-{{ $_pay_key }}">
                                                <input type="radio"
                                                       id="payment-{{ $_pay_key }}"
                                                       value="{{ $_pay_key }}"
                                                       autocomplete="off"
                                                       {{ $loop->index == 0 ? 'checked' : '' }}
                                                       name="payment"
                                                       class="uk-radio">
                                                <span>
                                                 {{ $_pay_key == 1 ? trans($_pay_data['name']) : trans('shop.payment_type') }}
                                            </span>
                                                <i class="{{ trans($_pay_data['class']) }} sprites uk-display-inline-block"></i>
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="uk-form-controls uk-margin-top">
                                <textarea name="comment"
                                          class="uk-textarea text-area"
                                          rows="3"
                                          autocomplete="off"
                                          placeholder="@lang('front.label_user_comment')"></textarea>
                            </div>
                            @if(!$_user)
                                <div class="checkbox-box uk-form-controls uk-margin-small-top uk-text-small"
                                     id="{{ $_form }}-agreement">
                                    <label>
                                        <input type="checkbox"
                                               autocomplete="off"
                                               id="agreement-1"
                                               name="agreement"
                                               value="1"
                                               class="uk-checkbox"
                                               checked>
                                        <span>
                                            @lang('forms.agreement')
                                        </span>
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div class="uk-text-center">
                            <button class="uk-button btn-basket uk-width-1-1 use-ajax"
                                    type="submit">
                                @lang('forms.order_confirmed')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="uk-alert uk-alert-warning uk-border-rounded uk-box-shadow-small uk-margin-top">
            <p>@lang('shop.alert_basket_empty')</p>
        </div>
    @endif
</div>

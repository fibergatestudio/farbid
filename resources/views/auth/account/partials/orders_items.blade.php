@php
    $_orders = $item->_orders();
@endphp
<div id="account-orders-items-list">
    @if($_orders->isNotEmpty())
        <div class="uk-child-width-1-2@m uk-child-width-1-3@xl uk-grid-medium"
             uk-grid="masonry: true">
            @foreach($_orders as $_order)
                <? $_order_data = $_order->info ?>
                <div>
                    <div class="item-my-order">
                        <div class="top-order uk-flex uk-flex-between">
                            <div class="number-order">
                                <span class="uk-visible@l">Заказ</span> {{ "№{$_order->order}" }}
                            </div>
                            <div class="data-order">
							   <span>
                                {{ $_order->created_at->format('d.m.Y') }}
							   </span>
                            </div>
                            <div class="data-order">
							  <span>
                                {{ $_order_data['total']['currency']['prefix'] }}
                                {{ $_order_data['total']['format']['view_price'] }}
                                {{ $_order_data['total']['currency']['suffix'] }}
							  </span>
                            </div>
                            <div class="data-order uk-text-uppercase">
                                <button type="button"
                                        class="use-ajax uk-button-link uk-button"
                                        data-path="{{ _r('ajax.shop.repeat_order') }}"
                                        data-order_id="{{ $_order->id }}">
                                    повторить
                                </button>
                            </div>
                        </div>
                        <div class="content-order">
                            @foreach($_order_data['items'] as $_product)
                                @if($_product['type'] == 'product')
                                    <div class="uk-flex uk-flex-middle item-order-product">
                                        @if($_product['entity'])
                                            <div class="product-image">
                                                <a href="{{ _u($_product['entity']->_alias->alias) }}"
                                                   target="_blank"
                                                   class="item-left uk-flex uk-flex-middle">
                                                    <img alt="{{ $_product['title'] }}"
                                                         src="{{ formalize_path($_product['preview']) }}">
                                                </a>
                                            </div>
                                            <div class="uk-flex-1">
                                                <a href="{{ _u($_product['entity']->_alias->alias) }}"
                                                   title="{{ $_product['title'] }}"
												   class="title">
                                                    {{ $_product['title'] }}
                                                </a>
                                                <div class="sky">
                                                    Артикул: {{ $_product['sky'] }}
                                                </div>
                                                <div class="price-count uk-flex">
                                                    <div class="count uk-margin-right">
                                                        {{ $_product['count'] }} шт.
                                                    </div>
                                                    <div class="price">
                                                        {{ $_product['amount']['currency']['prefix'] }}
                                                        {{ $_product['amount']['format']['view_price'] }}
                                                        {{ $_product['amount']['currency']['suffix'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="product-image">
                                                <div class="item-left uk-flex uk-flex-middle">
                                                    <img alt="{{ $_product['title'] }}"
                                                         src="{{ formalize_path($_product['preview']) }}">
                                                </div>
                                            </div>
                                            <div class="uk-flex-1">
                                                <div>
                                                    {{ $_product['title'] }}
                                                </div>
                                                <div class="sky">
                                                    Артикул: {{ $_product['sky'] }}
                                                </div>
                                                <div class="price-count uk-flex">
                                                    <div class="count uk-margin-right">
                                                        {{ $_product['count'] }} шт.
                                                    </div>
                                                    <div class="price">
                                                        {{ $_product['amount']['currency']['prefix'] }}
                                                        {{ $_product['amount']['format']['view_price'] }}
                                                        {{ $_product['amount']['currency']['suffix'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="uk-flex uk-flex-middle item-order-product-group">
                                        <div class="item-product">
                                            @foreach($_product['products'] as $_product_key => $_product_inside)
                                                <div class="uk-flex uk-flex-middle">
                                                    @if($_product_inside['entity'])
                                                        <div class="product-image">
                                                            <a href="{{ _u($_product_inside['entity']->_alias->alias) }}"
                                                               target="_blank"
                                                               class="item-left uk-flex uk-flex-middle">
                                                                <img alt="{{ $_product_inside['title'] }}"
                                                                     src="{{ formalize_path($_product_inside['preview']) }}">
                                                            </a>
                                                        </div>
                                                        <div class="uk-flex-1">
                                                            <a href="{{ _u($_product_inside['entity']->_alias->alias) }}"
                                                               title="{{ $_product_inside['title'] }}"
															   class="title">
                                                                {{ $_product_inside['title'] }}
                                                            </a>
                                                            <div class="sky">
                                                                Артикул: {{ $_product_inside['sky'] }}
                                                            </div>
                                                            @if($_product_key == 'primary')
                                                                <div class="price">
                                                                    {{ $_product_inside['price']['price']['currency']['prefix'] }}
                                                                    {{ $_product_inside['price']['price']['format']['view_price'] }}
                                                                    {{ $_product_inside['price']['price']['currency']['suffix'] }}
                                                                </div>
                                                            @else
                                                                <div class="price">
                                                                    {{ $_product_inside['discount_price']['price']['currency']['prefix'] }}
                                                                    {{ $_product_inside['discount_price']['price']['format']['view_price'] }}
                                                                    {{ $_product_inside['discount_price']['price']['currency']['suffix'] }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="product-image">
                                                            <div class="item-left uk-flex uk-flex-middle">
                                                                <img alt="{{ $_product_inside['title'] }}"
                                                                     src="{{ formalize_path($_product_inside['preview']) }}">
                                                            </div>
                                                        </div>
                                                        <div class="uk-flex-1">
                                                            <div>
                                                                {{ $_product_inside['title'] }}
                                                            </div>
                                                            <div class="sky">
                                                                Артикул: {{ $_product_inside['sky'] }}
                                                            </div>
                                                            @if($_product_key == 'primary')
                                                                <div class="price">
                                                                    {{ $_product_inside['price']['price']['currency']['prefix'] }}
                                                                    {{ $_product_inside['price']['price']['format']['view_price'] }}
                                                                    {{ $_product_inside['price']['price']['currency']['suffix'] }}
                                                                </div>
                                                            @else
                                                                <div class="price">
                                                                    {{ $_product_inside['discount_price']['price']['currency']['prefix'] }}
                                                                    {{ $_product_inside['discount_price']['price']['format']['view_price'] }}
                                                                    {{ $_product_inside['discount_price']['price']['currency']['suffix'] }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="price-count uk-flex uk-margin-left">
                                            <div class="count uk-margin-right">
                                                {{ $_product['count'] }} шт.
                                            </div>
                                            <div class="price">
                                                {{ $_product['amount']['currency']['prefix'] }}
                                                {{ $_product['amount']['format']['view_price'] }}
                                                {{ $_product['amount']['currency']['suffix'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="uk-alert uk-alert-warning uk-border-rounded uk-box-shadow-small">
            <p>@lang('others.no_items')</p>
        </div>
    @endif
</div>
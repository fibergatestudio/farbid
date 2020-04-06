@php($_discount_timer = $item->_discount_timer)
<div class="item">
    <div class="product-item">
        <div class="row">
            <div class="col-md-6 col-12 deals-img">
                <div class="product-image">
                    <a href="{{ _u($item->_alias->alias, [], TRUE) }}"
                       title="{{ $item->title }}">
                        {!! $item->_preview_asset('thumb_shop_product', ['attributes' => ['uk-cover' => TRUE, 'alt' => $item->title]]) !!}
                    </a>
                </div>
            </div>
            <div class="col-md-6 col-12 mt-xs-30">
                <div class="product-item-details">
                    <div class="product-item-name">
                        <a href="{{ _u($item->_alias->alias, [], TRUE) }}"
                           title="{{ $item->title }}">
                            {{ $item->title }}
                        </a>
                    </div>
                    @if($item->prices_product['availability'])
                        <div class="price-box">
                        <span class="price">
                            {{ $item->prices_product['price']['original']['price'] }}
                            <span>{{ $item->prices_product['price']['currency']['suffix'] }}</span>
                        </span>
                            @if($item->prices_product['old_price'])
                                <del class="price old-price">
                                    {{ $item->prices_product['old_price']['original']['price'] }}
                                    <span>{{ $item->prices_product['price']['currency']['suffix'] }}</span>
                                </del>
                            @endif
                        </div>
                    @else
                        <div class="box-out-of-stock">
                            @lang('others.out_of_stock')
                        </div>
                    @endif
                    <p>Lorem ipsum dolor consectetuer adipiscing elit. Donec eros, scelerisque nec, rhoncus eget.</p>
                </div>
                <div class="product-detail-inner">
                    <div class="detail-inner-left">
                        <ul>
                            <li class="pro-cart-icon">
                                <button class="uk-button use-ajax"
                                        data-product="{{ $item->id }}"
                                        data-path="{{ _r('ajax.shop.basket') }}"
                                        type="button">
                                    <span></span>В корзину
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                @if($_discount_timer->exists)
                    <div class="item-offer-clock">
                        <ul class="countdown-clock"
                            data-id="{{ $_discount_timer->id }}"
                            data-type="teaser"
                            data-time="{{ $_discount_timer->finish_date->format('m/d/Y H:i:s') }}">
                            <li>
                                <span class="days">00</span>
                                <p class="days_ref">дни</p>
                            </li>
                            <li class="seperator">:</li>
                            <li>
                                <span class="hours">00</span>
                                <p class="hours_ref">часы</p>
                            </li>
                            <li class="seperator">:</li>
                            <li>
                                <span class="minutes">00</span>
                                <p class="minutes_ref">мин</p>
                            </li>
                            <li class="seperator">:</li>
                            <li>
                                <span class="seconds">00</span>
                                <p class="seconds_ref">сек</p>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
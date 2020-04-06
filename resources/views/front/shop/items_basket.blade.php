<div id="basket-inside-items">
    <div id="form-print"
         class="admission-form-wrapper">
        @foreach($items['items'] as $_item)
            @if($_item['type'] == 'product')
                <div class="uk-flex item-basket-product">
                    <div class="product-image">
                        <a href="{{ _u($_item['alias']) }}">
                            <img alt=""
                                 src="{{ $_item['preview'],('preview_basket') }}">
                        </a>
                    </div>
                    <div class="box-title-column">
                        <div class="uk-flex uk-flex-middle uk-width-large@xl uk-width-medium@l">
                            <div class="product-title">
                                <a href="{{ _u($_item['alias']) }}"
                                   class="title uk-display-block">
                                    {{ $_item['title'] }}
                                </a>
                                <button type="button"
                                        class="uk-button remove use-ajax uk-hidden@m"
                                        data-product="{{ $_item['id'] }}"
                                        data-action="remove"
                                        data-path="{{ _r('ajax.shop.basket') }}">
                                    <i class="icon-trash sprites cart-remove-item uk-display-inline-block"></i>
                                </button>
                                <div class="box-sky-price">
                                    @if($_item['sky'])
                                        <div class="sky">
                                            Артикул:
                                            {!! $_item['sky'] !!}
                                        </div>
                                    @endif
                                    @if($_item['price']['availability'])
                                        <div class="uk-hidden@m">
                                            <div class="price-box uk-text-right">
                                                @if($_item['price']['old_price'])
                                                    <del class="old-price">
                                                        {{ $_item['price']['old_price']['format']['view_price'] }}
                                                    </del>
                                                @endif
                                                <span class="info-deta price-original">
                                                    {{ $_item['price']['price']['currency']['prefix'] }}
                                                    {{ $_item['price']['price']['format']['view_price'] }}
                                                    {{ $_item['price']['price']['currency']['suffix'] }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @if($_item['price']['availability'])
                                    <div class="available">
                                        @lang('shop.product_available')
                                    </div>
                                @else
                                    <div class="available not">
                                        @lang('shop.product_not_available')
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="uk-flex-1 uk-flex uk-flex-between">
                            <div class="uk-flex uk-flex-between uk-flex-middle product-basket-content">

                                <div class="product-qty">
                                    <div class="custom-qty uk-flex uk-flex-middle">
                                        <button type="button"
                                                class="uk-button reduced items use-ajax"
                                                data-product="{{ $_item['id'] }}"
                                                data-action="down"
                                                data-path="{{ _r('ajax.shop.basket') }}">
                                            <i class="icon-minus sprites uk-display-inline-block"></i>
                                        </button>
                                        <i class="input-text qty uk-display-inline-block uk-text-center">
                                            {{ "{$_item['count']}шт." }}
                                        </i>
                                        <button type="button"
                                                class="uk-button increase items use-ajax"
                                                data-product="{{ $_item['id'] }}"
                                                data-action="up"
                                                data-path="{{ _r('ajax.shop.basket') }}">
                                            <i class="icon-plus sprites uk-display-inline-block"></i>
                                        </button>
                                    </div>
                                    <div class="total-price uk-margin-medium-right uk-hidden@m">
                                        {{ $_item['amount']['currency']['prefix'] }}
                                        {{ $_item['amount']['format']['view_price'] }}
                                        {{ $_item['amount']['currency']['suffix'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="uk-flex-1 uk-flex uk-flex-between uk-flex-middle uk-grid-collapse uk-visible@m"
                                 uk-grid>
                                <div class="price-box uk-text-right uk-visible@m uk-width-1-3">
                                    @if($_item['price']['old_price'])
                                        <del class="old-price">
                                            {{ $_item['price']['old_price']['format']['view_price'] }}
                                        </del>
                                    @endif
                                    <span class="info-deta price-original">
                                    {{ $_item['price']['price']['currency']['prefix'] }}
                                        {{ $_item['price']['price']['format']['view_price'] }}
                                        {{ $_item['price']['price']['currency']['suffix'] }}
                                </span>
                                </div>
                                <div class="uk-flex-1 uk-flex uk-flex-right uk-width-2-3">
                                    <div data-id="100"
                                         class="total-price uk-margin-medium-right uk-visible@m">
                                        {{ $_item['amount']['currency']['prefix'] }}
                                        {{ $_item['amount']['format']['view_price'] }}
                                        {{ $_item['amount']['currency']['suffix'] }}
                                    </div>
                                    <button type="button"
                                            class="uk-button remove use-ajax"
                                            data-product="{{ $_item['id'] }}"
                                            data-action="remove"
                                            data-path="{{ _r('ajax.shop.basket') }}">
                                        <i class="icon-trash sprites cart-remove-item uk-display-inline-block"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($_item['type'] == 'product_group')
                <div class="set">@lang('forms.promotional_kit')</div>
                <div class="uk-flex uk-flex-between uk-margin-bottom item-basket-product group-basket-product">
                    <div class="box-title-column">
                        <div>
                            @foreach($_item['products'] as $_product)
                                <div class="uk-flex left-half uk-flex-middle">
                                    <div class="product-image">
                                        <a href="{{ _u($_product['alias']) }}">
                                            <img alt=""
                                                 src="{{ $_product['preview'],('preview_basket') }}">
                                        </a>
                                    </div>
                                    <div class="uk-flex uk-flex-middle uk-width-large@xl uk-width-medium@l">
                                        <div class="product-title ">
                                            <a href="{{ _u($_product['alias']) }}"
                                               class="title uk-display-block">
                                                {{ $_product['title'] }}
                                            </a>
                                            <div class="box-sky-price">
                                                @if($_product['sky'])
                                                    <div class="sky">
                                                        Артикул:
                                                        {!! $_product['sky'] !!}
                                                    </div>
                                                @endif
                                                @if($_product['price']['availability'])
                                                    <div class="uk-hidden@m">
                                                        <div class="price-box uk-text-right">
                                                            @if($_product['price']['old_price'])
                                                                <del class="old-price">
                                                                    {{ $_product['price']['old_price']['format']['view_price'] }}
                                                                </del>
                                                            @endif
                                                            <span class="info-deta price-original">
                                                        {{ $_product['price']['price']['currency']['prefix'] }}
                                                                {{ $_product['price']['price']['format']['view_price'] }}
                                                                {{ $_product['price']['price']['currency']['suffix'] }}
                                                    </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($_product['price']['availability'])
                                                <div class="available">
                                                    @lang('shop.product_available')
                                                </div>
                                            @else
                                                <div class="available not">
                                                    @lang('shop.product_not_available')
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <button type="button"
                                    class="uk-button remove use-ajax uk-hidden@m"
                                    data-product="{{ $_item['id'] }}"
                                    data-action="remove"
                                    data-product_group="1"
                                    data-path="{{ _r('ajax.shop.basket') }}">
                                <i class="icon-trash sprites cart-remove-item uk-display-inline-block"></i>
                            </button>
                        </div>
                        <div class="uk-flex-1 uk-flex uk-flex-between">
                            <div class="product-qty uk-flex uk-flex-middle">
                                <div class="custom-qty uk-flex uk-flex-middle">
                                    <button type="button"
                                            class="uk-button reduced items use-ajax"
                                            data-product="{{ $_item['id'] }}"
                                            data-product_group="1"
                                            data-action="down"
                                            data-path="{{ _r('ajax.shop.basket') }}">
                                        <i class="icon-minus sprites uk-display-inline-block"></i>
                                    </button>
                                    <i class="input-text qty uk-display-inline-block uk-text-center">
                                        {{ "{$_item['count']}шт." }}
                                    </i>
                                    <button type="button"
                                            class="uk-button increase items use-ajax"
                                            data-product="{{ $_item['id'] }}"
                                            data-product_group="1"
                                            data-action="up"
                                            data-path="{{ _r('ajax.shop.basket') }}">
                                        <i class="icon-plus sprites uk-display-inline-block"></i>
                                    </button>
                                </div>
                                <div class="total-price uk-margin-medium-right uk-hidden@m">
                                    {{ $_item['amount']['currency']['prefix'] }}
                                    {{ $_item['amount']['format']['view_price'] }}
                                    {{ $_item['amount']['currency']['suffix'] }}
                                </div>
                            </div>
                            <div class="uk-flex-1 uk-flex uk-flex-between uk-grid-collapse uk-visible@m"
                                 uk-grid>
                                <div class="uk-flex uk-flex-stretch uk-width-1-3 group-price uk-flex-column">
                                    @foreach($_item['products'] as $_type => $_product)
                                        <div class="uk-flex-grow-1 uk-flex uk-flex-middle">
                                            @if($_type == 'primary')
                                                @if($_product['price']['availability'])
                                                    <div class="price-box uk-text-right">
                                                        @if($_product['price']['old_price'])
                                                            <del class="old-price">
                                                                {{ $_product['price']['old_price']['format']['view_price'] }}
                                                            </del>
                                                        @endif
                                                        <span class="info-deta price-original">
                                                {{ $_product['price']['price']['currency']['prefix'] }}
                                                            {{ $_product['price']['price']['format']['view_price'] }}
                                                            {{ $_product['price']['price']['currency']['suffix'] }}
                                            </span>
                                                    </div>
                                                @endif
                                            @else
                                                @if($_product['price']['availability'])
                                                    <div class="price-box uk-text-right">
                                                        <del class="old-price">
                                                            {{ $_product['price']['price']['format']['view_price'] }}
                                                        </del>
                                                        <span class="info-deta price-original">
                                                {{ $_product['discount_price']['price']['currency']['prefix'] }}
                                                            {{ $_product['discount_price']['price']['format']['view_price'] }}
                                                            {{ $_product['discount_price']['price']['currency']['suffix'] }}
                                            </span>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="uk-flex-1 uk-flex uk-flex-middle uk-flex-right uk-width-2-3">
                                    <div class="total-price uk-margin-medium-right">
                                        {{ $_item['amount']['currency']['prefix'] }}
                                        {{ $_item['amount']['format']['view_price'] }}
                                        {{ $_item['amount']['currency']['suffix'] }}
                                    </div>
                                    <button type="button"
                                            class="uk-button remove use-ajax"
                                            data-product="{{ $_item['id'] }}"
                                            data-action="remove"
                                            data-product_group="1"
                                            data-path="{{ _r('ajax.shop.basket') }}">
                                        <i class="icon-trash sprites cart-remove-item uk-display-inline-block"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <div class="price-total uk-padding-remove-horizontal">
        <span class="title-total">
            @lang('shop.form_title_amount')
        </span>
        @if($items['total']['currency']['prefix'])
            <span class="currency">
                {{ $items['total']['currency']['prefix'] }}
            </span>
        @endif
        <span class="price-total-fin">
            {{ $items['total']['format']['view_price'] }}
        </span>
        @if($items['total']['currency']['suffix'])
            <span class="currency">
                {{ $items['total']['currency']['suffix'] }}
            </span>
        @endif
    </div>
</div>
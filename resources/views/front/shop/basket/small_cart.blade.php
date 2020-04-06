@php
    $_basket = ShopBasket::get_basket();
    $_count = $_basket->get('count', 0);
    $_shop_basket_page = wrap()->get('pages.shop_basket');
    $_shop_basket_page_alias = $_shop_basket_page->_alias;
    $_shop_basket_page_alias = $_shop_basket_page_alias->language != DEFAULT_LANGUAGE ? _u("{$_shop_basket_page_alias->language}/{$_shop_basket_page_alias->alias}") : _u($_shop_basket_page_alias->alias);
@endphp
<div class="cart-icon uk-flex uk-flex-middle"
     id="card-basket">
    @if($_count)
        <a href="{{ $_shop_basket_page_alias }}"
           class="uk-flex link-go-to-basket">
            <div class="{{ wrap()->get('pages.shop_basket') ? ' no-empty' : NULL }}"
                 id="link-basket">
                <i class="icon-header-basket sprites uk-display-block"></i>
            </div>
            <span class="count-top uk-visible@s">
                <small class="cart-notification">{{ $_count }}</small>
            </span>
            @include('front.shop.basket.small_cart_inside', compact('_count', '_basket'))
        </a>
    @else
        <div class="uk-flex basket-empty-div">
            <i class="icon-header-basket sprites uk-display-block"></i>
            <span class="count-top uk-visible@s">
        </span>
            @include('front.shop.basket.small_cart_inside', compact('_count', '_basket'))
        </div>
    @endif
</div>

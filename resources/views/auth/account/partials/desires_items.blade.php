@php
    $desires = NULL;
    if($_desires->isNotEmpty()){
        foreach($_desires as $_id => $_product){
            $desires[] = shop_product_load($_product->product_id, $language);
        }
    }
@endphp
<div id="desires-box-items">
    @if(!is_null($desires))
        <div uk-slider
             class="uk-visible@s">
            <div class="uk-position-relative">
                <div class="uk-slider-container uk-light">
                    <ul class="uk-slider-items uk-grid-collapse uk-child-width-1-2 uk-child-width-1-3@m uk-child-width-1-4@l uk-child-width-1-5@xl"
                        uk-grid
                        uk-height-match="target: .title; row: false">
                        @foreach ($desires as $_product)
                            <li class="uk-first-column">
                                <div class="uk-position-relative like-teaser-cart">
                                    @include('front.shop.teaser_product', ['item' => $_product])
                                    <div
                                        class="uk-position-absolute like-hover uk-flex uk-flex-column uk-flex-between">
                                        <a href="{{ _u($_product->_alias->alias) }}"
                                           class="uk-display-block uk-text-center">
                                            <i class="icon-arrow-like sprites uk-display-block"></i>
                                            перейти на<br>
                                            страницу товара
                                        </a>
                                        <button class="uk-button uk-flex uk-flex-middle use-ajax"
                                                type="button"
                                                data-path="{{ _r('ajax.shop.product_desires') }}"
                                                data-product="{{ $_product->relation_entity->id }}"
                                                data-refresh="1"
                                                rel="nofollow">
                                            <i class="icon-remove-like sprites uk-display-inline-block"></i>
                                            <span class="uk-display-inline-block uk-text-center">
                                                Удалить из моих желаний
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="uk-visible@xl">
                    <a class="uk-position-center-left-out uk-position-small"
                       href="#"
                       uk-slider-item="previous">
                        <i class="icon-arrow-prev-watch uk-display-block sprites"></i>
                    </a>
                    <a class="uk-position-center-right-out uk-position-small"
                       href="#"
                       uk-slider-item="next">
                        <i class="icon-arrow-next-watch uk-display-block sprites"></i>
                    </a>
                </div>
            </div>
            <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin-large"></ul>
        </div>
        <div uk-slider class="uk-hidden@s slider-chunk like-product">
            <div class="uk-slider-items uk-grid-collapse uk-child-width-1-2 " uk-grid
                 uk-height-match="target: .title; row: false">
                @foreach ($desires as $_product)
                    <div class="item">
                        <div>
                            @include('front.shop.teaser_product', ['item' => $_product])
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="uk-hidden@s uk-light">
                <a class="uk-position-center-left" href="#"
                   uk-slidenav-previous
                   uk-slider-item="previous"></a>
                <a class="uk-position-center-right"
                   href="#"
                   uk-slidenav-next
                   uk-slider-item="next"></a>
            </div>
            <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
        </div>
    @else
        <div class="uk-alert uk-alert-warning uk-border-rounded uk-box-shadow-small">
            <p>@lang('others.no_items')</p>
        </div>
    @endif
</div>
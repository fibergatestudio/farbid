@if($item->related_products->isNotEmpty())
    <div class="uk-visible@s">
        <div class="uk-container uk-container-large">
            <div class="product-listing uk-padding uk-padding-remove-horizontal">
                <h2 class="block-title uk-text-center uk-text-uppercase">
                    <span>@lang('shop.recommended_products')</span>
                </h2>
                <div
                    class="uk-grid-collapse uk-child-width-1-2 uk-child-width-1-3@m uk-child-width-1-4@l uk-child-width-1-5@xl"
                    uk-grid
                    uk-height-match="target: .title; row: false">
                    @foreach ($item->related_products as $_product)
                        <div class="item">
                            @include('front.shop.teaser_product', ['item' => $_product, 'language' => $_language])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="uk-hidden@s">
        <div class="uk-container uk-container-large uk-position-relative">
            <div class="product-listing uk-padding uk-padding-remove-horizontal">
                <h2 class="block-title uk-text-center uk-text-uppercase">
                    <span>@lang('shop.recommended_products')</span>
                </h2>
                <div uk-slider
                     class="uk-hidden@s slider-chunk">
                    <div
                        class="uk-slider-items uk-grid-collapse uk-child-width-1-2 uk-child-width-1-3@m uk-child-width-1-4@l uk-child-width-1-5@xl"
                        uk-grid
                        uk-height-match="target: .title; row: false">
                        @foreach ($item->related_products->chunk(2) as $_product_chunk)
                            <div class="item">
                                @foreach($_product_chunk as $_product)
                                    @include('front.shop.teaser_product', ['item' => $_product, 'language' => $_language])
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <div class="uk-hidden@s uk-light">
                        <a class="uk-position-center-left"
                           href="#"
                           rel="nofollow"
                           uk-slidenav-previous
                           uk-slider-item="previous"></a>
                        <a class="uk-position-center-right"
                           href="#"
                           uk-slidenav-next
                           rel="nofollow"
                           uk-slider-item="next"></a>
                    </div>
                    <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                </div>
            </div>
        </div>
    </div>
@endif
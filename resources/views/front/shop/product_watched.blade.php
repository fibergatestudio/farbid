@if($item->watched_product && $item->watched_product->isNotEmpty())
    <div class="product-watched">
        <div class="uk-container uk-container-large">
            <div class="product-listing uk-padding uk-padding-remove-horizontal uk-padding-remove-bottom">
                <h2 class="block-title uk-text-center uk-text-uppercase">
                    <span>@lang('shop.viewed_products'):</span>
                </h2>
                <div uk-slider>
                    <div class="uk-position-relative">
                        <div class="uk-slider-container uk-light">
                            <ul class="uk-slider-items uk-grid-collapse uk-child-width-1-2 uk-child-width-1-3@m uk-child-width-1-4@l uk-child-width-1-5@xl"
                                uk-grid
                                uk-height-match="target: .title; row: false">
                                @foreach ($item->watched_product as $product)
                                    <li>
                                        @include('front.shop.teaser_product', ['item' => shop_product_load($product->product_id), 'language' => $_language])
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="uk-visible@xl">
                            <a class="uk-position-center-left-out uk-position-small"
                               rel="nofollow"
                               href="#"
                               uk-slider-item="previous">
                                <i class="icon-arrow-prev-watch uk-display-block sprites"></i>
                            </a>
                            <a class="uk-position-center-right-out uk-position-small"
                               href="#"
                               rel="nofollow"
                               uk-slider-item="next">
                                <i class="icon-arrow-next-watch uk-display-block sprites"></i>
                            </a>
                        </div>
                    </div>
                    <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin-large"></ul>
                </div>
            </div>
        </div>
    </div>
@endif
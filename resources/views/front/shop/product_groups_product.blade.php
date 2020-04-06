@if($item->groups_product->isNotEmpty())
    <div class="buy-together uk-padding uk-padding-remove-horizontal">
        <div class="uk-container uk-container-large">
            <h2 class="block-title uk-text-center uk-text-uppercase">
                @lang('shop.cheaper_together'):
            </h2>
            <div class="uk-text-center">
                <div class="title-product uk-display-inline-block uk-text-uppercase">
                    {!! $_wrap['page']['_title'] !!}
                    <span>
                            {{ $item->prices_product['price']['currency']['prefix'] }}
                        {{ $item->prices_product['price']['format']['view_price_2'] }}
                        {{ $item->prices_product['price']['currency']['suffix'] }}
                        </span>
                </div>
            </div>
            <div uk-slider>
                <div class="uk-position-relative">
                    <div class="uk-slider-container uk-light">
                        <ul class="uk-slider-items uk-grid-collapse uk-child-width-1-2 uk-child-width-1-3@m uk-child-width-1-4@l uk-child-width-1-5@xl"
                            uk-grid
                            uk-height-match="target: .title; row: false">
                            @foreach ($item->groups_product as $product)
                                @if($product->prices_product['availability'])
                                    <li>
                                        @include('front.shop.teaser_product_together', ['item' => $product, 'language' => $_language])
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="uk-visible@xl">
                        <a class="uk-position-center-left-out uk-position-small"
                           href="#"
                           rel="nofollow"
                           uk-slider-item="previous">
                            <i class="icon-slider-prev uk-display-block sprites"></i>
                        </a>
                        <a class="uk-position-center-right-out uk-position-small"
                           href="#"
                           rel="nofollow"
                           uk-slider-item="next">
                            <i class="icon-slider-next uk-display-block sprites"></i>
                        </a>
                    </div>
                </div>
                <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin uk-hidden@xl"></ul>
            </div>
        </div>
    </div>
@endif
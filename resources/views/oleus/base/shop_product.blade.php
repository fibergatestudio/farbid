@php($_teaser_image = $item->_preview_asset('thumb_shop_product'))
<div class="shop-product-teaser shop-product-item-{{ $item->id }} shop-product-item-{{ $item->id }}">
    <div class="uk-card uk-card-default uk-margin-bottom">
        @if($_teaser_image)
            <div class="uk-card-media-top">
                <a href="{{ _u($item->_alias->alias, [], TRUE) }}"
                   title="{{ $item->title }}">
                    {!! $_teaser_image !!}
                </a>
            </div>
        @endif
        <div class="uk-card-body">
            <h3 class="uk-card-title shop-product-title">
                <a href="{{ _u($item->_alias->alias, [], TRUE) }}"
                   title="{{ $item->title }}">
                    {{ $item->title }}
                </a>
            </h3>
            <div class="shop-product-data">
                <div class="shop-product-price">
                    @if($item->old_price)
                        <div class="shop-product-old-price">{{ $item->old_price }}</div>
                    @endif
                    <div class="shop-product-current-price">{{ $item->price }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
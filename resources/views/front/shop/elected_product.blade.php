@php
    $_wrap = wrap()->get();
    $language = $_wrap['locale'];
    $location = $_wrap['location'];
    $currency = $_wrap['currency']['current'];
@endphp
<div class="uk-container uk-container-large content-hit uk-position-relative">
    <div class="uk-padding uk-padding-remove-horizontal uk-padding-remove-bottom">
        <h2 class="block-title uk-text-center uk-text-uppercase">
            <span>@lang('shop.title_elected_products')</span>
        </h2>
        <div uk-slider="center: false;"
             class="slider-chunk">
            <ul class="uk-slider-items uk-grid-collapse uk-child-width-1-5@xl uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2"
                uk-grid
                uk-height-match="target: .title; row: false">
                @foreach($items as $_item)
                    <li class="item">
                        @include('front.shop.teaser_product', ['item' => $_item, 'language' => $language])
                    </li>
                @endforeach
            </ul>
            <div class="uk-light uk-hidden@s">
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
            <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin uk-margin-remove-bottom"></ul>
        </div>
    </div>
</div>

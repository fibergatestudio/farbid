ц@php
    $_wrap = wrap()->get();
    $language = $_wrap['locale'];
    $location = $_wrap['location'];
    $currency = $_wrap['currency']['current'];
@endphp
<section class="pt-70">
    <div class="container">
        <div class="product-listing">
            <div class="row">
                <div class="col-12">
                    <div class="heading-part mb-30 mb-xs-15">
                        <h2 class="main_title heading"><span>@lang('shop.title_discount_products')</span></h2>
                    </div>
                </div>
            </div>
            <div class="pro_cat">
                <div class="row">
                    <div class="owl-carousel pro-cat-slider ">
                        @foreach($items as $_item)
                            <div class="item">
                                @include('front.shop.teaser_product', ['item' => shop_product_load($_item->id), 'language' => $language, 'location' => $location, 'currency' => $currency])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
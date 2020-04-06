@php
    $_page_title = $_wrap['page']['_title'];
    $_back_link = request()->server('HTTP_REFERER');
    $_more_load = isset($more_load) && $more_load ? TRUE : FALSE;
@endphp

<div id="shop-category-items-card"
     class="uk-position-relative">
    <div class="uk-flex uk-flex-wrap uk-flex-between uk-padding-small uk-padding-remove-vertical">
        <div class="uk-flex-1">
            @include('front.partials.breadcrumb')
        </div>
        @if($filter['filter'])
            <div class="uk-visible@m">
                @include('front.shop.field_catalog_sort')
            </div>
        @endif
    </div>
    {{--<div class="uk-padding-small uk-padding-remove-vertical uk-hidden@m">--}}
    {{--<h2 class="uk-text-uppercase cat-title-mobile">--}}
    {{--<a href="{{$_back_link}}">--}}
    {{--{!! $_page_title !!}--}}
    {{--</a>--}}
    {{--</h2>--}}
    {{--</div>--}}
    <div class="sorts-list-mobile uk-padding-small uk-padding-remove-vertical uk-margin-small-bottom uk-hidden@m">
        <button class="uk-button uk-button-default uk-margin-small-right"
                uk-toggle="#offcanvas-filter">
                {{ __('Фильтрация') }}
            <i class="icon-filter sprites uk-display-inline-block"></i>
        </button>
        @if($filter['filter'])
            <button class="uk-button uk-button-default uk-margin-small-left"
                    uk-toggle="#offcanvas-sort">
                {{ __('Сортировка') }}
                <i class="icon-sort sprites uk-display-inline-block"></i>
            </button>
        @endif
    </div>
    <div class="uk-grid-collapse uk-grid"
         uk-grid>
        @if($filter['filter'])
            <div class="uk-width-1-6@l uk-width-1-4 uk-visible@m">
                {!! $filter['filter'] !!}
            </div>
        @elseif($sub_categories->isNotEmpty())
            <div class="uk-width-1-6@l uk-width-1-4 uk-visible@m">
                @include('front.shop.parent_filter_menu', ['items' => $sub_categories])
            </div>
        @endif
        <div
            class="{{ $filter['filter'] || $sub_categories->isNotEmpty() ? 'uk-width-5-6@l uk-width-3-4@m ' : NULL }} uk-width-1-1">
            @if(method_exists($items, 'total') && $items->total())
                <div class="product-listing">
                    <div class="inner-listing">
                        <div
                            class="uk-grid-collapse uk-child-width-1-4@xl uk-child-width-1-3@l uk-child-width-1-2 grid-category-product"
                            uk-grid
                            uk-height-match="target: .title; row: false">
                            @include('front.shop.items_category_products', compact('items', 'language'))
                        </div>
                    </div>
                </div>
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded uk-box-shadow-small">
                    <p>@lang('others.no_items')</p>
                </div>
            @endif
        </div>
        <? /* ?>
        @if($sub_categories->isNotEmpty())
            <div
                class="uk-grid uk-grid-small uk-grid-match uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-1 uk-margin-bottom sub-category uk-margin-large-top"
                uk-grid>
                @foreach($sub_categories as $_sub_category)
                    <?
                    $_sub_category_alias = $_sub_category->_alias;
                    $_sub_category_alias = $_sub_category_alias->language != DEFAULT_LANGUAGE ? "{$_sub_category_alias->language}/{$_sub_category_alias->alias}" : $_sub_category_alias->alias;
                    $_bg_sub_category = $_sub_category->_icon_asset('thumb_shop_category', ['attributes' => ['alt' => $_sub_category->title]]);
                    $_bg_sub_category = $_bg_sub_category ? "style=\"background-image: url('{$_bg_sub_category}')\"" : '';
                    ?>
                    <div>
                        <div class="uk-card uk-card-default uk-text-center uk-border-rounded">
                            <div class="uk-card-body">
                                {!! _l("<div class='bg-sub-category' {$_bg_sub_category}></div>" . $_sub_category->title, $_sub_category_alias) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <? */ ?>
    </div>
    <div class="block-load uk-position-fixed">
        <div class="uk-position-center loading-img">
            <img src="{{ formalize_path('template/img/loading.gif') }}"
                 alt="">
        </div>
    </div>
</div>



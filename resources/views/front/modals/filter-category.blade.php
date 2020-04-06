@php
    $_wrap = wrap()->get();
@endphp

<div id="offcanvas-filter"
     uk-offcanvas>
    <div class="uk-offcanvas-bar filter-canvas">
        <button class="uk-offcanvas-close"
                type="button">
            <i class="icon-btn-cat-close sprites-m uk-display-block"></i>
        </button>
        <div class="filter-category">
            <div id="shop-category-items-card-filter">
                <div class="catalog-filter">
                    @if($filter['filter'])
                        {!! $filter['filter'] !!}
                    @elseif($sub_categories->isNotEmpty())
                        @include('front.shop.parent_filter_menu', ['items' => $sub_categories])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
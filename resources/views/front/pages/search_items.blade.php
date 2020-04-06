@php
    $_more_load = isset($_more_load) && $_more_load ? TRUE : FALSE;
    $_language = $language;
@endphp
@foreach($items as $_item)
    <div class="item">
        @include('front.shop.teaser_product', ['item' => $_item, 'language' => $_language])
    </div>
@endforeach
@if(method_exists($items, 'links'))
    <div id="pagination-page">
        <div class="box-pagination uk-text-center">
            {!! $items->links('front.partials.pagination_ajax', ['page_alias' => (isset($alias) && $alias ? $alias : NULL)]) !!}
        </div>
    </div>
@endif


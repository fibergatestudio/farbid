@php
    $_EC = ECommerce::view_item_list($items);
@endphp
@foreach($items as $product)
    @include('front.shop.teaser_product', ['item' => $product, 'language' => $language])
@endforeach
@if(method_exists($items, 'links'))
    <div class="last-block-pagination">
        <div class="box-pagination uk-text-center">
            {!! $items->links('front.partials.pagination_ajax', ['page_alias' => (isset($alias) && $alias ? $alias : NULL)]) !!}
        </div>
    </div>
@endif
<script type="text/javascript">
    @if($_EC)
        if (window.commerce !== undefined) window.commerce.event('view_item_list', {!! $_EC !!});
    @endif
</script>
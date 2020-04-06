@php
    $_wrap = wrap()->get();
    $_contacts = $_wrap['contacts'];
@endphp
@if(count($item->items))
    <ul class="nav navbar-nav">
        @foreach($item->items as $_item)
            @include('front.menus.shop_catalog_menu_item', ['item' => $_item])
        @endforeach
    </ul>
@endif


@if(count($item->items))
    <ul class="uk-navbar-nav uk-flex-column" uk-height-match="target: .uk-navbar-dropdown">
        @foreach($item->items as $_item)
            @include('front.menus.shop_catalog_menu_item', ['item' => $_item])
        @endforeach
    </ul>
@endif


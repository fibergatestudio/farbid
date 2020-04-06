@if(count($item->items))
    <ul class="uk-nav">
        @foreach($item->items as $_item)
            @include('front.menus.shop_catalog_menu_groups_item', ['item' => $_item])
        @endforeach
    </ul>
@endif


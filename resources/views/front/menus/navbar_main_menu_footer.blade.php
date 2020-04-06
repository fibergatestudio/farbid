@if(count($item->items))
    <ul class="uk-navbar-nav uk-flex-wrap uk-visible@m">
        @foreach($item->items as $_item)
            @include('front.menus.menu_footer_item', ['item' => $_item])
        @endforeach
    </ul>
@endif
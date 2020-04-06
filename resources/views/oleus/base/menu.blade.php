@if(count($item->items))
    <ul class="uk-nav"
        id="menu-{{ str_replace('_', '-', $item->key) }}">
        @foreach($item->items as $_item)
            @include('oleus.base.menu_item', ['item' => $_item])
        @endforeach
    </ul>
@endif
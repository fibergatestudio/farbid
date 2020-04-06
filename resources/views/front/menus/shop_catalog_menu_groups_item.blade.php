@php
    $_active_path = active_path($_item['item']['path']) ? 'active' : '';
    $item['list_item']['class'][] = $_active_path;
    $_is_open = $item['children'];
    if(!$_is_open) $_is_open = wrap()->get('is_shop_product');
@endphp
<li class="{{ render_attributes($item['list_item']['class']) }} level {{ $_is_open == FALSE ? '' : 'sub-megamenu' }}">
    @if($item['children'])
        <div class="uk-child-width-1-4@m uk-child-width-auto uk-grid-collapse uk-margin-remove masonry-group"
             uk-grid>
            @foreach($item['children'] as $child)
                <ul class="uk-nav">
                    @include('front.menus.shop_catalog_menu_groups_sub_item', ['item' => $child])
                </ul>
            @endforeach
        </div>
    @endif
</li>

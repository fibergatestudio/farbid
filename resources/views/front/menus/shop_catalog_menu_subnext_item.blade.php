@php
    $_active_path = active_path($_item['item']['path']) ? 'active' : '';
    $item['list_item']['class'] = array_merge($item['list_item']['class'], [
        $_active_path
    ]);
    $item['item']['class'] = array_merge($item['item']['class'], [
        'uk-flex uk-flex-middle'
    ])
@endphp
<li class="{{ render_attributes($item['list_item']['class']) }} level3">
    {!!
        _l($item['item']['title'], $item['item']['path'], [
            'a'           => [
                'class'          => $item['item']['class'],
                'data-menu-item' => $item['entity']->id,
                'id'             => $item['item']['id']
            ],
            'anchor'      => $item['item']['anchor'],
            'description' => $item['item']['description']
        ])
    !!}
    @if($item['children'])
        <ul class="sub-menu-level3">
            @foreach($item['children'] as $child)
                @include('front.menus.shop_catalog_menu_subnext_item', ['item' => $child])
            @endforeach
        </ul>
    @endif
</li>

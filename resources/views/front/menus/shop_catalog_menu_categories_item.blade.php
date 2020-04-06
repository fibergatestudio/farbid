@php
    $_active_path = active_path($_item['item']['path']) ? 'active' : '';
    $item['list_item']['class'][] = $_active_path;
    $_is_open = $item['children'];
    if(!$_is_open) $_is_open = wrap()->get('is_shop_product');
@endphp
<div class="item-inner">
    <div class="cate-detail">
        {!!
            _l($item['item']['title'], $item['item']['path'], [
                'a'           => [
                    'class'          => 'page-scroll',
                    'data-menu-item' => $item['entity']->id,
                    'id'             => $item['item']['id']
                ],
                'anchor'      => $item['item']['anchor'],
                'prefix'      => '<i>' . $item['item']['icon'] . '</i>',
                'suffix'      => '',
                'description' => $item['item']['description']
            ])
        !!}
    </div>
</div>
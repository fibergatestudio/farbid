@php
    $_active_path = active_path($_item['item']['path']) ? 'active' : '';
    $item['list_item']['class'] = array_merge($item['list_item']['class'], [
        $_active_path
    ]);
    $item['item']['class'] = array_merge($item['item']['class'], [
        'uk-flex uk-flex-middle'
    ]);
@endphp
<li class="{{ render_attributes($item['list_item']['class']) }} level2">
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
	 @if($_wrap['device'] == 'pc')
     @else
  <button class='uk-button uk-button-default uk-position-relative link-drop' type='button'></button>
    @endif
    @if($item['children'])
        <div class="uk-navbar-dropdown">
            <div class="parent-title uk-text-uppercase">{{$item['entity']['title']}}</div>
            <div class="megamenu-last mobile-sub-menu">
                <div class="megamenu-inner-top">
                    <ul class="sub-menu-level2">
                        @foreach($item['children'] as $child)
                            @include('front.menus.shop_catalog_menu_subnext_item', ['item' => $child])
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @else
        <div class="uk-navbar-dropdown">
            <div class="uk-child-width-1-1 uk-grid-collapse" uk-grid>
                <div>
                    <ul class="uk-nav uk-navbar-dropdown-nav">
                        <li>
                            <div class="no-subcategories uk-position-center uk-text-uppercase">
                                Нет доступных подкатегорий
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</li>

@php
  $_wrap = wrap()->get();
    $_active_path = active_path($_item['item']['path']) ? 'active' : '';
    $item['list_item']['class'][] = $_active_path;
    $_is_open = $item['children'];
    if(!$_is_open) $_is_open = wrap()->get('is_shop_product');
@endphp
<li class="{{ render_attributes($item['list_item']['class']) }} level {{ $_is_open == FALSE ? '' : 'sub-megamenu' }}">
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
	 @if($_wrap['device'] == 'pc')
     @else
  <button class='uk-button uk-button-default uk-position-relative link-drop' type='button'></button>
    @endif
    @if($item['children'])
        <div class="uk-navbar-dropdown">
            <div class="child-content">
                <div class="parent-title uk-text-uppercase">{{$item['entity']['title']}}</div>
                @foreach($item['children'] as $child)
                    <div>
                        <ul class="uk-nav uk-navbar-dropdown-nav">
                            @include('front.menus.shop_catalog_menu_sub_item', ['item' => $child])
                        </ul>
                    </div>
                @endforeach
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

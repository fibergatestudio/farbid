    <div id="offcanvas-usage" uk-offcanvas>
        <div class="uk-offcanvas-bar">

            <button class="uk-offcanvas-close" type="button" uk-close></button>

            <div class="box-menu-category">
        <ul class="uk-nav-default uk-nav-parent-icon menu-category uk-nav" uk-nav>
            @foreach($item->items as $_item)
                @include('front.menus.shop_catalog_menu_item', ['item' => $_item])
            @endforeach
        </ul>
    </div>
        </div>
    </div>

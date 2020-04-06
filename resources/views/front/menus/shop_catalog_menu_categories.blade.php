@if(count($item->items))
    @foreach($item->items as $_item)
        <div class="item ">
            @include('front.menus.shop_catalog_menu_categories_item', ['item' => $_item])
        </div>
    @endforeach
@endif


              
                  
               
               
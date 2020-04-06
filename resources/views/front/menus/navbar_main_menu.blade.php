@if(count($item->items))
    <ul class="uk-navbar-nav"
        id="menu-main">
        @foreach($item->items as $_item)
		@if($loop->index < 5)
            @include('oleus.base.menu_item', ['item' => $_item])
		@endif	
        @endforeach
		@if(count($item->items) > 5)
			<li>
              <button class="uk-button uk-button-default" type="button">
			  <i class="uk-display-block icon-arrow"></i>
			  </button>
              <div uk-dropdown="mode: click">
			  <ul>
			   @foreach($item->items as $_item)
		         @if($loop->index >= 5)
                   @include('oleus.base.menu_item', ['item' => $_item])
		         @endif
               @endforeach
			  </ul>
			  </div>
            </li>
		@endif
    </ul>
@endif
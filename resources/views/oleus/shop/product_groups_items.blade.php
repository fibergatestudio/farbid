<div class="uk-form-row">
    <div id="list-product-groups-items">
        @isset($items)
            @if($items->isNotEmpty())
                @foreach($items as $_id => $_item)
                    @include('oleus.shop.product_groups_item', [
                        'item' => $_item,
                        'entity' => $entity,
                        'id' => $_id
                    ])
                @endforeach
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded uk-item-empty"
                     uk-alert>
                    @lang('others.item_list_is_empty')
                </div>
            @endif
        @endisset
    </div>
    <div class="uk-clearfix uk-text-right uk-form-row">
        {!! _l(trans('forms.button_add_product_groups'), 'oleus.shop_products.product_groups', ['p' => ['items' => $entity->id], 'a' => ['class' => 'uk-button uk-button-medium uk-waves uk-button-secondary use-ajax uk-border-rounded']]) !!}
    </div>
</div>
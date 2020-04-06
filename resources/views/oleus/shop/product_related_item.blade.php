<div class="uk-item uk-form-row"
     id="product-related-item-{{ $id }}">
    <div class="uk-grid uk-grid-small"
         uk-grid>
        <div class="uk-width-expand">
            {!!
                field_render("related_product.{$id}", [
                    'type' => 'autocomplete',
                    'value' => NULL,
                    'selected' => NULL,
                    'class' => 'uk-autocomplete',
                    'selected' => isset($item->title) ? $item->title: NULL,
                    'value' => isset($item->id) ? $item->id: NULL,
                    'attributes' => [
                        'id' => $id,
                        'data-url' => _r('oleus.shop_products.get_products'),
                        'data-value' => 'name',
                        'placeholder' => trans('forms.label_name_product')
                    ]
                ])
            !!}
        </div>
        <div class="uk-width-auto uk-flex uk-flex-middle">
            {!! _l('', 'oleus.shop_products.related_product', ['p' => ['items' => $entity->id, 'action' => 'remove', 'id' => $id], 'a' => ['class' => 'use-ajax uk-text-danger', 'uk-icon' => 'icon: ui_delete_forever', 'uk-tooltip' => 'title: ' . trans('forms.button_delete')]]) !!}
        </div>
    </div>
</div>
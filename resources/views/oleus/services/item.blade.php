@php
    $_id_price = $item->exists ? $item->id : time();
@endphp
<li id="service-price-item-{{ $_id_price }}">
    <div class="uk-grid uk-grid-small uk-margin-small-bottom">
        <div class="uk-width-expand">
            {!!
                field_render("service_prices.{$_id_price}.title", [
                    'value' => $item->exists ? $item->title : NULL,
                    'required' => TRUE
                ])
            !!}
        </div>
        <div class="uk-width-1-5">
            {!!
                field_render("service_prices.{$_id_price}.sub_title", [
                    'value' => $item->exists ? $item->sub_title : NULL,
                    'required' => TRUE
                ])
            !!}
        </div>
        <div class="uk-width-1-6">
            {!!
                field_render("service_prices.{$_id_price}.sort", [
                    'type' => 'number',
                    'value' => $item->exists ? $item->sort : 0,
                    'attributes' => [
                        'min' => 0
                    ]
                ])
            !!}
        </div>
        <div class="uk-width-small">
            {!!
                field_render("service_prices.{$_id_price}.price", [
                    'value' => $item->exists ? $item->price : NULL,
                ])
            !!}
        </div>
        <div class="uk-width-auto">
            {!! _l('', 'oleus.services.prices', ['p' => ['service' => $service, 'id' => $_id_price], 'a' => ['class' => 'uk-button-icon uk-button uk-button-danger uk-waves uk-border-rounded use-ajax',  'uk-icon' => 'icon: ui_delete_forever', 'uk-tooltip' => 'title: '. trans('forms.button_delete')]]) !!}
        </div>
    </div>
</li>
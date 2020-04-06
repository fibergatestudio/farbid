@if(is_null($modifications))
    <div
        class="uk-alert uk-alert-warning uk-border-rounded uk-margin-small-top uk-margin-remove-bottom">@lang('forms.help_product_modifications_is_empty')</div>
@else
    <div id="list-modifications-items">
        @include('oleus.shop.product_modifications_items', ['modifications' => $modifications])
    </div>
    <div class="uk-clearfix uk-text-right">
        {!! _l(trans('forms.button_add_modify_product'), 'oleus.shop_products.modify', ['p' => ['item' => $modifications['primary']->id], 'a' => ['class' => 'uk-button uk-button-medium uk-waves uk-button-secondary use-ajax uk-border-rounded']]) !!}
    </div>
@endif
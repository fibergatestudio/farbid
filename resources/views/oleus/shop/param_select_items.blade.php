<div class="uk-form-row">
    <label class="uk-form-label">@lang('forms.label_list_items')</label>
    <div class="uk-form-controls">
        <div id="list-param-select-items">
            @isset($items)
                @if($items->isNotEmpty())
                    @include('oleus.shop.param_select_item', compact('items'))
                @else
                    <div class="uk-alert uk-alert-warning uk-border-rounded"
                         uk-alert>
                        @lang('pages.shop_param_list_element_empty')
                    </div>
                @endif
            @endisset
        </div>
        <div class="uk-clearfix uk-text-right">
            {!! _l(trans('forms.button_add_element'), 'oleus.shop_params.item', ['p' => ['param' => $entity->id, 'action' => 'add'], 'a' => ['class' => 'uk-button uk-button-medium uk-waves uk-button-secondary use-ajax uk-border-rounded']]) !!}
        </div>
    </div>
</div>
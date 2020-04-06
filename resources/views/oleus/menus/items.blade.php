<div class="uk-form-row">
    <div id="list-menu-items">
        @isset($items)
            @if($items->isNotEmpty())
                @include('oleus.menus.items_table', compact('items'))
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded"
                     uk-alert>
                    @lang('others.item_list_is_empty')
                </div>
            @endif
        @endisset
    </div>
    <div class="uk-clearfix uk-text-right">
        {!! _l(trans('forms.button_add_item'), 'oleus.menus.item', ['p' => ['menu' => $entity->id, 'action' => 'add'], 'a' => ['class' => 'uk-button uk-button-medium uk-waves uk-button-secondary use-ajax uk-border-rounded']]) !!}
    </div>
</div>
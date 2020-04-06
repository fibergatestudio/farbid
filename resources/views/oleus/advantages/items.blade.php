<div class="uk-form-row">
    <label class="uk-form-label">@lang('pages.items_advantages')</label>
    <div class="uk-form-controls">
        <ul id="list-advantages-items"
            class="uk-list">
            @isset($items)
                @if($items->isNotEmpty())
                    @foreach($items as $_item)
                        @include('oleus.advantages.item', ['item' => $_item])
                    @endforeach
                @else
                    <li class="uk-item uk-item-empty">
                        <div class="uk-alert uk-alert-warning uk-border-rounded"
                             uk-alert>
                            @lang('pages.param_list_element_empty')
                        </div>
                    </li>
                @endif
            @endisset
        </ul>
        <div class="uk-clearfix uk-text-right">
            {!! _l(trans('forms.button_add_advantage'), 'oleus.advantages.item', ['p' => ['advantages' => $entity->id, 'action' => 'add'], 'a' => ['class' => 'uk-button uk-button-medium uk-waves uk-button-secondary use-ajax uk-border-rounded']]) !!}
        </div>
    </div>
</div>
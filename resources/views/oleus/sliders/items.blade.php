<div class="uk-form-row">
    <label class="uk-form-label">@lang('pages.items_sliders')</label>
    <div class="uk-form-controls">
        <div id="list-sliders-items"
             class="uk-list">
            @isset($items)
                @if($items->isNotEmpty())
                    @include('oleus.sliders.item', compact('items'))
                @else
                    <div class="uk-alert uk-alert-warning uk-border-rounded"
                         uk-alert>
                        @lang('others.no_items')
                    </div>
                @endif
            @endisset
        </div>
        <div class="uk-clearfix uk-text-right">
            {!! _l(trans('forms.button_add_slide'), 'oleus.sliders.item', ['p' => ['slider' => $entity->id, 'action' => 'add'], 'a' => ['class' => 'uk-button uk-button-medium uk-waves uk-button-secondary use-ajax uk-border-rounded']]) !!}
        </div>
    </div>
</div>
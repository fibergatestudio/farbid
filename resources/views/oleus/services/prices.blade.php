<div class="uk-form-row">
    <label class="uk-form-label">@lang('pages.prices_service')</label>
    <div class="uk-form-controls">
        <ul id="list-service-prices"
            class="uk-list">
            <li>
                <div class="uk-grid uk-grid-small uk-margin-small-bottom">
                    <div class="uk-width-expand">
                        @lang('forms.label_name') <span class="uk-text-danger">*</span>
                    </div>
                    <div class="uk-width-1-5">
                        @lang('forms.label_signature')
                    </div>
                    <div class="uk-width-1-6">
                        @lang('forms.label_sort')
                    </div>
                    <div class="uk-width-small">
                        @lang('forms.label_price') <span class="uk-text-danger">*</span>
                    </div>
                    <div class="uk-width-auto">
                        <div style="width: 40px;"></div>
                    </div>
                </div>
                <hr>
            </li>
            @isset($items)
                @foreach($items as $_item)
                    @include('oleus.services.item', ['item' => $_item, 'service' => $entity->id])
                @endforeach
            @endisset
        </ul>
        <div class="uk-clearfix uk-text-right">
            {!! _l(trans('forms.button_add_item'), 'oleus.services.prices', ['p' => ['service' => $entity->id], 'a' => ['class' => 'uk-button uk-button-medium uk-waves uk-button-secondary use-ajax uk-border-rounded']]) !!}
        </div>
    </div>
</div>
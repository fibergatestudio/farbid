<div class="uk-text-muted uk-text-small uk-display-block uk-margin-small-bottom">
    <span uk-icon="icon: ui_info; ratio: .8"
          class="uk-margin-small-right uk-text-primary"></span>
    @lang('forms.help_category_sortable_params')
</div>
@if(!is_null($params))
    <div id="list-category-params">
        <div>
            <div class="uk-grid uk-grid-small uk-child-width-1-2 uk-margin-small-bottom"
                 uk-grid>
                <div class="uk-margin-small-bottom uk-text-uppercase">
                    @lang('forms.label_selected_category_params')
                </div>
                <div class="uk-margin-small-bottom uk-text-uppercase">
                    @lang('forms.label_not_selected_category_params')
                </div>
            </div>
        </div>
        <div>
            <div class="uk-grid uk-grid-match uk-grid-small uk-child-width-1-2"
                 uk-grid>
                <div
                    class="uk-sortable-bar sortable-params-list applicable">
                    <div class="uk-card uk-card-body uk-border-rounded">
                        @if($params['applicable']->isNotEmpty())
                            @foreach($params['applicable'] as $_param)
                                <div class="uk-item uk-margin-auto-vertical uk-text-bold uk-text-primary">
                                    <span class="uk-text-bold">{{ $_param->title }}</span>
                                    <input type="hidden"
                                           class="uk-input-sort"
                                           value="{{ $_param->sort }}"
                                           name="category_params[{{ $_param->id }}][sort]">
                                    <input type="hidden"
                                           class="uk-input-applicable"
                                           value="1"
                                           name="category_params[{{ $_param->id }}][applicable]">
                                    <input type="hidden"
                                           class="uk-input-param-id"
                                           value="{{ $_param->id }}"
                                           name="category_params[{{ $_param->id }}][id]">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div
                    class="uk-sortable-bar sortable-params-list not-applicable">
                    <div class="uk-card uk-card-body uk-border-rounded">
                        @if($params['not_applicable']->isNotEmpty())
                            @foreach($params['not_applicable'] as $_param)
                                <div class="uk-item uk-margin-auto-vertical uk-text-bold uk-text-primary">
                                    <span class="uk-text-bold">{{ $_param->title }}</span>
                                    <input type="hidden"
                                           class="uk-input-sort"
                                           value="{{ $_param->sort }}"
                                           name="category_params[{{ $_param->id }}][sort]">
                                    <input type="hidden"
                                           class="uk-input-applicable"
                                           value="0"
                                           name="category_params[{{ $_param->id }}][applicable]">
                                    <input type="hidden"
                                           class="uk-input-param-id"
                                           value="{{ $_param->id }}"
                                           name="category_params[{{ $_param->id }}][id]">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="uk-form-row uk-margin-top"
             id="form-field-relative-param-object">
            <label for="form-field-relative-param"
                   class="uk-form-label">@lang('forms.label_category_relative_param')</label>
            <div class="uk-form-controls">
                <div class="uk-inline uk-width-1-1 sortable-relation-param"
                     id="relation-category-param"
                     data-href="{{ _r('oleus.shop_categories.relation_param') }}">
                    @if($params['applicable']->isNotEmpty())
                        @if($_modify = $item->modify_param)
                            @include('oleus.shop.param_category_relation_select', $_modify)
                        @else
                            <div
                                class="uk-alert uk-alert-warning uk-border-rounded uk-margin-remove">@lang('forms.help_category_no_matching_relative_param')</div>
                        @endif
                    @else
                        <div
                            class="uk-alert uk-alert-warning uk-border-rounded uk-margin-remove">@lang('forms.help_category_empty_relative_param')</div>
                    @endif
                </div>
                <span class="uk-help-block uk-display-block">@lang('forms.help_category_relative_param')</span>
            </div>
        </div>
    </div>
@else
    <div class="uk-alert uk-alert-warning uk-border-rounded">
        <a href="{{ _r('oleus.shop_params.create') }}"
           class="uk-text-primary uk-float-right">
            @lang('forms.button_add')
        </a>
        {{ trans('pages.shop_params_empty') }}
    </div>
@endif
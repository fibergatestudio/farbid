<div class="uk-form-row">
    <label class="uk-form-label">
        {!! $item['city_name'] !!}
        @if($item['city_default'])
            <span class="uk-text-danger">*</span>
        @endif
    </label>
    <div class="uk-form-controls">
        <div class="uk-inline uk-width-1-1">
            <div class="uk-grid uk-grid-small uk-child-width-1-4"
                 uk-grid>
                <div>
                    {!!
                        field_render([
                            'label'      => trans('fields.field_price'),
                            'name'       => 'price',
                            'base_name'  => "prices[{$item['city_id']}]",
                            'value'      => $item['price'],
                            'required'   => $item['city_default'] ? TRUE : FALSE,
                            'attributes' => [
                                'placeholder' => isset($default['price']) ? $default['price'] : NULL
                            ]
                        ])
                    !!}
                </div>
                <div>
                    {!!
                        field_render([
                            'label'      => trans('fields.field_weight'),
                            'name'       => 'weight',
                            'base_name'  => "prices[{$item['city_id']}]",
                            'value'      => $item['weight'],
                            'required'   => $item['city_default'] ? TRUE : FALSE,
                            'attributes' => [
                                'placeholder' => isset($default['weight']) ? $default['weight'] : NULL
                            ]
                        ])
                    !!}
                </div>
                <div>
                    {!!
                        field_render([
                            'type'      => 'select',
                            'label'     => trans('fields.field_sort_link'),
                            'name'      => 'sort',
                            'base_name' => "prices[{$item['city_id']}]",
                            'value'     => $item['sort'],
                            'values'    => sortArray(),
                            'class'     => 'uk-select2'
                        ])
                    !!}
                </div>
                <div style="padding-top: 36px;">
                    {!!
                        field_render([
                            'type'      => 'checkbox',
                            'label'     => __('fields.published_node'),
                            'name'      => 'status',
                            'base_name' => "prices[{$item['city_id']}]",
                            'selected'  => $item['status']
                        ])
                    !!}
                </div>
            </div>
        </div>
    </div>
</div>

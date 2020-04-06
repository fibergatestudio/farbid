@if(is_null($param->relation))
    {!!
        field_render('param_item.min_value', [
            'type' => 'number',
            'label' => trans('forms.label_min_value'),
            'value' => $item ? $item->min_value : NULL
        ])
    !!}
    {!!
        field_render('param_item.max_value', [
            'type' => 'number',
            'label' => trans('forms.label_max_value'),
            'value' => $item ? $item->max_value : NULL
        ])
    !!}
    {!!
        field_render('param_item.step_value', [
            'type' => 'number',
            'label' => trans('forms.label_step_value'),
            'value' => $item ? $item->step_value : NULL,
            'attributes' => [
                'step' => 0.01
            ]
        ])
    !!}
@endif
{!!
    field_render('param_item.unit_value', [
        'label' => trans('forms.label_unit_value'),
        'value' => $item ? $item->unit_value : NULL
    ])
!!}
{!!
    field_render('param_item.param_id', [
        'type' => 'hidden',
        'value' => $param->id
    ])
!!}
{!!
    field_render('param_item.type', [
        'type' => 'hidden',
        'value' => $param->type
    ])
!!}
@if(is_null($param->relation))
    <hr class="uk-divider-icon">
    {!!
        field_render('param_item.style_id', [
            'label'=> trans('forms.label_style_id'),
            'value' => $item ? $item->style_id : NULL
        ])
    !!}
    {!!
        field_render('param_item.style_class', [
            'label'=> trans('forms.label_style_class'),
            'value' => $item ? $item->style_class : NULL
        ])
    !!}
    {!!
        field_render('param_item.icon_fid', [
            'type'   => 'file',
            'label'  => trans('forms.label_icon'),
            'allow'  => 'jpg|jpeg|gif|png|svg',
            'values' => $item && $item->_icon ? [$item->_icon] : NULL,
        ])
    !!}
    {!!
        field_render('param_item.attribute', [
            'type'   => 'textarea',
            'label'  => trans('forms.label_additional_attribute'),
            'value' => $item ? $item->attribute : NULL,
            'attributes' => [
                'rows' => 3
            ]
        ])
    !!}
@endif
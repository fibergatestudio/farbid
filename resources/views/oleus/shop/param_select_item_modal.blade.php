<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked uk-form-horizontal"
      method="POST"
      action="{{ $item->exists ? _r('oleus.shop_params.item', ['param' => $param, 'action' => 'update', 'id' => $item->id]) : _r('oleus.shop_params.item', ['param' => $param, 'action' => 'save']) }}">
    <input type="hidden"
           value="{{ $item->exists ? $item->id : NULL }}"
           name="param_item[id]">
    <input type="hidden"
           value="{{ $param->id }}"
           name="param_item[param_id]">
    <input type="hidden"
           value="{{ $param->type }}"
           name="param_item[type]">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">{{ $item->exists ? trans('pages.shop_params_item_update') : trans('pages.shop_params_item_create') }}</h2>
    </div>
    <div class="uk-modal-body">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-1-4">
                <ul class="uk-tab-left uk-height-1-1"
                    uk-tab="connect: #uk-tab-modal-body; animation: uk-animation-fade; swiping: false;">
                    <li class="uk-active">
                        <a href="#">@lang('others.tab_list_item')</a>
                    </li>
                    <li>
                        <a href="#">@lang('others.tab_style')</a>
                    </li>
                    @if(USE_MULTI_LANGUAGE)
                        <li>
                            <a href="#">@lang('others.tab_translate')</a>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="uk-width-3-4">
                <ul id="uk-tab-modal-body"
                    class="uk-switcher uk-margin">
                    <li>
                        <div class="uk-form-row">
                            {!!
                                field_render('param_item.name', [
                                    'label'=> trans('forms.label_name'),
                                    'value' => $item->exists ? $item->name : NULL,
                                    'required' => TRUE
                                ])
                            !!}
                            @if(!$item->exists || ($item->exists && is_null($item->relation)))
                                <hr class="uk-divider-icon">
                                <div class="uk-grid uk-grid-small">
                                    <div class="uk-width-1-2">
                                        {!!
                                            field_render('param_item.sort', [
                                                'type' => 'select',
                                                'label' => trans('forms.label_sort'),
                                                'selected' => $item->exists ? $item->sort : 0,
                                                'values' => sort_field(),
                                                'class' => 'uk-select2'
                                            ])
                                        !!}
                                    </div>
                                    <div class="uk-width-1-2"
                                         style="padding-top: 33px;">
                                        {!!
                                            field_render('param_item.visible_in_filter', [
                                                'type'     => 'checkbox',
                                                'label'    => trans('forms.label_visible_params_in_filter'),
                                                'selected' => $item->exists ? $item->visible_in_filter : 0
                                            ])
                                        !!}
                                    </div>
                                </div>
                            @endif
                            @if($param->name == 'color')
                                <hr class="uk-divider-icon">
                                {!!
                                    field_render('param_item.color_shade', [
                                        'type' => 'select',
                                        'label' => trans('forms.label_color_shade'),
                                        'selected' => $item->exists ? $item->color_shade : 0,
                                        'values' => config('os_colors'),
                                        'class' => 'uk-select2'
                                    ])
                                !!}
                            @endif
                        </div>
                    </li>
                    <li>
                        <div class="uk-form-row">
                            <div class="uk-grid uk-grid-small">
                                <div class="uk-width-1-2">
                                    {!!
                                        field_render('param_item.style_id', [
                                            'label'=> trans('forms.label_style_id'),
                                            'value' => $item->exists ? $item->style_id : NULL
                                        ])
                                    !!}
                                </div>
                                <div class="uk-width-1-2">
                                    {!!
                                        field_render('param_item.style_class', [
                                            'label'=> trans('forms.label_style_class'),
                                            'value' => $item->exists ? $item->style_class : NULL
                                        ])
                                    !!}
                                </div>
                            </div>
                            {!!
                                field_render('param_item.icon_fid', [
                                    'type'   => 'file',
                                    'label'  => trans('forms.label_icon'),
                                    'allow'  => 'jpg|jpeg|gif|png|svg',
                                    'values' => $item->exists && $item->_icon ? [$item->_icon] : NULL,
                                ])
                            !!}
                            {!!
                                field_render('param_item.attribute', [
                                    'type'   => 'textarea',
                                    'label'  => trans('forms.label_additional_attribute'),
                                    'value' => $item->exists ? $item->attribute : NULL,
                                    'attributes' => [
                                        'rows' => 3
                                    ]
                                ])
                            !!}
                        </div>
                    </li>
                    @if(USE_MULTI_LANGUAGE)
                        <li>
                            <div class="uk-form-row">
                                @php($_languages = config('os_languages.languages'))
                                @foreach($_languages as $_key_language => $_value_language)
                                    @if($_key_language != DEFAULT_LANGUAGE)
                                        {!!
                                            field_render("param_item.translate.{$_key_language}", [
                                                'label' => $_value_language['full_name'],
                                                'value' => $item->exists ? $item->_translate_name($_key_language) : NULL,
                                            ])
                                        !!}
                                    @endif
                                @endforeach
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="uk-modal-footer uk-text-right">
        <button type="submit"
                name="save"
                value="1"
                class="uk-button uk-button-secondary use-ajax uk-waves uk-border-rounded">
            @lang('forms.button_save')
        </button>
        @if($item->exists)
            <button type="button"
                    name="delete"
                    value="1"
                    title="@lang('forms.button_delete')"
                    uk-icon="icon: ui_delete_forever"
                    class="uk-button uk-button-danger use-ajax uk-waves uk-button-icon uk-border-rounded"></button>
            <button class="uk-button uk-button-default uk-modal-close uk-waves uk-button-icon uk-border-rounded"
                    title="@lang('forms.button_close')"
                    uk-icon="icon: ui_close"
                    type="button"></button>
        @endif
    </div>
</form>
@if($item->exists)
    <form
        action="{{ _r('oleus.shop_params.item', ['param' => $param, 'action' => 'destroy', 'id' => $item->id]) }}"
        id="form-delete-object"
        method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endif
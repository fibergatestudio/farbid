<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked"
      method="POST"
      action="{{ $item->exists ? _r('oleus.menus.item', ['menu' => $menu, 'action' => 'update', 'id' => $item->id]) : _r('oleus.menus.item', ['menu' => $menu, 'action' => 'save']) }}">
    <input type="hidden"
           value="{{ $item->exists ? $item->id : NULL }}"
           name="menu_item[id]">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">{{ $item->exists ? trans('pages.menus_item_update') : trans('pages.menus_item_create') }}</h2>
    </div>
    <div class="uk-modal-body">
        <div class="uk-grid uk-grid-small">
            <div class="uk-width-1-4">
                <ul class="uk-tab-left uk-height-1-1"
                    uk-tab="connect: #uk-tab-modal-body; animation: uk-animation-fade; swiping: false;">
                    <li class="uk-active">
                        <a href="#">@lang('others.tab_menu_item')</a>
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
                        <div class="uk-grid uk-grid-small">
                            <div class="uk-width-1-2">
                                {!!
                                    field_render('menu_item.name', [
                                        'label'=> trans('forms.label_link_title'),
                                        'value' => $item->exists ? $item->title : NULL,
                                        'required' => TRUE
                                    ])
                                !!}
                            </div>
                            <div class="uk-width-1-2">
                                {!!
                                    field_render('menu_item.sub_name', [
                                        'label'=> trans('forms.label_link_sub_title'),
                                        'value' => $item->exists ? $item->sub_title : NULL
                                    ])
                                !!}
                            </div>
                        </div>
                        <div class="uk-grid uk-grid-small">
                            <div class="uk-width-1-2">
                                @php($getAlias = $item->exists ? $item->_getAlias() : NULL)
                                {!!
                                    field_render('menu_item.link', [
                                        'type' => 'autocomplete',
                                        'label' => trans('forms.label_composed_link'),
                                        'value' => $item->exists && is_numeric($item->alias_id) && $getAlias ? $item->alias_id : NULL,
                                        'selected' => $item->exists && $getAlias ? $getAlias->name : NULL,
                                        'class' => 'uk-autocomplete',
                                        'attributes' => [
                                            'data-url' => _r('oleus.menus.link'),
                                            'data-value' => 'name'
                                        ],
                                        'required' => TRUE,
                                        'help' => trans('forms.help_link_composed_alias')
                                    ])
                                !!}
                            </div>
                            <div class="uk-width-1-2">
                                {!!
                                    field_render('menu_item.anchor', [
                                        'label'=> trans('forms.label_anchor'),
                                        'value' => $item->exists ? $item->anchor : NULL
                                    ])
                                !!}
                            </div>
                        </div>
                        @if($parents && count($parents) > 1)
                            <div class="uk-grid uk-grid-small">
                                <div class="uk-width-1-2">
                                    {!!
                                        field_render('menu_item.parent_id', [
                                            'type' => 'select',
                                            'label' => trans('forms.label_link_parent'),
                                            'selected' => $item->exists ? $item->parent_id : NULL,
                                            'values' => $parents,
                                            'class' => 'uk-select2'
                                        ])
                                    !!}
                                </div>
                                <div class="uk-width-1-2">
                                    {!!
                                        field_render('menu_item.sort', [
                                            'type' => 'select',
                                            'label' => trans('forms.label_sort'),
                                            'selected' => $item->exists ? $item->sort : 0,
                                            'values' => sort_field(),
                                            'class' => 'uk-select2'
                                        ])
                                    !!}
                                </div>
                            </div>
                        @endif
                        {!!
                            field_render('menu_item.status', [
                                'type' => 'checkbox',
                                'label' => trans('forms.label_visible_link'),
                                'selected' => $item->exists ? $item->status : 1
                            ])
                        !!}
                    </li>
                    <li>
                        @php($_data = $item->exists && $item->data ? unserialize($item->data) : NULL)
                        <div class="uk-grid uk-grid-small">
                            <div class="uk-width-1-3">
                                {!!
                                    field_render('menu_item.data.item_class', [
                                        'label' => '&lt;li class=\'...\'',
                                        'value' => $_data && isset($_data['item_class']) ? $_data['item_class'] : NULL,
                                    ])
                                !!}
                            </div>
                            <div class="uk-width-1-3">
                                {!!
                                    field_render('menu_item.data.id', [
                                        'label' => '&lt;a id=\'...\'',
                                        'value' => $_data && isset($_data['id']) ? $_data['id'] : NULL,
                                    ])
                                !!}
                            </div>
                            <div class="uk-width-1-3">
                                {!!
                                    field_render('menu_item.data.class', [
                                        'label' => '&lt;a class=\'...\'',
                                        'value' => $_data && isset($_data['class']) ? $_data['class'] : NULL,
                                    ])
                                !!}
                            </div>
                        </div>
                        <div class="uk-grid uk-grid-small">
                            <div class="uk-width-1-2">
                                {!!
                                    field_render('menu_item.data.prefix', [
                                        'label' => 'Prefix',
                                        'help' => trans('forms.help_prefix_code'),
                                        'value' => $_data && isset($_data['prefix']) ? $_data['prefix'] : NULL,
                                    ])
                                !!}
                            </div>
                            <div class="uk-width-1-2">
                                {!!
                                    field_render('menu_item.data.suffix', [
                                        'label' => 'Suffix',
                                        'help' => trans('forms.help_suffix_code'),
                                        'value' => $_data && isset($_data['suffix']) ? $_data['suffix'] : NULL,
                                    ])
                                !!}
                            </div>
                        </div>
                        {!!
                            field_render('menu_item.data.icon', [
                                'type' => 'file',
                                'label' => trans('forms.label_icon'),
                                'allow'  => 'jpg|jpeg|gif|png|ico|svg',
                                'values' => $_data && isset($_data['icon'])? [f_get($_data['icon'])] : NULL
                            ])
                        !!}
                    </li>
                    @if(USE_MULTI_LANGUAGE)
                        <li>
                            @php($_languages = config('os_languages.languages'))
                            @foreach($_languages as $_key_language => $_value_language)
                                @if($_key_language != DEFAULT_LANGUAGE)
                                    {!!
                                        field_render("menu_item.data.translate.{$_key_language}.name", [
                                            'label' => $_value_language['full_name'],
                                            'value' => $_data && isset($_data['translate'][$_key_language]['name']) ? $_data['translate'][$_key_language]['name'] : NULL,
                                            'attributes' => [
                                                'placeholder' => trans('forms.label_link_title')
                                            ]
                                        ])
                                    !!}
                                    {!!
                                        field_render("menu_item.data.translate.{$_key_language}.sub_name", [
                                            'value' => $_data && isset($_data['translate'][$_key_language]['sub_name']) ? $_data['translate'][$_key_language]['sub_name'] : NULL,
                                            'attributes' => [
                                                'placeholder' => trans('forms.label_link_sub_title')
                                            ]
                                        ])
                                    !!}
                                @endif
                            @endforeach
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
    <form action="{{ _r('oleus.menus.item', ['menu' => $menu, 'action' => 'destroy', 'id' => $item->id]) }}"
          id="form-delete-object"
          method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endif
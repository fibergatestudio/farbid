@if(!is_null($related_items['items']) && $related_items['items']->isNotEmpty())
    <table class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
        <thead>
            <tr>
                <th class="uk-width-xsmall uk-text-center"
                    uk-tooltip="title: {{ trans('forms.label_id') }}">
                    <span uk-icon="icon: ui_more_horiz"></span>
                </th>
                <th>@lang('forms.label_title_name')</th>
                @if(USE_SEVERAL_LOCATION)
                    <th class="uk-width-xsmall uk-text-center"
                        uk-tooltip="title: {{ trans('forms.label_related_location') }}">
                        <span uk-icon="icon: ui_room"></span>
                    </th>
                @endif
                @if(USE_MULTI_LANGUAGE)
                    <th class="uk-width-xsmall uk-text-center"
                        uk-tooltip="title: {{ trans('forms.label_related_language') }}">
                        <span uk-icon="icon: ui_language"></span>
                    </th>
                @endif
                @if($form->relation['view_link'])
                    <th class="uk-width-xsmall uk-text-center"
                        uk-tooltip="title: {{ trans('others.link_to_material') }}">
                        <span uk-icon="icon: ui_link"></span>
                    </th>
                @endif
                @if($form->relation['view_status'])
                    <th class="uk-width-xsmall uk-text-center"
                        uk-tooltip="title: {{ trans('forms.label_published') }}">
                        <span uk-icon="icon: ui_laptop"></span>
                    </th>
                @endif
                @can('update_pages')
                    <th class="uk-width-xsmall"></th>
                @endcan
            </tr>
        </thead>
        <tbody>
            @foreach($related_items['items'] as $_object)
                <tr>
                    <td class="uk-text-center uk-text-bold">{{ $_object->id }}</td>
                    <td>
                        @if(method_exists($_object, '_alias') && is_object($_object->_alias))
                            {!! _l($_object->title, $_object->_alias->alias, ['a' => ['target' => '_blank']], ['language' => $_object->language, 'location' => $_object->location]) !!}
                        @else
                            {!! str_limit($_object->title, 75) !!}
                        @endif
                    </td>
                    @if(USE_SEVERAL_LOCATION)
                        <td class="uk-text-center uk-text-small uk-text-primary uk-text-nowrap">
                            {{ $_object->location_city }}
                        </td>
                    @endif
                    @if(USE_MULTI_LANGUAGE)
                        <td class="uk-text-center uk-text-small uk-text-primary uk-text-nowrap">
                            {{ $_object->language_name }}
                        </td>
                    @endif
                    @if($form->relation['view_link'])
                        <td class="uk-text-center">
                            @if(method_exists($_object, '_alias') && is_object($_object->_alias))
                                {!! $_object->_alias ? _l('', $_object->_alias->alias, ['a' => ['uk-tooltip' => "title: {$_object->title}", 'target' => '_blank', 'uk-icon' => 'icon: ui_link', 'class' => 'uk-text-primary']], ['language' => $_object->language, 'location' => $_object->location]) : '-' !!}
                            @endif
                        </td>
                    @endif
                    @if($form->relation['view_status'])
                        <td class="uk-text-center">
                            @isset($_object->status)
                                {!! $_object->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
                            @else
                                -
                            @endisset
                        </td>
                    @endif
                    <td class="uk-text-center">
                        {!! _l('', "oleus.{$route}.edit", ['p' => ['id' => $_object->id], 'a' => ['class' => 'uk-text-primary',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
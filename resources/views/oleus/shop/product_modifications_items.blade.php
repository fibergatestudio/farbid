@if($modifications['items']->isNotEmpty())
    <table
        class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
        <thead>
            <tr>
                <th class="uk-width-xsmall"></th>
                <th class="uk-width-xsmall uk-text-center"
                    uk-tooltip="title: {{ trans('forms.label_id') }}">
                    <span uk-icon="icon: ui_more_horiz"></span>
                </th>
                <th>@lang('forms.label_name_product')</th>
                <th class="uk-width-small">@lang('forms.label_price')</th>
                <th class="uk-width-small">@lang('forms.label_count')</th>
                <th class="uk-width-xsmall uk-text-center"
                    uk-tooltip="title: {{ trans('forms.label_base') }}">
                    <span uk-icon="icon: ui_looks_one"></span>
                </th>
                <th class="uk-width-xsmall uk-text-center"
                    uk-tooltip="title: {{ trans('forms.label_visibility') }}">
                    <span uk-icon="icon: ui_laptop"></span>
                </th>
                <th class="uk-width-xsmall"></th>
                <th class="uk-width-xsmall"></th>
        </thead>
        <tbody>
            @foreach($modifications['items'] as $_modification)
                @php
                    $_modification_count = $_modification->out_of_stock ? '<span class="uk-text-warning uk-text-lowercase">'. trans('forms.label_out_of_stock').'</span>' : ($_modification->not_limited ? '<span class="uk-text-success uk-text-lowercase">'.trans('forms.label_limited').'</span>' : $_modification->count . ' ' . trans('others.units'));
                    $_modification_this = $_modification->id == $modifications['this']->id ? TRUE : FALSE;
                @endphp
                <tr>
                    <td>
                        @if($_modification_this)
                            <span class="uk-text-primary" uk-icon="icon: ui_arrow_forward"></span>
                        @endif
                    </td>
                    <td class="uk-text-center uk-text-bold">{{ $_modification->id }}</td>
                    <td>{!! _l($_modification->title, $_modification->_alias->alias, ['a' => ['target' => '_blank']], TRUE) !!}</td>
                    <td class="uk-text-right">{{ $_modification->price }}</td>
                    <td class="uk-text-right">{!! $_modification_count !!}</td>
                    <td>
                        @if($modifications['primary']->id == $_modification->id)
                            <span class="uk-text-success" uk-icon="icon: ui_done"></span>
                        @endif
                    </td>
                    <td class="uk-text-center">
                        {!! $_modification->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
                    </td>
                    <td class="uk-text-center">
                        @if(!$_modification_this)
                            {!! _l('', 'oleus.shop_products.edit', ['p' => ['id' => $_modification->id], 'a' => ['class' => 'uk-text-primary', 'uk-icon' => 'icon: ui_mode_edit', 'title' => trans('fields.button_edit')]]) !!}
                        @endif
                    </td>
                    <td class="uk-text-center">
                        @if(!$_modification_this)
                            {!! _l('', 'oleus.shop_products.modify_remove', ['p' => ['items' => $_modification->id], 'a' => ['class' => 'use-ajax uk-text-danger', 'uk-icon' => 'icon: ui_remove_circle', 'title' => trans('fields.button_edit')]]) !!}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div
        class="uk-alert uk-alert-warning uk-border-rounded uk-margin-small-top">@lang('others.item_list_is_empty')</div>
@endif
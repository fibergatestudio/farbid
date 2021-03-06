<table
    class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
    <thead>
        <tr>
            <th class="uk-width-xsmall uk-text-center"
                uk-tooltip="title: {{ trans('forms.label_id') }}">
                <span uk-icon="icon: ui_more_horiz"></span>
            </th>
            <th>@lang('forms.label_name')</th>
            <th class="uk-width-xsmall uk-text-center"
                uk-tooltip="title: {{ trans('forms.label_sort') }}">
                <span uk-icon="icon: ui_sort"></span>
            </th>
            <th class="uk-width-xsmall uk-text-center"
                uk-tooltip="title: {{ trans('forms.label_visibility') }}">
                <span uk-icon="icon: ui_laptop"></span>
            </th>
            <th class="uk-width-xsmall"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $_item)
            <tr>
                <td class="uk-text-center uk-text-bold">{{ $_item->id }}</td>
                <td>{{ $_item->title }}</td>
                <td class="uk-text-center">{{ $_item->sort }}</td>
                <td class="uk-text-center">
                    {!! $_item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
                </td>
                <td>
                    {!! _l('', 'oleus.sliders.item', ['p' => ['slider' => $_item->slider_id, 'action' => 'edit', 'id' => $_item->id], 'a' => ['class' => 'use-ajax uk-text-primary', 'uk-icon' => 'icon: ui_mode_edit', 'title' => __('fields.button_edit')]]) !!}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
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
    </thead>
    <tbody>
        @foreach($items as $_item)
            @include('oleus.menus.item', ['item' => $_item])
        @endforeach
    </tbody>
</table>
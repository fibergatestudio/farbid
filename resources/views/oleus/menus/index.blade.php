@extends('oleus.index')

@section('page')
    <article class="uk-article uk-margin-large-bottom">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove">
                {!! wrap()->get('page._title') !!}
            </h1>
        </div>
        <div
            class="uk-card uk-card-default uk-card-small uk-radius uk-radius-5 uk-margin-large-bottom uk-border-rounded">
            @can('create_menus')
                <div class="uk-card-header uk-text-right">
                    {!! _l(trans('forms.button_add'), 'oleus.menus.create', ['a' => ['class' => 'uk-button uk-button-secondary uk-waves uk-border-rounded']]) !!}
                </div>
            @endcan
            <div class="uk-card-body">
                @if($items->count())
                    <table class="uk-table uk-table-small uk-table-hover uk-table-middle">
                        <thead>
                            <tr>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_id') }}">
                                    <span uk-icon="icon: ui_more_horiz"></span>
                                </th>
                                <th class="uk-width-medium"
                                    uk-tooltip="title: {{ trans('forms.label_machine_name') }}">
                                    <span uk-icon="icon: ui_flag"></span>
                                </th>
                                <th>@lang('forms.label_name')</th>
                                {{--<th class="uk-width-small uk-text-center"--}}
                                    {{--uk-tooltip="title: {{ trans('forms.label_menu_location') }}">--}}
                                    {{--<span uk-icon="icon: ui_room"></span>--}}
                                {{--</th>--}}
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_menu_items_count') }}">
                                    <span uk-icon="icon: ui_menu"></span>
                                </th>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_visibility') }}">
                                    <span uk-icon="icon: ui_laptop"></span>
                                </th>
                                @can('update_menus')
                                    <th class="uk-width-xsmall"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td class="uk-text-center uk-text-bold">{{ $item->id }}</td>
                                    <td>{{ $item->key }}</td>
                                    <td>{{ $item->title }}</td>
                                    {{--<td>--}}
                                        {{--{{ $item->location_city }}--}}
                                    {{--</td>--}}
                                    <td class="uk-text-center uk-text-bold">
                                        {{ $item->_items->count() }}
                                    </td>
                                    <td class="uk-text-center">
                                        {!! $item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
                                    </td>
                                    @can('update_menus')
                                        <td class="uk-text-center">
                                            {!! _l('', 'oleus.menus.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-button-icon uk-button uk-button-primary uk-waves uk-border-rounded',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="uk-alert uk-alert-warning uk-border-rounded">
                        @lang('others.no_items')
                    </div>
                @endif
            </div>
        </div>
    </article>
@endsection
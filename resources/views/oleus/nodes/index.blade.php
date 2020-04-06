@php($percentage_of_relation = exists_relation())

@extends('oleus.index')

@section('page')
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove">
                {!! wrap()->get('page._title') !!}
            </h1>
        </div>
        <div class="uk-card uk-card-default uk-card-small uk-border-rounded uk-margin-large-bottom">
            @can('create_nodes')
                <div class="uk-card-header uk-text-right">
                    {!! _l(trans('forms.button_add'), 'oleus.nodes.create', ['a' => ['class' => 'uk-button uk-button-secondary uk-waves uk-border-rounded']]) !!}
                </div>
            @endcan
            <div class="uk-card-body">
                @if($items->count())
                    <table
                        class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
                        <thead>
                            <tr>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_id') }}">
                                    <span uk-icon="icon: ui_more_horiz"></span>
                                </th>
                                <th>@lang('forms.label_title')</th>
                                <th class="uk-width-medium">@lang('forms.label_type')</th>
                                @if($percentage_of_relation->count)
                                    <th class="uk-width-xsmall uk-text-center"
                                        uk-tooltip="title: {{ trans('others.percentage_of_relation') }}">
                                        <span uk-icon="icon: ui_all_inclusive"></span>
                                    </th>
                                @endif
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('others.link_to_material') }}">
                                    <span uk-icon="icon: ui_link"></span>
                                </th>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_published') }}">
                                    <span uk-icon="icon: ui_laptop"></span>
                                </th>
                                @can('update_nodes')
                                    <th class="uk-width-xsmall"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td class="uk-text-center uk-text-bold">{{ $item->id }}</td>
                                    <td>
                                        {!! _l($item->title, 'oleus.nodes.edit', ['p' => ['id' => $item->id]]) !!}
                                    </td>
                                    <td>
                                        {!! $item->type ? _l($item->type->title, 'oleus.pages.edit', ['p' => ['id' => $item->type->id], 'a' => ['target' => '_blank']]) : ' - ' !!}
                                    </td>
                                    @if($percentage_of_relation->count)
                                        <td class="uk-text-center uk-text-small uk-text-primary">
                                            {{ $item->percentage_of_relation }}%
                                        </td>
                                    @endif
                                    <td class="uk-text-center">{!! $item->_alias ? _l('', $item->_alias->alias, ['a' => ['uk-tooltip' => "title: {$item->title}", 'target' => '_blank', 'uk-icon' => 'icon: ui_link', 'class' => 'uk-text-primary']]) : '-' !!}</td>
                                    <td class="uk-text-center">
                                        {!! $item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
                                    </td>
                                    @can('update_nodes')
                                        <td class="uk-text-center">
                                            {!! _l('', 'oleus.nodes.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-text-primary',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="uk-clearfix">
                        {{ $items->links('oleus.base.pagination-default') }}
                    </div>
                @else
                    <div class="uk-alert uk-alert-warning uk-border-rounded">
                        @lang('others.no_items')
                    </div>
                @endif
            </div>
        </div>
    </article>
@endsection
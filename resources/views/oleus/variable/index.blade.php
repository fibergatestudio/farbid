@php($percentage_of_relation = exists_relation())

@extends('oleus.index')

@section('page')
    <article class="uk-article uk-margin-large-bottom">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove">
                {!! wrap()->get('page._title') !!}
            </h1>
        </div>
        <div class="uk-card uk-card-default uk-card-small uk-border-rounded">
            @can('create_variables')
                <div class="uk-card-header uk-text-right">
                    {!! _l(trans('forms.button_add'), 'oleus.variables.create', ['a' => ['class' => 'uk-button uk-button-secondary uk-waves uk-border-rounded']]) !!}
                </div>
            @endcan
            <div class="uk-card-body">
                @if($items->count())
                    <table
                        class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
                        <thead>
                            <tr>
                                <th class="uk-width-medium"
                                    uk-tooltip="title: {{ trans('forms.label_machine_name') }}">
                                    <span uk-icon="icon: ui_flag"></span>
                                </th>
                                <th>@lang('forms.label_name')</th>
                                @if($percentage_of_relation->count)
                                    <th class="uk-width-xsmall uk-text-center"
                                        uk-tooltip="title: {{ trans('others.percentage_of_relation') }}">
                                        <span uk-icon="icon: ui_all_inclusive"></span>
                                    </th>
                                @endif
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ strip_tags(trans('forms.label_do_code_2')) }}">
                                    <span uk-icon="icon: ui_code"></span>
                                </th>
                                @can('update_variables')
                                    <th class="uk-width-xsmall"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->key }}</td>
                                    <td>{{ $item->title }}</td>
                                    @if($percentage_of_relation->count)
                                        <td class="uk-text-center uk-text-small uk-text-primary">
                                            {{ $item->percentage_of_relation }}%
                                        </td>
                                    @endif
                                    <td class="uk-text-center">
                                        {!! $item->do ? '<span class="uk-text-success" uk-icon="icon: ui_done" uk-tooltip="title: '. trans('forms.label_unblocked') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_close" uk-tooltip="title: '. trans('forms.label_blocked') .'"></span>' !!}
                                    </td>
                                    @can('update_variables')
                                        <td class="uk-text-center">
                                            {!! _l('', 'oleus.variables.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-text-primary',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
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
                    <div class="uk-alert uk-alert-warning">
                        @lang('others.no_items')
                    </div>
                @endif
            </div>
        </div>
    </article>
@endsection
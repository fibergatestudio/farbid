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
            @can('create_shop_params')
                <div class="uk-card-header uk-text-right">
                    {!! _l(trans('forms.button_add'), 'oleus.shop_params.create', ['a' => ['class' => 'uk-button uk-button-secondary uk-waves uk-border-rounded']]) !!}
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
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_visible_params_in_filter') }}">
                                    <span uk-icon="icon: ui_filter_list"></span>
                                </th>
                                @can('update_shop_params')
                                    <th class="uk-width-xsmall"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td class="uk-text-center uk-text-bold">{{ $item->id }}</td>
                                    <td>{!! $item->title !!}</td>
                                    <td>@lang("forms.value_field_{$item->type}")</td>
                                    <td class="uk-text-center">
                                        {!! $item->visible_in_filter ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
                                    </td>
                                    @can('update_shop_params')
                                        <td class="uk-text-center">
                                            {!! _l('', 'oleus.shop_params.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-text-primary',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
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
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
            @can('create_shop_categories')
                <div class="uk-card-header uk-text-right">
                    {!! _l(trans('forms.button_add'), 'oleus.shop_categories.create', ['a' => ['class' => 'uk-button uk-button-secondary uk-waves uk-border-rounded']]) !!}
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
                                <th>@lang('forms.label_name')</th>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_sort') }}">
                                    <span uk-icon="icon: ui_sort"></span>
                                </th>
                                @if($percentage_of_relation->count)
                                    <th class="uk-width-xsmall uk-text-center"
                                        uk-tooltip="title: {{ trans('others.percentage_of_relation') }}">
                                        <span uk-icon="icon: ui_all_inclusive"></span>
                                    </th>
                                @endif
                                {{--<th class="uk-width-xsmall uk-text-center"--}}
                                    {{--uk-tooltip="title: {{ trans('others.link_to_material') }}">--}}
                                    {{--<span uk-icon="icon: ui_link"></span>--}}
                                {{--</th>--}}
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_visibility') }}">
                                    <span uk-icon="icon: ui_laptop"></span>
                                </th>
                                @can('update_shop_categories')
                                    <th class="uk-width-xsmall"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                @include('oleus.shop.index_category_item', compact($item))
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
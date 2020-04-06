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
            @can('create_shop_products')
                <div class="uk-card-header">
                    <div class="uk-grid uk-grid-collapse">
                        <div class="uk-width-expand">
                            <form action=""
                                  method="get">
                                <div class="uk-grid uk-grid-small">
                                    <div class="uk-width-medium">
                                        {!!
                                            field_render('title', [
                                                'value' => request()->get('title'),
                                                'attributes' => [
                                                    'placeholder' => 'Наименование товара'
                                                ]
                                            ])
                                        !!}
                                    </div>
                                    <div class="uk-width-large">
                                        {!!
                                            field_render('category', [
                                                'type' => 'select',
                                                'selected' => request()->get('category', 0),
                                                'values' => $_categories_all,
                                                'class' => 'uk-select2'
                                            ])
                                        !!}
                                    </div>
                                    <div class="uk-width-auto">
                                        <button type="submit"
                                                class="uk-button uk-button-primary uk-button-icon uk-border-rounded uk-margin-small-right"
                                                uk-icon="ui_sort"></button>
                                        <a href="{{ _r('oleus.shop_products') }}"
                                           class="uk-button uk-button-danger uk-button-icon uk-border-rounded"
                                           uk-icon="ui_close">
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div>
                            {!! _l(trans('forms.button_add'), 'oleus.shop_products.create', ['a' => ['class' => 'uk-button uk-button-secondary uk-waves uk-border-rounded']]) !!}
                        </div>
                    </div>
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
                            <th>@lang('forms.label_name_product')</th>
                            <th class="uk-width-medium">@lang('forms.label_sky')</th>
                            {{--@if($percentage_of_relation->count)--}}
                                {{--<th class="uk-width-xsmall uk-text-center"--}}
                                    {{--uk-tooltip="title: {{ trans('others.percentage_of_relation') }}">--}}
                                    {{--<span uk-icon="icon: ui_all_inclusive"></span>--}}
                                {{--</th>--}}
                            {{--@endif--}}
                            <th class="uk-width-xsmall uk-text-center"
                                uk-tooltip="title: {{ trans('others.link_to_material') }}">
                                <span uk-icon="icon: ui_link"></span>
                            </th>
                            <th class="uk-width-xsmall uk-text-center"
                                uk-tooltip="title: {{ trans('forms.label_published') }}">
                                <span uk-icon="icon: ui_laptop"></span>
                            </th>
                            @can('update_shop_products')
                                <th class="uk-width-xsmall"></th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td class="uk-text-center uk-text-bold">{{ $item->id }}</td>
                                <td>{!! _l($item->title, 'oleus.shop_products.edit', ['p' => ['id' => $item->id]]) !!}</td>
                                <td>{{ $item->sky }}</td>
                                {{--@if($percentage_of_relation->count)--}}
                                    {{--<td class="uk-text-center uk-text-small uk-text-primary">--}}
                                        {{--{{ $item->percentage_of_relation }}%--}}
                                    {{--</td>--}}
                                {{--@endif--}}
                                <td>
                                    {!! $item->_alias ? _l('', $item->_alias->alias, ['a' => ['uk-tooltip' => "title: {$item->title}", 'target' => '_blank', 'uk-icon' => 'icon: ui_link', 'class' => 'uk-text-primary']]) : '-' !!}
                                </td>
                                <td class="uk-text-center">
                                    {!! $item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
                                </td>
                                @can('update_shop_products')
                                    <td class="uk-text-center">
                                        {!! _l('', 'oleus.shop_products.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-text-primary',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
                                    </td>
                                @endcan
                            </tr>
                            @php($_modifications = $item->modifications)
                            @if(!is_null($_modifications))
                                @if($_modifications['items']->isNotEmpty())
                                    @foreach($_modifications['items'] as $_item)
                                        @if($_item->id != $_modifications['primary']->id)
                                            <tr>
                                                <td class="uk-text-center uk-text-bold">{{ $_item->id }}</td>
                                                <td>
                                                        <span uk-icon="icon : ui_subdirectory_arrow_right; ratio: .8"
                                                              class="uk-position-relative"
                                                              style="top: -3px;"></span>
                                                    {!! _l($_item->title, $_item->_alias->alias, ['a' => ['target' => '_blank']], TRUE) !!}
                                                </td>
                                                <td>{{ $_item->sky }}</td>
                                                @if($percentage_of_relation->count)
                                                    <td class="uk-text-center uk-text-small uk-text-primary">
                                                        {{ $_item->percentage_of_relation }}%
                                                    </td>
                                                @endif
                                                <td>
                                                    {!! $_item->_alias ? _l('', $_item->_alias->alias, ['a' => ['uk-tooltip' => "title: {$_item->title}", 'target' => '_blank', 'uk-icon' => 'icon: ui_link', 'class' => 'uk-text-primary']]) : '-' !!}
                                                </td>
                                                <td class="uk-text-center">
                                                    {!! $_item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
                                                </td>
                                                @can('update_shop_products')
                                                    <td class="uk-text-center">
                                                        {!! _l('', 'oleus.shop_products.edit', ['p' => ['id' => $_item->id], 'a' => ['class' => 'uk-text-primary',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
                                                    </td>
                                                @endcan
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    <div class="uk-clearfix">
                        {{ $items->appends(request()->all())->links('oleus.base.pagination-default') }}
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
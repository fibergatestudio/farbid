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
            <div class="uk-card-header">
                <form action=""
                      method="get">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-medium">
                            {!!
                                field_render('title', [
                                    'value' => request()->get('title'),
                                    'attributes' => [
                                        'placeholder' => 'Заголовок страницы'
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
                            {!!
                                field_render('language', [
                                    'type' => 'select',
                                    'selected' => request()->get('language', 'ru'),
                                    'values' => [
                                        'ru' => 'Русский язык',
                                        'uk' => 'Украинский язык'
                                    ],
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
                            <th>@lang('forms.label_category')</th>
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
                            <tr>
                                <td class="uk-text-center uk-text-bold">
                                    {{ $item->id }}
                                </td>
                                <td>
                                    {!! _l($item->title, 'oleus.shop_categories.edit', ['p' => ['id' => $item->id]]) !!}
                                </td>
                                <td>
                                    {!! _l($item->_category->title, 'oleus.shop_categories.edit', ['p' => ['id' => $item->_category->id], 'a' => ['class' => 'uk-text-primary', 'target' => '_blank']]) !!}
                                </td>
                                <td class="uk-text-center">
                                    {!! $item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
                                </td>
                                @can('update_shop_categories')
                                    <td class="uk-text-center">
                                        {!! _l('', 'oleus.shop_filter_pages.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-text-primary',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
                                    </td>
                                @endcan
                            </tr>
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
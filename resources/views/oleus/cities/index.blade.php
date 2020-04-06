@extends('oleus.index')

@section('page')
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom">
            <h1 class="uk-article-title uk-margin-remove">{!! $_page->page_title !!}</h1>
        </div>
        <div class="uk-card uk-card-default uk-card-small">
            <div class="uk-card-header uk-text-right">
                {!! _l(__('fields.button_add'), 'oleus.settings.city.create', ['a' => ['class' => 'uk-button uk-button-secondary uk-waves']]) !!}
            </div>
            <div class="uk-card-body">
                @if($items->count())
                    <table class="uk-table uk-table-small uk-table-hover uk-table-middle">
                        <thead>
                            <tr>
                                <th>{{ __('pages.city') }}</th>
                                <th class="uk-width-medium">{{ __('pages.address') }}</th>
                                <th class="uk-width-medium">{{ __('pages.manager') }}</th>
                                <th class="uk-width-xsmall uk-text-center">
                                    <span uk-icon="icon: ui_looks_one"></span>
                                </th>
                                <th class="uk-width-xsmall uk-text-center">
                                    <span uk-icon="icon: ui_laptop"></span>
                                </th>
                                <th class="uk-width-xsmall"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>
                                        {{ $item->city }}
                                    </td>
                                    <td>
                                        {{ $item->address }}
                                    </td>
                                    <td>
                                        {{ $item->_manager ? $item->_manager->fullName : ' - ' }}
                                    </td>
                                    <td class="uk-text-center">
                                        {!! $item->default ? '<span class="uk-text-success" uk-icon="icon: ui_check"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_close"></span>' !!}
                                    </td>
                                    <td class="uk-text-center">
                                        {!! $item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off"></span>' !!}
                                    </td>
                                    <td class="uk-text-center">
                                        {!! _l('', 'oleus.settings.city.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-button-icon uk-button uk-button-primary uk-waves',  'uk-icon' => 'icon: ui_mode_edit']]) !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @isset($items->links)
                        <div class="uk-clearfix">
                            {{ $items->links('oleus.base.pagination-default') }}
                        </div>
                    @endisset
                @else
                    <div class="uk-alert uk-alert-warning">
                        {{ __('notice.no_items') }}
                    </div>
                @endif
            </div>
        </div>
    </article>
@endsection
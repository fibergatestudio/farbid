@extends('oleus.index')

@section('page')
    <article class="uk-article uk-margin-large-bottom">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom">
            <h1 class="uk-article-title uk-margin-remove">{!! $_page->page_title !!}</h1>
        </div>
        <div class="uk-card uk-card-default uk-card-small">
            <div class="uk-card-header uk-text-right">
                {!! _l(__('fields.button_add'), 'oleus.discount.create', ['a' => ['class' => 'uk-button uk-button-secondary uk-waves']]) !!}
            </div>
            <div class="uk-card-body">
                @if($items->count())
                    <table class="uk-table uk-table-small uk-table-hover uk-table-middle">
                        <thead>
                            <tr>
                                <th>{{ __('pages.title') }}</th>
                                <th class="uk-width-medium">{{ __('pages.city') }}</th>
                                <th class="uk-width-xsmall uk-text-center">
                                    <span uk-icon="icon: ui_language"
                                          title="{{ __('pages.status') }}"></span>
                                </th>
                                <th class="uk-width-xsmall uk-text-center">
                                    <span uk-icon="icon: ui_laptop"
                                          title="{{ __('pages.status') }}"></span>
                                </th>
                                <th class="uk-width-xsmall"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>
                                        Слайд {{ $loop->index + 1 }}
                                    </td>
                                    <td>{{ $item->_city->city }}</td>
                                    <td class="uk-text-center">
                                        {!! $item->link ? '<span class="uk-text-success" uk-icon="icon: ui_check"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_close"></span>' !!}
                                    </td>
                                    <td class="uk-text-center">
                                        {!! $item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off"></span>' !!}
                                    </td>
                                    <td class="uk-text-center">
                                        {!! _l('', 'oleus.discount.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-button-icon uk-button uk-button-primary uk-waves',  'uk-icon' => 'icon: ui_mode_edit']]) !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="uk-clearfix">
                        {{ $items->links('oleus.base.pagination-default') }}
                    </div>
                @else
                    <div class="uk-alert uk-alert-warning">
                        {{ __('notice.no_items') }}
                    </div>
                @endif
            </div>
        </div>
    </article>
@endsection
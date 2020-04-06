@extends('oleus.index')

@section('page')
    <article class="uk-article uk-margin-large-bottom">
        <div class="uk-card uk-card-default uk-padding-small">
            <h1 class="uk-article-title uk-margin-remove">{!! $_page->page_title !!}</h1>
        </div>
        <div class="uk-card uk-card-default uk-card-small">
            @if($_page->user->role == 'admin')
                <div class="uk-card-header uk-text-right">
                    {!! _l(__('fields.button_add'), 'oleus.product.dishes.create', ['a' => ['class' => 'uk-button uk-button-secondary uk-waves']]) !!}
                </div>
            @endif
            <div class="uk-card-body">
                @if($items->count())
                    <table class="uk-table uk-table-small uk-table-hover uk-table-middle">
                        <thead>
                            <tr>
                                <th>{{ __('fields.field_name') }}</th>
                                <th class="uk-width-medium">{{ __('fields.field_category') }}</th>
                                <th class="uk-width-xsmall"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>
                                        {{ $item->name }}
                                    </td>
                                    <td>{{ $item->_category->title }}</td>
                                    <td class="uk-text-center">
                                        {!! _l('', 'oleus.product.dishes.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-button-icon uk-button uk-button-primary uk-waves',  'uk-icon' => 'icon: ui_mode_edit']]) !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="uk-clearfix">
                        {{ $items->links('oleus.base.pagination') }}
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
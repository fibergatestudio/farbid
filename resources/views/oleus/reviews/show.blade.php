@extends('oleus.index')

@section('page')
    <article class="uk-article uk-margin-large-bottom">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom">
            <h1 class="uk-article-title uk-margin-remove">{!! $_page->page_title !!}</h1>
        </div>
        <div class="uk-card uk-card-default uk-card-small">
            <div class="uk-card-header uk-text-right">
                {!! _l('', 'oleus.reviews', ['a' => ['class' => 'uk-button uk-button-default uk-waves uk-button-icon', 'uk-icon' => 'icon: ui_exit_to_app']]) !!}
            </div>
            <div class="uk-card-body">
                <dl class="uk-description-list">
                    <dt>@lang('pages.name_user')</dt>
                    <dd>{{ $item->name }}</dd>
                    <dt>@lang('pages.phone')</dt>
                    <dd>{{ $item->phone }}</dd>
                    <dt>E-mail</dt>
                    <dd>{{ $item->email }}</dd>
                    <dt>@lang('pages.review')</dt>
                    <dd>{{ $item->text }}</dd>
                </dl>
            </div>
        </div>
    </article>
@endsection
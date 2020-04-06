@php
    $_background = wrap()->get('page._background');
    $_background = $_background ? "style=\"background-image: url('{$_background}')\"" : '';
@endphp

@extends('front.index')

@section('page')
    <article class="uk-article page-type-{{ $item->type }} page-item page-item-{{ $item->id }}">
        <div class="page-background uk-margin-bottom"
            {!! $_background !!}>
            <div class="uk-container">
                <h1 class="uk-article-title uk-heading-divider page-title">{!! wrap()->get('page._title') !!}</h1>
                @if($item->sub_title)
                    <div class="uk-article-meta page-sub-title">{{ $item->sub_title }}</div>
                @endif
            </div>
        </div>
        @if($item->body)
            <div class="uk-container page-body uk-margin-bottom">
                {!! content_render($item) !!}
            </div>
        @endif
        @if($item->_prices)
            <div class="uk-container">
                @include('oleus.base.service_prices', ['prices' => $item->_prices])
            </div>
        @endif
    </article>
@endsection

@php
    $_background = wrap()->get('page._background');
    $_background = $_background ? "style=\"background-image: url('{$_background}')\"" : '';
@endphp

@extends('front.index')

@section('page')
    <article class="uk-article uk-margin-bottom page-type-{{ $item->type }} page-item page-item-{{ $item->id }}{{ $item->style_class ? " {$item->style_class}" : '' }}">
        <div class="page-bg page-type-{{ $item->type }}{{ $_background ? ' exist' : '' }}{{ $item->style_class ? " {$item->style_class}" : '' }}"
            {!! $_background !!}>
            <div class="uk-container">
                <h1 class="uk-article-title page-title">{!! wrap()->get('page._title') !!}</h1>
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
    </article>
@endsection

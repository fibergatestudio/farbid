@php
    $_background = wrap()->get('page._background');
    $_background = $_background ? "style=\"background-image: url('{$_background}')\"" : '';
@endphp

@extends('front.index')

@section('page')
    <article
        class="uk-article node-type-{{ $item->type->type }} node-item node-item-{{ $item->id }}{{ $item->style_class ? " {$item->style_class}" : '' }}">
        <div class="page-bg{{ $_background ? ' exist' : '' }}{{ $item->style_class ? " {$item->style_class}" : '' }}"
            {!! $_background !!}>
            <div class="uk-container">
                <h1 class="uk-article-title uk-heading-divider node-title">{!! wrap()->get('page._title') !!}</h1>
                @if($item->sub_title)
                    <div class="uk-article-meta page-sub-title">{{ $item->sub_title }}</div>
                @endif

            </div>
        </div>
        <div class="uk-container uk-margin-bottom">
            <p class="uk-text-meta node-published-date">
                {{ $item->published_at->format('d/m/Y') }}
            </p>
            <div class="node-body content">
                {!! content_render($item) !!}
            </div>
        </div>
    </article>
@endsection

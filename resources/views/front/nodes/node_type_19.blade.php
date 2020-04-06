@php
    $_wrap = wrap()->get();
    $_background = $_wrap['page']['_background'];
    $_back_link = request()->server('HTTP_REFERER');
@endphp

@extends('front.index')

@section('page')
    <article
        class="uk-article node-type-{{ $item->type->type }} node-item node-item-{{ $item->id }}{{ $item->style_class ? " {$item->style_class}" : '' }}{{ $_background ? ' page-background-exist' : '' }}"
        style="{{ $_background }}">
        <div class="other-page">
            <hr>
            <div class="uk-container uk-container-large">
            @include('oleus.base.breadcrumb')
                <div class="uk-text-center">
                    {!! $item['preview']['full_img'] !!}
                </div>
                <div class="uk-padding-small uk-padding-remove-horizontal">
                    <div class="post-date">
                        {{ $item->published_at->format('d.m.Y') }}
                    </div>
                </div>
                <div class="blog-body">
                    {!! $item->body !!}
                </div>
            </div>
        </div>
    </article>
@endsection

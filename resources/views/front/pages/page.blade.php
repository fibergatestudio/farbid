@php
    $_wrap = wrap()->get();
    $_page_title = $_wrap['page']['_title'];
    $_page_id = $_wrap['page']['_id'];
    $_page_class = $_wrap['page']['_class'];
    $_background = $_wrap['page']['_background'];
    $_back_link = request()->server('HTTP_REFERER');
@endphp

@extends('front.index')

@section('page')
    <article id="{{ $_page_id }}"
             class="uk-article page-type-{{ $item->type }} page-item page-item-{{ $item->id . ' ' . $_page_class . ' ' . ($_background ? 'page-background-exist' : '') }}"
             style="{{ $_background }}">
        <div class="other-page">
            <hr>
            <div class="uk-container uk-container-large">
                @include('oleus.base.breadcrumb')
                {{--<h1 class="uk-heading-bullet uk-text-uppercase">--}}
                    {{--<a href="{{ $_back_link }}">--}}
                        {{--{!! $_page_title !!}--}}
                    {{--</a>--}}
                {{--</h1>--}}
                @if($item->sub_title)
                    <h2 class="heading">
                        <span>{{ $item->sub_title }}</span>
                    </h2>
                @endif
                @if($item->body)
                    <div class="page-body">
                        {!! $item->body !!}
                    </div>
                @endif
            </div>
        </div>
    </article>
@endsection

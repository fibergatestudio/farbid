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
        <div class="other-page basket uk-position-relative">
            <hr>
            <div class="uk-container uk-container-large">
                @include('oleus.base.breadcrumb')
                {{--<h1 class="uk-heading-bullet uk-text-uppercase">--}}
                {{--<a href="{{ $_back_link }}">--}}
                {{--{!! $_page_title !!}--}}
                {{--</a>--}}
                {{--</h1>--}}
                <div class="checkout-content">
                    @include('front.shop.inside_basket', ['items' => $item->items])
                    @if($item->body)
                        <div class="page-body">
                            {!! $item->body !!}
                        </div>
                    @endif
                </div>
            </div>
            <div class="block-load uk-position-fixed">
                <div class="uk-position-center loading-img">
                    <img src="{{ formalize_path('template/img/loading.gif') }}"
                         alt="">
                </div>
            </div>
        </div>
    </article>
@endsection

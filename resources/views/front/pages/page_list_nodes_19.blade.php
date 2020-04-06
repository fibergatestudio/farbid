@php
    $_wrap = wrap()->get();
    $_page_title = $_wrap['page']['_title'];
    $_page_id = $_wrap['page']['_id'];
    $_page_class = $_wrap['page']['_class'];
    $_background = $_wrap['page']['_background'];
    $_page_number = $_wrap['seo']['_page_number'];
    $_back_link = request()->server('HTTP_REFERER');
@endphp

@extends('front.index')

@section('page')
    <article id="{{ $_page_id }}"
             class="uk-article page-type-{{ $item->type }} page-item page-item-{{ $item->id . ' ' . $_page_class . ' ' . ($_background ? 'page-background-exist' : '') }}"
             style="{{ $_background }}">
        <div class="other-page uk-position-relative">
            <hr>
            <div class="uk-container uk-container-large">
                @include('oleus.base.breadcrumb')
                {{--<h1 class="uk-heading-bullet uk-text-uppercase">--}}
                {{--<a href="{{ $_back_link }}">--}}
                {{--{!! $_page_title !!}--}}
                {{--</a>--}}
                {{--</h1>--}}
                @if($item->items->isNotEmpty())
                    <div id="list-items-page"
                         class="blog-listing">
                        <div class=""
                             uk-grid
                             uk-height-match="target: .blog-title; row: false">
                            @include('front.pages.page_list_nodes_19_items', ['items' => $item->items])
                        </div>
                    </div>
                @else
                    <div class="uk-alert uk-alert-warning uk-border-rounded uk-box-shadow-small">
                        <p>@lang('others.no_items')</p>
                    </div>
                @endif
                <div id="description-body-page">
                    @if($item->body && $_page_number == 1)
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

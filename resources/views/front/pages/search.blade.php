@php
    $_wrap = wrap()->get();
    $_page_title = $_wrap['page']['_title'];
    $_page_id = $_wrap['page']['_id'];
    $_page_class = $_wrap['page']['_class'];
    $_background = $_wrap['page']['_background'];
    $_page_number = $_wrap['seo']['_page_number'];
    $_language = $_wrap['locale'];
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
                @if($item->items->isNotEmpty())
                    <div id="list-items-page"
                         class="uk-grid-collapse uk-child-width-1-5@xl uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2 mark-category uk-flex-center"
                        uk-grid
                        uk-height-match="target: .title; row: false">
                        @include('front.pages.search_items', ['items' => $item->items, 'language' => $_language])
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
        </div>
    </article>
@endsection

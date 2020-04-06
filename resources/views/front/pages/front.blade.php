@php
    $_wrap = wrap()->get();
    $_contacts = $_wrap['contacts'];
    $_page_id = $_wrap['page']['_id'];
    $_page_class = $_wrap['page']['_class'];
    $_background = $_wrap['page']['_background'];
@endphp

@extends('front.index')

@section('page')
    <article id="{{ $_page_id }}"
             class="uk-article page-type-{{ $item->type }} page-item page-item-{{ $item->id . ' ' . $_page_class . ' ' . ($_background ? 'page-background-exist' : '') }}"
             style="{{ $_background }}">
        {!! slider_render(['entity' => 2, 'language' => $_wrap['locale']]) !!}
        @if($other['elected']->isNotEmpty())
            @include('front.shop.elected_product', ['items' => $other['elected']])
        @endif
        @if($other['hits']->isNotEmpty())
            @include('front.shop.hits_product', ['items' => $other['hits']])
        @endif
        @if($other['watched']->isNotEmpty())
            @include('front.shop.watched_product', ['items' => $other['watched']])
        @endif
        {{--<div class="groups-product uk-position-relative"--}}
             {{--style="{{ ($other['elected']->isNotEmpty() || $other['hits']->isNotEmpty() || $other['watched']->isNotEmpty() ? 'margin-top: 50px;' : '') }}">--}}
            {{--<div class="uk-container uk-container-large">--}}
                {{--<div class="item-description text-description-content"--}}
                     {{--id="short_text">--}}
                    {{--<h2 class="block-title uk-text-center uk-text-uppercase">--}}
                        {{--<span>@lang('forms.groups_goods')</span>--}}
                    {{--</h2>--}}
                    {{--{!! menu_render(['entity' => 'shop_catalog_group', 'theme' => 'front.menus.shop_catalog_menu_groups']) !!}--}}
                {{--</div>--}}
                {{--<a href="#"--}}
                   {{--rel="nofollow"--}}
                   {{--class="description-more-link uk-position-bottom-center uk-text-center uk-visible@m"--}}
                   {{--id="short_text_show_link">--}}
                    {{--@lang('forms.view_categories')--}}
                {{--</a>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div class="newsletter uk-margin-top">
            <div class="uk-container uk-container-small">
                <div class="newsletter-inner">
                    <div class="uk-flex uk-flex-middle uk-flex-center">
                        <div>
                            <div class="newsletter-title uk-text-right@m">
                                @if($_subscribe_newsletter = variable('subscribe_newsletter'))
                                    {!! $_subscribe_newsletter !!}
                                @endif
                            </div>
                        </div>
                        <div>
                            @include('front.forms.call_subscribe')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-seo">
            <div class="uk-container uk-container-large">
                <h1 class="uk-text-center">
                    {{ $item->title }}
                </h1>
                @if($item->sub_title)
                    <h3>
                        {{ $item->sub_title }}
                    </h3>
                @endif
                @if($item->body)
                    <div class="description">
                        {!! $item->body !!}
                    </div>
                @endif
                <hr>
            </div>
        </div>
        <? /* ?>
        <div id="contact-maps"
             class="gmap3"
             data-lon="{{ $_contacts['all']['dp']['offices']['office_1']['lon'] }}"
             data-lat="{{ $_contacts['all']['dp']['offices']['office_1']['lat'] }}">
        </div>
        <? */ ?>
    </article>
@endsection

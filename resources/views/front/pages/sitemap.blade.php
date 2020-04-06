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
                <ul id="sitemap-tree"
                    class="uk-list uk-margin-top uk-margin-bottom">
                    @foreach($item->items as $_item)
                        <li>
                            <a href="{{ $_item['url'] }}"
                               title="{{ $_item['name'] }}"
                               class="level-1">{{ $_item['name'] }}</a>
                            @if($_item['items'])
                                <ul class="uk-list">
                                    @foreach($_item['items'] as $_item_1)
                                        <li>
                                            <a href="{{ $_item_1['url'] }}"
                                               title="{{ $_item_1['name'] }}"
                                               class="level-2">{{ $_item_1['name'] }}</a>
                                            @if($_item_1['items'])
                                                <ul class="uk-list">
                                                    @foreach($_item_1['items'] as $_item_2)
                                                        <li>
                                                            <a href="{{ $_item_2['url'] }}"
                                                               title="{{ $_item_2['name'] }}"
                                                               class="level-2">{{ $_item_2['name'] }}</a>
                                                            @if($_item_2['items'])
                                                                <ul class="uk-list">
                                                                    @foreach($_item_2['items'] as $_item_3)
                                                                        <li>
                                                                            <a href="{{ $_item_3['url'] }}"
                                                                               title="{{ $_item_3['name'] }}"
                                                                               class="level-2">{{ $_item_3['name'] }}</a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
                @if($item->body)
                    <div class="page-body">
                        {!! $item->body !!}
                    </div>
                @endif
            </div>
        </div>
    </article>
@endsection

@php
    $_wrap = wrap()->get();
    $_contacts = $_wrap['contacts'];
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
                {{--<a href="{{ $_back_link }}"--}}
                {{--rel="nofollow">--}}
                {{--{!! $_page_title !!}--}}
                {{--</a>--}}
                {{--</h1>--}}
                <div class="uk-grid-collapse uk-child-width-1-3@m uk-child-width-1-1 contact-info uk-margin-bottom"
                     uk-grid>
                    <div>
                        <div class="city">
                            {{ $_contacts['current']['city'] }}
                        </div>
                        <div class="address">
                            {{ $_contacts['current']['address'] }}
                        </div>
                        <div class="work_time">
                            <div>
                                {{ $_contacts['current']['work_time_weekdays'] }}
                            </div>
                            {{ $_contacts['current']['work_time_saturday'] }},
                            {{ $_contacts['current']['work_time_sunday'] }}
                        </div>
                    </div>
                    <div class="call-phone">
                        <div class="call-us">{{ __('Звоните нам:') }}</div>
                        @if($_contacts['current']['phone_1'] || $_contacts['current']['phone_2'] || $_contacts['current']['phone_3'])
                            <div>
                                <a href="tel:{{ preg_replace('~\D+~', '', $_contacts['current']['phone_1']) }}">
                                    {!! format_phone_number($_contacts['current']['phone_1']) !!}
                                </a>
                            </div>
                            <div>
                                <a href="tel:{{ preg_replace('~\D+~', '', $_contacts['current']['phone_2']) }}">
                                    {!! format_phone_number($_contacts['current']['phone_2']) !!}
                                </a>
                            </div>
                            <div>
                                <a href="tel:{{ preg_replace('~\D+~', '', $_contacts['current']['phone_3']) }}">
                                    {!! format_phone_number($_contacts['current']['phone_3']) !!}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @if($item->body)
                    <div class="page-body">
                        {!! $item->body !!}
                    </div>
                @endif
            </div>
        </div>
        <div id="contact-maps"
             class="gmap3"
             data-lon="{{ $_contacts['all']['dp']['offices']['office_1']['lon'] }}"
             data-lat="{{ $_contacts['all']['dp']['offices']['office_1']['lat'] }}">
        </div>
    </article>
@endsection

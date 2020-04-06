@php
    $_wrap = wrap()->get();
    $_contacts = $_wrap['contacts'];
    $_logotype_first = $_wrap['site']['_logotype']['first'];
    $_logotype_last = $_wrap['site']['_logotype']['last'];
    $_logotype_next = $_wrap['site']['_logotype']['next'];
    $_logotype_modal = $_wrap['site']['_logotype']['modal'];
    $_search_history = $_wrap['search_history'];
    $_language = $_wrap['locale'];
    $_front_class = $_wrap['is_front'] ? 'front' : 'not-front';
    $_user = $_wrap['user'];
@endphp

@extends('html')

@section('body')
    <div class="uk-offcanvas-content">
        <div class="wrapper">
            <header class="{{ $_front_class }}">
                <div class="uk-container uk-container-large">
                    <div class="uk-flex uk-flex-wrap">
                        @if($_wrap['device'] == 'pc' || $_wrap['device'] == 'tablet')
                            @if($_logotype_first)
                                <div class="logo-type pc">
                                    @if($_wrap['is_front'])
                                        <span class="navbar-brand">
                                        {!! image_render($_logotype_first) !!}
                                    </span>
                                    @else
                                        <a href="{{ _u('/', [], TRUE) }}"
                                           class="navbar-brand">
                                            {!! image_render($_logotype_first) !!}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        @else
                        @endif
                        <div class="uk-flex-1 uk-flex uk-flex-column uk-flex-between">
                            <div class="uk-flex uk-flex-wrap uk-flex-between">
                                <div class="uk-position-relative icon-menu uk-hidden@l">
                                    <button class="uk-button icon-menu-mobile"
                                            type="button"
                                            rel="nofollow"
                                            uk-toggle="target: #offcanvas-catalog">
                                    </button>
                                </div>
                                <div class="btn-search uk-position-relative uk-position-z-index">
                                    <button class="uk-button uk-button-default uk-padding-remove btn-modal"
                                            type="button"
                                            rel="nofollow"
                                            data-id="toggle-search">
                                        <i class="icon-menu-search sprites-m"></i>
                                    </button>
                                </div>
                                @if($_logotype_next)
                                    <div class="logo-type mobile">
                                        @if($_wrap['is_front'])
                                            <span class="navbar-brand">
                                            {!! image_render($_logotype_next) !!}
                                            </span>
                                        @else
                                            <a href="{{ _u('/', [], TRUE) }}"
                                               title="{{ $_wrap['site']['_name'] }}"
                                               class="navbar-brand">
                                                {!! image_render($_logotype_next) !!}
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                <div class="uk-visible@l">
                                    {!! menu_render(['entity' => 'main_menu', 'theme' => 'front.menus.navbar_main_menu']) !!}
                                </div>
                                <div class="top-phone change uk-flex"
                                     id="toggle-phone">
                                    @if($_contacts['current']['phone_1'] || $_contacts['current']['phone_2'] || $_contacts['current']['phone_3'])
                                        <div class="block-icon-tel uk-flex uk-flex-middle">
                                            <i class="icon-tel sprites uk-display-block"></i>
                                        </div>
                                        <h4 class="uk-text-center">
                                            {{ __('Звоните нам:') }}
                                        </h4>
                                        <ul class="uk-navbar-nav">
                                            @php
                                                $_link_phone = preg_replace('~\D+~', '', $_contacts['current']['phone_1']);
                                            @endphp
                                            @if($_link_phone)
                                                <li>
                                                    <a href="tel:+{{ str_is('38*', $_link_phone) ? $_link_phone : "38{$_link_phone}" }}">
                                                        {!! format_phone_number($_contacts['current']['phone_1']) !!}
                                                    </a>
                                                </li>
                                            @endif
                                            @php
                                                $_link_phone = preg_replace('~\D+~', '', $_contacts['current']['phone_2']);
                                            @endphp
                                            @if($_link_phone)
                                                <li>
                                                    <a href="tel:+{{ str_is('38*', $_link_phone) ? $_link_phone : "38{$_link_phone}" }}">
                                                        {!! format_phone_number($_contacts['current']['phone_2']) !!}
                                                    </a>
                                                </li>
                                            @endif
                                            @php
                                                $_link_phone = preg_replace('~\D+~', '', $_contacts['current']['phone_3']);
                                            @endphp
                                            @if($_link_phone)
                                                <li>
                                                    <a href="tel:+{{ str_is('38*', $_link_phone) ? $_link_phone : "38{$_link_phone}" }}">
                                                        {!! format_phone_number($_contacts['current']['phone_3']) !!}
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    @endif
                                    <h4 class="uk-text-center">
                                        {{ __('или пишите нам:') }}
                                    </h4>
                                    <div class="messenger mobile uk-flex uk-flex-center uk-text-center">
                                        @if($_contacts['current']['telegram']))
                                        <a class="uk-display-block"
                                           rel="nofollow"
                                           href="{{$_contacts['current']['telegram']}}">
                                            <div
                                                class="icon telegram uk-flex uk-flex-center uk-flex-middle uk-margin-auto">
                                                <i class="icon-telegram sprites-m uk-display-block"></i>
                                            </div>
                                            <div class="name uk-text-uppercase">
                                                telegram
                                            </div>
                                        </a>
                                        @endif
                                        @if($_contacts['current']['viber'])
                                            <a class="uk-display-block"
                                               rel="nofollow"
                                               href="{{$_contacts['current']['viber']}}">
                                                <div
                                                    class="icon viber uk-flex uk-flex-center uk-flex-middle uk-margin-auto">
                                                    <i class="icon-viber sprites-m uk-display-block"></i>
                                                </div>
                                                <div class="name uk-text-uppercase">
                                                    viber
                                                </div>
                                            </a>
                                        @endif
                                        @if($_contacts['current']['whatsapp'])
                                            <a class="uk-display-block"
                                               rel="nofollow"
                                               href="{{$_contacts['current']['whatsapp']}}">
                                                <div
                                                    class="icon whatsapp uk-flex uk-flex-center uk-flex-middle uk-margin-auto">
                                                    <i class="icon-whatsapp sprites-m uk-display-block"></i>
                                                </div>
                                                <div class="name uk-text-uppercase">
                                                    whatsapp
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @if(isset($_language) && count($_wrap['languages']) > 1)
                                    <div class="language uk-flex uk-flex-middle uk-visible@m">
                                        <button class="uk-button uk-button-default"
                                                rel="nofollow"
                                                type="button">
                                            {{ $_language }}
                                        </button>
                                        <div uk-dropdown="mode: click">
                                            <ul class="uk-nav uk-dropdown-nav">
                                                @foreach($_wrap['languages'] as $_language_code => $_language_data)
                                                    @if($_language_code != $_language)
                                                        <li>
                                                            <button
                                                                data-path="{{ _r('language.selected', ['language' => $_language_code]) }}"
                                                                data-alias_id="{{ ($_wrap['alias']['id'] ?? ($_wrap['alias']->id ?? NULL)) }}"
                                                                type="button"
                                                                rel="nofollow"
                                                                class="uk-button data-alias-id use-ajax">
                                                                {{ $_language_code }}
                                                            </button>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                                @include('front.shop.basket.small_cart')
                                <div class="account">
                                    <button class="uk-button uk-button-default"
                                            rel="nofollow"
                                            uk-toggle="#modal-account">
                                    </button>
                                </div>
                                <div class="phone-mobile uk-position-relative">
                                    <button class="uk-button uk-button-default uk-padding-remove btn-modal"
                                            type="button"
                                            rel="nofollow"
                                            data-id="toggle-phone">
                                        <i class="icon-phone sprites-m uk-display-block"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="uk-flex uk-position-relative search-block">
                                <div class="our-catalog">
                                    <button class="uk-button btn-catalog pc uk-button-default"
                                            type="button"
                                            rel="nofollow"
                                            uk-toggle="target: #offcanvas-catalog">
                                        {{ __('Наш Каталог') }}
                                    </button>
                                    <div id="offcanvas-catalog"
                                         uk-offcanvas>
                                        <div class="uk-offcanvas-bar uk-background-default canvas-catalog">
                                            <button
                                                class="uk-offcanvas-close uk-position-absolute uk-position-top-right button-close uk-visible@l"
                                                type="button">
                                            </button>
                                            <nav class="uk-navbar-container uk-navbar-transparent uk-flex-column"
                                                 uk-navbar="dropbar: true;">
                                                <div
                                                    class="uk-flex uk-flex-center@m uk-flex-between uk-flex-middle top-modal-logo">
                                                    @if(isset($_language) && count($_wrap['languages']) > 1)
                                                        <div class="language uk-flex uk-flex-middle uk-hidden@m">
                                                            <button class="uk-button uk-button-default"
                                                                    rel="nofollow"
                                                                    type="button">
                                                                {{ $_language }}
                                                            </button>
                                                            <div uk-dropdown="mode: click">
                                                                <ul class="uk-nav uk-dropdown-nav">
                                                                    @foreach($_wrap['languages'] as $_language_code => $_language_data)
                                                                        @if($_language_code != $_language)
                                                                            <li>
                                                                                <button
                                                                                    data-path="{{ _r('language.selected', ['language' => $_language_code]) }}"
                                                                                    data-alias_id="{{ ($_wrap['alias']->id ?? NULL) }}"
                                                                                    type="button"
                                                                                    rel="nofollow"
                                                                                    class="uk-button data-alias-id use-ajax">
                                                                                    {{ $_language_code }}
                                                                                </button>
                                                                            </li>
                                                                        @endif
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="logo-type-modal uk-text-center">
                                                        @if($_logotype_modal)
                                                            @if($_wrap['is_front'])
                                                                <span class="navbar-brand">
                                                                  {!! image_render($_logotype_modal) !!}
                                                                </span>
                                                            @else
                                                                <a href="{{ _u('/', [], TRUE) }}"
                                                                   title="{{ $_wrap['site']['_name'] }}"
                                                                   class="navbar-brand">
                                                                    {!! image_render($_logotype_modal) !!}
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <button
                                                        class="uk-offcanvas-close uk-button-default btn-cat-close uk-hidden@l"
                                                        rel="nofollow"
                                                        type="button">
                                                        <i class="icon-btn-cat-close sprites-m uk-display-block"></i>
                                                    </button>
                                                </div>
                                                <div class="account-form">
                                                    <div id="account-mobile-box">
                                                        @if($_user)
                                                            <a href="{{ _u(($_language != DEFAULT_LANGUAGE) ? $_language . '/account' : 'account') }}"
                                                               class="uk-button uk-button-default uk-width-1-1 uk-margin-small-bottom">
                                                                @lang('forms.in_profile')
                                                            </a>
                                                            <form action="{{ _r('logout') }}"
                                                                  method="POST">
                                                                {{ method_field('POST') }}
                                                                {{ csrf_field() }}
                                                                <button type="submit"
                                                                        class="uk-button uk-button-danger uk-width-1-1">
                                                                    @lang('forms.exit_account')
                                                                </button>
                                                            </form>
                                                        @else
                                                            <div class="box-input">
                                                                @include('front.forms.account_mobile_login')
                                                            </div>
                                                            <button
                                                                class="uk-button uk-button-default user-account user-account-open uk-width-1-1">
                                                                <i class="icon-account sprites-m uk-display-inline-block"></i>
                                                                @lang('forms.log_your_account')
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="uk-hidden@l navbar-mobile">
                                                    {!! menu_render(['entity' => 'main_menu', 'theme' => 'front.menus.navbar_main_menu_mobile']) !!}
                                                </div>
                                                <div class="uk-navbar-left">
                                                    {!! menu_render(['entity' => 'shop_catalog_menu', 'theme' => 'front.menus.shop_catalog_menu']) !!}
                                                </div>
                                                {{--<div class="uk-navbar-left uk-hidden@m">--}}
                                                {{--{!! menu_render(['entity' => 'shop_catalog_menu', 'theme' => 'front.menus.shop_catalog_menu_categories']) !!}--}}
                                                {{--</div>--}}
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                                <div class="main-search uk-flex-1 change"
                                     id="toggle-search">
                                    <div class="header_search_toggle desktop-view">
                                        <div id="search-container"
                                             class="search-container">
                                            <div class="top-search">
                                                <form
                                                    action="{{ _u($_wrap['pages']['search']->_alias->alias, [], TRUE) }}"
                                                    method="GET">
                                                    <div class="search-box uk-position-relative">
                                                        <input type="text"
                                                               class="input-text uk-search-input ajax"
                                                               value="{{ request()->get('query_string') }}"
                                                               name="query_string"
                                                               data-path="{{ _r('ajax.search') }}"
                                                               autocomplete="off"
                                                               placeholder="@lang('others.search_by_products')">
                                                        <button type="submit"
                                                                title="@lang('others.search')"
                                                                class="search-btn uk-button uk-position-absolute uk-position-center-right">
                                                            @lang('forms.find')
                                                        </button>
                                                        <a href="#"
                                                           title="@lang('others.search_history')"
                                                           class="search-btn history-btn uk-button uk-position-absolute uk-position-center-right"
                                                           id="search-history-front">
                                                            <span class="uk-visible@s">
                                                                @lang('forms.search_history')
                                                            </span>
                                                            <span class="uk-hidden@s">
                                                                <img src="{{ formalize_path('template/img/history-icon-grey.png') }}"
                                                                  alt="">
                                                            </span>
                                                        </a>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="uk-search-results-items">
                                                <div class="result-list"></div>
                                                <div class="result-list-history">
                                                    <div class="uk-grid-collapse"
                                                         uk-grid>
                                                        <div class="uk-width-4-5@m">
                                                            @if($_search_history->isNotEmpty())
                                                                <div class="search-cat">
                                                                    @lang('forms.previously_looking'):
                                                                </div>
                                                                <ul>
                                                                    @foreach($_search_history as $_product)
                                                                        <li>
                                                                            @include('front.shop.search_item_product', ['item' => $_product, 'language' => $_language])
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <div
                                                                    class="nothin">
                                                                    @lang('forms.searched_anything')
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="uk-width-1-5@m uk-flex uk-flex-bottom">
                                                            <div class="search-history">
                                                                <button id="search-history-close"
                                                                        type="button"
                                                                        rel="nofollow"
                                                                        class="uk-button uk-button-default uk-width-1-1">
                                                                    @lang('forms.close_history')
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <div class="content">
                <div class="messenger pc uk-position-fixed uk-position-center-left uk-position-z-index uk-text-center">
                    @if($_contacts['current']['telegram'])
                        <a class="uk-display-block"
                           rel="nofollow"
                           href="{{$_contacts['current']['telegram']}}">
                            <div class="icon telegram uk-flex uk-flex-center uk-flex-middle uk-margin-auto">
                                <i class="icon-telegram sprites-m uk-display-block"></i>
                            </div>
                            <div class="name">
                                telegram
                            </div>
                        </a>
                    @endif
                    @if($_contacts['current']['viber'])
                        <a class="uk-display-block"
                           rel="nofollow"
                           href="{{$_contacts['current']['viber']}}">
                            <div class="icon viber uk-flex uk-flex-center uk-flex-middle uk-margin-auto">
                                <i class="icon-viber sprites-m uk-display-block"></i>
                            </div>
                            <div class="name">
                                viber
                            </div>
                        </a>
                    @endif
                    @if($_contacts['current']['whatsapp'])
                        <a class="uk-display-block"
                           rel="nofollow"
                           href="{{$_contacts['current']['whatsapp']}}">
                            <div class="icon whatsapp uk-flex uk-flex-center uk-flex-middle uk-margin-auto">
                                <i class="icon-whatsapp sprites-m uk-display-block"></i>
                            </div>
                            <div class="name">
                                whatsapp
                            </div>
                        </a>
                    @endif
                </div>
                @yield('page')
            </div>
            <div class="footer uk-padding uk-padding-remove-horizontal">
                <div class="uk-container uk-container-large">
                    <div class="footer-inner uk-grid-collapse"
                         uk-grid
                         uk-height-match="target: .item-f; row: false">
                        <div class="uk-width-1-5@m">
                            <div class="logo-type-footer uk-flex uk-flex-middle item-f">
                                @if($_logotype_first || $_logotype_last)
                                    @if($_wrap['is_front'])
                                        {!! image_render(($_logotype_last ? $_logotype_last : $_logotype_first)) !!}
                                    @else
                                        <a href="/">
                                            {!! image_render(($_logotype_last ? $_logotype_last : $_logotype_first)) !!}
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="uk-width-2-5@m">
                            <div class="item-f">
                                {!! menu_render(['entity' => 'main_menu', 'theme' => 'front.menus.navbar_main_menu_footer']) !!}
                                <div class="address-footer">
                                    {{ $_contacts['current']['city'] }}
                                    {{ $_contacts['current']['address'] }}
                                </div>
                                <div class="work-time-footer">
                                    {{ $_contacts['current']['work_time_weekdays'] }}
                                    {{ $_contacts['current']['work_time_saturday'] }}
                                    {{ $_contacts['current']['work_time_sunday'] }}
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-2-5@m uk-flex uk-flex-column uk-flex-between">
                            @if($_contacts['current']['phone_1'] || $_contacts['current']['phone_2'] || $_contacts['current']['phone_3'])
                                <ul class="uk-navbar-nav phone-f uk-flex-right@l">
                                    @php
                                        $_link_phone = preg_replace('~\D+~', '', $_contacts['current']['phone_1']);
                                    @endphp
                                    @if($_link_phone)
                                        <li>
                                            <a href="tel:+{{ str_is('38*', $_link_phone) ? $_link_phone : "38{$_link_phone}" }}">
                                                {!! format_phone_number($_contacts['current']['phone_1']) !!}
                                            </a>
                                        </li>
                                    @endif
                                    @php
                                        $_link_phone = preg_replace('~\D+~', '', $_contacts['current']['phone_2']);
                                    @endphp
                                    @if($_link_phone)
                                        <li>
                                            <a href="tel:+{{ str_is('38*', $_link_phone) ? $_link_phone : "38{$_link_phone}" }}">
                                                {!! format_phone_number($_contacts['current']['phone_2']) !!}
                                            </a>
                                        </li>
                                    @endif
                                    @php
                                        $_link_phone = preg_replace('~\D+~', '', $_contacts['current']['phone_3']);
                                    @endphp
                                    @if($_link_phone)
                                        <li>
                                            <a href="tel:+{{ str_is('38*', $_link_phone) ? $_link_phone : "38{$_link_phone}" }}">
                                                {!! format_phone_number($_contacts['current']['phone_3']) !!}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            @endif
                            <div class="box-oleus">
                                <a href="https://site-devel.com/"
                                   class="oleus-link"
                                   target="_blank">
                                    <div class="oleus">
                                        <img src="{{ formalize_path('template/img/oleus.png') }}"
                                             alt="OLEUS - cоздание сайтов для бизнеса">
                                        oleus
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('front.modals.account')
@endsection


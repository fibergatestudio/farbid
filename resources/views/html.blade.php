@php
    $_wrap = wrap()->get();
    if($_wrap['compress']) ob_start('clear_html');
@endphp
    <!DOCTYPE html>
<html lang="{{ $_wrap['seo']['_language'] }}">
<head>
    <base href="{{ $_wrap['base_url'] }}">
    <title>{{ "{$_wrap['seo']['_title']} {$_wrap['seo']['_title_suffix']}" }}</title>
    <meta name="description"
          content="{{ $_wrap['seo']['_description'] }}">
          @if($_wrap['seo']['_keywords'])
    <meta name="keywords"
          content="{{ $_wrap['seo']['_keywords'] }}">
@endif
@if($_wrap['seo']['_robots'])
    <meta name="robots"
          content="{{ $_wrap['seo']['_robots'] }}" />
          @endif
    <meta charset="utf-8">
    <meta http-equiv="Content-Type"
          content="text/html; charset=utf-8" />
    <meta name="url"
          content="{{ $_wrap['seo']['_url'] }}">
    <link rel="canonical"
          href="{{ $_wrap['seo']['_canonical'] }}" />
    <meta name="viewport"
          content="width=device-width,initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="theme-color"
          content="{{ $_wrap['seo']['_color'] }}">
    <meta name="copyright"
          content="{{ $_wrap['seo']['_copyright'] }}">
    <meta name="csrf-token"
          content="{{ $_wrap['token'] }}">
    <meta name="HandheldFriendly"
          content="True" />
    <meta name="SKYPE_TOOLBAR"
          content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
    <meta http-equiv="X-UA-Compatible"
          content="IE=edge" />
    <meta http-equiv="Last-Modified"
          content="{{ $_wrap['seo']['_last_modified'] }}">
    @if($_favicon = $_wrap['seo']['_favicon'])
        <link href="{{ image_render($_favicon, NULL, ['only_way' => TRUE]) }}"
              rel="shortcut icon"
              type="image/x-icon" />
    @endif
    @if($_link_prev = $_wrap['seo']['_link_prev'])
        <link rel="prev"
              href="{{ $_link_prev }}" />
    @endif
    @if($_link_next = $_wrap['seo']['_link_next'])
        <link rel="next"
              href="{{ $_link_next }}" />
    @endif
    @if($_wrap['variables']['seo']['analytics']['google'])
        <script async
                src="https://www.googletagmanager.com/gtag/js?id={{ $_wrap['variables']['seo']['analytics']['google'] }}"></script>
        <script src="/js/ecommerce.js"
                type="text/javascript"></script>
        <script type="text/javascript">
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ $_wrap["variables"]["seo"]["analytics"]["google"] }}');
            window.commerce = new eCommerce();
        </script>
    @endif
    @if($_wrap['variables']['seo']['analytics']['facebook'])
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $_wrap["variables"]["seo"]["analytics"]["facebook"] }}');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1"
                 width="1"
                 style="display:none"
                 src="https://www.facebook.com/tr?id={{ $_wrap['variables']['seo']['analytics']['facebook'] }}&ev=PageView&noscript=1"
            />
        </noscript>
    @endif
    <script type="text/javascript">
        window.Laravel = {!! $_wrap['page']['_js_settings'] !!};
    </script>
    @if(isset($_wrap['page']['_styles']['in_head']) && ($_link_styles_in_head = $_wrap['page']['_styles']['in_head']) && $_link_styles_in_head)
        {!! $_link_styles_in_head !!}
    @endif
    @if(isset($_wrap['page']['_scripts']['in_head']) && ($_link_scripts_in_head = $_wrap['page']['_scripts']['in_head']) && $_link_scripts_in_head)
        {!! $_link_scripts_in_head !!}
    @endif
</head>
<body class="{{ $_wrap['page']['_class'] }} homepage">
    @yield('body')
    @if(isset($_wrap['page']['_styles']['in_footer']) && ($_link_styles_in_footer = $_wrap['page']['_styles']['in_footer']) && $_link_styles_in_footer)
        {!! $_link_styles_in_footer !!}
    @endif
    @if(isset($_wrap['page']['_scripts']['in_footer']) && ($_link_scripts_in_footer = $_wrap['page']['_scripts']['in_footer']) && $_link_scripts_in_footer)
        {!! $_link_scripts_in_footer !!}
    @endif
    @stack('styles')
    @stack('scripts')
    @if($_wrap['dashboard'])
        @include('oleus.base.notice')
    @else
        @include('front.partials.notice')
    @endif
</body>
</html>
@php
    if($_wrap['compress']) ob_end_flush()
@endphp
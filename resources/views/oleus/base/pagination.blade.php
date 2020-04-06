@if($paginator->hasPages())
    @php
        $_current_page = $paginator->currentPage();
        $_current_url = preg_replace('/page-[0-9]+/i', '', request()->url());
        $_more_link = NULL;
        $_next_page = $_current_page + 1;
        $_prev_page = ($_prev = $paginator->currentPage() - 1) && $_prev > 0 ? $_prev : 1;
        $_query_string = NULL;
        if($_query_array = request()->query()) {
            unset($_query_array['page']);
            if(count($_query_array)){
                foreach ($_query_array as $_query_key => $_query_value) {
                    if($_query_value && is_array($_query_value)){
                        foreach($_query_value as $_query_key_data => $_query_data_value) {
                            if(is_string($_query_key_data)){
                                $_query_string[] = "{$_query_key}[{$_query_key_data}]={$_query_data_value}";
                            }else{
                                $_query_string[] = "{$_query_key}[]={$_query_data_value}";
                            }
                        }
                    }elseif($_query_value){
                        $_query_string[] = "{$_query_key}={$_query_value}";
                    }
                }
                $_query_string = $_query_string ? '?'. implode('&', $_query_string) : '';
            }
        }
        $countMoreNode = ($paginator->lastPage() - $paginator->currentPage()) > 1 ? $paginator->perPage() : $paginator->total() - ($paginator->perPage() * $paginator->currentPage());
        if($_current_page < $paginator->lastPage()){
            $url = trim($_current_url, '/') . "/page-{$_next_page}";
            $_next_page_link =
            $_more_link = _u($url) . $_query_string;
        }
        if($_current_page > 1){
            $url = trim($_current_url, '/') . "/page-{$_prev_page}";
            $_prev_page_link = _u($url) . $_query_string;
        }
    @endphp
    <ul class="uk-pagination uk-flex-center">
        @if ($paginator->onFirstPage())
        @else
        @endif
        @foreach ($elements as $element)
            @if (is_string($element))
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @php
                        $url = $page > 1 ? trim($_current_url, '/') . "/page-{$page}" : $_current_url;
                        $url = _u($url) . $_query_string;
                    @endphp
                    @if ($page == $_current_page)
                        <li class="uk-active">
                            <span>{{ $page }}</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
                {{--@foreach ($element as $page => $url)--}}
                {{--@if ($page == $paginator->currentPage())--}}
                {{--<li class="uk-active">--}}
                {{--<span>{{ $page }}</span>--}}
                {{--</li>--}}
                {{--@else--}}
                {{--@php--}}
                {{--$parseUrl = parse_url($url, PHP_URL_QUERY);--}}
                {{--parse_str($parseUrl, $variables);--}}
                {{--@endphp--}}
                {{--<li>--}}
                {{--@if(isset($variables['page']) && $variables['page'] == 1)--}}
                {{--<a href="{{ _u($_current_url) }}">{{ $page }}</a>--}}
                {{--@else--}}
                {{--<a href="{{ $url }}">{{ $page }}</a>--}}
                {{--@endif--}}
                {{--</li>--}}
                {{--@endif--}}
                {{--@endforeach--}}
            @endif
        @endforeach
        @if($paginator->hasMorePages())
        @else
        @endif
    </ul>
@endif

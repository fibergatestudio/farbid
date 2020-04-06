@if($paginator->hasPages())
    @php
        $_current_page = $paginator->currentPage();
        $_current_page_url = isset($page_alias) && $page_alias ? "/{$page_alias}" : request()->url();
        $_current_url = preg_replace('/page-[0-9]+/i', '', $_current_page_url);
        $_next_page = $_current_page + 1;
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
        if($paginator->hasMorePages()){
            $url = trim($_current_url, '/') . "/page-{$_next_page}";
            $_next_page_link = _u($url) . $_query_string;
        }
    @endphp
    @if($paginator->hasMorePages())
        <a href="{{ $_next_page_link }}"
           id="catalog-more-load"
           data-more_load="1"
           data-not_ajax_load="1"
           class="use-ajax"
           data-view_load="0">
            <div class="show-more">
                <div class="show">
                    {!! __('Показать<br>еще') !!}
                </div>
                <div class="more">
                    {{ $paginator->perPage() * $paginator->currentPage() }}
                    {{ __('из') }}
                    {{ $paginator->total() }}
                </div>
            </div>
            <div class="title">
                <i class="icon-arrow-more sprites uk-display-block"></i>
            </div>
        </a>
    @else
        <div>
            <div class="show-more">
                <div class="show">
                    {{ __('Показано') }}
                </div>
                <div class="more">
                    {{ $paginator->total() }}
                    {{ __('из') }}
                    {{ $paginator->total() }}
                </div>
            </div>
            <div class="title">
            </div>
        </div>
    @endif
    <ul class="uk-pagination uk-flex-center">
        @if ($paginator->onFirstPage())
        @else
        @endif
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="uk-disabled">
                    <span>{{ $element }}</span>
                </li>
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
                            <a href="{{ $url }}"
                               data-view_load="0"
                               class="use-ajax">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach
        @if($paginator->hasMorePages())
        @else
            <li class="uk-disabled">
                <span><i class="fa fa-angle-right"></i></span>
            </li>
        @endif
    </ul>
@endif

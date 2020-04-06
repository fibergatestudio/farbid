@if ($paginator->hasPages())
    <ul class="uk-pagination uk-flex-center">
        @if ($paginator->onFirstPage())
            <li class="uk-disabled">
                <span uk-pagination-previous></span>
            </li>
        @else
            <li>
                <a href="{{ $paginator->previousPageUrl() }}"
                   rel="prev"
                   uk-pagination-previous></a>
            </li>
        @endif
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="uk-disabled"><span>{{ $element }}</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="uk-active"><span style="width: 21px;border-radius: 50%;background-color: #ffde02;text-align: center;color: #000">{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}"
                   rel="next"
                   uk-pagination-next></a>
            </li>
        @else
            <li class="uk-disabled">
                <span uk-pagination-next></span>
            </li>
        @endif
    </ul>
@endif

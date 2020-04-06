@if($_breadcrumb = wrap()->get('breadcrumb'))
    <ul class="uk-breadcrumb">
        @foreach($_breadcrumb as $_item)
            @if($_item['url'])
                <li>
                    {!! _l($_item['name'], $_item['url'], ['a' => ['title' => $_item['name']]]) !!}
                </li>
            @else
                <li class="active">
                    <span>{!! $_item['name'] !!}</span>
                </li>
            @endif
        @endforeach
    </ul>
@endif
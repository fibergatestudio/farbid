@php
    $_active_path = active_path($_item['item']['path']) ? 'active' : '';
    $item['list_item']['class'][] = $_active_path;
@endphp
<li class="{{ render_attributes($item['list_item']['class']) }}">
    {!!
        _l($item['item']['title'], $item['item']['path'], [
            'a'           => [
                'class'          => $item['item']['class'],
                'id'             => $item['item']['id'],
                'rel'            => 'nofollow',
            ],
            'anchor'      => $item['item']['anchor'],
            'prefix'      => $item['item']['icon'].$item['item']['prefix'],
            'suffix'      => $item['item']['suffix'],
            'description' => $item['item']['description']
        ]);
    !!}
    @if($item['children'])
        <ul class="uk-nav uk-nav-sub">
            @foreach($item['children'] as $child)
                @include('oleus.base.menu_footer_item', ['item' => $child])
            @endforeach
        </ul>
    @endif
</li>
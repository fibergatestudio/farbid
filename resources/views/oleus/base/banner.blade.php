@php
    $_link = NULL;
    $_target_blank = FALSE;
    $_banner = $item->_banner;
    if($item->alias_id){
        $_link = _u(UrlAlias::where('id', $item->alias_id)->value('alias'));
    }elseif($item->link){
        $_link = $item->link;
        $_target_blank = TRUE;
    }
@endphp
<div class="uk-banner banner-{{ $item->id }}">
    @if($_link)
        <a href="{{ $_link }}"
           title="{{ $_banner->title }}"
            {{ $_target_blank ? 'target="_blank"' : '' }}>
            <img src="{{ image_render($_banner, $item->preset, ['only_way' => true, 'no_last_modify' => true]) }}"
                 alt="{{ $_banner->alt }}">
        </a>
    @else
        <img src="{{ image_render($_banner, $item->preset) }}"
             alt="{{ $_banner->alt }}">
    @endif
</div>
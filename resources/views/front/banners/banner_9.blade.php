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
<div class="banner-{{ $item->id }}">
    @if($_link)
        <a href="{{ $_link }}"
           title="{{ $_banner->title }}"
            {{ $_target_blank ? 'target="_blank"' : '' }}>
            <div class="sub-banner sub-banner2">
            <img src="{{ image_render($_banner, $item->preset, ['only_way' => TRUE]) }}"
                 alt="{{ $_banner->alt }}">
            </div>
        </a>
    @else
        <div class="sub-banner sub-banner2">
        <img src="{{ image_render($_banner, $item->preset, ['only_way' => TRUE]) }}"
             alt="{{ $_banner->alt }}">
        </div>
    @endif
</div>
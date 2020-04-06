@php
    $_background = $item->background ? image_render($item->background) : NULL;
    $_background = $_background ? "style=\"background-image: url('{$_background}')\"" : '';
@endphp

<div class="uk-clearfix uk-advantages{{ $item->style_class ? " {$item->style_class}" : NULL }}"
     {!! $_background !!}
     id="{{ $item->style_id ? $item->style_id : NULL }}">
    @if(!$item->hidden_title)
        <div class="uk-advantages-heading uk-text-center">
            <h2 class="uk-h2 uk-text-uppercase advantages-title">
                {!! $item->title !!}
            </h2>
            @if($item->sub_title)
                <div class="uk-text-muted uk-text-uppercase advantages-subtitle">
                    {!! $item->sub_title !!}
                </div>
            @endif
        </div>
    @endif
    @if($item->position == 'under' && ($_body = content_render($item)))
        <div class="uk-advantages-body">
            {!! $_body !!}
        </div>
    @endif
    @if($item->_items)
        <div class="uk-advantages-items">
            <div class="uk-grid  uk-grid-match uk-grid-collapse uk-flex-center uk-text-center">
                @foreach($item->_items as $_item)
                    <div class="uk-text-center uk-width-1-4@l item item-{{ $_item->id }}">
                        @if($_item->_icon)
                            <div class="item-img">
                                {!! image_render($_item->_icon) !!}
                            </div>
                        @endif
                        @if(!$_item->hidden_title)
                            <h3 class="item-title">
                                {{ $_item->title }}
                            </h3>
                            @if($_item->sub_title)
                                <div class="item-sub-title">
                                    {{ $_item->sub_title }}
                                </div>
                            @endif
                        @endif
                        @if($_item->body )
                            <div class="item-description">
                                {!! $_item->body !!}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @if($item->position == 'above' && ($_body = content_render($item)))
        <div class="uk-advantages-body">
            {!! $_body !!}
        </div>
    @endif
</div>
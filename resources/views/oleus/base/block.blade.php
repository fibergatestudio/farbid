@php
    $_background = $item->background ? image_render($item->background) : NULL;
    $_background = $_background ? "style=\"background-image: url('{$_background}')\"" : '';
@endphp

<div class="uk-clearfix uk-block{{ $item->style_class ? " {$item->style_class}" : NULL }}"
     {!! $_background !!}
     id="{{ $item->style_id ? $item->style_id : NULL }}">
    @if($item->hidden_title == 0)
        <div class="uk-block-heading">
            <h2 class="uk-h2 uk-text-uppercase block-title">
                {{ $item->title }}
            </h2>
            @if($item->sub_title)
                <div class="uk-text-muted uk-text-uppercase block-subtitle">
                    {{ $item->sub_title }}
                </div>
            @endif
        </div>
    @endif
    <div class="uk-block-body">
        {!! content_render($item) !!}
    </div>
</div>
@php
    $_background = $item->background ? image_render($item->background) : NULL;
    $_background = $_background ? "style=\"background-image: url('{$_background}')\"" : '';
@endphp

<div class="brand-logo uk-block{{ $item->style_class ? " {$item->style_class}" : NULL }}"
     {!! $_background !!}
     id="{{ $item->style_id ? $item->style_id : NULL }}">
    <div class="container">
    @if($item->hidden_title == 0)
            <div class="row">
                <div class="col-12 ">
                    <div class="heading-part mb-30 mb-xs-15">
            <h2 class="main_title heading">
                <span>
                {{ $item->title }}
                </span>
            </h2>
            @if($item->sub_title)
                <div class="block-subtitle">
                    {{ $item->sub_title }}
                </div>
            @endif
        </div>
                </div>
            </div>
    @endif
        <div class="row brand">
            <div class="col-md-12">
        {!! content_render($item) !!}
            </div>
    </div>
    </div>
</div>
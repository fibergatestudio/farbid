<div class="uk-clearfix uk-sliders{{ $item->style_class ? " {$item->style_class}" : NULL }}"
     id="{{ $item->style_id ? $item->style_id : NULL }}"
     uk-slider>
    <div class="uk-position-relative uk-visible-toggle">
        <ul class="uk-slider-items">
            @foreach($item->items as $_slide_item)
                <li class="uk-width-1-1">
                    <div class="uk-position-relative">
                        <img src="{{ $_slide_item->_background_asset(NULL, TRUE, TRUE) }}"
                             alt="{!! $_slide_item->title !!}"
                             class="uk-position-absolute"
                             uk-cover>
                        <div class="uk-container uk-position-relative">
                            @if(!$_slide_item->hidden_title)
                                <div class="uk-slider-heading uk-text-center">
                                    <h2 class="uk-h2 uk-text-uppercase slide-title">
                                        {!! $_slide_item->title !!}
                                    </h2>
                                    @if($_slide_item->sub_title)
                                        <div class="uk-text-muted uk-text-uppercase slide-subtitle">
                                            {!! $_slide_item->sub_title !!}
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @if($_slide_item->body )
                                <div class="uk-slider-description">
                                    {!! $_slide_item->body !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    <ul class="uk-slider-nav uk-dotnav"></ul>
</div>
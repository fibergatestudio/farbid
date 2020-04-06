<div class="box-slider uk-position-relative">
    <div class="uk-clearfix uk-sliders{{ $item->style_class ? " {$item->style_class}" : NULL }}"
         id="{{ $item->style_id ? $item->style_id : NULL }}"
         uk-slideshow="animation: fade;min-height: 400; max-height: 530">
        <div class="uk-position-relative uk-visible-toggle uk-light">
            <ul class="uk-slideshow-items">
                @foreach($item->items as $_slide_item)
                    <li>
                        <img src="{{ $_slide_item->_background_asset($item->preset, ['only_way' => TRUE]) }}"
                             alt="{!! $_slide_item->title !!}"
                             uk-cover>
                        @if(!$_slide_item->hidden_title)
                            <div class="uk-position-center uk-position-small uk-text-center slider-content">
                                <h2 class="slider-title uk-text-uppercase">
                                    {!! $_slide_item->title !!}
                                </h2>
                                @if($_slide_item->sub_title)
                                    <div class="slide-subtitle">
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
                    </li>
                @endforeach
            </ul>
            <a class="uk-position-center-left uk-position-small link-slider-prev uk-visible@m"
               href="#"
               uk-slideshow-item="previous">
                <i class="icon-slider-prev sprites uk-display-block"></i>
            </a>
            <a class="uk-position-center-right uk-position-small link-slider-next uk-visible@m"
               href="#"
               uk-slideshow-item="next">
                <i class="icon-slider-next sprites uk-display-block"></i>
            </a>
        </div>
        <ul class="uk-slideshow-nav uk-dotnav uk-position-small uk-position-bottom-center uk-overlay uk-hidden@m"></ul>
    </div>
    <noindex>
        <button
            class="uk-button btn-catalog mobile uk-button-default uk-position-absolute uk-position-bottom-center uk-margin-small uk-position-z-index"
            type="button"
            uk-toggle="target: #offcanvas-catalog">КАТАЛОГ товаров
        </button>
    </noindex>
</div>



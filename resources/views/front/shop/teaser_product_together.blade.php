<div>
    <div class="teaser-cart uk-position-relative">
        <a href="{{ _u($item->_alias->alias) }}"
           title="{{ $item->title }}">
            <div class="preview uk-flex uk-flex-center uk-flex-middle">
                {!! $item->_preview_asset('thumb_shop_product_200', ['attributes' => ['alt' => $item->title, 'class' => 'uk-visible@s'], 'only_way' => FALSE]) !!}
                {!! $item->_preview_asset('thumb_shop_product_100', ['attributes' => ['alt' => $item->title, 'class' => 'uk-hidden@s'], 'only_way' => FALSE]) !!}
                @if($item->modification_links && isset($item->modification_links['view']['data'][44]))
                    <div class="box-color">
                        <div class="uk-position-cover">
                            @if(count($item->modification_links['view']['data'][44]) > 1)
                                @foreach($item->modification_links['view']['data'][44] as $_color)
                                    @if($_color['found'])
                                        @if($_color['found']->current)
                                            <div
                                                class="active item-color"
                                                title="{{ $_color['name'] }}"
                                                style="background-color: {!! $_color['style']['class'] !!}">
                                            </div>
                                        @else
                                            <div
                                                class="item-color"
                                                title="{{ $_color['name'] }}"
                                                style="background-color: {!! $_color['style']['class'] !!}">
                                            </div>
                                        @endif
                                        @if($_color['found']->current)
                                            <div class="line-border uk-position-cover"
                                                 style="border-color: {!! $_color['style']['class'] !!}">
                                                <div class="line-background"
                                                     style="background-color: {!! $_color['style']['class'] !!}">
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            @else
                                <?
                                $_colors = $item->modification_links['view']['data'][44];
                                $_color = array_shift($_colors);
                                ?>
                                <div class="line-border uk-position-cover"
                                     style="border-color: {!! $_color['style']['class'] !!}">
                                    <div class="line-background"
                                         style="background-color: {!! $_color['style']['class'] !!}">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    @isset($item->params_product['color'])
                        @if($item->params_product['color']->selected)
                            <?
                            $_color_style = null;
                            $_color_name = null;
                            foreach($item->params_product['color']->selected as $_option_id) {
                                if($language == DEFAULT_LANGUAGE) {
                                    $_color_name = $item->params_product['color']->data->get($_option_id);
                                } else {
                                    $_translate = $item->params_product['color']->translate_data->get($_option_id);
                                    $_color_name = isset($_translate[$language]) && $_translate[$language] ? $_translate[$language] : $item->params_product['color']->data->get($_option_id);
                                }
                                $_color_style = $item->params_product['color']->style_data->get($_option_id);
                            }
                            ?>
                            <div class="line-border uk-position-cover"
                                 style="border-color: {!! $_color_style['style_class'] !!}">
                                <div class="line-background"
                                     style="background-color: {!! $_color_style['style_class'] !!}">
                                </div>
                            </div>
                        @endif
                    @endisset
                @endif
            </div>
        </a>
        <div class="content-cart">
            <a href="{{ _u($item->_alias->alias) }}"
               title="{{ $item->title }}"
               uk-tooltip>
                <div class="title">
                    {{ $item->title }}
                </div>
            </a>
			<!--
            @isset($item->params_product['manufacturer'])
                @if($item->params_product['manufacturer']->selected)
                    <?
                    if($language == DEFAULT_LANGUAGE) {
                        $_param_label = $item->params_product['manufacturer']->title;
                    } else {
                        $_param_label = $item->params_product['manufacturer']->translate[$language];
                    }
                    $_param_values = null;
                    foreach($item->params_product['manufacturer']->selected as $_option_id) {
                        if($language == DEFAULT_LANGUAGE) {
                            $_param_values[] = $item->params_product['manufacturer']->data->get($_option_id);
                        } else {
                            $_translate = $item->params_product['manufacturer']->translate_data->get($_option_id);
                            $_param_values[] = isset($_translate[$language]) && $_translate[$language] ? $_translate[$language] : $item->params_product['manufacturer']->data->get($_option_id);
                        }
                    }
                    ?>
                    @if($_param_values)
                        {!! "<div style='color:#000;'>{$_param_label}: " . implode(', ', $_param_values) .'</div>' !!}
                    @endif
                @endif
            @endisset
			-->
            @if($item->prices_product['availability'])
                <div class="available">
                    @lang('shop.product_available')
                </div>
                <div class="uk-grid-collapse box-price"
                     uk-grid>
                    <div class="uk-width-2-3 uk-flex uk-flex-bottom">
                        <div class="uk-flex uk-flex-column">
                            <div class="old-price">
                                <del>
                                    {{ $item->prices_product['price']['format']['view_price'] }}
                                </del>
                            </div>
                            <div class="price">
                                @if($item->discount_price_product['price']['currency']['prefix'])
                                    <span>
                                        {{ $item->discount_price_product['price']['currency']['prefix'] }}
                                    </span>
                                @endif
                                {{ $item->discount_price_product['price']['format']['view_price'] }}
                                @if($item->discount_price_product['price']['currency']['suffix'])
                                    <span>
                                        {{ $item->discount_price_product['price']['currency']['suffix'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-1-3 uk-text-right">&nbsp;</div>
                </div>
            @else
                <div class="available not">
                    @lang('shop.product_not_available')
                </div>
                <div class="box-price">&nbsp;</div>
            @endif
        </div>
    </div>
    <div class="button-buy-together uk-box-shadow-hover-small">
        <button class="uk-button use-ajax uk-width-1-1 uk-flex uk-flex-middle"
                data-product="{{ $item->id_product_groups }}"
                data-product_group="1"
                data-path="{{ _r('ajax.shop.basket') }}"
                rel="nofollow"
                type="button">
            <i class="icon-basket sprites uk-display-inline-block uk-margin-remove uk-visible@m"></i>
            <i class="icon-basket-teaser sprites-m uk-display-inline-block uk-hidden@m"></i>
            <div class="set-total">
                <div class="set uk-text-left">
                    @lang('shop.buy_set_for')
                </div>
                <div class="total">
                    @if($item->price_product_group['price']['currency']['prefix'])
                        <span class="currency">
                            {{ $item->price_product_group['price']['currency']['prefix'] }}
                        </span>
                    @endif
                    <span class="number">
                        {{ $item->price_product_group['price']['format']['view_price'] }}
                    </span>
                    @if($item->price_product_group['price']['currency']['suffix'])
                        <span class="currency">
                            {{ $item->price_product_group['price']['currency']['suffix'] }}
                        </span>
                    @endif
                </div>
            </div>
        </button>
    </div>
</div>










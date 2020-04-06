<div>
    <div class="teaser-cart uk-box-shadow-hover-xlarge uk-position-relative">
        @if($item->relation_entity->mark_discount || $item->discount_timer_product->exists || $item->relation_entity->mark_new)
            <div class="uk-mark-list uk-position-absolute">
                @if($item->relation_entity->mark_discount || $item->discount_timer_product->exists)
                    <div class="uk-mark mark-discount">
                        @lang('shop.product_status_stock')
                    </div>
                @endif
                @if($item->relation_entity->mark_new)
                    <div class="uk-mark mark-new">
                        @lang('shop.product_status_new')
                    </div>
                @endif
            </div>
        @endif
        <a href="{{ _u(($item->_alias->language != DEFAULT_LANGUAGE ? "{$item->_alias->language}/{$item->_alias->alias}" : $item->_alias->alias)) }}"
           title="{{ $item->title }}">
            <div class="preview uk-flex uk-flex-center uk-flex-middle">
                @if($item->_preview_asset())
                    {!! $item->_preview_asset('thumb_shop_product_200', ['attributes' => ['alt' => $item->title, 'class' => 'uk-visible@s'], 'only_way' => FALSE]) !!}
                    {!! $item->_preview_asset('thumb_shop_product_100', ['attributes' => ['alt' => $item->title, 'class' => 'uk-hidden@s'], 'only_way' => FALSE]) !!}
                @else
                    <img src="{{ formalize_path('images/no-image.png') }}"
                         alt="">
                @endif
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
                                                style="background-color: {!! $_color['style']['color_shade'] !!}">
                                            </div>
                                        @else
                                            <div
                                                class="item-color"
                                                title="{{ $_color['name'] }}"
                                                style="background-color: {!! $_color['style']['color_shade'] !!}">
                                            </div>
                                        @endif
                                        @if($_color['found']->current)
                                            <div class="line-border uk-position-cover"
                                                 style="border-color: {!! $_color['style']['color_shade'] !!}">
                                                <div class="line-background"
                                                     style="background-color: {!! $_color['style']['color_shade'] !!}">
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
                                     style="border-color: {!! $_color['style']['color_shade'] !!}">
                                    <div class="line-background"
                                         style="background-color: {!! $_color['style']['color_shade'] !!}">
                                        {{ $_color['name'] }}
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
                            foreach ($item->params_product['color']->selected as $_option_id) {
                                if ($language == DEFAULT_LANGUAGE) {
                                    $_color_name = $item->params_product['color']->data->get($_option_id);
                                } else {
                                    $_translate = $item->params_product['color']->translate_data->get($_option_id);
                                    $_color_name = isset($_translate[$language]) && $_translate[$language] ? $_translate[$language] : $item->params_product['color']->data->get($_option_id);
                                }
                                $_color_style = $item->params_product['color']->style_data->get($_option_id);
                            }
                            ?>
                            <div class="line-border uk-position-cover"
                                 style="border-color: {!! $_color_style['color_shade'] !!}">
                                <div class="line-background"
                                     style="background-color: {!! $_color_style['color_shade'] !!}">
                                </div>
                            </div>
                        @endif
                    @endisset
                @endif
            </div>
        </a>
        <div class="content-cart">
            <a href="{{ _u($item->_alias->alias) }}"
               title="{{ $item->title }}">
                <div class="title">
                    {{ $item->title }}
                </div>
            </a>
            <? /* ?>
            @isset($item->params_product['manufacturer'])
                @if($item->params_product['manufacturer']->selected)
                    <?
                    if ($language == DEFAULT_LANGUAGE) {
                        $_param_label = $item->params_product['manufacturer']->title;
                    } else {
                        $_param_label = $item->params_product['manufacturer']->translate[$language];
                    }
                    $_param_values = null;
                    foreach ($item->params_product['manufacturer']->selected as $_option_id) {
                        if ($language == DEFAULT_LANGUAGE) {
                            $_param_values[] = $item->params_product['manufacturer']->data->get($_option_id);
                        } else {
                            $_translate = $item->params_product['manufacturer']->translate_data->get($_option_id);
                            $_param_values[] = isset($_translate[$language]) && $_translate[$language] ? $_translate[$language] : $item->params_product['manufacturer']->data->get($_option_id);
                        }
                    }
                    ?>
                    @if($_param_values)
                        {!! "{$_param_label}: " . implode(', ', $_param_values) !!}
                    @endif
                @endif
            @endisset
            <? */ ?>

            @if($item->prices_product['availability'])
                <div class="available">
                    @lang('shop.product_available')
                </div>
                <div class="uk-grid-collapse box-price"
                     uk-grid>
                    <div class="uk-width-2-3 uk-flex uk-flex-bottom">
                        <div class="uk-flex uk-flex-column">
                            <div class="old-price">
                                @if($item->prices_product['old_price'])
                                    <del>
                                        {{ $item->prices_product['old_price']['format']['view_price'] }}
                                    </del>
                                @endif
                            </div>
                            <div class="price">
                                @if($item->prices_product['price']['currency']['prefix'])
                                    <span>
                                        {{ $item->prices_product['price']['currency']['prefix'] }}
                                    </span>
                                @endif
                                {{ $item->prices_product['price']['format']['view_price'] }}
                                @if($item->prices_product['price']['currency']['suffix'])
                                    <span>
                                        {{ $item->prices_product['price']['currency']['suffix'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-1-3 uk-text-right">
                        <button class="uk-button use-ajax button-teaser uk-position-relative"
                                data-product="{{ $item->relation_entity->id }}"
                                data-path="{{ _r('ajax.shop.basket') }}"
                                rel="nofollow"
                                type="button">
                            <i class="icon-basket sprites uk-display-block uk-visible@m"></i>
                            <i class="icon-basket-teaser sprites-m uk-display-block uk-hidden@m"></i>
                        </button>
                    </div>
                </div>
            @else
                <div class="available not">
                    @lang('shop.product_not_available')
                </div>
                <div class="box-price">&nbsp;</div>
            @endif
        </div>
    </div>
</div>










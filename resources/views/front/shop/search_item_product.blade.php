<li>
    <a href="{{ _u((DEFAULT_LANGUAGE != $item->_alias->language ? "{$item->_alias->language}/{$item->_alias->alias}" : $item->_alias->alias)) }}"
       class="uk-display-block shop-product-link"
       data-product="{{ $item->relation_entity->id }}">
        <div class="item-search uk-flex uk-flex-middle@s">
            <div class="item-left uk-flex uk-flex-middle">
                @if($item->_preview_asset())
                    {!! $item->_preview_asset('thumb_shop_product_77', ['attributes' => ['width' => 77, 'alt' => $item->title], 'only_way' => FALSE]) !!}
                @else
                    <img src="{{ formalize_path('images/no-image.png') }}"
                         alt="">
                @endif
            </div>
            <div class="item-righ uk-flex-1 uk-flex uk-flex-middle uk-flex-between">
                <div>
                    <div class="title">
                        {{ $item->title }}
                    </div>
                    @isset($item->params_product['model'])
                        @if($item->params_product['model']->selected)
                            <?
                            if ($language == DEFAULT_LANGUAGE) {
                                $_param_label = $item->params_product['model']->title;
                            } else {
                                $_param_label = $item->params_product['model']->translate[$language];
                            }
                            $_param_values = null;
                            if ($language == DEFAULT_LANGUAGE) {
                                $_param_values[] = $item->params_product['model']->selected . (isset($item->params_product['model']->data['unit']) && $item->params_product['model']->data['unit'] ? " {$item->params_product['model']->data['unit']}" : NULL);
                            } else {
                                $_param_values[] = $item->params_product['model']->selected . (isset($item->params_product['model']->translate_data[$language]) && $item->params_product['model']->translate_data[$language] ? " {$item->params_product['model']->translate_data[$language]}" : (isset($item->params_product['model']->data['unit']) && $item->params_product['model']->data['unit'] ? " {$item->params_product['model']->data['unit']}" : NULL));
                            }
                            ?>
                            @if($_param_values)
                                <div class="sky">
                                    {!! "{$_param_label}: " . implode(', ', $_param_values) !!}
                                </div>
                            @endif
                        @endif
                    @endisset
                    @if($item->prices_product['availability'])
                        <div class="available">
                            @lang('shop.product_available')
                        </div>
                    @else
                        <div class="available not">
                            @lang('shop.product_not_available')
                        </div>
                    @endif
                </div>
                @if($item->prices_product['availability'])
                    <div class="price-search">
                        @if($item->prices_product['old_price'])
                            <del class="old-price">
                                {{ $item->prices_product['old_price']['format']['view_price'] }}
                            </del>
                        @endif
                        <span class="price">
                            {{ $item->prices_product['price']['currency']['prefix'] }}
                            {{ $item->prices_product['price']['format']['view_price'] }}
                            {{ $item->prices_product['price']['currency']['suffix'] }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </a>
</li>
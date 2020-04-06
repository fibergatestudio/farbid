@php
    $_back_link = request()->server('HTTP_REFERER');
    $_language = $_wrap['locale'];
    $_contacts = $_wrap['contacts'];
    $_EC = ECommerce::view_item($item);
@endphp
<div id="box-view-shop-product">
    <div class="other-page product uk-position-relative">
        <hr>
        <div class="uk-container uk-container-large">
            <div class="product-breadcrumb uk-visible@m">
                @include('oleus.base.breadcrumb')
            </div>
            <div class="uk-grid-collapse box-top-card"
                 uk-grid>
                <div class="uk-width-1-3@m uk-width-1-1 uk-position-relative">
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
                    <div class="uk-flex uk-flex-middle uk-flex-between uk-hidden@m">
                        <a href="{{ $_back_link }}"
                           class="icon-back-product sprites-m uk-display-block">
                        </a>
                        <a href="#modal-share"
                           uk-toggle>
                            <i class="icon-share-mobile sprites-m uk-display-block"></i>
                        </a>
                    </div>
                    <div class="uk-position-relative uk-visible-toggle uk-light"
                         uk-slideshow="min-height: 430; animation: fade">
                        @if($item->medias_product['render']['full']->count())
                            <ul class="uk-slideshow-items"
                                uk-lightbox>
                                @foreach($item->medias_product['render']['full'] as $_id => $_media)
                                    <li>
                                        <a href="{{ $item->medias_product['render']['original'][$_id]  }}"
                                           class="uk-display-block uk-child-width-1-1 uk-height-1-1">
                                            {!! $_media !!}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div uk-slider
                                 class="product-slider-thumb uk-visible@m">
                                <div class="uk-position-relative">
                                    <div class="uk-slider-container uk-light">
                                        <ul class="uk-thumbnav uk-slider-items uk-text-center uk-child-width-1-4 uk-grid-large uk-grid">
                                            @foreach($item->medias_product['render']['thumb'] as $_media)
                                                <li uk-slideshow-item="{{ $loop->index }}">
                                                    <a href="#"
                                                       rel="nofollow">
                                                        {!! $_media !!}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <a class="uk-position-center-left-out uk-position-small"
                                           href="#"
                                           rel="nofollow"
                                           uk-slider-item="previous">
                                            <i class="icon-thumbnav-prev sprites uk-display-block"></i>
                                        </a>
                                        <a class="uk-position-center-right-out uk-position-small"
                                           href="#"
                                           rel="nofollow"
                                           uk-slider-item="next">
                                            <i class="icon-thumbnav-next sprites uk-display-block"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <ul class="uk-slideshow-nav uk-dotnav uk-flex-center uk-margin uk-hidden@m"></ul>
                        @else
                            <ul class="uk-slideshow-items">
                                <li>
                                    <a href="#"
                                       class="uk-display-block uk-child-width-1-1 uk-height-1-1">
                                        {!! image_render(NULL, 'full_shop_product', ['attributes' => ['uk-cover' => TRUE]]) !!}
                                    </a>
                                </li>
                            </ul>
                        @endif
                    </div>
                </div>
                <div class="uk-width-2-3@m">
                    <div class="product-detail-main">
                        <div class="product-item-details">
                            @if($item->discount_timer_product->exists)
                                <?
                                $_utc = \Carbon\Carbon::now('Europe/Kiev')->format('P');
                                $_utc = (int)$_utc;
                                ?>
                                <div class="item-offer-clock uk-flex">
                                    <div class="stock-timer uk-text-uppercase">
                                        @lang('shop.product_status_stock')
                                    </div>
                                    <ul class="countdown-clock uk-flex"
                                        data-type="full"
                                        data-id="{{ $item->discount_timer_product->id }}"
                                        data-utc="{{ $_utc }}"
                                        data-time="{{ $item->discount_timer_product->finish_date->format('m/d/Y H:i:s') }}">
                                        <li>
                                            <span class="days">00</span>
                                            <p class="days_ref">days</p>
                                        </li>
                                        <li class="seperator">:</li>
                                        <li>
                                            <span class="hours">00</span>
                                            <p class="hours_ref">hrs</p>
                                        </li>
                                        <li class="seperator">:</li>
                                        <li>
                                            <span class="minutes">00</span>
                                            <p class="minutes_ref">min</p>
                                        </li>
                                        <li class="seperator">:</li>
                                        <li>
                                            <span class="seconds">00</span>
                                            <p class="seconds_ref">sec</p>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                            <div class="uk-flex uk-flex-middle uk-flex-between">
                                <h1 class="product-item-name uk-text-uppercase">
                                    {!! $_wrap['page']['_title'] !!}
                                </h1>
                                <div class="uk-visible@m">
                                    <a href="#modal-share"
                                       uk-toggle>
                                        <i class="icon-share sprites uk-display-block"></i>
                                    </a>
                                </div>
                            </div>
                            @if($item->params_product)
                                <div class="all-param uk-flex uk-flex-wrap">
                                    <?
                                    $_view_param = [
                                        'country',
                                        'manufacturer',
                                        'model',
                                    ];
                                    ?>
                                    @foreach($_view_param as $_param_name)
                                        @isset($item->params_product[$_param_name])
                                            @if($item->params_product[$_param_name]->selected)
                                                <?
                                                if($_language == DEFAULT_LANGUAGE) {
                                                    $_param_label = $item->params_product[$_param_name]->title;
                                                } else {
                                                    $_param_label = $item->params_product[$_param_name]->translate[$_language];
                                                }
                                                $_param_values = null;
                                                if($item->params_product[$_param_name]->type == 'select') {
                                                    foreach($item->params_product[$_param_name]->selected as $_option_id) {
                                                        if($_language == DEFAULT_LANGUAGE) {
                                                            $_param_values[] = $item->params_product[$_param_name]->data->get($_option_id);
                                                        } else {
                                                            $_translate = $item->params_product[$_param_name]->translate_data->get($_option_id);
                                                            $_param_values[] = isset($_translate[$_language]) && $_translate[$_language] ? $_translate[$_language] : $item->params_product[$_param_name]->data->get($_option_id);
                                                        }
                                                    }
                                                } else {
                                                    if($_language == DEFAULT_LANGUAGE) {

                                                        $_param_values[] = $item->params_product[$_param_name]->selected . (isset($item->params_product[$_param_name]->data['unit']) && $item->params_product[$_param_name]->data['unit'] ? " {$item->params_product[$_param_name]->data['unit']}" : NULL);
                                                    } else {
                                                        $_param_values[] = $item->params_product[$_param_name]->selected . (isset($item->params_product[$_param_name]->translate_data[$_language]) && $item->params_product[$_param_name]->translate_data[$_language] ? " {$item->params_product[$_param_name]->translate_data[$_language]}" : (isset($item->params_product[$_param_name]->data['unit']) && $item->params_product[$_param_name]->data['unit'] ? " {$item->params_product[$_param_name]->data['unit']}" : NULL));
                                                    }
                                                }
                                                ?>
                                                @if($_param_values)
                                                    {!! "<div><span>{$_param_label}</span>: " . implode(', ', $_param_values) . "</div>" !!}
                                                @endif
                                            @endif
                                        @endisset
                                    @endforeach
                                </div>
                            @endif
                            @if($item->modification_links)
                                <? $_color = isset($item->modification_links['view']['data'][44]) ? true : false; ?>
                                <div class="{{ $_color ? 'color' : 'number' }} uk-visible">
                                    <div class="{{ $_color ? 'box-color ' : 'uk-text-center' }} uk-flex uk-flex-wrap">
                                        @foreach($item->modification_links['view']['data'] as $_link_params)
                                            @foreach($_link_params as $_link_item)
                                                @if($_link_item['found'])
                                                    @if($_link_item['found']->current)
                                                        <a href="javascript:void(0);"
                                                           class="{{ $_color ? 'uk-radio uk-flex uk-flex-middle uk-flex-center' : 'link-number uk-display-block' }} active "
                                                           title="{{ $_link_item['name'] }}"
                                                           style="{!! $_link_item['style']['color_shade'] ? "background-color: {$_link_item['style']['color_shade']}" : '' !!}">
                                                            {{ $_link_item['name'] }}
                                                        </a>
                                                    @else
                                                        <a href="{{ _u($_link_item['found']->url_alias, [], TRUE) }}"
                                                           class="{{ $_color ? 'uk-radio' : 'link-number uk-display-block' }} use-ajax"
                                                           title="{{ $_link_item['found']->title }}"
                                                           style="{!! $_link_item['style']['color_shade'] ? "background-color: {$_link_item['style']['color_shade']}" : '' !!}">
                                                            {{ $_link_item['name'] }}
                                                        </a>
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="free uk-flex uk-flex-between uk-flex-middle uk-flex-wrap">
                                <div>
                                    @if($item->modification_links && isset($item->modification_links['view']['data'][49]))
                                        <div class="number">
                                            <div class="uk-flex uk-flex-wrap uk-text-center"
                                                 id="clones-number">
                                                @foreach($item->modification_links['view']['data'][49] as $_volume)
                                                    @if($_volume['found'])
                                                        @if($_volume['found']->current)
                                                            <a href="javascript:void(0);"
                                                               class="active link-number uk-display-block"
                                                               title="{{ $_volume['found']->title }}"
                                                               style="background-color: {!! $_volume['style']['class'] !!}">
                                                                {{ $_volume['name'] }}<br>
                                                                <span>литр</span>
                                                            </a>
                                                        @else
                                                            <a href="{{ _u($_volume['found']->url_alias, [], TRUE) }}"
                                                               class="use-ajax link-number uk-display-block"
                                                               title="{{ $_volume['found']->title }}"
                                                               style="background-color: {!! $_volume['style']['class'] !!}">
                                                                {{ $_volume['name'] }}<br>
                                                                <span>литр</span>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="free-client uk-text-right uk-visible@m">
                                    {!! variable('delivery_free') !!}
                                </div>
                            </div>
                            <div class="uk-flex uk-flex-between uk-flex-middle uk-flex-bottom">
                                <div class="price-box">
                                    @if($item->prices_product['old_price'])
                                        <del class="price old-price">
                                            {{ $item->prices_product['old_price']['format']['view_price'] }}
                                        </del>
                                    @endif
                                    <div class="price">
                                        @if($item->prices_product['price']['currency']['prefix'])
                                            <span class="price-currency uk-text-uppercase">
                                                {{ $item->prices_product['price']['currency']['prefix'] }}
                                            </span>
                                        @endif
                                        <span class="price-number">
                                            {{ $item->prices_product['price']['format']['view_price'] }}
                                        </span>
                                        @if($item->prices_product['price']['currency']['suffix'])
                                            <span class="price-currency uk-text-uppercase">
                                                {{ $item->prices_product['price']['currency']['suffix'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="bottom-detail cart-button uk-flex uk-flex-wrap">
                                    <div>
                                        <button
                                            class="uk-button uk-button-link link-like uk-display-block uk-visible@m use-ajax"
                                            type="button"
                                            data-path="{{ _r('ajax.shop.product_desires') }}"
                                            data-product="{{ $item->relation_entity->id }}"
                                            rel="nofollow">
                                            <i class="icon-like sprites uk-display-inline-block"></i>
                                            @lang('forms.button_like')
                                        </button>
                                        <button
                                            class="uk-button uk-button-default link-one-click uk-display-block use-ajax"
                                            data-path="{{ _r('ajax.shop.buy_one_click.form') }}"
                                            data-product="{{ $item->relation_entity->id }}"
                                            rel="nofollow"
                                            type="button">
                                            <span class="uk-visible@s">
                                                @lang('forms.button_buy_one_click')
                                            </span>
                                            <span class="uk-hidden@s">
                                                @lang('forms.button_buy_one_click_mobile')
                                            </span>
                                        </button>
                                    </div>
                                    <div>
                                        <button id="form-product-add-to-basket-button"
                                                class="uk-button link-bay use-ajax 
												 @if($item->prices_product['availability']) @else uk-disabled @endif
												"
                                                data-product="{{ $item->relation_entity->id }}"
                                                data-count="1"
                                                data-path="{{ _r('ajax.shop.basket') }}"
                                                rel="nofollow"
                                                type="button">
												@if($item->prices_product['availability'])
                                            <span class="uk-visible@l">
                                                @lang('forms.button_add_basket')
                                            </span>
                                            <i class="icon-arrow-buy sprites uk-display-inline-block uk-visible@l"></i>
                                            <i class="icon-basket-buy sprites uk-display-inline-block uk-visible@m"></i>
                                            <i class="icon-basket-buy-modal sprites-m uk-display-inline-block uk-hidden@m"></i>
											@else
												@lang('shop.product_not_available')
											@endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--<div class="free-client-amount uk-padding uk-padding-remove-horizontal">--}}
                            {{--{!! variable('delivery_free_amount') !!}--}}
                            {{--<div class="free-client uk-hidden@m">--}}
                                {{--{!! variable('delivery_free') !!}--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        @if($item->body || $item->equipment_product || $item->structural_features_product || $item->specifications_product)
                            <div class="characteristics">
                                <ul uk-tab>
                                    @if($item->body)
                                        <li>
                                            <a href="#"
                                               rel="nofollow">
                                                @lang('shop.tab_description')
                                            </a>
                                        </li>
                                    @endif
                                    @if($item->specifications_product)
                                        <li>
                                            <a href="#"
                                               rel="nofollow">
                                                @lang('shop.tab_specifications')
                                            </a>
                                        </li>
                                    @endif
                                    <? /* ?>
                                        @if($item->equipment_product)
                                            <li>
                                                <a href="#"
                                                    rel="nofollow">
                                                    @lang('shop.tab_equipment')
                                                </a>
                                            </li>
                                        @endif
                                        @if($item->structural_features_product)
                                            <li>
                                                <a href="#"
                                                    rel="nofollow">
                                                    @lang('shop.tab_structural_features')
                                                </a>
                                            </li>
                                        @endif
                                    <? */ ?>
                                </ul>
                                <ul class="uk-switcher uk-margin">
                                    @if($item->body)
                                        <li>
                                            {!! $item->body !!}
                                        </li>
                                    @endif
                                    @if($item->specifications_product)
                                        <li>
                                            @foreach($item->specifications_product as $_specification)
                                                @if($_specification[1])
                                                    <div class="uk-grid-small uk-child-width-1-2 item-param"
                                                         uk-grid>
                                                        <div class="name">
                                                            {!! $_specification[0] !!}
                                                        </div>
                                                        <div class="param">
                                                            {!! $_specification[1] !!}
                                                        </div>
                                                    </div>
                                                @else
                                                    <h3>
                                                        {!! $_specification[0] !!}
                                                    </h3>
                                                @endif
                                            @endforeach
                                        </li>
                                    @endif
                                    <? /* ?>
                                        @if($item->equipment_product)
                                            <li>
                                                {!! $item->equipment_product !!}
                                            </li>
                                        @endif
                                            @if($item->structural_features_product)
                                            <li>
                                                {!! $item->structural_features_product !!}
                                            </li>
                                        @endif
                                    <? */ ?>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @include('front.shop.product_groups_product')
        @include('front.shop.product_related')
        @include('front.shop.product_watched')
        <div class="block-load uk-position-fixed">
            <div class="uk-position-center loading-img">
                <img src="{{ formalize_path('template/img/loading.gif') }}"
                     alt="">
            </div>
        </div>
    </div>
</div>
@include('front.modals.share', compact('_contacts'))
@if($_EC)
    <script type="text/javascript">
        if (window.commerce !== undefined) window.commerce.event('view_item', {!! $_EC !!});
    </script>
@endif
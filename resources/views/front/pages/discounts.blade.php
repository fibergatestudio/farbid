@php
    $_wrap = wrap()->get();
    $_page_title = $_wrap['page']['_title'];
    $_page_id = $_wrap['page']['_id'];
    $_page_class = $_wrap['page']['_class'];
    $_background = $_wrap['page']['_background'];
    $_back_link = request()->server('HTTP_REFERER');
    $items = $item->items;
    $language = $_wrap['locale'];
@endphp

@extends('front.index')

@section('page')
    <article id="{{ $_page_id }}"
             class="uk-article page-type-{{ $item->type }} page-item page-item-{{ $item->id . ' ' . $_page_class . ' ' . ($_background ? 'page-background-exist' : '') }}"
             style="{{ $_background }}">
        <div class="other-page">
            <hr>
            <div class="uk-container uk-container-large">
                @include('oleus.base.breadcrumb')
                @if($item->sub_title)
                    <h2 class="heading">
                        <span>{{ $item->sub_title }}</span>
                    </h2>
                @endif
                @if($items->isNotEmpty() )
                    <div class="product-listing">
                        <div class="inner-listing">
                            <div
                                class="uk-grid-collapse uk-child-width-1-5@xl uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2 grid-category-product"
                                uk-grid
                                uk-height-match="target: .title; row: false">
                                @include('front.shop.items_category_products', compact('items', 'language'))
                            </div>
                        </div>
                    </div>
                @else
                    <div class="uk-alert uk-alert-warning uk-border-rounded uk-box-shadow-small">
                        <p>@lang('others.no_items')</p>
                    </div>
                @endif
                @if($item->body)
                    <div class="page-body">
                        {!! $item->body !!}
                    </div>
                @endif
            </div>
        </div>
    </article>
@endsection

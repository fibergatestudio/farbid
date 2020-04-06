@php
    $_wrap = wrap()->get();
    $_page_id = $_wrap['page']['_id'];
    $_page_class = $_wrap['page']['_class'];
    $_background = $_wrap['page']['_background'];
    $_page_number = $_wrap['seo']['_page_number'];
    $sub_categories = $_category->children;
    if($sub_categories->isNotEmpty()){
        $sub_categories = $sub_categories->filter(function($_cat){
            // return $_cat->relation_entity->status ? TRUE : FALSE;
            return $_cat->status ? TRUE : FALSE;
        });
    }
    if(request()->has('code')){
        // dd($item->body, $_page_number);
    }
@endphp

@extends('front.index')

@section('page')
    <article id="{{ $_page_id }}"
             class="uk-article category-item category-item-{{ $item->id . ' ' . $_page_class . ' ' . ($_background ? 'page-background-exist' : '') }}"
             style="{{ $_background }}">
        <div class="other-page">
            <hr>
            <div class="uk-container uk-container-large">
                @include('front.shop.items', ['category' => $_category, 'filter' => $_category_filter, 'items' => $_category_items, 'sub_categories' => $sub_categories, 'language' => $_wrap['locale'], 'location' => $_wrap['location'], 'currency' => $_wrap['currency']['current']])
            </div>
            <div id="shop-category-description-card">
                @if($item->body && $_page_number == 1)
                    <div class="description-seo">
                        <div class="uk-container uk-container-large">
                            {!! $item->body !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </article>
    {{--@include('front.modals.filter-category', ['items' => $_wrap['shop_category'], 'language' => $_wrap['locale'], 'location' => $_wrap['location'], 'currency' => $_wrap['currency']['current']])--}}
    {{--@include('front.modals.sort-category', ['items' => $_wrap['shop_category'], 'language' => $_wrap['locale'], 'location' => $_wrap['location'], 'currency' => $_wrap['currency']['current']])--}}
    @include('front.modals.filter-category', ['category' => $_category, 'filter' => $_category_filter,'items' => $_category_items, 'sub_categories' => $sub_categories, 'language' => $_wrap['locale'], 'location' => $_wrap['location'], 'currency' => $_wrap['currency']['current']])
    @include('front.modals.sort-category')
@endsection

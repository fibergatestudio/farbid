@php
    $_wrap = wrap()->get();
    $_page_id = $_wrap['page']['_id'];
    $_page_class = $_wrap['page']['_class'];
    $_background = $_wrap['page']['_background'];
@endphp

@extends('front.index')

@section('page')
    <article id="{{ $_page_id }}"
             class="uk-article product-item product-item-{{ $item->id . ' ' . $_page_class . ' ' . ($_background ? 'page-background-exist' : '') }}"
             style="{{ $_background }}">
{{--        @include('front.shop.item_product', ['item' => $item])--}}
    </article>
    {{--@include('front.modals.share')--}}
@endsection

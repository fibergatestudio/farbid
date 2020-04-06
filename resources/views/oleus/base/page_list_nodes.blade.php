@php
    $_background = wrap()->get('page._background');
    $_background = $_background ? "style=\"background-image: url('{$_background}')\"" : '';
@endphp

@extends('front.index')

@section('page')
    <article
        class="uk-article uk-margin-bottom page-type-list-nodes page-item page-item-{{ $item->id }}{{ $item->style_class ? " {$item->style_class}" : '' }}">
        <div
            class="page-bg page-bg-list-nodes{{ $_background ? ' exist' : '' }}{{ $item->style_class ? " {$item->style_class}" : '' }}"
            {!! $_background !!}>
            <div class="uk-container">
                <h1 class="uk-heading-primary uk-article-title page-title">{!! wrap()->get('page._title') !!}</h1>
                @if($item->sub_title)
                    <div class="uk-article-meta page-sub-title">{{ $item->sub_title }}</div>
                @endif
            </div>
        </div>
        <div class="uk-container uk-margin-bottom uk-margin-large-top">
            @if($items->isNotEmpty())
                <div class="uk-margin-bottom">
                    @foreach($items as $_item)
                        @include('oleus.base.teaser_node', ['item' => $_item])
                    @endforeach
                </div>
                @if(method_exists($items, 'links'))
                    {!! $items->links('oleus.base.pagination') !!}
                @endif
            @else
                <div class="uk-alert uk-alert-warning uk-border-rounded uk-box-shadow-small">
                    <p>@lang('others.no_items')</p>
                </div>
            @endif
            @if($item->body && wrap()->get('seo._page_number') == 1)
                <div class="page-body">
                    {!! content_render($item) !!}
                </div>
            @endif
        </div>
    </article>
@endsection

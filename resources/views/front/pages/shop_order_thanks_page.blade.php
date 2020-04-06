@php
    $_background = wrap()->get('page._background');
    $_background = $_background ? "style=\"background-image: url('{$_background}')\"" : '';
@endphp

@extends('front.index')

@section('page')
    <article
        class="uk-article page-type-{{ $item->type }} page-item page-item-{{ $item->id }}{{ $item->style_class ? " {$item->style_class}" : '' }}{{ $_background ? ' page-background-exist' : '' }}"
        {!! $_background !!}>
        <div class="other-page">
            <hr>
            <div class="uk-container uk-container-large">
                <h1 class="uk-heading-bullet uk-text-uppercase">{!! wrap()->get('page._title') !!}</h1>
                <div class="thanks-content uk-text-center">
                    @if($item->sub_title)
                        <div class="thanks-small-text uk-margin">{!! $item->sub_title !!}</div>
                    @endif
                    <div class="box-social">
                        @if($item->body)
                            <div class="error-slogan">
                                {!! $item->body !!}
                            </div>
                        @endif
                        <div class="uk-grid-collapse uk-child-width-1-2" uk-grid>
                            <div class=""></div>
                            <div class=""></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endsection

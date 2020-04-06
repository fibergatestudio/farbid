@extends('front.index')

@section('page')
    <article
        class="uk-article page-type-error page-type-{{ $item->type }} page-item page-item-{{ $item->id }}{{ $item->style_class ? " {$item->style_class}" : '' }}">
        <div class="other-page error-block-main uk-background-top-left" style="background-image: url({{ formalize_path('template/img/bg-error.png') }})">
            <hr>
            <div class="uk-container uk-container-large">
                <h1 class="uk-heading-bullet uk-text-uppercase">{!! wrap()->get('page._title') !!}</h1>
                <div class="main-error-text">{{ $item->sub_title }}</div>
                <div class="error-catalog uk-padding uk-padding-remove-horizontal">
                    <a class="uk-text-uppercase" href="/">
                        Перейти в каталог
                    </a>
                </div>
            </div>
        </div>
    </article>
@endsection
@extends('front.index')

@section('page')
    <article
        class="uk-article uk-margin-bottom page-type-error page-type-{{ $item->type }} page-item page-item-{{ $item->id }}{{ $item->style_class ? " {$item->style_class}" : '' }}">
        @if($item->body)
            <div class="uk-container page-body uk-margin-large-top uk-margin-large-bottom uk-text-center">
                <div class="content uk-margin-large-top uk-margin-large-bottom">
                    <div class="code-error">403</div>
                </div>
                {!! content_render($item) !!}
                {!! _l(trans('others.link_back_to_home'), '/') !!}
            </div>
        @endif
    </article>
@endsection
@extends('oleus.index')

@section('page')
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove">
                <div class="uk-float-right">
                    <button
                        type="button"
                        data-path="{{ _r('ajax.clear_cache') }}"
                        class="uk-button uk-button-danger uk-border-rounded use-ajax">
                        Clear cache
                    </button>
                </div>
                {!! wrap()->get('page._title') !!}
            </h1>
        </div>
        @if($other['orders'])
            @include('oleus.main.last_orders', ['items' => $other['orders']])
        @endif
        @if($other['buy_one_click'])
            @include('oleus.main.last_buy', ['items' => $other['buy_one_click']])
        @endif
    </article>
@endsection
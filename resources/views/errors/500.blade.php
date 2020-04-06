@extends('front.index')

@section('page')
    <article
        class="uk-article uk-margin-bottom page-type-error page-type-{{ $item->type }} page-item page-item-{{ $item->id }}{{ $item->style_class ? " {$item->style_class}" : '' }}">
        <div class="bg-error">
            <div class="box-offers">
                <div class="uk-container uk-padding-small">
                    @include('oleus.base.breadcrumb')
                    <div class="box-error uk-flex uk-flex-bottom">
                        <div>
                            <div>
                                <div class="error-number uk-display-inline-block">
                                    500
                                </div>
                                <div class="error uk-display-inline-block">
                                    @lang('others.error')
                                </div>
                            </div>
                            <div class="not-found">
                                @lang('notice.internal_server_error')
                            </div>
                            @if($item->body)
                                <div class="description">
                                    {!! $item->body !!}
                                </div>
                            @endif
                            <div class="link-home uk-display-inline-block">
                                <a href="{{ _u('/', [], TRUE) }}">
                                    @lang('others.link_back_to_home')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endsection
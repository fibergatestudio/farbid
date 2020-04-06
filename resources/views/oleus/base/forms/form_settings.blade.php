@extends('oleus.index')

@section('page')
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove">{!! wrap()->get('page._title') !!}</h1>
        </div>
        <form class="uk-form uk-form-stacked uk-form-horizontal uk-width-1-1 uk-margin-large-bottom"
              method="POST"
              action="{{ _r('oleus.settings', ['page' => $form->route_tag]) }}">
            {{ csrf_field() }}
            {{ method_field('POST') }}
            <div class="uk-card uk-card-default uk-card-small uk-border-rounded">
                <div class="uk-card-header uk-text-right">
                    @if($form->additional_buttons)
                        @foreach($form->additional_buttons as $_button)
                            {!! $_button !!}
                        @endforeach
                    @endif
                    @if($form->translate)
                        <button type="button"
                                name="translate"
                                data-path="{{ _r('oleus.settings.translate', ['page' => $form->route_tag]) }}"
                                value="1"
                                class="uk-button uk-button-primary uk-waves uk-border-rounded use-ajax">
                            @lang('forms.button_add_translate')
                        </button>
                    @endif
                    <button type="submit"
                            name="save"
                            value="1"
                            class="uk-button uk-button-secondary uk-waves uk-border-rounded">
                        @lang('forms.button_save_settings')
                    </button>
                </div>
                <div class="uk-card-body">
                    <div class="uk-clearfix">
                        <ul class="uk-tab"
                            uk-tab="connect: #uk-tab-body; animation: uk-animation-fade; swiping: false;">
                            @foreach($form->tabs as $tab)
                                <li class="{{ $loop->index == 0 ? 'uk-active' : '' }}">
                                    <a href="#">{{ $tab['title'] }}</a>
                                </li>
                            @endforeach
                            @if($form->seo)
                                <li>
                                    <a href="#">{{ __('pages.tab_meta_tags') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div>
                        <ul id="uk-tab-body"
                            class="uk-switcher uk-margin">
                            @foreach($form->tabs as $tab)
                                <li class="{{ $loop->index == 0 ? 'uk-active' : '' }}">
                                    @foreach($tab['content'] as $content)
                                        {!! $content !!}
                                    @endforeach
                                </li>
                            @endforeach
                            @if($form->seo)
                                <li>
                                    @include('oleus.base.forms.fields_group_meta_tags')
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </article>
@endsection

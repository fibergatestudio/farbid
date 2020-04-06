@php
    $_background = wrap()->get('page._background');
    $_background = $_background ? "style=\"background-image: url('{$_background}')\"" : '';
@endphp

@extends('front.index')

@section('page')
    <article
        class="uk-article uk-margin-bottom page-type-{{ $item->type }} page-item page-item-{{ $item->id }}{{ $item->style_class ? " {$item->style_class}" : '' }}">
        <div
            class="page-bg page-type-reviews page-item page-item-{{ $item->id }}{{ $item->style_class ? " {$item->style_class}" : '' }}{{ $_background ? ' exist' : '' }}{{ $item->style_class ? " {$item->style_class}" : '' }}"
            {!! $_background !!}>
            <div class="uk-container">
                <h1 class="uk-article-title uk-text-uppercase page-title">{!! wrap()->get('page._title') !!}</h1>
                @if($item->sub_title)
                    <div class="uk-article-meta page-sub-title">{{ $item->sub_title }}</div>
                @endif
            </div>
        </div>
        <div class="uk-container uk-margin-bottom uk-margin-large-top">
            <div class="uk-margin-large-bottom">
                <div class="uk-card review-form open">
                    <h2 class="review-form uk-text-uppercase">
                        <span class="text-color-red">Оставьте отзыв</span> о нашей работе
                    </h2>
                    @php($_form = 'forms-review')
                    <form class="uk-form uk-form-stacked"
                          method="POST"
                          id="{{ $_form }}"
                          action="{{ _r('ajax.review.form') }}">
                        <input type="hidden"
                               value="{{ $_form }}"
                               name="forms">
                        <div class="uk-form-row"
                             id="{{ $_form }}-rating-object">
                            @for($i = 1; $i < 6; $i++)
                                <span uk-icon="icon: ui_star; ratio: 1.6"></span>
                            @endfor
                            <input type="hidden"
                                   value="0"
                                   name="rating">
                        </div>
                        <div class="uk-form-row"
                             id="{{ $_form }}-review-object">
                            <div class="uk-form-controls">
                                <div class="uk-inline uk-width-1-1">
                                        <textarea name="review"
                                                  class="uk-textarea"
                                                  autocomplete="off"
                                                  placeholder="Ваш отзыв"
                                                  id="{{ $_form }}-review"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="uk-grid uk-grid-small">
                            <div class="uk-width-medium@m">
                                <div class="uk-form-row"
                                     id="{{ $_form }}-name-object">
                                    <div class="uk-form-controls">
                                        <div class="uk-inline uk-width-1-1">
                                            <input type="text"
                                                   id="{{ $_form }}-name"
                                                   name="name"
                                                   value=""
                                                   autocomplete="off"
                                                   placeholder="Имя"
                                                   class="uk-input uk-width-1-1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-width-expand@m">
                                <div class="uk-form-row"
                                     id="{{ $_form }}-subject-object">
                                    <div class="uk-form-controls">
                                        <div class="uk-inline uk-width-1-1">
                                            <input type="text"
                                                   id="{{ $_form }}-subject"
                                                   name="subject"
                                                   value=""
                                                   autocomplete="off"
                                                   placeholder="Тема отзыва"
                                                   class="uk-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-grid uk-grid-small">
                            <div class="uk-width-expand@m">
                                <div class="uk-form-row"
                                     id="{{ $_form }}-confirm-object">
                                    <div class="uk-form-controls uk-form-controls-text uk-form-controls-checkbox">
                                        <label class="uk-text-small">
                                            <div>
                                                <input name="confirm"
                                                       type="checkbox"
                                                       id="{{ $_form }}-confirm"
                                                       class="uk-checkbox"
                                                       value="1"
                                                       checked><span>Я ознакомлен и принимаю условия Соглашения об использовании сайта, в том числе в части обработки и использования моих персональных данных</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-width-medium@m">
                            </div>
                        </div>
                        <button type="submit"
                                name="save"
                                value="1"
                                class="uk-button use-ajax">
                            @lang('forms.button_sent_review')
                        </button>
                    </form>
                </div>
            </div>
            @if($item->items->isNotEmpty())
                <div class="uk-margin-bottom">
                    @php($_columns = [0=>[],1=>[]])
                    @foreach($item->items as $_item)
                        @if($loop->index % 2 == 0)
                            @php($_columns[0][] = $_item)
                        @else
                            @php($_columns[1][] = $_item)
                        @endif
                    @endforeach
                    <div class="uk-grid uk-grid-divider uk-grid-match uk-grid-small"
                         uk-grid>
                        <div class="uk-width-1-2@m">
                            @foreach($_columns[0] as $_item)
                                @include('oleus.base.review', ['item' => $_item])
                            @endforeach
                        </div>
                        <div class="uk-width-1-2@m">
                            @foreach($_columns[1] as $_item)
                                @include('oleus.base.review', ['item' => $_item])
                            @endforeach
                        </div>
                    </div>
                </div>
                @if(method_exists($item->items, 'links'))
                    {!! $item->items->links('oleus.base.pagination') !!}
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

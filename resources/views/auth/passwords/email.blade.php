@extends('front.index')

@section('page')
    <article class="uk-article">
        <div class="other-page">
            <hr>
            <div class="uk-container uk-container-large">
                <div class="heading-part heading-bg mb-30">
                    <h2 class="uk-heading-bullet uk-text-uppercase">
                        {{ $item->title }}
                    </h2>
                </div>
                <div class="uk-text-center">
                    <form class="uk-form uk-form-stacked uk-width-large uk-inline uk-text-left"
                          method="POST"
                          action="{{ _r('password.email') }}">
                        {{ csrf_field() }}
                        {{ method_field('post') }}
                        <div class="uk-width-medium uk-margin-auto">
                            <div class="uk-margin-bottom">
                                {!!
                                    field_render('email', [
                                        'attributes' => [
                                            'placeholder' => trans('forms.label_email'),
                                            'autofocus' => NULL
                                        ]
                                    ])
                                !!}
                            </div>
                            <div class="uk-text-center">
                                <button type="submit"
                                        class="uk-button uk-waves uk-width-1-1">
                                    @lang('forms.button_reset')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </article>
@endsection

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
                    <form class="main-form"
                          method="POST"
                          action="{{ _r('register') }}">
                        {{ csrf_field() }}
                        {{ method_field('post') }}
                        <div class="uk-width-medium uk-margin-auto">
                            <div class="uk-margin-bottom">
                                {!!
                                    field_render('name', [
                                        'attributes' => [
                                            'placeholder' => trans('forms.label_login'),
                                        ]
                                    ])
                                !!}
                            </div>
                            <div class="uk-margin-bottom">
                                {!!
                                    field_render('email', [
                                        'type' => 'email',
                                        'attributes' => [
                                            'placeholder' => trans('forms.label_email')
                                        ]
                                    ])
                                !!}
                            </div>
                            <div class="uk-margin-bottom">
                                {!!
                                    field_render('password', [
                                        'type' => 'password',
                                        'attributes' => [
                                            'placeholder' => trans('forms.label_password')
                                        ]
                                    ])
                                !!}
                            </div>
                            <div class="uk-margin-bottom">
                                {!!
                                    field_render('password_confirmation', [
                                        'type' => 'password',
                                        'attributes' => [
                                            'placeholder' => trans('forms.label_password_confirmation')
                                        ]
                                    ])
                                !!}
                            </div>
                            <div class="uk-margin-bottom">
                                @if(config('os_common.users.registration'))
                                    <div>
                                        {!! _l(trans('others.link_login'), 'login', ['a' => ['class' => 'uk-text-small']]) !!}
                                    </div>
                                    <div>
                                        {!! _l(trans('others.link_reset_password'), 'password/reset', ['a' => ['class' => 'uk-text-small']]) !!}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <button type="submit"
                                        class="uk-button uk-waves uk-width-1-1">
                                    @lang('forms.button_register')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </article>
@endsection
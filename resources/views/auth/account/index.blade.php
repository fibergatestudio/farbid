@php
    $_wrap = wrap()->get();
    $language = $_wrap['locale'];
    $_back_link = request()->server('HTTP_REFERER');
    $_form = 'form-account-edit';
    $_profile_avatar_path = formalize_path('template/img/user.png');
    if($_profile_avatar = $item->_profile->_avatar){
        $_profile_avatar_path = image_render($_profile_avatar, 'account_avatar', ['only_way' => TRUE]);
    }
@endphp

@extends('front.index')

@section('page')
    <article class="uk-article account-user">
        <div class="other-page user uk-position-relative">
            <hr>
            <div class="uk-container uk-container-large">
                <h2 class="uk-heading-bullet title-block uk-text-uppercase">
                    <a href="{{ $_back_link }}">
                        @lang('forms.personal_information')
                    </a>
                </h2>
                <form method="POST"
                      id="{{ $_form }}"
                      action="{{ _r('ajax.account_edit_form') }}">
                    {{ csrf_field() }}
                    {{ method_field('POST') }}
                    <input type="hidden"
                           value="{{ $_form }}"
                           name="forms">
                    <input type="hidden"
                           value="{{ $item->id }}"
                           name="user_id">
                    <input type="hidden"
                           value="{{ $item->_profile->avatar_fid }}"
                           id="{{ $_form }}-avatar_fid"
                           name="avatar_fid">
                    <div class="user-info uk-flex uk-flex-between">
                        <div class="user-photo uk-padding-small uk-margin-right uk-visible@s">
                            <span uk-icon="icon: camera;"></span>
                            <div
                                class="js-upload uk-placeholder uk-text-center uk-border-rounded uk-background-cover uk-background-center-center"
                                style="background-image: url({{ $_profile_avatar_path }})">
                                <div data-url="{{ _r('ajax.account_avatar_upload') }}"
                                     data-allow="*.(jpg|jpeg|gif|png|svg)"
                                     data-field="avatar_fid"
                                     data-multiple="0"
                                     data-view=""
                                     class="uk-field file-upload-field"
                                     uk-form-custom>
                                    <input type="file">
                                </div>
                            </div>
                            <progress class="uk-progress"
                                      value="0"
                                      max="100"
                                      hidden></progress>
                        </div>
                        <div class="uk-flex-1">
                            <div class="uk-flex-inline user-top">
                                <div class="user-photo uk-padding-small uk-margin-right uk-hidden@s">
                                    <div
                                        class="js-upload uk-placeholder uk-text-center uk-border-rounded uk-background-cover uk-background-center-center"
                                        style="background-image: url({{ $_profile_avatar_path }})">
                                        <div data-url="{{ _r('ajax.account_avatar_upload') }}"
                                             data-allow="*.(jpg|jpeg|gif|png|svg)"
                                             data-field="avatar_fid"
                                             data-multiple="0"
                                             data-view=""
                                             class="uk-field file-upload-field"
                                             uk-form-custom>
                                            <input type="file">
                                        </div>
                                    </div>
                                    <progress class="uk-progress"
                                              value="0"
                                              max="100"
                                              hidden></progress>
                                </div>
                                <div class="uk-flex-1">
                                    <div class="form-item uk-flex-inline uk-flex-middle uk-margin-right">
                                        <label class="uk-form-label">
                                            @lang('forms.surname')
                                        </label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input"
                                                   type="text"
                                                   name="last_name"
                                                   placeholder=""
                                                   id="{{ $_form }}-last_name"
                                                   autocomplete="off"
                                                   value="{{ $item->_profile->last_name }}">
                                        </div>
                                    </div>
                                    <div class="form-item uk-flex-inline uk-flex-middle uk-margin-right">
                                        <label class="uk-form-label">
                                            @lang('forms.name')
                                        </label>
                                        <div class="uk-form-controls">
                                            <input class="uk-input"
                                                   type="text"
                                                   name="first_name"
                                                   placeholder=""
                                                   id="{{ $_form }}-first_name"
                                                   autocomplete="off"
                                                   value="{{ $item->_profile->first_name }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-item email uk-flex-inline uk-flex-middle uk-margin-right">
                                <label class="uk-form-label">@lang('forms.password')</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input"
                                           type="password"
                                           name="password"
                                           placeholder=""
                                           id="{{ $_form }}-password"
                                           autocomplete="off"
                                           value="">
                                </div>
                                <label class="uk-form-label">@lang('forms.repeat')</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input"
                                           type="password"
                                           name="password_confirmation"
                                           id="{{ $_form }}-password_confirmation"
                                           placeholder=""
                                           autocomplete="off"
                                           value="">
                                </div>
                            </div>
                            <div class="form-item email uk-flex-inline uk-flex-middle">
                                <label class="uk-form-label uk-visible@s">@lang('forms.phone')</label>
                                <label class="uk-form-label uk-hidden@s">Тел.:</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input phone-mask"
                                           type="text"
                                           name="phone"
                                           placeholder=""
                                           id="{{ $_form }}-phone"
                                           autocomplete="off"
                                           value="{{ $item->_profile->phone }}">
                                </div>
                            </div>
                            <div class="form-item email uk-flex-inline uk-flex-middle uk-margin-top">
                                <label class="uk-form-label">E-mail:</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input"
                                           type="text"
                                           name="email"
                                           id="{{ $_form }}-email"
                                           placeholder=""
                                           autocomplete="off"
                                           value="{{ $item->email }}">
                                </div>
                            </div>
                            <div class="form-item email uk-flex-inline uk-flex-middle uk-margin-top uk-margin-left">
                                <label class="uk-form-label">@lang('forms.city')</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input"
                                           type="text"
                                           name="city_delivery"
                                           id="{{ $_form }}-city"
                                           placeholder=""
                                           autocomplete="off"
                                           value="{{ $item->_profile->city_delivery }}">
                                </div>
                            </div>
                            <div class="uk-flex uk-margin-top user-edit">
                                <div class="form-item uk-flex-1 uk-flex uk-flex-middle uk-margin-right">
                                    <label class="uk-form-label">@lang('forms.address_delivery')</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input"
                                               type="text"
                                               name="address_delivery"
                                               placeholder=""
                                               id="{{ $_form }}-address"
                                               autocomplete="off"
                                               value="{{ $item->_profile->address_delivery }}">
                                    </div>
                                </div>
                                <button class="uk-button use-ajax"
                                        type="submit">
                                    @lang('forms.edit')
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            @include('auth.account.partials.box_my_desires')
            @include('auth.account.partials.box_my_orders')
			<div class="block-load uk-position-fixed">
            <div class="uk-position-center loading-img">
                <img src="{{ formalize_path('template/img/loading.gif') }}"
                     alt="">
            </div>
        </div>
        </div>
    </article>
@endsection

@extends('mail.mail')

@section('body')
    <h1 style="font-weight: 500; font-size: 1.3em;">
        Добрый день!
    </h1>
    <p>
        Ваш адрес электронной почты был указан при регистрации на сайте <a href="{{ $site_url }}"
                                                                           target="_blank">{{ $site_name }}</a>.
    </p>
    <p>
        Для активации аккаунта и продолжения работы с сайтом перейдите по ссылке ниже.
    </p>
    <div style="text-align: center; margin-top: 15px;">
        <a href="{{ $site_url . $activate_link }}"
           style="text-transform: uppercase; font-size: 1.125em;"
           target="_blank">
            Активировать аккаунт
        </a>
    </div>
@endsection
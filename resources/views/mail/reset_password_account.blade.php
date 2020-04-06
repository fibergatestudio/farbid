@extends('mail.mail')

@section('body')
    <h1 style="font-weight: 500; font-size: 1.3em;">
        Добрый день!
    </h1>
    <p>
        Мы получили запрос на сброс пароля для вашей учетной записи <a href="{{ $site_url }}"
                                                                           target="_blank">{{ $site_name }}</a>.
    </p>
    <div style="text-align: center; margin-top: 15px;">
        <a href="{{ $site_url . $reset_link }}"
           style="text-transform: uppercase; font-size: 1.125em;"
           target="_blank">
            Сбросить пароля
        </a>
    </div>
    <p>Если Вы не посылали запрос на смену пароля, проигнорируйте данное сообщение.</p>
@endsection
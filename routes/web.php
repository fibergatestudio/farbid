<?php

    Route::get('dev', 'Dev\DevelController@index');

    // Auth
    Auth::routes();

    // Site Map
    Route::get('sitemap.xml', 'Oleus\SiteMapController@generate');

    // Account
    Route::get('account', 'Auth\AccountController@index');
    Route::get('account/activate/{token?}', 'Auth\AccountController@activate')
        ->name('account.activate');

    // Other
    Route::match([
        'get',
        'post'
    ], '{language?}/{location?}/{path?}', 'QueryPathController@index')
        ->where([
            'path' => '^(?!images|oleus|ajax).*?'
        ]);
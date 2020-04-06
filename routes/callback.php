<?php

    // Upload file
    Route::post('file-upload', [
        'as'   => 'ajax.file.upload',
        'uses' => 'FileController@upload'
    ]);

    // Updated file
    Route::post('file-update', [
        'as'   => 'ajax.file.update',
        'uses' => 'FileController@update'
    ]);

    // Remove file
    Route::post('file-remove', [
        'as'   => 'ajax.file.remove',
        'uses' => 'FileController@remove'
    ]);

    // Account form in modal (login/register/reset password)
    Route::post('show-account-form', [
        'as'   => 'ajax.show_account_form',
        'uses' => 'AjaxController@show_account_form'
    ]);

    Route::post('account-edit-form', [
        'as'   => 'ajax.account_edit_form',
        'uses' => 'AjaxController@submit_account_edit_form'
    ]);

    Route::post('account-avatar-upload', [
        'as'   => 'ajax.account_avatar_upload',
        'uses' => 'AjaxController@upload_account_avatar'
    ]);

    Route::post('account-log-in', [
        'as'   => 'ajax.account_log_in',
        'uses' => 'AjaxController@submit_account_login'
    ]);

    // Shop
    Route::post('product-desires', [
        'as'   => 'ajax.shop.product_desires',
        'uses' => 'AjaxController@add_product_desires'
    ]);

    Route::post('basket', [
        'as'   => 'ajax.shop.basket',
        'uses' => 'AjaxController@submit_shop_basket_action'
    ]);

    Route::post('issue-order', [
        'as'   => 'ajax.shop.order',
        'uses' => 'AjaxController@submit_shop_order'
    ]);

    Route::post('buy-one-click', [
        'as'   => 'ajax.shop.buy_one_click.form',
        'uses' => 'AjaxController@submit_shop_but_one_click'
    ]);

    Route::post('liqpay-callback', [
        'as'   => 'ajax.shop.liqpay_callback',
        'uses' => 'AjaxController@liqPay_shop_callback'
    ]);

    Route::post('nova-poshta', [
        'as'   => 'np.ajax',
        'uses' => 'AjaxController@np_ajax'
    ]);

    Route::post('repeat-order', [
        'as'   => 'ajax.shop.repeat_order',
        'uses' => 'AjaxController@repeat_order'
    ]);

    Route::post('view-orders', [
        'as'   => 'ajax.shop.view_orders',
        'uses' => 'AjaxController@view_orders'
    ]);

    // Search
    Route::post('search', [
        'as'   => 'ajax.search',
        'uses' => 'AjaxController@submit_search'
    ]);

    Route::post('search-history', [
        'as'   => 'ajax.search.history',
        'uses' => 'AjaxController@add_to_search_history'
    ]);

    // Language
    Route::post('language/{language}', [
        'as'   => 'language.selected',
        'uses' => 'AjaxController@selected_language'
    ]);

    // Subscribe
    Route::post('subscribe-form', [
        'as'   => 'ajax.subscribe.form',
        'uses' => 'AjaxController@submit_subscribe_application'
    ]);

    // Count down
    Route::post('count-down', [
        'as'   => 'ajax.count_down',
        'uses' => 'AjaxController@count_down'
    ]);

    /**
     * -
     * -
     * -
     * -
     * -
     * -
     */

    // Choice location
    Route::post('choice-location', [
        'as'   => 'ajax.choice_location.form',
        'uses' => 'LocationController@form'
    ]);

    // Clear cache
    Route::post('clear-cache', [
        'as'   => 'ajax.clear_cache',
        'uses' => 'AjaxController@clear_cache'
    ]);

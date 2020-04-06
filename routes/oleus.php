<?php

    // Dashboard
    Route::get('/', [
        'as'   => 'oleus.front_page',
        'uses' => 'MainController@index'
    ]);

    // Fields
    Route::match([
        'post'
    ], 'callback/fields/{type}/{action}', [
        'as'   => 'oleus.fields.item',
        'uses' => 'FieldsController@field'
    ]);

    // Users
    Route::resource('users', 'UserController', [
        'names'  => [
            'index'   => 'oleus.users',
            'create'  => 'oleus.users.create',
            'update'  => 'oleus.users.update',
            'store'   => 'oleus.users.store',
            'edit'    => 'oleus.users.edit',
            'destroy' => 'oleus.users.destroy'
        ],
        'except' => [
            'show'
        ]
    ]);

    // Permissions
    Route::resource('permissions', 'PermissionController', [
        'names'  => [
            'index' => 'oleus.permissions',
        ],
        'except' => [
            'show',
            'create',
            'update',
            'store',
            'edit',
            'destroy'
        ]
    ]);

    // Roles
    Route::resource('roles', 'RoleController', [
        'names'  => [
            'index'   => 'oleus.roles',
            'create'  => 'oleus.roles.create',
            'update'  => 'oleus.roles.update',
            'store'   => 'oleus.roles.store',
            'edit'    => 'oleus.roles.edit',
            'destroy' => 'oleus.roles.destroy'
        ],
        'except' => [
            'show'
        ]
    ]);

    // Menu
    Route::resource('menus', 'MenuController', [
        'names'  => [
            'index'   => 'oleus.menus',
            'create'  => 'oleus.menus.create',
            'edit'    => 'oleus.menus.edit',
            'update'  => 'oleus.menus.update',
            'store'   => 'oleus.menus.store',
            'destroy' => 'oleus.menus.destroy',
        ],
        'except' => [
            'show'
        ]
    ]);
    Route::post('callback/menus/link', [
        'as'   => 'oleus.menus.link',
        'uses' => 'MenuController@link'
    ]);
    Route::match([
        'delete',
        'post'
    ], 'callback/menus/item/{menu}/{action}/{id?}', [
        'as'   => 'oleus.menus.item',
        'uses' => 'MenuController@item'
    ]);

    // Pages
    Route::resource('pages', 'PageController', [
        'names'  => [
            'index'   => 'oleus.pages',
            'create'  => 'oleus.pages.create',
            'update'  => 'oleus.pages.update',
            'store'   => 'oleus.pages.store',
            'edit'    => 'oleus.pages.edit',
            'destroy' => 'oleus.pages.destroy'
        ],
        'except' => [
            'show'
        ]
    ]);
    Route::post('pages/relation', [
        'as'   => 'oleus.pages.relation',
        'uses' => 'PageController@relation'
    ]);

    // Nodes
    Route::resource('nodes', 'NodeController', [
        'names'  => [
            'index'   => 'oleus.nodes',
            'create'  => 'oleus.nodes.create',
            'update'  => 'oleus.nodes.update',
            'store'   => 'oleus.nodes.store',
            'edit'    => 'oleus.nodes.edit',
            'destroy' => 'oleus.nodes.destroy'
        ],
        'except' => [
            'show'
        ]
    ]);
    Route::post('nodes/relation', [
        'as'   => 'oleus.nodes.relation',
        'uses' => 'NodeController@relation'
    ]);

    // Blocks
    Route::resource('blocks', 'BlockController', [
        'names'  => [
            'index'   => 'oleus.blocks',
            'create'  => 'oleus.blocks.create',
            'update'  => 'oleus.blocks.update',
            'store'   => 'oleus.blocks.store',
            'edit'    => 'oleus.blocks.edit',
            'destroy' => 'oleus.blocks.destroy'
        ],
        'except' => [
            'show'
        ]
    ]);
    Route::post('blocks/relation', [
        'as'   => 'oleus.blocks.relation',
        'uses' => 'BlockController@relation'
    ]);

    // Banners
    Route::resource('banners', 'BannerController', [
        'names'  => [
            'index'   => 'oleus.banners',
            'create'  => 'oleus.banners.create',
            'update'  => 'oleus.banners.update',
            'store'   => 'oleus.banners.store',
            'edit'    => 'oleus.banners.edit',
            'destroy' => 'oleus.banners.destroy'
        ],
        'except' => [
            'show'
        ]
    ]);

    // Advantages
    Route::resource('advantages', 'AdvantageController', [
        'names'  => [
            'index'   => 'oleus.advantages',
            'create'  => 'oleus.advantages.create',
            'update'  => 'oleus.advantages.update',
            'store'   => 'oleus.advantages.store',
            'edit'    => 'oleus.advantages.edit',
            'destroy' => 'oleus.advantages.destroy'
        ],
        'except' => [
            'show'
        ]
    ]);
    Route::match([
        'delete',
        'post'
    ], 'callback/advantages/item/{advantages}/{action}/{id?}', [
        'as'   => 'oleus.advantages.item',
        'uses' => 'AdvantageController@item'
    ]);
    Route::post('advantages/relation', [
        'as'   => 'oleus.advantages.relation',
        'uses' => 'AdvantageController@relation'
    ]);

    // Sliders
    Route::resource('sliders', 'SlidersController', [
        'names'  => [
            'index'   => 'oleus.sliders',
            'create'  => 'oleus.sliders.create',
            'update'  => 'oleus.sliders.update',
            'store'   => 'oleus.sliders.store',
            'edit'    => 'oleus.sliders.edit',
            'destroy' => 'oleus.sliders.destroy'
        ],
        'except' => [
            'show'
        ]
    ]);
    Route::match([
        'delete',
        'post'
    ], 'callback/sliders/item/{slider}/{action}/{id?}', [
        'as'   => 'oleus.sliders.item',
        'uses' => 'SlidersController@item'
    ]);
    Route::post('sliders/relation', [
        'as'   => 'oleus.sliders.relation',
        'uses' => 'SlidersController@relation'
    ]);

    // Settings
    Route::match([
        'get',
        'post'
    ], 'settings/{setting}', [
        'as'   => 'oleus.settings',
        'uses' => 'SettingsController@_view'
    ]);
    Route::post('settings/translate/{setting}', [
        'as'   => 'oleus.settings.translate',
        'uses' => 'SettingsController@_translate'
    ]);
    Route::match([
        'get',
        'post'
    ], 'settings/modal_{setting}', [
        'as'   => 'oleus.modal.settings',
        'uses' => 'SettingsController@_view'
    ]);

    // Variables
    Route::resource('variables', 'VariablesController', [
        'names'  => [
            'index'   => 'oleus.variables',
            'create'  => 'oleus.variables.create',
            'edit'    => 'oleus.variables.edit',
            'update'  => 'oleus.variables.update',
            'store'   => 'oleus.variables.store',
            'destroy' => 'oleus.variables.destroy',
        ],
        'except' => [
            'show'
        ]
    ]);
    Route::post('variables/relation', [
        'as'   => 'oleus.variables.relation',
        'uses' => 'VariablesController@relation'
    ]);

    // Services
    Route::resource('services', 'Services\ServicesController', [
        'names'  => [
            'index'   => 'oleus.services',
            'create'  => 'oleus.services.create',
            'update'  => 'oleus.services.update',
            'store'   => 'oleus.services.store',
            'edit'    => 'oleus.services.edit',
            'destroy' => 'oleus.services.destroy'
        ],
        'except' => [
            'show'
        ]
    ]);
    Route::post('callback/services/prices/{service}/{id?}', [
        'as'   => 'oleus.services.prices',
        'uses' => 'Services\ServicesController@prices'
    ]);
    Route::get('services/relation-location/{entity_id}/{city_id}', [
        'as'   => 'oleus.services.relation_location',
        'uses' => 'Services\ServicesController@relation'
    ]);

    // ServiceOrders
    Route::resource('service-orders', 'Services\OrdersController', [
        'names'  => [
            'index'   => 'oleus.service_orders',
            'show'    => 'oleus.service_orders.show',
            'destroy' => 'oleus.service_orders.destroy'
        ],
        'except' => [
            'update',
            'edit',
            'store',
            'create'
        ]
    ]);

    // Reviews
    Route::resource('reviews', 'ReviewController', [
        'names'  => [
            'index'   => 'oleus.reviews',
            'create'  => 'oleus.reviews.create',
            'edit'    => 'oleus.reviews.edit',
            'update'  => 'oleus.reviews.update',
            'store'   => 'oleus.reviews.store',
            'destroy' => 'oleus.reviews.destroy'
        ],
        'except' => [
            'show',
        ]
    ]);

    // Callback
    Route::resource('callbacks', 'CallbackController', [
        'names'  => [
            'show'    => 'oleus.callbacks.show',
            'destroy' => 'oleus.callbacks.destroy'
        ],
        'except' => [
            'index',
            'update',
            'edit',
            'store',
            'create'
        ]
    ]);
    Route::get('callbacks', [
        'as'   => 'oleus.callbacks',
        'uses' => 'CallbackController@callback'
    ]);
    Route::get('complaint', [
        'as'   => 'oleus.complaint',
        'uses' => 'CallbackController@complaint'
    ]);

    // Shop params
    Route::resource('shop-params', 'Shop\ParamsController', [
        'names'  => [
            'index'   => 'oleus.shop_params',
            'create'  => 'oleus.shop_params.create',
            'edit'    => 'oleus.shop_params.edit',
            'update'  => 'oleus.shop_params.update',
            'store'   => 'oleus.shop_params.store',
            'destroy' => 'oleus.shop_params.destroy',
        ],
        'except' => [
            'show'
        ]
    ]);
    Route::match([
        'delete',
        'post'
    ], 'callback/shop-params/item/{param}/{action}/{id?}', [
        'as'   => 'oleus.shop_params.item',
        'uses' => 'Shop\ParamsController@item'
    ]);

    // Shop category
    Route::resource('shop-categories', 'Shop\CategoryController', [
        'names'  => [
            'index'   => 'oleus.shop_categories',
            'create'  => 'oleus.shop_categories.create',
            'edit'    => 'oleus.shop_categories.edit',
            'update'  => 'oleus.shop_categories.update',
            'store'   => 'oleus.shop_categories.store',
            'destroy' => 'oleus.shop_categories.destroy',
        ],
        'except' => [
            'show'
        ]
    ]);
    Route::post('shop-categories/relation', [
        'as'   => 'oleus.shop_categories.relation',
        'uses' => 'Shop\CategoryController@relation'
    ]);
    Route::post('shop-categories/relation-param', [
        'as'   => 'oleus.shop_categories.relation_param',
        'uses' => 'Shop\CategoryController@relation_param'
    ]);

    // Shop filter page
    Route::resource('shop-filter-pages', 'Shop\FilterPageController', [
        'names'  => [
            'index'   => 'oleus.shop_filter_pages',
            'update'  => 'oleus.shop_filter_pages.update',
            'edit'    => 'oleus.shop_filter_pages.edit',
            'destroy' => 'oleus.shop_filter_pages.destroy'
        ],
        'except' => [
            'show',
            'store',
            'create'
        ]
    ]);

    // Shop order
    Route::resource('shop-orders', 'Shop\OrderController', [
        'names'  => [
            'index'   => 'oleus.shop_orders',
            'update'  => 'oleus.shop_orders.update',
            'edit'    => 'oleus.shop_orders.edit',
            'destroy' => 'oleus.shop_orders.destroy'
        ],
        'except' => [
            'show',
            'store',
            'create'
        ]
    ]);

    // Shop products
    Route::resource('shop-products', 'Shop\ProductsController', [
        'names'  => [
            'index'   => 'oleus.shop_products',
            'create'  => 'oleus.shop_products.create',
            'edit'    => 'oleus.shop_products.edit',
            'update'  => 'oleus.shop_products.update',
            'store'   => 'oleus.shop_products.store',
            'destroy' => 'oleus.shop_products.destroy',
        ],
        'except' => [
            'show'
        ]
    ]);
    Route::post('shop-products/relation', [
        'as'   => 'oleus.shop_products.relation',
        'uses' => 'Shop\ProductsController@relation'
    ]);
    Route::post('shop-products/params/{item?}', [
        'as'   => 'oleus.shop_products.params',
        'uses' => 'Shop\ProductsController@params'
    ]);
    Route::post('shop-products/modify/{item?}', [
        'as'   => 'oleus.shop_products.modify',
        'uses' => 'Shop\ProductsController@modify'
    ]);
    Route::post('shop-products/modify-relation-item/{item}', [
        'as'   => 'oleus.shop_products.modify_relation_item',
        'uses' => 'Shop\ProductsController@modify_relation_item'
    ]);
    Route::post('shop-products/modify-remove/{item}', [
        'as'   => 'oleus.shop_products.modify_remove',
        'uses' => 'Shop\ProductsController@modify_remove'
    ]);
    Route::post('shop-products/related-product/{item}/{action?}/{id?}', [
        'as'   => 'oleus.shop_products.related_product',
        'uses' => 'Shop\ProductsController@related_product'
    ]);
    Route::post('shop-products/product_groups/{item}/{action?}/{id?}', [
        'as'   => 'oleus.shop_products.product_groups',
        'uses' => 'Shop\ProductsController@product_groups'
    ]);
    Route::post('shop-products/get-products', [
        'as'   => 'oleus.shop_products.get_products',
        'uses' => 'Shop\ProductsController@get_products'
    ]);

    // Shop form buy one click
    Route::resource('shop-buy-one-click', 'Shop\BuyOneClickController', [
        'names'  => [
            'index'   => 'oleus.shop_products_form_buy_one_click',
            'edit'    => 'oleus.shop_products_form_buy_one_click.edit',
            'update'  => 'oleus.shop_products_form_buy_one_click.update',
            'destroy' => 'oleus.shop_products_form_buy_one_click.destroy'
        ],
        'except' => [
            'show',
            'store',
            'create'
        ]
    ]);

// Application
Route::resource('application', 'ApplicationController', [
    'names'  => [
        'index'   => 'oleus.form_application',
        'edit'    => 'oleus.form_application.edit',
        'update'  => 'oleus.form_application.update',
        'destroy' => 'oleus.form_application.destroy'
    ],
    'except' => [
        'show',
        'store',
        'create'
    ]
]);

// Message
Route::resource('message', 'MessageController', [
    'names'  => [
        'index'   => 'oleus.message',
        'edit'    => 'oleus.message.edit',
        'update'  => 'oleus.message.update',
        'destroy' => 'oleus.message.destroy'
    ],
    'except' => [
        'show',
        'store',
        'create'
    ]
]);

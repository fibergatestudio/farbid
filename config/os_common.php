<?php

    return [
        'users'   => [
            'registration'   => TRUE,
            'reset_password' => TRUE,
        ],
        'styles'  => [
            [
                'url'     => '//fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&amp;subset=cyrillic',
                'in_head' => TRUE,
            ],
            [
                'url'     => 'components/uikit/dist/css/uikit.min.css',
                'in_head' => TRUE,
            ],
            [
                'url'       => 'components/select2/dist/css/select2.min.css',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/easy.autocomplete/dist/easy-autocomplete.min.css',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/air-datepicker/dist/css/datepicker.min.css',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/codemirror/lib/codemirror.css',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/codemirror/theme/idea.css',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/codemirror/addon/display/fullscreen.css',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/sweetalert2/dist/sweetalert2.min.css',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'css/app.css',
                'in_footer' => TRUE,
            ]
        ],
        'scripts' => [
            [
                'url'     => 'components/jquery/dist/jquery.min.js',
                'in_head' => TRUE,
            ],
            [
                'url'     => 'components/uikit/dist/js/uikit.min.js',
                'in_head' => TRUE,
            ],
            [
                'url'     => 'components/uikit/dist/js/uikit-icons.min.js',
                'in_head' => TRUE,
            ],
            [
                'url'       => 'components/select2/dist/js/select2.full.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/ckeditor_sdk/vendor/ckeditor/ckeditor.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/air-datepicker/dist/js/datepicker.min.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/easy.autocomplete/dist/jquery.easy-autocomplete.min.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/codemirror/lib/codemirror.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/codemirror/addon/selection/active-line.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/codemirror/mode/xml/xml.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/codemirror/mode/javascript/javascript.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/codemirror/addon/display/fullscreen.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/sweetalert2/dist/sweetalert2.min.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/upload.file/upload.file.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'components/use.ajax/use.ajax.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'js/app.js',
                'in_footer' => TRUE,
            ],
        ],
        'menu'    => [
            [
                'link'       => 'link_admin_menu_front',
                'route'      => 'oleus',
                'params'     => [],
                'permission' => 'access_dashboard'
            ],
            [
                'link'       => 'link_admin_menu_users_and_roles',
                'route'      => NULL,
                'permission' => [
                    'read_roles',
                    'read_users'
                ],
                'children'   => [
                    [
                        'link'       => 'link_admin_menu_users',
                        'route'      => 'oleus.users',
                        'params'     => [],
                        'permission' => 'read_users'
                    ],
                    [
                        'link'       => 'link_admin_menu_roles',
                        'route'      => 'oleus.roles',
                        'params'     => [],
                        'permission' => 'read_roles'
                    ]
                ]
            ],
            [
                'link'       => 'link_admin_menu_structure',
                'route'      => NULL,
                'permission' => [
                    'read_blocks',
                    'read_menus',
                    'read_pages',
                    'read_nodes',
                    'read_landing',
                    'read_product',
                    'read_history',
                    'read_i18n',
                    'read_services',
                    'read_variables',
                    'read_sliders',
                ],
                'children'   => [
//                    [
//                        'link'       => 'link_admin_menu_advantage',
//                        'route'      => 'oleus.advantages',
//                        'params'     => [],
//                        'permission' => 'read_advantages'
//                    ],
//                    [
//                        'link'       => 'link_admin_menu_block',
//                        'route'      => 'oleus.blocks',
//                        'params'     => [],
//                        'permission' => 'read_blocks'
//                    ],
//                    [
//                        'link'       => 'link_admin_menu_banner',
//                        'route'      => 'oleus.banners',
//                        'params'     => [],
//                        'permission' => 'read_banners'
//                    ],
                    [
                        'link'       => 'link_admin_menu_menu',
                        'route'      => 'oleus.menus',
                        'params'     => [],
                        'permission' => 'read_menus'
                    ],
                    [
                        'link'       => 'link_admin_menu_pages',
                        'route'      => 'oleus.pages',
                        'params'     => [],
                        'permission' => 'read_pages'
                    ],
                    [
                        'link'       => 'link_admin_menu_nodes',
                        'route'      => 'oleus.nodes',
                        'params'     => [],
                        'permission' => 'read_nodes'
                    ],
                    [
                        'link'       => 'link_admin_menu_sliders',
                        'route'      => 'oleus.sliders',
                        'params'     => [],
                        'permission' => 'read_sliders'
                    ],
                    //                    [
                    //                        'link'       => 'link_admin_menu_services',
                    //                        'route'      => 'oleus.services',
                    //                        'params'     => [],
                    //                        'permission' => 'read_services'
                    //                    ],
                    [
                        'link'       => 'link_admin_menu_variables',
                        'route'      => 'oleus.variables',
                        'params'     => [],
                        'permission' => 'read_variables'
                    ]
                ]
            ],
            [
                'link'       => 'link_admin_menu_shop',
                'route'      => NULL,
                'permission' => [
                    'read_shop_params',
                    'read_shop_categories',
                    'read_shop_products'
                ],
                'children'   => [
                    [
                        'link'       => 'link_admin_menu_shop_category',
                        'route'      => 'oleus.shop_categories',
                        'params'     => [],
                        'permission' => 'read_shop_categories'
                    ],
//                    [
//                        'link'       => 'link_admin_menu_shop_filter_pages',
//                        'route'      => 'oleus.shop_filter_pages',
//                        'params'     => [],
//                        'permission' => 'read_shop_categories'
//                    ],
                    [
                        'link'       => 'link_admin_menu_shop_params',
                        'route'      => 'oleus.shop_params',
                        'params'     => [],
                        'permission' => 'read_shop_params'
                    ],
                    [
                        'link'       => 'link_admin_menu_shop_products',
                        'route'      => 'oleus.shop_products',
                        'params'     => [],
                        'permission' => 'read_shop_products'
                    ],
                ]
            ],
            [
                'link'       => 'link_admin_menu_forms',
                'route'      => NULL,
                'permission' => [
                    'read_service_orders',
                    'read_callback',
                    'read_applications',
                    'read_shop_buy_one_click',
                    'read_shop_order',
                ],
                'children'   => [
                    //                    [
                    //                        'link'       => 'link_admin_menu_service_orders',
                    //                        'route'      => 'oleus.service_orders',
                    //                        'params'     => [],
                    //                        'permission' => 'read_service_orders'
                    //                    ],
//                    [
//                        'link'       => 'link_admin_menu_callbacks',
//                        'route'      => 'oleus.callbacks',
//                        'params'     => [],
//                        'permission' => 'read_callback'
//                    ],
                    [
                        'link'       => 'link_shop_buy_one_click',
                        'route'      => 'oleus.shop_products_form_buy_one_click',
                        'params'     => [],
                        'permission' => 'read_shop_buy_one_click'
                    ],
                    [
                        'link'       => 'link_shop_orders',
                        'route'      => 'oleus.shop_orders',
                        'params'     => [],
                        'permission' => 'read_shop_order'
                    ],
                    [
                        'link'       => 'link_admin_menu_applications',
                        'route'      => 'oleus.form_application',
                        'params'     => []
                    ],
//                    [
//                        'link'       => 'link_admin_menu_message',
//                        'route'      => 'oleus.message',
//                        'params'     => []
//                    ],
                    //                    [
                    //                        'link'       => 'link_admin_menu_reviews',
                    //                        'route'      => 'oleus.reviews',
                    //                        'params'     => [],
                    //                        'permission' => 'read_reviews'
                    //                    ],
                    //                    [
                    //                        'link'       => 'link_admin_menu_complaint',
                    //                        'route'      => 'oleus.complaint',
                    //                        'params'     => [],
                    //                        'permission' => 'read_callback'
                    //                    ],
                ]
            ],
            [
                'link'       => 'link_admin_menu_settings',
                'route'      => NULL,
                'permission' => [
                    'read_settings'
                ],
                'children'   => [
                    [
                        'link'   => 'link_admin_menu_settings_seo',
                        'route'  => 'oleus.settings',
                        'params' => ['setting' => 'seo']
                    ],
                    [
                        'link'   => 'link_admin_menu_settings_overall',
                        'route'  => 'oleus.settings',
                        'params' => ['setting' => 'overall']
                    ],
                    [
                        'link'   => 'link_admin_menu_settings_contacts',
                        'route'  => 'oleus.settings',
                        'params' => ['setting' => 'contacts'],
                    ],
                    [
                        'link'   => 'link_admin_menu_settings_languages',
                        'route'  => 'oleus.settings',
                        'params' => ['setting' => 'languages']
                    ],
                    [
                        'link'   => 'link_admin_menu_settings_currencies',
                        'route'  => 'oleus.settings',
                        'params' => ['setting' => 'currencies']
                    ],
                    [
                        'link'   => 'link_admin_menu_settings_delivery',
                        'route'  => 'oleus.settings',
                        'params' => ['setting' => 'delivery']
                    ],
                    [
                        'link'   => 'link_admin_menu_settings_payment',
                        'route'  => 'oleus.settings',
                        'params' => ['setting' => 'payment']
                    ],
                ]
            ]
        ]
    ];


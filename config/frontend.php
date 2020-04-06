<?php

    return [
        'styles'  => [
            [
                'url'     => 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700|Roboto+Slab:300,400,700|Roboto:100,300,400,500,700,900',
                'in_head' => TRUE,
            ],
            [
                'url'     => 'template/css/uikit.min.css',
                'in_head' => TRUE,
            ],
            [
                'url'     => 'template/css/main.css',
                'in_head' => TRUE,
            ],
            [
                'url'     => 'template/css/media.css',
                'in_head' => TRUE,
            ],
            [
                'url'       => 'template/css/custom.css',
                'in_footer' => TRUE,
            ]
        ],
        'scripts' => [
            [
                'url'     => 'components/jquery/dist/jquery.min.js',
                'in_head' => TRUE,
            ],
            [
                'url'     => 'template/js/uikit.min.js',
                'in_head' => TRUE,
            ],
            [
                'url'     => 'template/js/uikit-icons.min.js',
                'in_head' => TRUE,
            ],
            // [
            //     'url'       => 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCyxfMcmO5T3dHgZa3-KVwix79wWIRLwGk',
            //     'in_footer' => TRUE,
            // ],
            [
                'url'       => 'template/js/jquery.downCount.js',
                'in_footer' => TRUE,
            ],
            [
                'url'        => 'components/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js',
                'in_footer'  => TRUE,

            ],
            //            [
            //                'url'       => 'components/shop/shop.js',
            //                'in_footer' => TRUE,
            //            ],
            [
                'url'        => 'template/js/clipboard.min.js',
                'in_footer'  => TRUE,
                'attributes' => [
                    'async' => TRUE
                ]
            ],
            [
                'url'        => 'template/js/custom.js',
                'in_footer'  => TRUE,
                'attributes' => [
                    'async' => TRUE
                ]
            ],
			[
                'url'        => 'template/js/gmap3.min.js',
                'in_footer'  => TRUE
            ],
            [
                'url'        => 'components/use.ajax/use.ajax.js',
                'in_footer'  => TRUE,
                'attributes' => [
                    'async' => TRUE
                ]
            ],
            [
                'url'        => 'components/search.ajax/search.ajax.js',
                'in_footer'  => TRUE,
                'attributes' => [
                    'async' => TRUE
                ]
            ],
            [
                'url'       => 'template/js/main.js',
                'in_footer' => TRUE,
            ],
            [
                'url'       => 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCiVw6DuEIywH4EzzdbO1wWDBSdgH-PEUo&callback=init_maps',
                'in_footer' => TRUE,
            ],
            
        ],
    ];
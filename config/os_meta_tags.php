<?php

    return [
        'default'  => [
            'label'            => NULL,
            'item_separator'   => ' ',
            'option_separator' => ', '
        ],
        'items'    => [
            'color'               => [],
            'tsvetovaya_gruppa'   => [],
            'tsvet_skotch_brayta' => [],
            'temperatura_i_tsvet' => [],
            'zvet'                => [],
            'volume'              => [],
            'tip_0'               => [],
            'zerno'               => [],
            'razmer_kruga'        => [],
            'tip'                 => [],
            'osnova_rulona'       => [],
            'razmer_podlozhki'    => [],
            'osnova_poloski'      => [],
            'svoystva'            => [],
            'diametr_sopla'       => [],
            'tolshchina'          => [],
            'shirina'             => [],
            'dlina'               => [],
            'manufacturer'        => [],
            'country'             => [],
        ],
        'to_block' => [],
        'model'    => [
            'App\Models\ShopCategory' => [
                'ru' => [
                    'title'            => '{title} {options}',
                    'meta_title'       => '{title} {options} - купить недорого: цены, отзывы, характеристики',
                    'meta_description' => '{title} {options} ☰ FARBID.COM.UA ☰ интернет-магазин №1 материалов и оборудования для кузовного ремонта авто. У нас выгоднее! Доставка – Киев, Днепр, Запорожье, Харьков и вся Украина.',
                    'meta_keywords'    => NULL,
                ],
                'uk' => [
                    'title'            => '{title} {options}',
                    'meta_title'       => '{title} {options} - купити недорого: ціни, відгуки, характеристики',
                    'meta_description' => '{title} {options} ☰ FARBID.COM.UA ☰ інтернет-магазин №1 матеріалів і устаткування для кузовного ремонту авто. У нас вигідніше! Доставка - Київ, Дніпро, Запоріжжя, Харків і вся Україна.',
                    'meta_keywords'    => NULL,
                ]
            ]
        ],
    ];
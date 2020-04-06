<?php

    use App\Models\Advantage;
    use App\Models\Banner;
    use App\Models\Block;
    use App\Models\Menu;
    use App\Models\Node;
    use App\Models\Page;
    use App\Models\Service;
    use App\Models\ShopCategory;
    use App\Models\ShopFilterParamsPage;
    use App\Models\ShopProduct;
    use App\Models\Slider;

    /**
     * Page
     */
    if (!function_exists('page_load')) {
        function page_load($id, $language = DEFAULT_LANGUAGE)
        {
            if (is_numeric($id)) {
                $item = Page::related($id)
                    ->language($language)
                    ->location()
                    ->with([
                        '_alias',
                        '_background',
                    ])
                    ->remember(15)
                    ->first();
            } else {
                $item = Page::where('type', $id)
                    ->language($language)
                    ->location()
                    ->with([
                        '_alias',
                        '_background',
                    ])
                    ->remember(15)
                    ->first();
            }
            if ($item) $item->_load();

            return $item;
        }
    }

    /**
     * Node
     */
    if (!function_exists('node_load')) {
        function node_load($id, $language = DEFAULT_LANGUAGE)
        {
            $item = Node::related($id)
                ->language($language)
                ->location()
                ->with([
                    '_alias'
                ])
                ->first();
            if ($item) $item->_load();

            return $item;
        }
    }

    /**
     * Slider
     */
    if (!function_exists('slider_load')) {
        function slider_load($id, $options = [])
        {
            $_options = array_merge([
                'language' => DEFAULT_LANGUAGE,
                'theme'    => NULL,
            ], $options);
            $item = Slider::related($id)
                ->with([
                    '_items' => function ($q) {
                        $q->remember(15);
                    }
                ])
                ->language($_options['language'])
                ->location()
                ->remember(15)
                ->first();
            if ($item) {
                if (is_string($_options['theme']) && view()->exists($_options['theme'])) $item->template = $_options['theme'];

                return $item->_load();
            }

            return NULL;
        }
    }

    /**
     * Menu
     */
    if (!function_exists('menu_load')) {
        function menu_load($key, $options = [])
        {
            $_language = \Illuminate\Support\Facades\App::getLocale();
            $_options = array_merge([
                'theme' => NULL,
            ], $options);
            $item = Cache::remember("menu_{$key}_{$_language}", 0.34, function () use ($key) {
                return Menu::where('key', $key)
                    ->with([
                        '_items' => function ($q) {
                            $q->remember(15);
                        }
                    ])
                    ->active()
                    ->remember(15)
                    ->first();
            });
            if ($item) {
                if (is_string($_options['theme']) && view()->exists($_options['theme'])) $item->template = $_options['theme'];

                return $item->_load();
            }

            return NULL;
        }
    }

    /**
     * Currency
     */
    if (!function_exists('currency_load')) {
        function currency_load()
        {
            $currency = config('os_currency');
            $_choice_currency = Session::get('currency', $currency['default_currency']);
            $_response = [
                'all'     => NULL,
                'current' => NULL
            ];
            foreach ($currency['currencies'] as $_currency_key => $_currency_data) {
                if ($_currency_data['use']) {
                    if ($_currency_key == $_choice_currency) {
                        $_response['current'] = [
                            'key'            => $_currency_key,
                            'full_name'      => $_currency_data['full_name'],
                            'iso_code'       => $_currency_data['iso_code'],
                            'ratio'          => $_currency_data['ratio'],
                            'precision_mode' => $_currency_data['precision_mode'],
                            'precision'      => $_currency_data['precision'],
                            'prefix'         => $_currency_data['prefix'],
                            'suffix'         => $_currency_data['suffix'],
                        ];
                    }
                    $_currency_data['key'] = $_currency_key;
                    $_response['all'][$_currency_key] = $_currency_data;
                }
            }

            return $_response;
        }
    }

    /**
     * Shop category
     */
    if (!function_exists('shop_category_load')) {
        function shop_category_load($id, $language = DEFAULT_LANGUAGE)
        {
            $item = ShopCategory::related($id)
                ->language($language)
                ->location()
                ->with([
                    '_alias',
                    //                    '_filter_param_options',
                    //                    '_category_params',
                ])
//                ->remember(15)
                ->first();
            if ($item) $item->_load();

            return $item;
        }
    }

    /**
     *  Shop filter params page
     */
    if (!function_exists('shop_filter_params_load')) {
        function shop_filter_params_load($id, $language = DEFAULT_LANGUAGE)
        {
            $item = ShopFilterParamsPage::where('id', $id)
                ->with([
                    '_alias',
                    '_category'
                ])
                ->first();
            if ($item) $item->_load();

            return $item;
        }
    }

    /**
     * Shop product
     */
    if (!function_exists('shop_product_load')) {
        function shop_product_load($entity, $language = DEFAULT_LANGUAGE, $view = 'full')
        {
            if ($entity instanceof ShopProduct) {
                $_item = $entity;
            } elseif (is_numeric($entity)) {
                $item = ShopProduct::related($entity)
                    ->language($language)
                    ->location()
                    ->with([
                        '_alias',
                        '_preview',
                        '_background'
                    ])
                    ->remember(15)
                    ->first();
            }
            if ($item) $item->_load($view);

            return $item;
        }
    }

    /**
     * Contacts
     */
    if (!function_exists('contacts_load')) {
        function contacts_load($language, $location)
        {
            $contacts = config('os_contacts');
            $_choice_city = $location;
            $_choice_office = NULL;
            $_response = [
                'all'     => NULL,
                'current' => NULL,
                'social'  => NULL
            ];
            foreach ($contacts['social'] as $_name_social_network => $_link_social_network) {
                if ($_link_social_network) {
                    $_response['social'][$_name_social_network] = $_link_social_network;
                }
            }
            foreach ($contacts['cities'] as $_city_key => $city_data) {
                if ($_city_key == $_choice_city) {
                    $_choice_office = $_choice_office ? $_choice_office : $city_data[$language]['default_office'];
                    $_response['current'] = [
                        'id'                 => $_city_key,
                        'city'               => $city_data[$language]['city'],
                        'address'            => $city_data[$language]['offices'][$_choice_office]['address'],
                        'work_time_weekdays' => $city_data[$language]['offices'][$_choice_office]['work_time_weekdays'],
                        'work_time_saturday' => $city_data[$language]['offices'][$_choice_office]['work_time_saturday'],
                        'work_time_sunday'   => $city_data[$language]['offices'][$_choice_office]['work_time_sunday'],
                        'email'              => $city_data[$language]['offices'][$_choice_office]['email'],
                        'skype'              => $city_data[$language]['offices'][$_choice_office]['skype'],
                        'viber'              => $city_data[$language]['offices'][$_choice_office]['viber'],
                        'whatsapp'           => $city_data[$language]['offices'][$_choice_office]['whatsapp'],
                        'telegram'           => $city_data[$language]['offices'][$_choice_office]['telegram']
                    ];
                    if (isset($city_data[$language]['offices'][$_choice_office]['phones'])) {
                        foreach ($city_data[$language]['offices'][$_choice_office]['phones'] as $_phone_key => $_phone_data) {
                            $_response['current'][$_phone_key] = $_phone_data;
                        }
                    }
                }
                $_response['all'][$_city_key] = $city_data[$language];
            }

            return $_response;
        }
    }

    /****
     *
     */


    if (!function_exists('service_load')) {
        /**
         * @param      $id
         * @param null $language
         * @return null
         */
        function service_load($id, $language = NULL)
        {
            $language = $language ? $language : config('app.locale');
            $item = Service::find($id);

            return $item ? $item->_render() : NULL;
        }
    }

    if (!function_exists('block_load')) {
        /**
         * @param      $id
         * @param null $theme
         * @return null|stdClass
         */
        function block_load($id, $theme = NULL)
        {
            $item = Block::where('id', $id)
                ->orWhere('relation', $id)
                ->language()
                ->location()
                ->first();
            if ($item) {
                if (is_string($theme) && view()->exists($theme)) {
                    $item->theme = $theme;
                }
                if ($item = $item->_load()) {
                    return $item;
                }
            }

            return NULL;
        }
    }

    if (!function_exists('advantage_load')) {
        /**
         * @param      $id
         * @param null $theme
         * @return null|stdClass
         */
        function advantage_load($id, $theme = NULL)
        {
            $item = Advantage::where('id', $id)
                ->orWhere('relation', $id)
                ->language()
                ->location()
                ->first();
            if ($item) {
                if (is_string($theme) && view()->exists($theme)) {
                    $item->theme = $theme;
                }
                if ($item = $item->_load()) {
                    return $item;
                }
            }

            return NULL;
        }
    }


    if (!function_exists('banner_load')) {
        /**
         * @param      $id
         * @param null $theme
         * @return null|stdClass
         */
        function banner_load($id, $theme = NULL)
        {
            $item = Banner::where('id', $id)
                //            ->orWhere('relation', $id)
                //            ->language()
                //            ->location()
                ->first();
            if ($item) {
                if (is_string($theme) && view()->exists($theme)) {
                    $item->theme = $theme;
                }
                if ($item = $item->_load()) {
                    return $item;
                }
            }

            return NULL;
        }
    }


    if (!function_exists('search_load')) {
        /**
         * @return mixed
         */
        function search_load($language = NULL, $location = NULL)
        {
            return Page::where('type', 'search')
                ->with([
                    '_alias'
                ])
                ->language($language)
                ->location($location)
                ->first();
        }
    }

    if (!function_exists('shop_basket_load')) {
        /**
         * @return mixed
         */
        function shop_basket_load($language = NULL, $location = NULL)
        {
            return Page::where('type', 'shop_basket')
                ->with([
                    '_alias'
                ])
                ->language($language)
                ->location($location)
                ->first();
        }
    }


    if (!function_exists('basket_load')) {
        /**
         * @return array
         */
        function basket_load()
        {
            if ($_basket = Session::get('basket')) return $_basket;

            return NULL;
        }
    }

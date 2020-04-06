<?php

    use App\Models\Variable;
    use Illuminate\Container\Container;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Session;

    define('DEFAULT_LOCATION', config('os_contacts.default_city'));
    define('USE_SEVERAL_LOCATION', config('os_contacts.several_location'));
    define('DEFAULT_LANGUAGE', config('os_languages.locale'));
    define('USE_MULTI_LANGUAGE', config('os_languages.multi_language'));
    define('DEFAULT_CURRENCY', config('os_currency.default_currency'));
    define('USE_MULTI_CURRENCIES', config('os_currency.multi_currency'));

    if (!function_exists('wrap')) {
        /**
         * @param array $parameters
         * @return mixed
         */
        function wrap($parameters = [])
        {
            return Container::getInstance()->make('wrap', $parameters);
        }
    }

    if (!function_exists('request_additional_params')) {
        function request_additional_params($request)
        {
            $_response = [
                'language' => config('app.locale'),
                'location' => config('app.location'),
            ];
            $_route_language = $request->route('language');
            $_route_location = $request->route('location');
            if ($_route_language && is_language($_route_language)) {
                App::setLocale($_route_language);
                wrap()->set('locale', $_route_language);
                $_response['language'] = $_route_language;
            } elseif ($_session_language = Session::get('language', DEFAULT_LANGUAGE)) {
                App::setLocale($_session_language);
                wrap()->set('locale', $_session_language);
                $_response['language'] = $_session_language;
            } else {
                App::setLocale(DEFAULT_LANGUAGE);
                wrap()->set('locale', DEFAULT_LANGUAGE);
                $_response['language'] = DEFAULT_LANGUAGE;
            }
            if ($_route_location && is_location($_route_location)) {

            } elseif ($_session_location = Session::get('location')) {

            } else {
                wrap()->set('location', DEFAULT_LOCATION);
                $_response['location'] = DEFAULT_LOCATION;
            }

            return $_response;
        }
    }

    if (!function_exists('is_language')) {
        /**
         * @param $lang
         * @return bool
         */
        function is_language($lang)
        {
            $_languages = config('os_languages.languages');

            return isset($_languages[$lang]) ? TRUE : FALSE;
        }
    }

    if (!function_exists('is_location')) {
        /**
         * @param $location
         * @return bool
         */
        function is_location($location)
        {
            $_locations = config('os_contacts.cities');

            return isset($_locations[$location]) ? TRUE : FALSE;
        }
    }

    if (!function_exists('clear_html')) {
        /**
         * @param string $html
         * @return null|string
         */
        function clear_html($html)
        {
            preg_match_all('!(<(?:code|pre|script).*>[^<]+</(?:code|pre|script)>)!', $html, $pre);
            $html = preg_replace('!<(?:code|pre).*>[^<]+</(?:code|pre)>!', '#pre#', $html);
            $html = preg_replace('#<!–[^\[].+–>#', '', $html);
            $html = preg_replace('/[\r\n\t]+/', ' ', $html);
            $html = preg_replace('/ {2,}/', ' ', $html);
            $html = preg_replace('/>[\s]+</', '><', $html);
            $html = preg_replace('/[\s]+/', ' ', $html);
            if (!empty($pre[0])) {
                foreach ($pre[0] as $tag) {
                    $html = preg_replace('!#pre#!', $tag, $html, 1);
                }
            }

            return $html;
        }
    }

    if (!function_exists('variable')) {
        /**
         * @param $key
         * @variables $key
         * @return mixed
         */

        function variable($key, $variables = NULL)
        {
            $_variable = new Variable();
            $_response = $_variable->get($key, wrap()->get('locale'), wrap()->get('location'));

            if (is_array($variables)) {
                $_variables = [];
                foreach ($variables as $_variable_key => $_variable_value) if (is_string($_variable_key)) $_variables["@{$_variable_key}"] = $_variable_value;
                if ($_response && $_variables) $_response = strtr($_response, $_variables);
            }

            return $_response;
        }
    }

    if (!function_exists('transcription_string')) {
        /**
         * @param $string
         * @return string
         */
        function transcription_string($string)
        {
            $string = trim(strip_tags($string));
            $_transcription = [
                'й' => 'q',
                'ц' => 'w',
                'у' => 'e',
                'к' => 'r',
                'е' => 't',
                'н' => 'y',
                'г' => 'u',
                'ш' => 'i',
                'щ' => 'o',
                'з' => 'p',
                'х' => '[',
                'ъ' => ']',
                'ф' => 'a',
                'ы' => 's',
                'в' => 'd',
                'а' => 'f',
                'п' => 'g',
                'р' => 'h',
                'о' => 'j',
                'л' => 'k',
                'д' => 'l',
                'ж' => ';',
                'э' => '\'',
                'я' => 'z',
                'ч' => 'x',
                'с' => 'c',
                'м' => 'v',
                'и' => 'b',
                'т' => 'n',
                'ь' => 'm',
                'б' => ',',
                'ю' => '.',
                '.' => '/',
                'Й' => 'Q',
                'Ц' => 'W',
                'У' => 'E',
                'К' => 'R',
                'Е' => 'T',
                'Н' => 'Y',
                'Г' => 'U',
                'Ш' => 'I',
                'Щ' => 'O',
                'З' => 'P',
                'Х' => '{',
                'Ъ' => '}',
                'Ф' => 'A',
                'Ы' => 'S',
                'В' => 'D',
                'А' => 'F',
                'П' => 'G',
                'Р' => 'H',
                'О' => 'J',
                'Л' => 'K',
                'Д' => 'L',
                'Ж' => ':',
                'Э' => '"',
                'Я' => 'Z',
                'Ч' => 'X',
                'С' => 'C',
                'М' => 'V',
                'И' => 'B',
                'Т' => 'N',
                'Ь' => 'M',
                'Б' => '<',
                'Ю' => '>',
                ',' => '?'
            ];
            if (preg_match('/[A-z]+/i', $string)) $_transcription = array_flip($_transcription);

            return strtr($string, $_transcription);
        }
    }

    //    if(!function_exists('transliteration')) {
    //        /**
    //         * @param        $string
    //         * @param string $pattern
    //         * @return mixed|null|string|string[]
    //         */
    //        function transliteration($string, $pattern = '-')
    //        {
    //            $converter = [
    //                'а' => 'a',
    //                'б' => 'b',
    //                'в' => 'v',
    //                'г' => 'g',
    //                'д' => 'd',
    //                'е' => 'e',
    //                'ё' => 'e',
    //                'ж' => 'zh',
    //                'з' => 'z',
    //                'и' => 'i',
    //                'й' => 'y',
    //                'к' => 'k',
    //                'л' => 'l',
    //                'м' => 'm',
    //                'н' => 'n',
    //                'о' => 'o',
    //                'п' => 'p',
    //                'р' => 'r',
    //                'с' => 's',
    //                'т' => 't',
    //                'у' => 'u',
    //                'ф' => 'f',
    //                'х' => 'h',
    //                'ц' => 'c',
    //                'ч' => 'ch',
    //                'ш' => 'sh',
    //                'щ' => 'sch',
    //                'ь' => '',
    //                'ы' => 'y',
    //                'ъ' => '',
    //                'э' => 'e',
    //                'ю' => 'yu',
    //                'я' => 'ya',
    //                'і' => 'i',
    //                'ї' => 'yi',
    //                'є' => 'e',
    //                'А' => 'A',
    //                'Б' => 'B',
    //                'В' => 'V',
    //                'Г' => 'G',
    //                'Д' => 'D',
    //                'Е' => 'E',
    //                'Ё' => 'E',
    //                'Ж' => 'Zh',
    //                'З' => 'Z',
    //                'И' => 'I',
    //                'Й' => 'Y',
    //                'К' => 'K',
    //                'Л' => 'L',
    //                'М' => 'M',
    //                'Н' => 'N',
    //                'О' => 'O',
    //                'П' => 'P',
    //                'Р' => 'R',
    //                'С' => 'S',
    //                'Т' => 'T',
    //                'У' => 'U',
    //                'Ф' => 'F',
    //                'Х' => 'H',
    //                'Ц' => 'C',
    //                'Ч' => 'Ch',
    //                'Ш' => 'Sh',
    //                'Щ' => 'Sch',
    //                'Ь' => '',
    //                'Ы' => 'Y',
    //                'Ъ' => '',
    //                'Э' => 'E',
    //                'Ю' => 'Yu',
    //                'Я' => 'Ya',
    //                'І' => 'I',
    //                'Ї' => 'Yi',
    //                'Є' => 'E',
    //            ];
    //            $string = strtr($string, $converter);
    //            $string = mb_strtolower($string);
    //            $string = trim(preg_replace('/(-)\1{1,}/', '\1', preg_replace('~[^-a-z0-9]+~u', $pattern, $string)), $pattern);
    //
    //            return $string;
    //        }
    //    }

    if (!function_exists('sort_field')) {
        /**
         * @param int $start
         * @param int $finish
         * @return array
         */
        function sort_field($start = -50, $finish = 50)
        {
            $_sort = [];
            for ($_i = $start; $_i <= $finish; $_i++) {
                $_sort[$_i] = trans('forms.label_sort_position', ['position' => $_i]);
            }

            return $_sort;
        }
    }

    if (!function_exists('plural_string')) {
        /**
         * @param $n
         * @param $forms
         * @return string
         */
        function plural_string($n, $forms, $visible_n = TRUE)
        {
            if ($visible_n) {
                return $n % 10 == 1 && $n % 100 != 11 ? "{$n} {$forms[0]}" : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? "{$n} {$forms[1]}" : "{$n} {$forms[2]}");
            } else {
                return $n % 10 == 1 && $n % 100 != 11 ? "{$forms[0]}" : ($n % 10 >= 2 && $n % 10 <= 4 && ($n % 100 < 10 || $n % 100 >= 20) ? "{$forms[1]}" : "{$forms[2]}");
            }
        }
    }

    if (!function_exists('format_phone_number')) {
        /**
         * @param $phone
         * @return string
         */
        function format_phone_number($phone)
        {

            $_code_1 = substr($phone, 0, 3);
            $_code_2 = substr($phone, 3, 16);

            return "<span class=\"phone-number-code code-center\">{$_code_1}</span><span class=\"phone-number-code code-finish\">{$_code_2}</span>";
        }
    }

    if (!function_exists('format_copyright')) {
        /**
         * @param $copyright
         * @return string
         */
        function format_copyright($copyright)
        {
            return str_replace(':year', date('Y'), $copyright);
        }
    }

    //    if (!function_exists('format_alias')) {
    //        function format_alias($alias)
    //        {
    //            $_route_params_1 = request()->route('language');
    //            $_route_params_2 = request()->route('location');
    //            $_saved_location = Session::get('location', NULL);
    //            if ($_route_params_1 && is_language($_route_params_1)) {
    //                wrap()->set('locale', $_route_params_1);
    //                $alias = preg_replace('/' . $_route_params_1 . '/', '', $alias, 1);
    //                $alias = trim(preg_replace('/([\/]){2,}/', '$1', $alias), '/');
    //            } elseif ($_route_params_2 && is_language($_route_params_2)) {
    //                wrap()->set('locale', $_route_params_2);
    //                $alias = preg_replace('/' . $_route_params_2 . '/', '', $alias, 1);
    //                $alias = trim(preg_replace('/([\/]){2,}/', '$1', $alias), '/');
    //            }
    //            if ($_route_params_1 && is_location($_route_params_1)) {
    //                wrap()->set('location', $_route_params_1);
    //                wrap()->set('contacts', contacts_load($_route_params_1, wrap()->get('locale')));
    //                $alias = preg_replace('/' . $_route_params_1 . '/', '', $alias, 1);
    //                $alias = trim(preg_replace('/([\/]){2,}/', '$1', $alias), '/');
    //            } elseif ($_route_params_2 && is_location($_route_params_2)) {
    //                wrap()->set('location', $_route_params_2);
    //                wrap()->set('contacts', contacts_load($_route_params_2, wrap()->get('locale')));
    //                $alias = preg_replace('/' . $_route_params_2 . '/', '', $alias, 1);
    //                $alias = trim(preg_replace('/([\/]){2,}/', '$1', $alias), '/');
    //            } elseif ($_saved_location) {
    //                wrap()->set('location', $_saved_location);
    //                wrap()->set('contacts', contacts_load($_saved_location, wrap()->get('locale')));
    //                $alias = trim(preg_replace('/([\/]){2,}/', '$1', $alias), '/');
    //            }
    //            if ($page = currentPage($alias)) {
    //                $alias = str_replace("/page-{$page}", '', $alias);
    //            } else {
    //                $alias = $alias;
    //            }
    //
    //            return $alias;
    //        }
    //    }

    if (!function_exists('format_alias')) {
        function format_alias(Request $request)
        {
            $_language = Session::get('language', DEFAULT_LANGUAGE);
            $_location = Session::get('location', DEFAULT_LOCATION);
            $_alias = '/';
            if ($request->ajax()) {
                $_language = $request->header('locale-code', DEFAULT_LANGUAGE);
                $_location = $request->header('location-code', $_location);
                wrap()->set('locale', $_language);
                wrap()->set('location', $_location);
                $_alias = $request->path();
                if ($_language != DEFAULT_LANGUAGE) {
                    $_alias = preg_replace('/^(\/' . $_language . '\/|' . $_language . '\/)/', '', $_alias, 1);
                    $_alias = trim(preg_replace('/([\/]){2,}/', '$1', $_alias), '/');
                }
            } else {
                $_alias = $request->path();
                $_request_language = $request->route('language');
                if ($_request_language && is_language($_request_language)) {
                    $_language = $_request_language;
                    $_alias = preg_replace('/^(\/' . $_language . '\/|' . $_language . '\/)/', '', $_alias, 1);
                    $_alias = trim(preg_replace('/([\/]){2,}/', '$1', $_alias), '/');
                }
                wrap()->set('locale', $_language);
                wrap()->set('location', $_location);
                App::setLocale($_language);
            }
            if ($_page_number = currentPage($_alias)) {
                $_current_url = wrap()->get('seo._canonical');
                $_current_url_query = wrap()->get('seo._url_query');
                $_alias = trim(preg_replace('/page-[0-9]+/i', '', $_alias), '/');
                $_prev_page = $_page_number - 1;
                if ($_page_number > 2) {
                    $_url = trim($_current_url, '/') . "/page-{$_prev_page}";
                    $_prev_page_link = _u($_url) . $_current_url_query;
                } else {
                    $_url = trim($_current_url, '/');
                    $_prev_page_link = _u($_url) . $_current_url_query;
                }
                wrap()->set('seo._link_prev', $_prev_page_link);
                wrap()->set('seo._page_number_suffix', ' - ' . trans('others.page_full', ['page' => $_page_number]) . trim(' ' . wrap()->get('seo._title_suffix')));
            }
            wrap()->set('seo._page_number', $_page_number);
            wrap()->set('seo._url_alias', $_alias);

            return $_alias;
        }
    }

    if (!function_exists('choice_template')) {
        function choice_template($templates = [])
        {
            $_template = NULL;
            if ($templates) {
                foreach ($templates as $template) {
                    if (view()->exists($template) && is_null($_template)) {
                        $_template = $template;
                    }
                }
            }

            return $_template;
        }
    }

    if (!function_exists('exists_relation')) {
        /**
         * @return object
         */
        function exists_relation()
        {
            $_response = [
                'count'    => 0,
                'location' => 0,
                'language' => 0,
            ];
            $_wrap = wrap()->get('variables');
            $_number_of_default_locations = count($_wrap['contacts']['cities']);
            $_number_of_default_languages = count($_wrap['i18n']['languages']);
            if ($_number_of_default_locations && $_number_of_default_languages && USE_MULTI_LANGUAGE) {
                $_response['count'] = $_number_of_default_locations * $_number_of_default_languages;
                $_response['location'] = $_number_of_default_locations - 1;
                $_response['language'] = $_number_of_default_languages - 1;
            } elseif ($_number_of_default_locations) {
                $_response['count'] = $_number_of_default_locations;
                $_response['location'] = $_number_of_default_locations - 1;
            }
            if ($_response['count']) $_response['count'] -= 1;

            return (object)$_response;
        }
    }


    function transform_price($price, $currency = NULL, $config = NULL, $default_currency = FALSE)
    {
        $_currencies = $config ? $config : config('os_currency');
        if ($default_currency) {
            $_wrap_current_currency = $_currencies['currencies'][$_currencies['default_currency']];
            $_wrap_current_currency['key'] = $_currencies['default_currency'];
        } else {
            $_wrap_current_currency = wrap()->get('currency')['current'];
        }
        $_decimals = (int)$_wrap_current_currency['precision'];
        if (is_array($price) && isset($price['currency'])) {
            $_entity_currency = $_currencies['currencies'][$price['currency']]['use'] ? $_currencies['currencies'][$price['currency']] : $_currencies['currencies'][DEFAULT_CURRENCY];;
            $_price = $price['price'];
        } elseif ($currency) {
            $_entity_currency = $_currencies['currencies'][$currency]['use'] ? $_currencies['currencies'][$currency] : $_currencies['currencies'][DEFAULT_CURRENCY];
            $_price = $price;
        } else {
            $_entity_currency = $_currencies['currencies'][DEFAULT_CURRENCY];
            $_price = $price;
        }
        if (($_wrap_current_currency['key'] == DEFAULT_CURRENCY) && (USE_MULTI_CURRENCIES && $_price && ($_entity_currency['iso_code'] != $_wrap_current_currency['iso_code']) && $_entity_currency['ratio'])) {
            $_price = $_price * (float)$_entity_currency['ratio'];
        } elseif (USE_MULTI_CURRENCIES && $_price && ($_entity_currency['iso_code'] != $_wrap_current_currency['iso_code']) && $_entity_currency['ratio']) {
            $_price = $_price * (float)$_entity_currency['ratio'] / (float)$_wrap_current_currency['ratio'];
        }
        if (($_entity_currency['iso_code'] != $_wrap_current_currency['iso_code']) && ($_wrap_current_currency['iso_code'] != DEFAULT_CURRENCY)) {
            if ($_wrap_current_currency['precision_mode'] == 2 && $_price) {
                $_decimals = 0;
                $_price = ceil((float)$_price / 10) * 10;
            } elseif ($_wrap_current_currency['precision_mode'] == 3 && $_price) {
                $_decimals = 0;
                $_price = ceil((float)$_price / 100) * 100;
            } elseif ($_wrap_current_currency['precision_mode'] == 0 && $_price) {
                if ($_decimals) {
                    $_decimals_pow = pow(10, $_decimals);
                    $_price = floor((float)$_price * $_decimals_pow) / $_decimals_pow;
                } else {
                    $_price = round((float)$_price, 0, PHP_ROUND_HALF_DOWN);
                }
            } elseif ($_wrap_current_currency['precision_mode'] == 1 && $_price) {
                if ($_decimals) {
                    $_decimals_pow = pow(10, $_decimals);
                    $_price = ceil((float)$_price * $_decimals_pow) / $_decimals_pow;
                } else {
                    $_price = round((float)$_price, 0, PHP_ROUND_HALF_UP);
                }
            }
        }
        $_response = [
            'original' => [
                'price'    => is_array($price) && isset($price['price']) ? $price['price'] : ($price ? $price : NULL),
                'currency' => is_array($price) && isset($price['currency']) ? $price['currency'] : ($currency ? $currency : NULL)
            ],
            'format'   => [
                'price'        => $_price,
                'view_price'   => $_price ? number_format($_price, $_decimals, ',', '') : 0,
                'view_price_2' => $_price ? number_format($_price, $_decimals, ',', ' ') : 0,
            ],
            'currency' => $_wrap_current_currency
        ];

        return $_response;
    }

    function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $_merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($_merged[$key]) && is_array($_merged[$key])) {
                $_merged[$key] = array_merge_recursive_distinct($_merged[$key], $value);
            } else {
                $_merged[$key] = $value;
            }
        }

        return $_merged;
    }


    //?
    if (!function_exists('fields_locations')) {
        function fields_locations($entity)
        {
            $_language = config('app.locale');
            $_location_default = config('os_contacts.default_city');
            $_locations = config('os_contacts.cities');
            $_response = NULL;
            foreach ($_locations as $_location_key => $_location_data) {
                if ($_location_key != $_location_default) {
                    $_response[$_location_key] = $_location_data[$_language];
                    $_model = $entity->getMorphClass();
                    $_response[$_location_key]['entity'] = $_model::where('language', $_language)
                        ->where('location', $_location_key)
                        ->where('relation_location_id', ($entity->relation_location_id ? $entity->relation_location_id : $entity->id))
                        ->first();
                }
            }

            return $_response;
        }
    }

    if (!function_exists('fields_relate_locations_values')) {
        /**
         * @return array|null
         */
        function fields_relate_locations_values()
        {
            $_locations = config('os_contacts.cities');
            if (count($_locations) == 1) return NULL;
            $_default_location = DEFAULT_LOCATION;
            $_default_language = DEFAULT_LANGUAGE;
            $_response = [
                NULL => trans('forms.value_not_selected')
            ];
            foreach ($_locations as $_location_code => $_location_data) if ($_location_code != $_default_location) $_response[$_location_code] = $_location_data[$_default_language]['city'];

            return $_response;
        }
    }

    if (!function_exists('fields_relate_languages_values')) {
        /**
         * @return array|null
         */
        function fields_relate_languages_values()
        {
            $_languages = config('os_languages.languages');
            if (count($_languages) == 1) return NULL;
            $_default_language = DEFAULT_LANGUAGE;
            $_response = [
                NULL => trans('forms.value_not_selected')
            ];
            foreach ($_languages as $_language_code => $_language_data) if ($_language_code != $_default_language) $_response[$_language_code] = $_language_data['full_name'];

            return $_response;
        }
    }

    if (!function_exists('w1251ToUtf8')) {
        function w1251ToUtf8($str)
        {
            return mb_convert_encoding($str, 'utf-8', 'windows-1251');
        }
    }


    /*****
     *
     */
    function parse_category_params($category, $query_params = NULL)
    {
        $_response = [
            'selected'       => [
                'params' => NULL,
                'query'  => NULL,
            ],
            'params'         => NULL,
            'base'           => NULL,
            'count_selected' => 0
        ];
        $_exclude_query_param = [
            'show_more',
            'view_load'
        ];
        if ($category instanceof \App\Models\ShopCategory && !is_null($query_params)) {
            $_meta_params = [];
            $query_params = explode('?', $query_params);
            if (isset($query_params[0]) && $query_params[0]) {
                $_query = explode('-and-', $query_params[0]);
                if (is_array($_query) && count($_query)) {
                    foreach ($_query as $_param_and_options) {
                        try {
                            $_pao = explode('-is-', $_param_and_options);
                            $_response['selected']['params'][$_pao[0]] = explode('-or-', $_pao[1]);
                        } catch (\Exception $exception) {
                        }
                    }
                }
            }
            $_category_params = $category->_category_params->keyBy('alias_name');
            $_category_params->each(function ($_param, $_alias_name_param) use (&$_response, &$_meta_params) {
                if (isset($_response['selected']['params'][$_alias_name_param]) && is_array($_response['selected']['params'][$_alias_name_param]) && $_response['selected']['params'][$_alias_name_param]) {
                    $_options = $_param->_items->keyBy('alias_name');
                    $_options = $_options->filter(function ($_option) use ($_response, $_alias_name_param) {
                        return in_array($_option->alias_name, $_response['selected']['params'][$_alias_name_param]);
                    });
                    if ($_options->isNotEmpty()) {
                        $_response['params'][$_alias_name_param] = $_options->sortBy('id');
                        if ($_param->type == 'select') {
                            $_response['base'][$_param->name] = [
                                'type'   => 'data',
                                'values' => $_options->pluck('id')->toArray(),
                                'alias'  => $_options->pluck('alias_name', 'id')->toArray()
                            ];
                            $_meta_params[$_param->name] = $_options->pluck('id')->toArray();
                        }

                    }
                }
            });
            if (isset($_response['base'])) {
                $_response['count_selected'] = 0;
                foreach ($_response['base'] as $_item) {
                    if ($_item['type'] == 'data') {
                        $_response['count_selected'] += count($_item['values']);
                    }
                }
                $_response['meta'] = $category->_generate_meta_tags($_meta_params);
            }
        }
        if ($query_url = request()->all()) {
            $_response['selected']['query'] = $query_url;
            if (isset($_response['selected']['query']) && is_array($_response['selected']['query']) && $_response['selected']['query']) {
                foreach ($_response['selected']['query'] as $_param => $_options) {
                    if (!in_array($_param, $_exclude_query_param)) {
                        if (is_array($_options)) {
                            $_response['base'][$_param] = [
                                'type'   => 'min_max',
                                'values' => $_options,
                                'alias'  => NULL
                            ];
                        } elseif (is_string($_options)) {
                            $_response['base'][$_param] = [
                                'type'   => 'string',
                                'values' => $_options,
                                'alias'  => NULL
                            ];
                        }
                    }
                }
            }
        }

        return $_response;
    }
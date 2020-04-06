<?php

    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\URL;
    use App\Models\UrlAlias;
    use Illuminate\Http\Request;

    if(!function_exists('_u')) {
        /**
         * @param       $path
         * @param array $parameters
         * @param bool  $use_additional_params
         * @return null|string|string[]
         */
        function _u($path, $parameters = [], $use_additional_params = NULL)
        {
            $_url = url($path, $parameters, config('os_seo.path.secure'));
            $_prepend = NULL;

            return formalize_url($_url, FALSE, $use_additional_params);
        }
    }

    if(!function_exists('_r')) {
        /**
         * @param       $path
         * @param array $parameters
         * @param bool  $use_additional_params
         * @return null|string|string[]
         */
        function _r($path, $parameters = [], $use_additional_params = NULL)
        {
            $_route = route($path, $parameters, config('os_seo.path.absolute'));

            return formalize_url($_route, FALSE, $use_additional_params);
        }
    }

    if(!function_exists('_l')) {
        /**
         * @param       $name
         * @param       $path
         * @param array $parameters
         * @param bool  $use_additional_params
         * @return string
         */
        function _l($name, $path, $parameters = [], $use_additional_params = NULL)
        {
            $_link_attributes = [];
            $_link_parameters = isset($parameters['p']) ? $parameters['p'] : [];
            $_link_prefix = isset($parameters['prefix']) && is_string($parameters['prefix']) ? $parameters['prefix'] : '';
            $_link_suffix = isset($parameters['suffix']) && is_string($parameters['suffix']) ? $parameters['suffix'] : '';
            $_link_anchor = isset($parameters['anchor']) && is_string($parameters['anchor']) ? '#' . trim($parameters['anchor'], '#') : '';
            $_link_description = isset($parameters['description']) && is_string($parameters['description']) ? "<div class='uk-description-link'>{$parameters['description']}</div>" : '';
            $_link_active = FALSE;
            if($path) {
                $parse_path = parse_url($path);
                if(!isset($parse_path['host'])) $path = $parse_path['path'];
            }
            if(!$path) {
                $_link_path = NULL;
                $_link_attributes['class'] = 'not-link';
            } elseif(Route::has($path)) {
                $_link_path = _r(trim($path, '/'), $_link_parameters, $use_additional_params);
            } else {
                $_link_path = _u($path, $_link_parameters, $use_additional_params);
            }
            if(isset($parameters['a']) && is_array($parameters['a'])) {
                foreach($parameters['a'] as $key => $attribute) {
                    switch($key) {
                        case 'class':
                            if($attribute && isset($_link_attributes['class'])) {
                                $_link_attributes['class'] .= is_array($attribute) ? ' ' . implode(' ', $attribute) : " {$attribute}";
                            } elseif($attribute) {
                                $_link_attributes['class'] = is_array($attribute) ? implode(' ', $attribute) : $attribute;
                            }
                            break;
                        case 'data':
                            if($attribute && is_array($attribute)) foreach($attribute as $data_name => $data_value) if($data_value) $_link_attributes["data-{$data_name}"] = $data_value;
                            break;
                        default:
                            if($attribute) $_link_attributes[$key] = $attribute;
                            break;
                    }
                }
            }
            if(!is_null($_link_path)) {
                $_link_path_active = config('os_seo.path.absolute') ? str_replace(config('app.url'), '', $_link_path) : $_link_path;
                if(request()->path() == '/' && request()->is($_link_path_active)) {
                    $_link_attributes['class'] = isset($_link_attributes['class']) ? "{$_link_attributes['class']} active" : 'active';
                    $_link_active = TRUE;
                } elseif(request()->path() != '/' && request()->is(trim($_link_path_active, '/'))) {
                    $_link_attributes['class'] = isset($_link_attributes['class']) ? "{$_link_attributes['class']} active" : 'active';
                    $_link_active = TRUE;
                }
            }
            if($_link_active || is_null($_link_path)) {
                if(!$_link_anchor) $_link_attributes['class'] = isset($_link_attributes['class']) ? "{$_link_attributes['class']} uk-current-link" : $_link_attributes['class'];
                $_attributes = render_attributes($_link_attributes);
                if($_link_anchor) {
                    $output = "<a href=\"{$_link_anchor}\" {$_attributes}>{$_link_prefix}<span class='uk-name-link'>{$name}</span>{$_link_suffix}{$_link_description}</a>";
                } else {
                    $output = "<span {$_attributes}>{$_link_prefix}<span class='uk-name-link'>{$name}</span>{$_link_suffix}{$_link_description}</span>";
                }
            } else {
                $_attributes = render_attributes($_link_attributes);
                $output = "<a href=\"{$_link_path}{$_link_anchor}\" {$_attributes}>{$_link_prefix}<span class='uk-name-link'>{$name}</span>{$_link_suffix}{$_link_description}</a>";
            }

            return $output;
        }
    }

    if(!function_exists('formalize_url')) {
        /**
         * @param      $url
         * @param bool $is_file
         * @param bool $use_additional_params
         * @return null|string|string[]
         */
        function formalize_url($url, $is_file = FALSE, $use_additional_params = NULL)
        {
            $_url = NULL;
            $_prepend = NULL;
            $_ending = config('os_seo.path.ending') && !$is_file ? '/' : '';
            $_url_parse = parse_url($url);
            if(config('os_seo.path.absolute') === TRUE) $_url = (config('os_seo.path.secure') === TRUE ? 'https://' : 'http://') . str_replace('www', '', $_url_parse['host']);
            $_url_params = (isset($_url_parse['query']) && $_url_parse['query'] ? "?{$_url_parse['query']}" : '');
            $_url .= (isset($_url_parse['path']) && $_url_parse['path'] ? $_url_parse['path'] : '/') . ($_url_params ? $_url_params : $_ending) . (isset($_url_parse['fragment']) && $_url_parse['fragment'] ? "#{$_url_parse['fragment']}" : '');
            if($is_file == FALSE && !is_null($use_additional_params)) {
                $_language = is_bool($use_additional_params) ? wrap()->get('locale') : (isset($use_additional_params['language']) && $use_additional_params['language'] ? $use_additional_params['language'] : wrap()->get('locale'));
                $_location = is_bool($use_additional_params) ? wrap()->get('location') : (isset($use_additional_params['location']) && $use_additional_params['location'] ? $use_additional_params['location'] : wrap()->get('location'));
                if(USE_MULTI_LANGUAGE && DEFAULT_LANGUAGE != $_language) $_prepend = "/{$_language}/";
                if(USE_SEVERAL_LOCATION) $_prepend .= $_prepend ? "{$_location}/" : "/{$_location}/";
            }

            return preg_replace('/([\/]){2,}/', '$1', $_prepend . $_url);
        }
    }

    if(!function_exists('formalize_path')) {
        /**
         * @param      $path
         * @param bool $do_no_use_timestamp
         * @return null|string|string[]
         */
        function formalize_path($path, $do_no_use_timestamp = FALSE)
        {
            if(Storage::disk('front')->exists($path) && !$do_no_use_timestamp) {
                $_file_lastModified = Storage::disk('front')->lastModified($path);
                $asset = asset("$path?{$_file_lastModified}", config('os_seo.path.secure'));
            } else {
                $asset = asset($path, config('os_seo.path.secure'));
            }

            return formalize_url($asset, TRUE);
        }
    }

    if(!function_exists('formalize_url_query')) {
        /**
         * @param null   $query
         * @param null   $element
         * @param string $prefix
         * @return null|string
         */
        function formalize_url_query($query = NULL, $element = NULL, $prefix = '?')
        {
            $_query = request()->query();
            if($query) $_query = array_merge($_query, $query);
            if(is_string($element) && isset($_query[$element])){
                unset($_query[$element]);
            }elseif(is_array($element)){
                $_query[$element['param']] = $element['data'];
            }
            if($_query) return $prefix . http_build_query($_query);

            return NULL;
        }
    }

    if(!function_exists('generate_alias')) {
        /**
         * @param       $alias
         * @param array $founder
         * @return string
         */
        function generate_alias($alias, $founder = [])
        {
            if($founder) {
                $founder[] = str_slug($alias);

                return implode('/', $founder);
            } else {
                $_alias = explode('/', $alias);
                $alias = [];
                foreach($_alias as $data) $alias[] = str_slug($data);

                return implode('/', $alias);
            }
        }
    }

    if(!function_exists('_ar')) {
        /**
         * @param        $path
         * @param null   $params
         * @param string $class
         * @return null|string
         */
        function _ar($path, $params = NULL, $class = 'uk-active uk-open')
        {
            $_current_url = trim(str_replace(trim(config('app.url'), '/'), '', URL::current()), '/');
            $_active = FALSE;
            if(is_string($path)) {
                if(Route::has($path)) {
                    $_route = trim(str_replace(trim(config('app.url'), '/'), '', route($path, $params)), '/');
                    if($_route == $_current_url) $_active = TRUE;
                } else {
                    $_url = trim(str_replace(trim(config('app.url'), '/'), '', url($path)), '/');
                    if($_url == $_current_url) $_active = TRUE;
                }

                return $_active ? $class : NULL;
            } elseif(is_array($path)) {
                foreach($path as $_path) if(stristr(Route::currentRouteName(), $_path)) return ' ' . $class;
            }

            return NULL;
        }
    }

    if(!function_exists('currentPage')) {
        /**
         * @param null $alias
         * @return int|null
         */
        function currentPage($alias = NULL)
        {
            $_current_page = NULL;
            if(is_null($alias)) $alias = request()->path();
            $pattern = '/page-[0-9]+/';
            preg_match($pattern, $alias, $_page);
            if(count($_page)) {
                $_page = array_shift($_page);
                $_current_page = (int)str_replace('page-', '', $_page);
            }

            return $_current_page;
        }
    }

    if(!function_exists('active_path')) {
        /**
         * @param null $path
         * @return int|null
         */
        function active_path($path = NULL)
        {
            if($path) {
                $_parse_path = parse_url($path);
                if(!isset($_parse_path['host'])) {
                    return request()->is(trim($_parse_path['path'],
                        '/')) ? TRUE : FALSE;
                }
            }

            return FALSE;
        }
    }

    function current_url_load(Request $request)
    {
        $_response = [
            'redirect' => FALSE,
            'url'      => $request->path(),
            'alias'      => $request->path(),
        ];
        $_alias = format_alias($request);
        $_response['alias'] = $_alias;
        $_language = wrap()->get('locale', DEFAULT_LANGUAGE);
        $_location = wrap()->get('location', DEFAULT_LOCATION);
        $_languages = array_keys(wrap()->get('languages', []));
        $_js_settings = wrap()->get('page._js_settings', []);
        $_config_seo = config('os_seo');
        if($_language != DEFAULT_LANGUAGE && $_response['url'] == '/') {
            $_response['redirect'] = TRUE;
            $_response['url'] = $_language;
        } elseif(($_response['url'] != '/') && ($_current_url = UrlAlias::where('alias', $_alias)
                ->where('model_type', '<>', 'App\\Models\\ShopFilterParamsPage')
                ->where('language', $_language)
                ->first())) {
            //            if(($_location == DEFAULT_LOCATION) && ($_location != $_current_url->location)) {
            //                $_location = wrap()->set('location', $_current_url->location);
            //            }
            if(isset($_config_seo['settings'][$_language]['site_copyright'])) wrap()->set('site._copyright', str_replace(':year', date('Y'), $_config_seo['settings'][$_language]['site_copyright']));
            wrap()->set('contacts', contacts_load($_language, $_location));
            wrap()->set('alias', $_current_url);
            //            wrap()->set('pages.search', page_load('search', $_language));
            //            wrap()->set('pages.shop_basket', page_load('shop_basket', $_language));
            $_response['url'] = $_current_url;
        } elseif($_response['url'] == '/' || in_array($_response['url'], $_languages)) {
            if(isset($_config_seo['settings'][$_language]['site_copyright'])) wrap()->set('site._copyright', str_replace(':year', date('Y'), $_config_seo['settings'][$_language]['site_copyright']));
            wrap()->set('contacts', contacts_load($_language, $_location));
            wrap()->set('alias', $_response['url']);
            //            wrap()->set('pages.search', page_load('search', $_language), TRUE);
            //            wrap()->set('pages.shop_basket', page_load('shop_basket', $_language), TRUE);
            $_response['url'] = '/';
        } else {
            if(isset($_config_seo['settings'][$_language]['site_copyright'])) wrap()->set('site._copyright', str_replace(':year', date('Y'), $_config_seo['settings'][$_language]['site_copyright']));
            wrap()->set('contacts', contacts_load($_language, $_location));
            //            wrap()->set('pages.search', page_load('search', $_language));
            //            wrap()->set('pages.shop_basket', page_load('shop_basket', $_language));
            $_response['url'] = $_current_url;
        }
        if($_language != DEFAULT_LANGUAGE) {
            wrap()->set('pages.search', page_load('search', $_language), TRUE);
            wrap()->set('pages.shop_basket', page_load('shop_basket', $_language), TRUE);
        }
        $_js_settings['locale'] = $_language;
        $_js_settings['location'] = $_location;
        wrap()->set('page._js_settings', $_js_settings);

        return (object)$_response;
    }

    function changed_page($entity_class, $id, $language, $location)
    {
        $_base_entity = NULL;
        $_default_entity = NULL;
        $_current_entity = NULL;
        $_base_page = $entity_class::where('id', $id)
            ->first([
                'id',
                'relation'
            ])->toArray();
        $_all_relations = $entity_class::where(function ($_query) use ($_base_page) {
            $_query->when($_base_page['relation'], function ($_sub_query) use ($_base_page) {
                $_sub_query->where('id', $_base_page['relation'])
                    ->orWhere('relation', $_base_page['relation']);
            });
            $_query->when(!$_base_page['relation'], function ($_sub_query) use ($_base_page) {
                $_sub_query->where('id', $_base_page['id'])
                    ->orWhere('relation', $_base_page['id']);
            });
        })
            ->with([
                '_alias'
            ])
            ->where('status', 1)
            ->get([
                'id',
                'relation',
                'language',
                'location',
                'alias_id'
            ]);
        $_all_relations->map(function ($_entity) use (&$_base_entity, &$_default_entity, &$_current_entity, $language, $location) {
            if($_entity->language == DEFAULT_LANGUAGE && $_entity->location == DEFAULT_LOCATION) {
                $_base_entity = UrlAlias::find($_entity->alias_id);
            }
            if($_entity->language == $language && $_entity->location == DEFAULT_LOCATION) {
                $_default_entity = UrlAlias::find($_entity->alias_id);
            }
            if($_entity->language == $language && $_entity->location == $location) {
                $_current_entity = UrlAlias::find($_entity->alias_id);
            }
        });
        if($_current_entity) {
            return $_current_entity;
        } elseif($_default_entity) {
            return $_default_entity;
        } elseif($_base_entity) {
            return $_base_entity;
        }
    }
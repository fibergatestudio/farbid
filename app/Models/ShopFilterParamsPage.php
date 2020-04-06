<?php

    namespace App\Models;

    use App\Library\BaseModel;

    class ShopFilterParamsPage extends BaseModel
    {
        protected $table = 'shop_filter_params_page';
        protected $guarded = [];
        public $data_page;
        public $classIndex = 'shop_category_filter_params_load';

        /**
         * Attribute
         */

        public function getParamsAttribute()
        {
            if ($this->selected_params) return unserialize($this->selected_params);

            return NULL;
        }

        /**
         * Other
         */

        public function _load()
        {
            $_meta = $this->_category->_generate_meta_tags($this->params);
            $this->title = $_meta['title'] ? : $this->title;
            $this->meta_title = $_meta['meta_title'];
            $this->meta_description = $_meta['meta_description'];
            $this->meta_keywords = $_meta['meta_keywords'];
            if ($_meta['meta_robots'] == 'noindex, nofollow') {
                $this->meta_robots = $_meta['meta_robots'];
            } else {
                $this->meta_robots = FALSE;
            }
            //$this->meta_robots = $_meta['meta_robots'];
            $this->template = choice_template([
                "front.shop.filter_params_{$this->id}",
                "front.shop.category_relation_{$this->_category->relation}_category_{$this->_category->id}",
                "front.shop.category_{$this->_category->id}",
                'front.shop.category',
                'oleus.base.shop_category'
            ]);
            $this->body = content_render($this);

            //            $entity = clone $this;
            //            $entity = Cache::rememberForever("{$this->classIndex}_{$this->id}", function () use ($entity) {
            //                $_response = new \stdClass();
            //                $_relation = clone $entity;
            //                if($entity->relation) $_relation = self::find($entity->relation);
            //                $_response->last_modified = $entity->_last_modified();
            //                $_response->body = content_render($entity);
            //                $_response->teaser = content_render($entity, 'teaser');
            //                $_response->background = [
            //                    'path'  => $entity->_background_asset(),
            //                    'style' => $entity->_background_style(),
            //                ];
            //                $_response->relation_entity = $_relation;
            //
            //                return $_response;
            //            });
            //            $_templates = [
            //                "front.shop.category_relation_{$entity->relation_entity->id}",
            //                "front.shop.category_relation_{$entity->relation_entity->id}_category_{$this->id}",
            //                "front.shop.category_{$this->id}",
            //                "front.shop.category",
            //                'oleus.base.shop_category'
            //            ];
            //            $this->template = choice_template($_templates);
            //            foreach($entity as $_key => $_data) $this->{$_key} = $_data;
        }

        public function _render()
        {
            $this->_load();
            $this->set_wrap([
                'seo._title'         => $this->meta_title ?? trans('shop.page_title_catalog_default', ['title' => $this->title]),
                'seo._keywords'      => $this->meta_keywords,
                'seo._description'   => $this->meta_description ?? trans('shop.page_description_catalog_default', ['title' => $this->title]),
                'seo._robots'        => $this->meta_robots,
                'seo._last_modified' => $this->_last_modified(),
                'page._title'        => $this->title,
                'page._id'           => $this->style_id,
                'page._class'        => $this->style_class,
                'page._background'   => $this->_background_style(),
                'page._scripts'      => [
                    [
                        'url'       => 'template/js/jquery-ui.min.js',
                        'in_footer' => TRUE,
                    ],
                    [
                        'url'       => 'components/shop/shop.js',
                        'in_footer' => TRUE,
                    ]
                ],
                'page._styles'       => [
                    [
                        'url'       => 'template/css/jquery-ui.min.css',
                        'in_footer' => TRUE,
                    ]
                ],
                'shop_filter_params' => $this->params,
                'alias'              => $this->_alias
            ]);
            wrap()->set('breadcrumb', breadcrumb_render(['entity' => $this]));

            return $this;
            //            $this->set_wrap([
            //                'seo._title'             => $this->meta_title ?? $this->title,
            //                'seo._keywords'          => $this->meta_keywords ?? $this->_category->meta_keywords,
            //                'seo._description'       => $this->meta_description ?? $this->_category->meta_description,
            //                'seo._robots'            => $this->meta_robots ?? $this->_category->meta_robots,
            //                'seo._last_modified'     => $this->_last_modified(),
            //                'page._title'            => $this->title,
            //                'page._id'               => $this->style_id,
            //                'page._class'            => $this->style_class,
            //                'page._scripts' => [
            //                    [
            //                        'url' => 'template/js/jquery-ui.min.js',
            //                        'in_footer' => true,
            //                    ],
            //                    [
            //                        'url' => 'components/shop/shop.js',
            //                        'in_footer' => true,
            //                    ],
            //                ],
            //                'page._styles' => [
            //                    [
            //                        'url' => 'template/css/jquery-ui.min.css',
            //                        'in_footer' => true,
            //                    ]
            //                ],
            //                'shop_category'          => $this->_category,
            //                'shop_primary_category'  => $this->_category->related['primary'],
            //                'shop_filter'            => $this,
            //                'shop_alias'             => $this->_alias ? _u($this->_alias->alias) : NULL,
            //                'shop_filter_params'     => $this->params,
            //                'shop_filter_page_query' => request()->query()
            //            ]);
            //            $this->template = choice_template([
            //                "front.shop.filter_params_{$this->id}",
            //                "front.shop.category_{$this->_category->relation}_{$this->_category->id}",
            //                "front.shop.category_{$this->_category->id}",
            //                'front.shop.category',
            //                'oleus.base.shop_category'
            //            ]);
            //            $this->body = content_render($this);
            //            $this->items = $this->_items();
            //            wrap()->set('breadcrumb', breadcrumb_render(['entity' => $this]));
            //
            //            return $this;
        }

        public function _category()
        {
            return $this->belongsTo(ShopCategory::class, 'category_id');
        }

        public function _filter()
        {
            return $this->_category->_load()->_filter();
        }

        public function _items()
        {
            return $this->_category->_load()->_items();
        }

        public function _render_ajax_command()
        {
            return $this->_category->_load()->_render_ajax_command($this);
        }

        /*******
         * @param      $category
         * @param      $params
         * @param null $checkedParam
         * @return array|null
         */

        public function _formation_params($category, $params, $checkedParam = NULL)
        {
            if ($category) {
                $_query_options_id = [];
                foreach ($params as $_key_data => $_value_data) {
                    foreach ($_value_data as $_value) {
                        if ($_value) {
                            array_push($_query_options_id, $_value);
                        }
                    }
                }
                $_query = $category->_filter_param_options->groupBy(function ($query) {
                    return $query->param_name;
                });
                $_language = $category->language;
                $_data = [];
                if ($_query->isNotEmpty()) {
                    $_query->each(function ($_options, $_param_name) use (&$_data, $params, $_language) {
                        if (isset($params[$_param_name])) {
                            $_options->each(function ($_option) use (
                                &$_data,
                                $params,
                                $_language
                            ) {
                                if (in_array($_option->option_id, $params[$_option->param_name])) {
                                    if (!isset($_data['params'][$_option->param_name])) {
                                        $_param_title = $_option->param_title;
                                        if ($_language != DEFAULT_LANGUAGE && $_option->param_translate) {
                                            $_param_translate = unserialize($_option->param_translate);
                                            if (isset($_param_translate[$_language]) && $_param_translate[$_language]) {
                                                $_param_title = $_param_translate[$_language];
                                            }
                                        }
                                        $_data['page'][$_option->param_name]['title'] = $_param_title;
                                        $_data['page'][$_option->param_name]['items'] = [];
                                        $_data['params'][$_option->param_name] = $_param_title;
                                    }
                                    $_option_name = $_option->option_name;
                                    if ($_language != DEFAULT_LANGUAGE && $_option->option_translate) {
                                        $_option_translate = unserialize($_option->option_translate);
                                        if (isset($_option_translate[$_language]) && $_option_translate[$_language]) {
                                            $_option_name = $_option_translate[$_language];
                                        }
                                    }
                                    $_data['page'][$_option->param_name]['items'][$_option->option_id] = $_option_name;
                                    $_data['options'][$_option->option_id] = $_option_name;
                                    $_data['request'][$_option->param_name][] = $_option->option_id;
                                } else {
                                    return TRUE;
                                }
                            });
                        } else {
                            return TRUE;
                        }
                    });
                }
                if ($_data['request']) {
                    foreach ($_data['request'] as $param => $options) {
                        $_options = array_unique($options);
                        sort($_options);
                        $_data['request'][$param] = $_options;
                    }
                }
                $_params = $this->_formation_data($category, $_data['page'], $_data['request'], $checkedParam);
                $_params['data'] = $_data['request'];
                $_params['params'] = $_data['params'];
                $_params['options'] = $_data['options'];

                return $_params;
            }

            return NULL;
        }

        public function _formation_data($category, $income_data = [], $_data_request, $checkedParam = NULL)
        {
            $_back_alias = NULL;
            $_filter_params_request = [];
            $_back_request = [];
            foreach ($_data_request as $_param => $_options) {
                foreach ($_options as $option) {
                    $_back_request[$_param][] = $option;
                    $_filter_params_request[] = $option;
                }
            }
            if ($checkedParam['param'] && $checkedParam['option'] && isset($_data_request[$checkedParam['param']]) && (($_key_option = array_search($checkedParam['option'],
                        $_data_request[$checkedParam['param']])) !== FALSE)
            ) {
                $_back_income_data = $income_data;
                unset($_back_request[$checkedParam['param']][$_key_option]);
                unset($_back_income_data[$checkedParam['param']]['items'][$checkedParam['option']]);
                if (!count($_back_request[$checkedParam['param']])) {
                    unset($_back_request[$checkedParam['param']]);
                } else {
                    sort($_back_request[$checkedParam['param']]);
                }
                if (!count($_back_income_data[$checkedParam['param']]['items'])) {
                    unset($_back_income_data[$checkedParam['param']]);
                }
                if ($_back_request && ($_back_page = $this->_formation_data($category, $_back_income_data, $_back_request))
                ) {
                    $_back_alias = $_back_page['alias'];
                }
            }
            sort($_filter_params_request);
            $_request_query_params = formalize_url_query();
            //            if($_shop_filter_page = self::from('shop_filter_params_page as sfp')
            //                ->leftJoin('url_alias as a', 'a.id', '=', 'sfp.alias_id')
            //                ->where('sfp.selected_params', serialize($_data_request))
            //                ->where('sfp.category_id', $category->id)
            //                ->select([
            //                    'sfp.*',
            //                    'a.alias'
            //                ])
            //                ->first()
            //            ) {
            //                return [
            //                    'id'         => $_shop_filter_page->id,
            //                    'alias'      => _u($_shop_filter_page->alias, [], TRUE) . $_request_query_params,
            //                    'back_alias' => $_back_alias,
            //                    'params'     => $_data_request
            //                ];
            //            } else {
            //                $_response = NULL;
            //                $_back_alias = NULL;
            //                foreach($income_data as $_name_param => $_value_param) {
            //                    $_response['title'][] = "{$_value_param['title']}: " . implode(', ', $_value_param['items']) . ';';
            //                    $_response['alias'][] = str_slug("{$_name_param}-" . implode('-or-', $_value_param['items']));
            //                }
            //                $_filter_page_title = is_array($_response['title']) ? $category->title . ' (' . implode(' ',
            //                        $_response['title']) . ')' : $category->title;
            //                $_shop_filter_page = $this->fill([
            //                    'category_id'      => $category->id,
            //                    'title'            => $_filter_page_title,
            //                    'language'         => $category->language,
            //                    'meta_description' => $category->meta_description ? "{$_filter_page_title}. {$category->meta_description}" : '',
            //                    'selected_params'  => serialize($_data_request),
            //                    'show'             => count($_filter_params_request) > 3 ? 0 : 1,
            //                    'sitemap'             => count($_filter_params_request) > 3 ? 0 : 1,
            //                ]);
            //                $_shop_filter_page->save();
            //                $_url_alias = new UrlAlias();
            //                $_url_alias->model_id = $_shop_filter_page->id;
            //                $_url_alias->model_type = $_shop_filter_page->getMorphClass();
            //                $_url_alias->alias = ($category->_alias->alias . '/' . implode('-', $_response['alias'])) . "-{$_shop_filter_page->id}";
            //                $_url_alias->language = $_shop_filter_page->language;
            //                $_url_alias->location = $_shop_filter_page->location;
            //                $_url_alias->re_render = 1;
            //                $_url_alias->save();
            //                $_shop_filter_page->alias_id = $_url_alias->id;
            //                $_shop_filter_page->save();
            //                //
            //                //
            //                // $_url_alias = new UrlAlias($_shop_filter_page);
            //                // $_url_alias->set(($category->_alias->alias . '/' . implode('-', $_response['alias'])));
            //
            //                return [
            //                    'id'         => $_shop_filter_page->id,
            //                    'alias'      => _u($_shop_filter_page->_alias->alias, [], TRUE) . $_request_query_params,
            //                    'back_alias' => $_back_alias,
            //                    'params'     => $_data_request
            //                ];
            //            }
        }

        public function _get_additional_page($params, $option)
        {
            $_this_params = $this->params;
            if (isset($_this_params[$params]) && in_array($option, $_this_params[$params])) {
                return $this;
            } elseif (isset($_this_params[$params]) && !in_array($option, $_this_params[$params])) {
                $_this_params[$params][] = $option;
                sort($_this_params[$params]);

                return self::where('selected_params', serialize($_this_params))
                    ->first();
            } else {
                $_this_params[$params][] = $option;

                return self::where('selected_params', serialize($_this_params))
                    ->first();
            }

            return NULL;
        }


        public static function filter_page($alias = NULL)
        {
            if (!is_null($alias) && str_is('*-frp-*', $alias)) {
                $_response = NULL;
                $_language = wrap()->get('locale');
                $_alias_parse_category_alias = explode('-frp-', $alias);
                try {
                    if ($_alias_parse_category_alias[0]) {
                        $_current_alias_model_id = UrlAlias::where('alias', $_alias_parse_category_alias[0])
                            ->where('model_type', 'App\\Models\\ShopCategory')
                            ->value('model_id');
                        if ($_current_alias_model_id) {
                            $_category = ShopCategory::where('id', $_current_alias_model_id)
                                ->with([
                                    '_alias',
                                ])
                                ->first();
                            if ($_category) {
                                $_category->filter_request = parse_category_params($_category, $_alias_parse_category_alias[1]);
                                $_category->sub_categories = $_category->childrens;
                                if(isset($_category->filter_request['meta'])) {
                                    $_category->title = $_category->filter_request['meta']['title'] ? : $_category->title;
                                    $_category->meta_title = $_category->filter_request['meta']['meta_title'];
                                    $_category->meta_description = $_category->filter_request['meta']['meta_description'];
                                    $_category->meta_keywords = $_category->filter_request['meta']['meta_keywords'];
                                    if ($_category->filter_request['meta']['meta_robots'] == 'noindex, nofollow') {
                                        $_category->meta_robots = $_category->filter_request['meta']['meta_robots'];
                                    } else {
                                        $_category->meta_robots = FALSE;
                                    }
                                    $_category->teaser = NULL;
                                    $_category->body = NULL;
                                }
                                $_response = $_category->_render();
                            }
                        }
                    }
                } catch (\Exception $exception) {

                }

                return $_response;
            }

            return NULL;
        }

    }
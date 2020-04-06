<?php

    namespace App\Models;

    use App\Library\BaseModel;

    class ShopParamItem extends BaseModel
    {
        protected $table = 'shop_param_items';
        protected $guarded = [];
        public $timestamps = FALSE;

        public function __construct()
        {
            parent::__construct();
        }

        public function _translate_name($language = NULL)
        {
            if ($this->exists) {
                $language = $language ?? $this->front_language;
                $_response = $this->name;
                if ($language != DEFAULT_LANGUAGE && $this->translate) {
                    $_translate = unserialize($this->translate);
                    if (isset($_translate[$language]) && $_translate[$language]) $_response = $_translate[$language];
                }

                return $_response;
            }

            return NULL;
        }

        public function _set_duplicate($language = NULL, $location = NULL)
        {
            if ($language || $location) {
                $_exists = self::where('relation', $this->id);
                if ($location) {
                    $_exists->where('location', $location);
                } else {
                    $_exists->where('location', DEFAULT_LOCATION);
                }
                if ($language) {
                    $_exists->where('language', $language);
                } else {
                    $_exists->where('language', DEFAULT_LANGUAGE);
                }
                $_exists = $_exists->count();
                if ($_exists == 0) {
                    $item = self::updateOrCreate([
                        'id' => NULL
                    ], [
                        'language'    => $language ?? $this->language,
                        'location'    => $location ?? $this->location,
                        'title'       => $this->title,
                        'style_id'    => $this->style_id,
                        'style_class' => $this->style_class,
                        'preset'      => $this->preset,
                        'status'      => $this->status,
                        'access'      => $this->access,
                        'relation'    => $this->id
                    ]);
                    $_slider_items = SliderItems::where('slider_id', $this->id)
                        ->get([
                            'title',
                            'sub_title',
                            'background_fid',
                            'body',
                            'sort',
                            'status',
                            'hidden_title',
                        ]);
                    if ($_slider_items->isNotEmpty()) {
                        $_slider_items = $_slider_items->map(function ($_item) use ($item) {
                            $_item['slider_id'] = $item->id;

                            return $_item;
                        });
                        SliderItems::insert($_slider_items->toArray());
                    }

                    return $item;
                }
            }

            return NULL;
        }

        /***
         * @param      $category
         * @param null $request_query
         * @return int
         */

        public function _number_result_opt($category, $request_query = NULL)
        {
            $_categories[] = $category->id;
            $_sub_categories = $category->sub_categories;
            if ($_sub_categories) {
                $_sub_categories = $_sub_categories->map(function ($_category) {
                    return $_category->id;
                })->filter(function ($_category) {
                    return $_category;
                })->toArray();
                if(is_array($_sub_categories) && count($_sub_categories)) {
                    $_categories = array_merge($_categories, $_sub_categories);
                }
            }
            $items = ShopProduct::from('shop_products as p')
                ->leftJoin('shop_product_categories as spc', 'spc.product_id', '=', 'p.id')
                ->select([
                    'p.modification_id',
                ])
                ->where('p.language', DEFAULT_LANGUAGE)
                ->where('p.location', DEFAULT_LOCATION)
                ->whereIn('spc.category_id', $_categories)
                ->whereNull('p.relation')
                ->where('p.status', 1)
                ->remember(15);
            if ($request_query && isset($request_query['base'])) {
                $request_query = $request_query['base'];
                $_index_param = 0;
                foreach ($request_query as $_query_param => $_query_param_values) {
                    if (!str_is('*utm*', $_query_param)) {
                        ++$_index_param;
                        if (is_array($_query_param_values) && $_query_param_values['type'] == 'data' && is_array($_query_param_values['values']) && count($_query_param_values['values'])) {
                            $items->leftJoin("shop_param_{$_query_param}_data as p{$_index_param}", "p{$_index_param}.product_id", '=', 'p.id')
                                ->whereIn("p{$_index_param}.option_id", $_query_param_values['values']);
                        } elseif (is_array($_query_param_values) && $_query_param_values['type'] == 'min_max' && is_array($_query_param_values['values']) && count($_query_param_values['values'])) {
                            $_min_value = isset($_query_param_values['values']['min']) && is_numeric($_query_param_values['values']['min']) ? $_query_param_values['values']['min'] : NULL;
                            $_max_value = isset($_query_param_values['values']['max']) && is_numeric($_query_param_values['values']['max']) ? $_query_param_values['values']['max'] : NULL;
                            if ($_query_param != 'price') {
                                $items->leftJoin("shop_param_{$_query_param}_data as p{$_index_param}", "p{$_index_param}.product_id", '=', 'p.id')
                                    ->when($_min_value, function ($query) use ($_min_value, $_index_param) {
                                        return $query->where("p{$_index_param}.value", '>=', $_min_value);
                                    })->when($_max_value, function ($query) use ($_max_value, $_index_param) {
                                        return $query->where("p{$_index_param}.value", '<=', $_max_value);
                                    });
                            } else {
                                $items->when($_min_value, function ($query, $_min_value) {
                                    return $query->where('p.price', '>=', $_min_value);
                                })->when($_max_value, function ($query, $_max_value) {
                                    return $query->where('p.price', '<=', $_max_value);
                                });
                            }
                        }
                    }
                }
            }

            return $items->count();
        }

        public function _page_alias($category, $request_query = NULL)
        {
            $_response = NULL;
            if ($request_query) {
                $_route_params = NULL;
                $_category_params = $category->_category_params
                    ->keyBy('id');
                if (isset($request_query['base'])) {
                    foreach ($request_query['base'] as $_param_name => $_settings) {
                        $_route_params[$_param_name] = $_settings['alias'];
                    }
                }
                if ($this->selected) {
                    if ($_category_params->has($this->param_id)) {
                        $_option_category = $_category_params->get($this->param_id);
                        if (isset($_route_params[$_option_category->name][$this->id])) unset($_route_params[$_option_category->name][$this->id]);
                    }
                } else {
                    if ($_category_params->has($this->param_id)) {
                        $_option_category = $_category_params->get($this->param_id);
                        $_route_params[$_option_category->name][$this->id] = $this->alias_name;
                    }
                }
                if ($_route_params) {
                    foreach ($_category_params as $_param) {
                        if (isset($_route_params[$_param->name]) && $_route_params[$_param->name]) {
                            ksort($_route_params[$_param->name]);
                            $_response[] = "{$_param->alias_name}-is-" . implode('-or-', $_route_params[$_param->name]);
                        }
                    }
                    $_alias = $category->_alias->language == DEFAULT_LANGUAGE ? $category->_alias->alias : "{$category->_alias->language}/{$category->_alias->alias}";
                    $_response = is_null($_response) ? _u($_alias) : _u("{$_alias}-frp-" . implode('-and-', $_response));
                }
            }
            return $_response;
        }


    }

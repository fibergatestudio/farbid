<?php

    namespace App\Models;

    use App\Library\BaseModel;

    class ShopParam extends BaseModel
    {
        protected $table = 'shop_params';
        protected $guarded = [];
        public $timestamps = FALSE;

        public function __construct()
        {
            parent::__construct();
        }

        public function _translate_title($language = DEFAULT_LANGUAGE)
        {
            if ($this->exists) {
                $language = $language ?? $this->front_language;
                $_response = $this->title;
                if ($language != DEFAULT_LANGUAGE && $this->translate) {
                    $_translate = unserialize($this->translate);
                    if (isset($_translate[$language]) && $_translate[$language]) {
                        $_response = $_translate[$language];
                    }
                }

                return $_response;
            }

            return NULL;
        }

        public function _translate_unit($language = DEFAULT_LANGUAGE)
        {
            if ($this->exists) {
                $language = $language ?? $this->front_language;
                $_option = ShopParamItem::where('param_id', $this->id)
                    ->where('type', $this->type)
                    ->first();
                if ($_option) {
                    $_response = $_option->unit_value;
                    if ($language != DEFAULT_LANGUAGE && $this->translate) {
                        $_translate = unserialize($_option->translate);
                        if (isset($_translate[$language]) && $_translate[$language]) {
                            $_response = $_translate[$language];
                        }
                    }

                    return $_response;
                }
            }

            return NULL;
        }

        public function _filter_data($_query = NULL, $_language = NULL)
        {
            $_selected = NULL;
            $_params = NULL;
            $_category = $this->current_category;
            $_primary_category = $this->primary_category;
            $_request_params = wrap()->get('shop_filter_params');
            $_param_name = $this->name;
            $request = request()->get($_param_name);
            if (isset($_request_params[$_param_name])) $_params = $_request_params[$_param_name];
            if (is_array($request)) {
                foreach ($request as $param_option_key => $param_option_value) {
                    $_selected[$param_option_key] = (int)$param_option_value;
                }
            }
            if ($request && is_array($_params)) {
                foreach ($_params as $param_option_value) {
                    if (!in_array((int)$param_option_value, $_selected)) {
                        $_selected[] = $param_option_value;
                    }
                }
            } elseif (is_array($_params)) {
                $_selected = $_params;
            }
            $_selected_data = NULL;

            if ($this->type == 'select') {
                $_response = collect([]);
                $_all_select_options = $this->_items()
                    ->leftJoin("{$this->table_param} as sp", 'shop_param_items.id', '=', 'sp.option_id')
                    ->leftJoin('shop_products as p', 'p.id', '=', 'sp.product_id')
                    ->leftJoin('shop_product_categories as spc', 'p.id', '=', 'spc.product_id')
                    ->where('p.status', 1)
                    ->where('spc.category_id', $_primary_category->id)
                    ->where('shop_param_items.visible_in_filter', 1)
                    ->distinct()
                    ->get([
                        'shop_param_items.*',
                    ]);
                if ($_all_select_options->isNotEmpty()) {
                    $_all_select_options->each(function ($_option) use (
                        $_param_name,
                        $_response,
                        $_selected,
                        $_request_params,
                        $_category,
                        $_query,
                        $_primary_category,
                        $_language,
                        &$_selected_data
                    ) {
                        //                        if ($_option->visible_in_filter) {
                        $_checked = $_selected && is_array($_selected) && in_array($_option->id,
                            $_selected) ? TRUE : ($_selected && $_selected == $_option->id ? TRUE : FALSE);
                        $_view_option_name = $_option->_translate_name($_language);
                        $_shop_filter_page_alias = NULL;
                        $_shop_filter_back_alias = NULL;
                        $_shop_filter_page = new ShopFilterParamsPage();
                        if (isset($_request_params[$_param_name]) && !in_array($_option->id,
                                $_request_params[$_param_name])
                        ) {
                            array_push($_request_params[$_param_name], $_option->id);
                        } else {
                            $_request_params[$_param_name][] = $_option->id;
                        }
                        $_request_params[$_param_name] = array_unique($_request_params[$_param_name]);
                        if ($_request_params) {
                            $_current_params = NULL;
                            if ($_checked) {
                                $_current_params = [
                                    'param'  => $_param_name,
                                    'option' => $_option->id
                                ];
                            }
                            $_shop_filter_page = $_shop_filter_page->_formation_params($_category, $_request_params, $_current_params);

                            //                            dd($_shop_filter_page);

                            //                                if ($_shop_filter_page) {
                            //                                    $_shop_filter_page_alias = $_shop_filter_page['alias'];
                            //                                    $_shop_filter_back_alias = $_shop_filter_page['back_alias'];
                            //                                }
                        }
                        $_query[$_param_name] = [
                            'type'   => 'data',
                            'values' => [$_option->id]
                        ];
                        $_response_option = [
                            'id'                       => $_option->id,
                            'name'                     => $_view_option_name,
                            'icon_fid'                 => $_option->icon_fid ? f_get($_option->icon_fid) : NULL,
                            'style_id'                 => $_option->style_id,
                            'style_class'              => $_option->style_class,
                            'color_shade'              => $_option->color_shade,
                            'attribute'                => $_option->attribute,
                            'selected'                 => $_checked,
                            'number_result'            => $this->_number_result($_primary_category, $_query),
                            'filter_params_page_alias' => $_shop_filter_page_alias,
                            'filter_params_back_alias' => $_shop_filter_back_alias ?? _u($_category->_alias->alias),
                        ];
                        $_response->put($_option->id, $_response_option);
                        if ($_checked) {
                            $_selected_data[$_option->id] = [
                                'name'  => $_view_option_name,
                                'alias' => $_response_option['filter_params_back_alias'] . formalize_url_query()
                            ];
                        }
                        unset($_shop_filter_page);
                        //                        }
                    });
                    if ($_response->isNotEmpty()) {
                        return [
                            'selected' => $_selected_data,
                            'options'  => $_response
                        ];
                    }
                }
            } elseif ($this->type == 'input_number') {
                $_input_option = ShopParamItem::where('param_id', $this->id)
                    ->first();
                if ($_input_option) {
                    $_min_max_values = DB::table("{$this->table_param} as sp")
                        ->leftJoin('shop_products as p', 'p.id', '=', 'sp.product_id')
                        ->where('p.status', 1)
                        ->select(DB::raw('max(sp.value) as max'), DB::raw('min(sp.value) as min'))
                        ->first();
                    if (isset($_selected['min']) && $_selected['min']) {
                        $_selected_data['min'] = $_selected['min'];
                    }
                    if (isset($_selected['max']) && $_selected['max'] && !isset($_selected_data['min'])) {
                        $_selected_data['min'] = $_min_max_values->min;
                    }
                    if (isset($_selected['max']) && $_selected['max']) {
                        $_selected_data['max'] = $_selected['max'];
                    }
                    if (isset($_selected_data['min']) && !isset($_selected_data['max'])) {
                        $_selected_data['max'] = $_min_max_values->max;
                    }
                    $_shop_filter_page_alias = NULL;
                    $_shop_filter_back_alias = NULL;
                    if ($_selected_data) {
                        $_selected_data['unit'] = $_input_option->unit_value;
                        $_shop_filter_page_alias = wrap()->get('shop_alias') . formalize_url_query(NULL, [
                                'param' => $_param_name,
                                'data'  => [
                                    'min' => $_selected_data['min'],
                                    'max' => $_selected_data['max']
                                ]
                            ]);
                        $_selected_data['alias'] =
                        $_shop_filter_back_alias = wrap()->get('shop_alias') . formalize_url_query(NULL, $_param_name);
                    } else {
                        $_shop_filter_back_alias =
                        $_shop_filter_page_alias = wrap()->get('shop_alias') . formalize_url_query();
                    }
                    $_view_option_unit = $_input_option->unit_value;

                    return [
                        'selected'                    => $_selected_data,
                        'number_result'               => 0,
                        'options'                     => [
                            'id'          => $_input_option->id,
                            'icon_fid'    => $_input_option->icon_fid ? f_get($_input_option->icon_fid) : NULL,
                            'style_id'    => $_input_option->style_id,
                            'style_class' => $_input_option->style_class,
                            'attribute'   => $_input_option->attribute,
                            'min_value'   => $_min_max_values->min,
                            'max_value'   => $_min_max_values->max,
                            'values'      => [
                                'min'    => isset($_selected['min']) && $_selected['min'] ? $_selected['min'] : $_min_max_values->min,
                                'max'    => isset($_selected['max']) && $_selected['max'] ? $_selected['max'] : $_min_max_values->max,
                                'slider' => [
                                    'min' => (isset($_selected['min']) && $_selected['min'] ? $_selected['min'] : $_min_max_values->min),
                                    'max' => (isset($_selected['max']) && $_selected['max'] ? $_selected['max'] : $_min_max_values->max),
                                ]
                            ],
                            'step_value'  => $_input_option->step_value,
                            'unit_value'  => $_view_option_unit,
                        ],
                        'filter_params_page_alias'    => $_shop_filter_page_alias,
                        'filter_params_back_alias'    => $_shop_filter_back_alias,
                        'filter_use_query_back_alias' => formalize_url_query(NULL, $_param_name) ? 1 : 0,
                    ];
                }
            }

            return NULL;
        }

        public function _number_result($category, $request_query = NULL)
        {
            $items = ShopProduct::from('shop_products as p')
                ->leftJoin('shop_product_categories as spc', 'spc.product_id', '=', 'p.id')
                ->select([
                    'p.modification_id',
                ])
                ->where('p.language', $this->primary_category->language)
                ->where('p.location', $this->primary_category->location)
                ->where('spc.category_id', $category->id)
                ->whereNull('p.relation')
                ->where('p.status', 1);
            if ($request_query) {
                $_index_param = 0;
                foreach ($request_query as $_query_param => $_query_param_values) {
                    if (!str_is('*utm*', $_query_param)) {
                        ++$_index_param;
                        if (is_array($_query_param_values) && count($_query_param_values['values']) && $_query_param_values['type'] == 'data') {
                            $items->leftJoin("shop_param_{$_query_param}_data as p{$_index_param}", "p{$_index_param}.product_id", '=', 'p.id')
                                ->whereIn("p{$_index_param}.option_id", $_query_param_values['values']);
                        } elseif (is_array($_query_param_values) && count($_query_param_values['values']) && $_query_param_values['type'] == 'min_max') {
                            $_min_value = isset($_query_param_values['values']['min']) && $_query_param_values['values']['min'] ? $_query_param_values['values']['min'] : NULL;
                            $_max_value = isset($_query_param_values['values']['max']) && $_query_param_values['values']['max'] ? $_query_param_values['values']['max'] : NULL;
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

        public function _items()
        {
            return $this->hasMany(ShopParamItem::class, 'param_id')
                ->orderBy('sort');
        }

        public function _set_duplicate($language = NULL)
        {
            if ($language) {
                $_exists = self::where('relation', $this->id);
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
                        'language'          => $language ? $language : $this->language,
                        'title'             => $this->title,
                        'name'              => $this->name,
                        'type'              => $this->type,
                        'table'             => $this->table,
                        'multiply'          => $this->multiply,
                        'visible_in_filter' => $this->visible_in_filter,
                        'relation'          => $this->id
                    ]);
                    $_param_items = ShopParamItem::where('param_id', $this->id)
                        ->get([
                            'id',
                            'name',
                            'type',
                            'unit_value',
                            'visible_in_filter',
                            'sort',
                        ]);
                    if ($_param_items->isNotEmpty()) {
                        $_param_items = $_param_items->map(function ($_item) use ($item) {
                            $_item['param_id'] = $item->id;
                            $_item['relation'] = $_item->id;
                            unset($_item->id);

                            return $_item;
                        });
                        ShopParamItem::insert($_param_items->toArray());
                    }

                    return $item;
                }
            }

            return NULL;
        }

        public function _generate_technical_name($option_name = NULL, $type = NULL)
        {
            if (is_null($option_name)) {
                if (is_null($this->name)) {
                    $this->name = str_slug($this->title, '_');
                    $index = 0;
                    if (self::where('name', $this->name)
                            ->count() > 0
                    ) {
                        while ($index <= 100) {
                            $_generate_name = "{$this->name}_{$index}";
                            if (self::where('name', $_generate_name)
                                    ->count() == 0
                            ) {
                                $this->name = $_generate_name;
                                break;
                            }
                            $index++;
                        }
                    }
                }
                $this->name = trim(preg_replace('/(_)\1{1,}/', '\1', preg_replace('~[^_a-z0-9]+~u', '_', $this->name)), '_');
                $this->save();

                return $this;
            } else {
                if(is_null($type)) {
                    $_alias_name = str_slug($option_name, '_');
                    if (ShopParamItem::where('alias_name', $_alias_name)
                            ->count() > 0) {
                        $index = 0;
                        while ($index <= 100) {
                            $_generate_name = "{$_alias_name}_{$index}";
                            if (ShopParamItem::where('alias_name', $_generate_name)
                                    ->count() == 0
                            ) {
                                $_alias_name = $_generate_name;
                                break;
                            }
                            $index++;
                        }
                    }
                }else{
                    $_alias_name = str_slug($this->title, '_');
                    if (self::where('alias_name', $_alias_name)
                            ->count() > 0) {
                        $index = 0;
                        while ($index <= 100) {
                            $_generate_name = "{$_alias_name}_{$index}";
                            if (self::where('alias_name', $_generate_name)
                                    ->count() == 0
                            ) {
                                $_alias_name = $_generate_name;
                                break;
                            }
                            $index++;
                        }
                    }
                }

                return $_alias_name;
            }
        }


        /***
         */


    }

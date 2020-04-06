<?php

    namespace App\Models;

    use App\Library\BaseModel;
    use App\Library\Frontend;
    use Illuminate\Pagination\Paginator;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;

    class ShopProduct extends BaseModel
    {
        use Frontend;

        protected $table = 'shop_products';
        protected $guarded = [];
        public $currency = DEFAULT_CURRENCY;
        public $classIndex = 'shop_product';

        public function __construct()
        {
            parent::__construct();
        }

        /**
         * Attribute
         */
        public function getMediaAttribute()
        {
            $_response = [
                'preview'   => $this->_preview,
                'medias'    => $this->_medias(),
                'all_files' => collect([]),
                'render'    => [
                    'thumb'    => collect([]),
                    'full'     => collect([]),
                    'original' => collect([]),
                ]
            ];
            if ($_response['preview']) $_response['all_files']->push($_response['preview']);
            if ($_response['medias']->isNotEmpty()) $_response['all_files'] = $_response['all_files']->merge($_response['medias']);
            if ($_response['all_files']->isNotEmpty()) {
                $_response['render']['full'] = $_response['all_files']->map(function ($_file) {
                    return image_render($_file, 'full_shop_product', ['attributes' => ['uk-cover' => TRUE]]);
                });
                $_response['render']['thumb'] = $_response['all_files']->map(function ($_file) {
                    return image_render($_file, 'thumb_shop_product_2', ['attributes' => ['width' => 70]]);
                });
                $_response['render']['original'] = $_response['all_files']->map(function ($_file) {
                    return "/uploads/{$_file->filename}";
                });
                //            } else {
                //                $_response['render']['full']->push(image_render(NULL, 'full_shop_product', ['attributes' => ['uk-cover' => TRUE]]));
            }

            return $_response;
        }

        public function getSpecificationsProductAttribute()
        {
            return $this->specifications ? json_decode($this->specifications) : NULL;
        }

        public function getMarksAttribute()
        {
            $_response = [
                'marks'   => [
                    'fasten_to_top' => trans('forms.label_mark_product_fasten_to_top'),
                    'mark_new'      => trans('forms.label_mark_product_new'),
                    'mark_hit'      => trans('forms.label_mark_product_hit'),
                    'mark_discount' => trans('forms.label_mark_product_discount'),
                    'mark_elected'  => trans('forms.label_mark_product_elected'),
                ],
                'checked' => []
            ];
            foreach ($_response['marks'] as $_mark_key => $_mark_value) if ($this->{$_mark_key}) $_response['checked'][] = $_mark_key;

            return $_response;
        }

        public function getPriceViewAttribute()
        {
            $_availability = ($this->count || $this->not_limited) && !$this->out_of_stock ? TRUE : FALSE;

            return [
                'availability' => $_availability,
                'old_price'    => ($_availability && (!is_null($this->old_price) && $this->old_price > 0) ? transform_price($this->old_price, $this->currency) : NULL),
                'price'        => ($_availability && (!is_null($this->price) && $this->price > 0) ? transform_price($this->price, $this->currency) : NULL),
            ];
        }

        public function getCategoriesAttribute()
        {
            $_response = NULL;
            $_categories_all = ShopCategory::location(DEFAULT_LOCATION)
                ->language(DEFAULT_LANGUAGE)
                ->orderBy('title')
                ->pluck('title', 'id');
            if ($_categories_all->isNotEmpty()) {
                $_response = [
                    'all'      => $_categories_all,
                    'selected' => NULL
                ];
                if ($this->exists) {
                    $_response['selected'] = ShopProductCategory::leftJoin('shop_categories', 'shop_categories.id', '=', 'shop_product_categories.category_id')
                        ->where('shop_product_categories.product_id', $this->id)
                        ->where('shop_categories.language', DEFAULT_LANGUAGE)
                        ->where('shop_categories.location', DEFAULT_LOCATION)
                        ->pluck('shop_categories.id');
                }
            }

            return $_response;
        }

        public function getCategoryAttribute()
        {
            $_category = ShopCategory::from('shop_categories as c')
                ->leftJoin('shop_product_categories as pc', 'pc.category_id', '=', 'c.id')
                ->where('pc.product_id', ($this->relation_entity->id ?? $this->id))
                ->first(['c.*']);

            return $_category ? shop_category_load($_category->id, $this->front_language) : NULL;
        }

        public function getModificationsAttribute()
        {
            $_response = NULL;
            if ($this->exists) {
                $_response = [
                    'this'    => $this,
                    'primary' => NULL,
                    'param'   => NULL,
                    'items'   => collect([]),
                ];
                if (is_null($this->modification_id)) {
                    $_response['primary'] = $this;
                } else {
                    $_response['primary'] = self::where('id', $this->modification_id)
                        ->with([
                            '_alias',
                            '_mod'
                        ]);
                }
                $_response['items'] = $_response['primary']->_mod ?? collect([]);
                if ($_response['items']->isNotEmpty() && ($this->language == $_response['primary']->language)) {
                    $_response['items']->prepend($_response['primary']);
                }
                $_categories = $this->categories ? $this->categories['selected'] : NULL;
                if ($_categories) {
                    $_related_params = ShopParam::rightJoin('shop_category_params', 'shop_params.id', '=',
                        'shop_category_params.param_id')
                        ->whereIn('shop_category_params.category_id', $_categories->values()->toArray())
                        ->where('shop_category_params.modify', 1)
                        ->get([
                            'shop_params.*'
                        ]);
                    if ($_related_params) {
                        $_response['param'] = $_related_params;
                    }
                }
            }

            return $_response;
        }

        /**
         * Other
         */
        public function _load($view = 'full')
        {
            $entity = clone $this;
            //                                                Cache::forget("{$this->classIndex}_{$this->id}");
            $entity = Cache::rememberForever("{$this->classIndex}_{$this->id}", function () use ($entity) {
                $_response = new \stdClass();
                $_relation = clone $entity;
                if ($entity->relation) $_relation = self::find($entity->relation);
                $_response->last_modified = $entity->_last_modified();
                $_response->body = content_render($entity);
                $_response->background = [
                    'path'  => $entity->_background_asset(),
                    'style' => $entity->_background_style(),
                ];
                $_response->medias_product = $_relation->media;
                $_response->equipment_product = $entity->equipment ?? $_relation->equipment;
                $_response->structural_features_product = $entity->structural_features ?? $_relation->structural_features;
                $_response->specifications_product = $entity->specifications_product ?? $_relation->specifications_product;
                $_response->category_product = $_relation->category;
                $_response->params_product = $_relation->_params();
                $_response->modification_links = $_relation->_modification_param_links($entity->language);
                $_response->relation_entity = $_relation;

                return $_response;
            });
            foreach ($entity as $_key => $_data) $this->{$_key} = $_data;
            $this->prices_product = $entity->relation_entity->price_view;
            $this->discount_timer_product = $entity->relation_entity->_discount_timer;
            if ($view == 'full') {
                $_templates = [
                    "front.shop.product_relation_{$entity->relation_entity->id}",
                    "front.shop.product_relation_{$entity->relation_entity->id}_product_{$this->id}",
                    "front.shop.product_{$this->id}",
                    "front.shop.product",
                    'oleus.base.shop_product',
                ];
                $this->related_products = $this->_related_products(1);
                $this->groups_product = $this->_groups_product(1);
                $this->template = choice_template($_templates);
            }
        }

        public function _render()
        {
            $this->_load();
            $this->_watched();
            $this->set_wrap([
                'seo._title'         => $this->meta_title ?? trans('shop.page_title_product_default', ['title' => $this->title]),
                'seo._keywords'      => $this->meta_keywords,
                'seo._description'   => $this->meta_description ?? trans('shop.page_description_product_default', ['title' => $this->title]),
                'seo._robots'        => $this->meta_robots,
                'seo._last_modified' => $this->_last_modified(),
                'page._title'        => $this->title,
                'page._id'           => $this->style_id,
                'page._class'        => $this->style_class,
                'page._background'   => $this->_background_style(),
                'breadcrumb'         => breadcrumb_render(['entity' => $this]),
                'alias'              => $this->_alias
            ]);
            $this->_counter_viewed();

            return $this;
        }

        public function _counter_viewed()
        {
            $_entity = $this->relation_entity;
            $_viewed = $_entity->viewed;
            $_already_seen = Session::get("counter_viewed.{$_entity->id}", FALSE);
            if ($_already_seen === FALSE) {
                $_viewed++;
                self::find($_entity->id)
                    ->update([
                        'viewed' => $_viewed
                    ]);
                Session::put("counter_viewed.{$_entity->id}", TRUE);
            }

            return $_viewed;
        }

        public function _counter_ordered()
        {
            $_entity = $this->relation_entity;
            $_ordered = $_entity->ordered;

            return $_ordered;
        }

        public function _render_full_page_ajax_command($item = NULL)
        {
            $item = $item ?? $this;
            if ($item->status) $item->_watched();
            $_wrap = wrap()->get();
            $_wrap['page']['_title'] = $item->title;
            wrap()->set('breadcrumb', breadcrumb_render(['entity' => $item]));
            $commands[] = [
                'command' => 'replaceWith',
                'target'  => '#box-view-shop-product',
                'data'    => clear_html(view('front.shop.item_product', compact('item', '_wrap'))
                    ->render())
            ];
            $commands[] = [
                'command' => 'change_url',
                'url'     => _u($item->_alias->alias, [], TRUE)
            ];
            $commands[] = [
                'command' => 'change_title',
                'title'   => ($item->meta_title ?? $item->title) . ' ' . $_wrap['seo']['_title_suffix']
            ];

            return $commands;
        }

        public function _discount_timer()
        {
            return $this->hasOne(ShopProductDiscountTimer::class, 'product_id')->withDefault();
        }

        public function _params($categories = NULL, $render_field = FALSE)
        {
            $_old = NULL;
            $_response = collect([]);
            $_params_list = NULL;
            if (is_null($categories) && $_old = old('categories')) {
                foreach ($_old as $key => $select) if ($select) $categories[] = $key;
            } elseif (is_null($categories) && $this->exists) {
                $categories = $this->categories['selected'];
            }
            if (is_object($categories)) $categories = $categories->toArray();
            if (!is_null($categories)) {
                $_params_list = ShopParam::leftJoin('shop_category_params', 'shop_params.id', '=', 'shop_category_params.param_id');
                if (is_array($categories)) {
                    $_params_list->whereIn('shop_category_params.category_id', $categories);
                } else {
                    $_params_list->where('shop_category_params.category_id', $categories);
                }
                $_params_list = $_params_list->where('shop_params.language', DEFAULT_LANGUAGE)
                    ->distinct()
                    ->with([
                        '_items'
                    ])
                    ->get([
                        'shop_params.*',
                        'shop_category_params.sort'
                    ])
                    ->sortBy('sort');
                if ($_params_list->isNotEmpty()) {
                    foreach ($_params_list as $_param) {
                        $_view = NULL;
                        $_param_data = NULL;
                        $_param_data_style = NULL;
                        $_param_selected = NULL;
                        $_values_translate = NULL;
                        $_filter_page = NULL;
                        $_param_items = $_param->_items;
                        if ($_param->type == 'select') {
                            if ($_param_items->isNotEmpty()) {
                                if ($this->exists) {
                                    $_value = DB::table($_param->table)
                                        ->where('product_id', $this->id)
                                        ->pluck('option_id')
                                        ->toArray();
                                } else {
                                    $_value = NULL;
                                }
                                $_values_translate = $_param_items->keyBy('id')->map(function ($_param_object) {
                                    return unserialize($_param_object->translate);
                                });
                                $_param_data_style = $_param_items->keyBy('id')->map(function ($_param_object) {
                                    return [
                                        'icon_fid'    => $_param_object->icon_fid,
                                        'style_id'    => $_param_object->style_id,
                                        'style_class' => $_param_object->style_class,
                                        'color_shade' => $_param_object->color_shade,
                                    ];
                                });
                                $_param_data = $_param_items->pluck('name', 'id');
                                $_param_selected = $_value;
                                if ($render_field) {
                                    $_values = $_param_items->pluck('name', 'id')->prepend(trans('forms.value_choice'), 0);
                                    $_view = field_render("params.{$_param->id}", [
                                        'type'     => 'select',
                                        'label'    => $_param->title,
                                        'value'    => $_value,
                                        'values'   => $_values,
                                        'class'    => 'uk-select2',
                                        'multiple' => $_param->type_view == 'multiple' ? TRUE : FALSE
                                    ]);
                                }
                            }
                        } elseif
                        ($_param->type == 'input_number') {
                            if ($_param_items->isNotEmpty()) {
                                $_values = $_param_items->first();
                                $_param_data = [];
                                $_values_translate = $_values->translate ? unserialize($_values->translate) : NULL;
                                if (!is_null($_values->min_value)) $_param_data['min'] = $_values->min_value;
                                if (!is_null($_values->max_value)) $_param_data['max'] = $_values->max_value;
                                if (!is_null($_values->step_value)) $_param_data['step'] = $_values->step_value;
                                if ($_values->unit_value) $_param_data['unit'] = $_values->unit_value;
                            }
                            if ($this->exists) {
                                $_value = DB::table($_param->table)
                                    ->where('product_id', $this->id)
                                    ->value('value');
                            } else {
                                $_value = NULL;
                            }
                            $_param_selected = $_value;
                            if ($render_field) {
                                $_view = field_render("params.{$_param->id}", [
                                    'type'       => 'number',
                                    'label'      => $_param->title . (isset($_values->unit_value) ? ", {$_values->unit_value}" : ''),
                                    'value'      => $_value,
                                    'attributes' => $_param_data,
                                ]);
                            }
                        } elseif ($_param->type == 'input_text') {
                            if ($_param_items->isNotEmpty()) {
                                $_values = $_param_items->first();
                                $_values_translate = $_values->translate ? unserialize($_values->translate) : NULL;
                                if ($_values->unit_value) $_param_data['unit'] = $_values->unit_value;
                            }
                            if ($this->exists) {
                                $_value = DB::table($_param->table)
                                    ->where('product_id', $this->id)
                                    ->value('value');
                            } else {
                                $_value = NULL;
                            }
                            $_param_selected = $_value;
                            if ($render_field) {
                                $_view = field_render("params.{$_param->id}", [
                                    'label'      => $_param->title . (isset($_values->unit_value) ? ", {$_values->unit_value}" : ''),
                                    'value'      => $_value,
                                    'attributes' => $_param_data,
                                ]);
                            }
                        }
                        $_response->put($_param->id, (object)[
                            'id'             => $_param->id,
                            'title'          => $_param->title,
                            'name'           => $_param->name,
                            'table'          => $_param->table,
                            'type'           => $_param->type,
                            'data'           => $_param_data,
                            'selected'       => $_param_selected,
                            'translate'      => $_param->translate ? unserialize($_param->translate) : NULL,
                            'translate_data' => $_values_translate,
                            'style_data'     => $_param_data_style,
                            'view'           => $_view,
                        ]);
                    }
                }
            }

            if ($_response->isNotEmpty()) {
                return $this->params_product = $_response->keyBy('name');
            } else {
                return $this->params_product = NULL;
            }
        }

        public function _modification_param_links($language = DEFAULT_LANGUAGE)
        {
            $_base_modification_param_id = 44;
            $_response = [
                'params' => NULL,
                'items'  => NULL,
                'view'   => [
                    'current' => NULL,
                    'data'    => NULL,
                ]
            ];
            $_modification = $this->modifications;
            if (!is_null($_modification['param'])) {
                $_response['params'] = $_modification['param']->sortBy('id')->keyBy('id')->map(function ($_param) {
                    $_output = [
                        'id'      => $_param->id,
                        'name'    => $_param->name,
                        'title'   => $_param->title,
                        'options' => $_param->_items
                    ];
                    if ($_output['options']->isNotEmpty()) {
                        $_output['options'] = $_output['options']->sortBy('sort')->keyBy('id')->map(function ($_option) {
                            return [
                                'id'    => $_option->id,
                                'name'  => $_option->name,
                                'style' => [
                                    'id'          => $_option->style_id,
                                    'class'       => $_option->style_class,
                                    'color_shade' => $_option->color_shade,
                                ]
                            ];
                        });
                    }

                    return (object)$_output;
                });
            }
            if ($_modification['items']->isNotEmpty()) {
                $_modification_params = $_response['params'];
                if ($language != DEFAULT_LANGUAGE) {
                    $_current_product = self::related($this->id)
                        ->language($language)
                        ->location()
                        ->value('id');
                } else {
                    $_current_product = $this->id;
                }
                $_response['items'] = $_modification['items']->sortBy('modification')->keyBy('id')->map(function ($_product) use ($_modification_params, $_current_product, $language, &$_response) {
                    $_product_params = $_product->_params();
                    $_product_params = $_product_params->keyBy('id');
                    $_output_params = NULL;
                    if (!is_null($_modification_params) && $_product_params) {
                        foreach ($_modification_params as $_param_key => $_param_data) {
                            if (isset($_product_params[$_param_key]->selected[0])) {
                                $_output_params[$_param_key] = $_product_params[$_param_key]->selected[0];
                                if (!isset($_response['view']['data'][$_param_key][$_product_params[$_param_key]->selected[0]])) {
                                    $_output_param_option = $_response['params']->get($_param_key)->options->get($_product_params[$_param_key]->selected[0]);
                                    $_output_param_option['found'] = NULL;
                                    $_response['view']['data'][$_param_key][$_product_params[$_param_key]->selected[0]] = $_output_param_option;
                                }
                            }
                        }
                    }
                    if ($language != DEFAULT_LANGUAGE) {
                        $_product = self::related($_product->id)
                            ->language($language)
                            ->location()
                            ->with([
                                '_alias'
                            ])
                            ->first();
                    }
                    $_output = [
                        'id'        => $_product->id,
                        'title'     => $_product->title,
                        'url_id'    => $_product->_alias->id,
                        'url_alias' => $_product->language == DEFAULT_LANGUAGE ? _u($_product->_alias->alias) : _u("{$_product->language}/{$_product->_alias->alias}"),
                        'style'     => [
                            'id'    => $_product->style_id,
                            'class' => $_product->style_class,
                        ],
                        'param'     => $_output_params,
                        'current'   => $_current_product == $_product->id ? 1 : 0,
                        'base'      => $_product->modification == 0 ? 1 : 0
                    ];
                    if ($_current_product == $_product->id) {
                        $_response['view']['current'] = $_output_params;
                    }

                    return (object)$_output;
                });
                if ($_response['view']['data']) {
                    foreach ($_response['view']['data'] as $_param_key => $_options) {
                        foreach ($_options as $_option_key => $_option) {
                            if (is_null($_response['view']['data'][$_param_key][$_option_key]['found'])) {
                                $_needle_product = $_response['view']['current'];
                                $_needle_product[$_param_key] = $_option_key;
                                $_temp = [];
                                foreach ($_response['items'] as $_product_id => $_product_data) {
                                    $_diff_value = array_diff_assoc($_needle_product, $_product_data->param);
                                    if (count($_diff_value) == 0) {
                                        $_response['view']['data'][$_param_key][$_option_key]['found'] = $_product_data;
                                        break;
                                    }
                                    if (isset($_product_data->param[$_param_key]) && $_product_data->param[$_param_key] == $_option_key) {
                                        $_temp[] = $_product_data;
                                    }
                                }
                                if (is_null($_response['view']['data'][$_param_key][$_option_key]['found']) && count($_temp) && $_base_modification_param_id == $_param_key) {
                                    $_response['view']['data'][$_param_key][$_option_key]['found'] = array_shift($_temp);
                                }
                            }
                        }
                    }
                }
            } else {
                $_response = NULL;
            }

            return $_response;
        }

        public function _watched()
        {
            $_viewed = new ShopProductWatched($this);
            $this->watched_product = $_viewed->_set();
        }

        public function _related_products($status = NULL)
        {
            if ($status) {
                $language = $this->front_language;
                $items = DB::table('shop_products as p')
                    ->leftJoin('shop_product_related as pr', 'p.id', '=', 'pr.related_id')
                    ->where('pr.product_id', $this->id)
                    ->where('p.status', $status)
                    ->get([
                        'p.id',
                    ]);
                if ($items->isNotEmpty()) {
                    $items = $items->map(function ($_product) use ($language) {
                        return shop_product_load($_product->id, $language);
                    });
                }

                return $items;
            } else {
                return DB::table('shop_products as p')
                    ->leftJoin('shop_product_related as pr', 'p.id', '=', 'pr.related_id')
                    ->where('pr.product_id', $this->id)
                    ->get([
                        'p.id',
                        'p.title',
                    ]);
            }
        }

        public function _groups_product($status = NULL)
        {
            if ($status) {
                $_language = $this->front_language;
                $_product_price = $this->prices_product;
                $items = DB::table('shop_products as p')
                    ->leftJoin('shop_product_groups as pg', 'p.id', '=', 'pg.related_id')
                    ->where('pg.product_id', $this->id)
                    ->where('p.status', $status)
                    ->get([
                        'p.id',
                        'pg.id as group_id',
                        'pg.percent',
                    ]);
                if ($items->isNotEmpty()) {
                    $items = $items->map(function ($_product) use ($_language, $_product_price) {
                        $_item = shop_product_load($_product->id, $_language);
                        $_item->discount_percent = $_product->percent;
                        $_item->relation_entity->price = $_item->relation_entity->price - $_item->relation_entity->price * ($_item->discount_percent / 100);
                        $_item->discount_price_product = $_item->relation_entity->price_view;
                        $_item->relation_entity->price = $_item->relation_entity->price + $_product_price['price']['format']['price'];
                        $_item->id_product_groups = $_product->group_id;
                        $_item->price_product_group = $_item->relation_entity->price_view;

                        return $_item;
                    });
                }

                return $items;
            } else {
                return DB::table('shop_products as p')
                    ->leftJoin('shop_product_groups as pg', 'p.id', '=', 'pg.related_id')
                    ->where('pg.product_id', $this->id)
                    ->get([
                        'p.id as product_id',
                        'p.title as product_title',
                        'pg.id',
                        'pg.percent',
                    ])->keyBy('id');
            }
        }

        /**
         * Items
         */
        public static function hit_items($language = DEFAULT_LANGUAGE)
        {
            $_items = ShopProduct::language(DEFAULT_LANGUAGE)
                ->with([
                    '_alias',
                    '_preview',
                    '_discount_timer'
                ])
                ->location()
                ->active()
                ->where(function ($query) {
                    $query->where('mark_hit', 1)
                        ->orWhere('viewed', '>', 0);
                })
                ->where('out_of_stock', 0)
                ->orderByDesc('mark_hit')
                ->orderByDesc('viewed')
                ->orderBy('sort')
                ->limit(10)
                ->remember(15)
                ->get();
            if ($_items->isNotEmpty()) {
                $_items = $_items->map(function ($_product) use ($language) {
                    if ($language != DEFAULT_LANGUAGE) return shop_product_load($_product->id, $language);
                    $_product->_load('short');

                    return $_product;
                });
            }

            return $_items;
        }

        public static function elected_items($language = DEFAULT_LANGUAGE)
        {
            $_items = ShopProduct::where('mark_elected', 1)
                ->language(DEFAULT_LANGUAGE)
                ->with([
                    '_alias',
                    '_preview',
                    '_discount_timer'
                ])
                ->location()
                ->active()
                ->where('out_of_stock', 0)
                ->orderByDesc('fasten_to_top')
                ->orderBy('sort')
                ->limit(10)
                ->remember(15)
                ->get();
            if ($_items->isNotEmpty()) {
                $_items = $_items->map(function ($_product) use ($language) {
                    if ($language != DEFAULT_LANGUAGE) return shop_product_load($_product->id, $language);
                    $_product->_load('short');

                    return $_product;
                });
            }

            return $_items;
        }

        public static function watched_items($language = DEFAULT_LANGUAGE)
        {
            $_viewed = new ShopProductWatched();
            $_viewed = $_viewed->_get();
            $_items = collect([]);
            if ($_viewed->isNotEmpty()) {
                $_items = $_viewed->map(function ($_product) use ($language) {
                    if ($language != DEFAULT_LANGUAGE) {
                        return shop_product_load($_product->product_id, $language);
                    } else {
                        if (isset($_product->_product)) {
                            $__product = $_product->_product;
                            $__product->_load('short');

                            return $__product;
                        } else {
                            return shop_product_load($_product->product_id, $language);
                        }
                    }
                });
            }

            return $_items;
        }

        public static function discount_items($entity)
        {
            $_wrap = wrap()->get();
            $_language = $_wrap['locale'];
            $_current_page = currentPage();
            $_per_page = 9;
            Paginator::currentPageResolver(function () use ($_current_page) {
                return $_current_page ? $_current_page : 1;
            });
            $items = ShopProduct::from('shop_products as p')
                ->select([
                    'p.id',
                ])
                ->where('p.mark_discount', 1)
                ->where('p.language', DEFAULT_LANGUAGE)
                ->whereNull('p.relation')
                ->where('p.status', 1)
                ->orderBy('p.out_of_stock')
                ->orderByDesc('p.fasten_to_top')
                ->orderBy('p.sort')
                ->paginate($_per_page);
            if ($items->isNotEmpty() && count($items->items())) {
                $items->getCollection()->transform(function ($_product) use ($_language) {
                    return shop_product_load($_product->id, $_language);
                });
            }
            $_current_url = preg_replace('/page-[0-9]+/i', '', request()->url());
            $_current_page = $items->currentPage();
            $_next_page = $_current_page + 1;
            $_prev_page = ($_prev = $_current_page - 1) && $_prev > 0 ? $_prev : 1;
            $_query_string = NULL;
            $_next_page_link = NULL;
            $_prev_page_link = NULL;
            if ($_request_query_array = request()->query()) {
                unset($_request_query_array['page']);
                if (count($_request_query_array)) {
                    foreach ($_request_query_array as $query => $value) {
                        if (is_string($value)) {
                            $_query_string[] = "{$query}={$value}";
                        } elseif (is_array($value)) {
                            foreach ($value as $_val) $_query_string[] = "{$query}[]={$_val}";
                        }
                    }
                    $_query_string = $_query_string ? '?' . implode('&', $_query_string) : '';
                }
            }
            if ($_current_page < $items->lastPage()) {
                $url = trim($_current_url, '/') . "/page-{$_next_page}";
                $_next_page_link = _u($url) . $_query_string;
            }
            if ($_current_page > 2) {
                $url = trim($_current_url, '/') . "/page-{$_prev_page}";
                $_prev_page_link = _u($url) . $_query_string;
            } else {
                $url = trim($_current_url, '/');
                $_prev_page_link = _u($url) . $_query_string;
            }
            wrap()->set('seo._link_prev', $_prev_page_link);
            wrap()->set('seo._link_next', $_next_page_link);
            wrap()->set('seo._page_number', $_current_page);
            if ($_current_page > 1) {
                wrap()->set('seo._robots', 'noindex, nofollow');
                wrap()->set('seo._title_suffix', ' - ' . trans('others.page_full', ['page' => $_current_page]) . ' ' . wrap()->get('seo._title_suffix'));
                wrap()->set('seo._description', ($entity->meta_description ?? $_wrap['seo']['_description']) . ' - ' . trans('others.page_full', ['page' => $_current_page]));
                wrap()->set('page._title', $entity->title . ' - <i class="page-number">' . trans('others.page_full', ['page' => $_current_page]) . '</i>');
            }
            $_breadcrumb = breadcrumb_render(['entity' => $entity]);
            wrap()->set('breadcrumb', $_breadcrumb, TRUE);

            return $items;
        }

        /***
         * @return array|null
         */

        //        public function getModificationsAttribute()
        //        {
        //            $_response = NULL;
        //            if($this->exists) {
        //                $_response = [
        //                    'this'    => $this,
        //                    'primary' => NULL,
        //                    'param'   => NULL,
        //                    'items'   => NULL,
        //                ];
        //                if(is_null($this->modification_id)) {
        //                    $_response['primary'] = $this;
        //                } else {
        //                    $_response['primary'] = self::find($this->modification_id);
        //                }
        //                $_response['items'] = self::where('modification_id', $_response['primary']->id)
        //                    ->where('modification', 1)
        //                    ->language()
        //                    ->location()
        //                    ->with([
        //                        '_alias',
        //                        '_preview'
        //                    ])
        //                    ->get();
        //                if($_response['items']->isNotEmpty() && ($this->language == $_response['primary']->language)) {
        //                    $_response['items']->prepend($_response['primary']);
        //                }
        //                $_categories = $this->categories ? $this->categories['selected'] : NULL;
        //                if($_categories) {
        //                    $_related_params = ShopParam::rightJoin('shop_category_params', 'shop_params.id', '=',
        //                        'shop_category_params.param_id')
        //                        ->whereIn('shop_category_params.category_id', $_categories->values()->toArray())
        //                        ->where('shop_category_params.modify', 1)
        //                        ->get([
        //                            'shop_params.*'
        //                        ]);
        //                    if($_related_params) {
        //                        $_response['param'] = $_related_params;
        //                    }
        //                }
        //            }
        //
        //            return $_response;
        //        }


        //        public function _categories()
        //        {
        //            $this->cat = ShopProductCategory::where('product_id', $this->id)
        //                ->pluck('category_id');
        //
        //            return $this->hasMany(ShopCategory::class, 'id', 'cat');
        //        }


        public function _get_param($param = NULL, $category = NULL)
        {
            $_response = collect([]);
            $this->_params();
            $_language = wrap()->get('locale');
            if ($this->params) {
                $_category = $category ?? ($this->_category() ? $this->_category()->id : NULL);
                if ($_category) {
                    foreach ($this->params as $_param_id => $_param_data) {
                        if ($_selected = $_param_data->selected) {
                            $_filter_page = NULL;
                            $_translate_data = $_param_data->translate_data;
                            $_data = NULL;
                            if ($_param_data->data && $_param_data->type == 'select') {
                                $_data = $_param_data->data->map(function ($_item_data, $_item_key) use ($_selected, $_translate_data) {
                                    if (is_array($_selected) && in_array($_item_key, $_selected)) {
                                        $_tmp = [
                                            DEFAULT_LANGUAGE => $_item_data
                                        ];
                                        if ($_item_translate = $_translate_data->get($_item_key)) $_tmp = array_merge($_tmp, unserialize($_item_translate));

                                        return $_tmp;
                                    } elseif (!is_array($_selected) && $_item_key == $_selected) {
                                        $_tmp = [
                                            DEFAULT_LANGUAGE => $_item_data
                                        ];
                                        if ($_item_translate = $_translate_data->get($_item_key)) $_tmp = array_merge($_tmp, unserialize($_item_translate));

                                        return $_tmp;
                                    }
                                })->filter(function ($_item_data) {
                                    return !is_null($_item_data);
                                })->all();
                                if (!is_array($_selected)) {
                                    $_data_request = [
                                        $_param_data->name => [
                                            $_param_data->selected
                                        ]
                                    ];
                                    if ($_shop_filter_page = ShopFilterParamsPage::where('selected_params', serialize($_data_request))
                                        ->where('category_id', $_category)
                                        ->where('language', $_language)
                                        ->with(['_alias'])
                                        ->first()) {
                                        $_filter_page = _u($_shop_filter_page->_alias->alias, [], TRUE);
                                    }
                                };
                            } elseif ($_param_data->type == 'input_number' || $_param_data->type == 'input_text') {
                                $_unit_data = NULL;
                                if ($_param_data->data) $_unit_data = [DEFAULT_LANGUAGE => $_param_data->data['unit']];
                                if ($_translate_data && $_unit_data) $_unit_data = array_merge($_unit_data, unserialize($_translate_data));
                                if ($_unit_data) {
                                    foreach ($_unit_data as &$_unit) $_unit = "{$_selected} <span class='unit'>{$_unit}</span>";
                                } else {
                                    $_unit_data[DEFAULT_LANGUAGE] = $_selected;
                                }
                                $_data[] = $_unit_data;
                            }
                            if (($_selected && !is_null($param) && ($_param_data->name == $param)) || ($_selected && is_null($param))) {
                                $_param_title = [
                                    DEFAULT_LANGUAGE => $_param_data->title
                                ];
                                if ($_param_data->translate) $_param_title = array_merge($_param_title, unserialize($_param_data->translate));
                                if (is_null($param)) {
                                    $_response->put($_param_id, [
                                        'title'             => $_param_title,
                                        'name'              => $_param_data->name,
                                        'type'              => $_param_data->type,
                                        'visible_in_filter' => $_param_data->visible_in_filter,
                                        'selected'          => $_selected,
                                        'data'              => $_data,
                                        'filter_page_alias' => $_filter_page
                                    ]);
                                } else {
                                    $_response = collect([
                                        'title'             => $_param_title,
                                        'name'              => $_param_data->name,
                                        'type'              => $_param_data->type,
                                        'visible_in_filter' => $_param_data->visible_in_filter,
                                        'selected'          => $_selected,
                                        'data'              => $_data,
                                        'filter_page_alias' => $_filter_page
                                    ]);
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            return $_response->isNotEmpty() ? $_response : NULL;
        }


        public function _items()
        {
            return $this->hasMany(ShopParamItem::class, 'param_id')
                ->orderBy('sort');
        }

        //        public function _set_duplicate($language = NULL)
        //        {
        //            if($language) {
        //                $_exists = self::where('relation', $this->id);
        //                if($language) {
        //                    $_exists->where('language', $language);
        //                } else {
        //                    $_exists->where('language', DEFAULT_LANGUAGE);
        //                }
        //                $_exists = $_exists->count();
        //                if($_exists == 0) {
        //                    $item = self::updateOrCreate([
        //                        'id' => NULL
        //                    ], [
        //                        'sky'              => $this->sky,
        //                        'modification_id'  => $this->modification_id,
        //                        'language'         => $language ? $language : $this->language,
        //                        'location'         => $this->location,
        //                        'title'            => "{$this->title} (copy)",
        //                        'sub_title'        => $this->sub_title,
        //                        'preview_fid'      => $this->preview_fid,
        //                        'background_fid'   => $this->background_fid,
        //                        'body'             => $this->body,
        //                        'style_id'         => $this->style_id,
        //                        'style_class'      => $this->style_class,
        //                        'meta_title'       => $this->meta_title,
        //                        'meta_description' => $this->meta_description,
        //                        'meta_keywords'    => $this->meta_keywords,
        //                        'meta_robots'      => $this->meta_robots,
        //                        'sitemap'          => $this->sitemap,
        //                        'sort'             => $this->sort,
        //                        'access'           => $this->access,
        //                        'status'           => $this->status,
        //                        'advice'           => $this->advice,
        //                        'relation'         => $this->id,
        //                        'price'            => $this->price,
        //                        'old_price'        => $this->old_price,
        //                        'currency'         => $this->currency,
        //                        'count'            => $this->count,
        //                        'not_limited'      => $this->not_limited,
        //                        'out_of_stock'     => $this->out_of_stock,
        //                        'ordered'          => $this->ordered,
        //                        'mark_new'         => $this->mark_new,
        //                        'mark_hit'         => $this->mark_hit,
        //                        'mark_discount'    => $this->mark_discount,
        //                        'fasten_to_top'    => $this->fasten_to_top
        //                    ]);
        //                    if($this->_alias) {
        //                        $_alias = UrlAlias::updateOrCreate([
        //                            'id' => NULL,
        //                        ], [
        //                            'model_id'   => $item->id,
        //                            'model_type' => $item->getMorphClass(),
        //                            'alias'      => "{$this->_alias->alias}-copy",
        //                            'language'   => $item->language,
        //                            'location'   => $item->location,
        //                        ]);
        //                        $item->alias_id = $_alias->id;
        //                        $item->save();
        //                    }
        //                    $_file_reference = FilesReference::where('model_type', $this->getMorphClass())
        //                        ->where('model_id', $this->id)
        //                        ->get([
        //                            'model_type',
        //                            'type',
        //                            'relation_fid'
        //                        ]);
        //                    if($_file_reference->isNotEmpty()) {
        //                        $_file_reference = $_file_reference->map(function ($_file) use ($item) {
        //                            $_file['model_id'] = $item->id;
        //
        //                            return $_file;
        //                        });
        //                        FilesReference::insert($_file_reference->toArray());
        //                    }
        //
        //                    return $item;
        //                }
        //            }
        //
        //            return NULL;
        //        }

        public function _set_modification($type_mode = 0, $save = [])
        {
            if ($type_mode == 2) {
                $item = self::updateOrCreate([
                    'id' => NULL
                ], [
                    'sky'              => $save['sky'],
                    'modification'     => 1,
                    'modification_id'  => $this->id,
                    'language'         => $this->language,
                    'location'         => $this->location,
                    'title'            => $save['title'],
                    'sub_title'        => $this->sub_title,
                    'preview_fid'      => $this->preview_fid,
                    'background_fid'   => $this->background_fid,
                    'body'             => $this->body,
                    'style_id'         => $this->style_id,
                    'style_class'      => $this->style_class,
                    'meta_title'       => $this->meta_title,
                    'meta_description' => $this->meta_description,
                    'meta_keywords'    => $this->meta_keywords,
                    'meta_robots'      => $this->meta_robots,
                    'sitemap'          => $this->sitemap,
                    'sort'             => $this->sort,
                    'access'           => $this->access,
                    'status'           => $this->status,
                    'advice'           => $this->advice,
                    'price'            => $save['price'],
                    'out_of_stock'     => 1,
                    'mark_new'         => $this->mark_new,
                    'mark_hit'         => $this->mark_hit,
                    'mark_discount'    => $this->mark_discount,
                    'fasten_to_top'    => $this->fasten_to_top
                ]);
                if ($this->_alias) {
                    $_alias = new UrlAlias($item);
                    $_alias->set();
                }
                $_file_reference = FilesReference::where('model_type', $this->getMorphClass())
                    ->where('model_id', $this->id)
                    ->get([
                        'model_type',
                        'type',
                        'relation_fid'
                    ]);
                if ($_file_reference->isNotEmpty()) {
                    $_file_reference = $_file_reference->map(function ($_file) use ($item) {
                        $_file['model_id'] = $item->id;

                        return $_file;
                    });
                    FilesReference::insert($_file_reference->toArray());
                }
                if ($_category = $this->category) {
                    ShopProductCategory::updateOrCreate([
                        'id' => NULL,
                    ], [
                        'product_id'  => $item->id,
                        'category_id' => $_category->id
                    ]);
                }
                if ($_params = $this->_params()) {
                    foreach ($_params as $_param) {
                        if ($_param->selected) {
                            $_param_options_insert = NULL;
                            if (isset($save['relation']['option'][$_param->id])) {
                                $_param->selected = [$save['relation']['option'][$_param->id]];
                            }
                            switch ($_param->type) {
                                case 'select':
                                    if (is_array($_param->selected) || is_object($_param->selected)) {
                                        foreach ($_param->selected as $_option) {
                                            $_param_options_insert[] = [
                                                'product_id' => $item->id,
                                                'option_id'  => $_option,
                                            ];
                                        }
                                    } else {
                                        $_param_options_insert = [
                                            'product_id' => $item->id,
                                            'option_id'  => $_param->selected
                                        ];
                                    }
                                    break;
                                case 'input_number':
                                case 'input_text':
                                    $_param_options_insert = [
                                        'product_id' => $item->id,
                                        'value'      => $_param->selected
                                    ];
                                    break;
                            }
                            if ($_param_options_insert) {
                                DB::table($_param->table)
                                    ->insert($_param_options_insert);
                            }
                        }
                    }
                }
            } else {
                $item = self::find($save['relation_item']['value'])
                    ->update([
                        'modification'    => 1,
                        'modification_id' => $this->id,
                    ]);
            }

            return $item;
        }


        public function _mod()
        {
            return $this->hasMany(self::class, 'modification_id')
                ->where('modification', 1)
                ->with([
                    '_alias'
                ]);
        }


        //        public function getModificationParamLinksAttribute()
        //        {
        //            $_base_modification_param_id = 44;
        //            $_response = [
        //                'params' => NULL,
        //                'items'  => NULL,
        //                'view'   => [
        //                    'current' => NULL,
        //                    'data'    => NULL,
        //                ]
        //            ];
        //            $_modification = $this->modifications;
        //            if(!is_null($_modification['param'])) {
        //                $_response['params'] = $_modification['param']->sortBy('id')->keyBy('id')->map(function ($_param) {
        //                    $_output = [
        //                        'id'      => $_param->id,
        //                        'name'    => $_param->name,
        //                        'title'   => $_param->title,
        //                        'options' => $_param->_items
        //                    ];
        //                    if($_output['options']->isNotEmpty()) {
        //                        $_output['options'] = $_output['options']->sortBy('sort')->keyBy('id')->map(function ($_option) {
        //                            return [
        //                                'id'    => $_option->id,
        //                                'name'  => $_option->name,
        //                                'style' => [
        //                                    'id'    => $_option->style_id,
        //                                    'class' => $_option->style_class,
        //                                ]
        //                            ];
        //                        });
        //                    }
        //
        //                    return (object)$_output;
        //                });
        //            }
        //            if($_modification['items']->isNotEmpty()) {
        //                $_modification_params = $_response['params'];
        //                $_current_product = $this->id;
        //                $_response['items'] = $_modification['items']->sortBy('modification')->keyBy('id')->map(function (
        //                    $_product
        //                ) use ($_modification_params, $_current_product, &$_response) {
        //                    $_product_params = $_product->_params();
        //                    $_output_params = NULL;
        //                    if(!is_null($_modification_params) && $_product_params) {
        //                        foreach($_modification_params as $_param_key => $_param_data) {
        //                            if(isset($_product_params[$_param_key])) {
        //                                $_output_params[$_param_key] = $_product_params[$_param_key]->selected;
        //                                if(!isset($_response['view']['data'][$_param_key][$_product_params[$_param_key]->selected])) {
        //                                    $_output_param_option = $_response['params']->get($_param_key)->options->get($_product_params[$_param_key]->selected);
        //                                    $_output_param_option['found'] = NULL;
        //                                    $_response['view']['data'][$_param_key][$_product_params[$_param_key]->selected] = $_output_param_option;
        //                                }
        //                            }
        //                        }
        //                    }
        //                    $_output = [
        //                        'id'        => $_product->id,
        //                        'title'     => $_product->title,
        //                        'url_id'    => $_product->_alias->id,
        //                        'url_alias' => $_product->_alias->alias,
        //                        'style'     => [
        //                            'id'    => $_product->style_id,
        //                            'class' => $_product->style_class,
        //                        ],
        //                        'param'     => $_output_params,
        //                        'current'   => $_current_product == $_product->id ? 1 : 0,
        //                        'base'      => $_product->modification == 0 ? 1 : 0
        //                    ];
        //                    if($_current_product == $_product->id) {
        //                        $_response['view']['current'] = $_output_params;
        //                    }
        //
        //                    return (object)$_output;
        //                });
        //                if($_response['view']['data']) {
        //                    foreach($_response['view']['data'] as $_param_key => $_options) {
        //                        foreach($_options as $_option_key => $_option) {
        //                            if(is_null($_response['view']['data'][$_param_key][$_option_key]['found'])) {
        //                                $_needle_product = $_response['view']['current'];
        //                                $_needle_product[$_param_key] = $_option_key;
        //                                $_temp = [];
        //                                foreach($_response['items'] as $_product_id => $_product_data) {
        //                                    $_diff_value = array_diff_assoc($_needle_product, $_product_data->param);
        //                                    if(count($_diff_value) == 0) {
        //                                        $_response['view']['data'][$_param_key][$_option_key]['found'] = $_product_data;
        //                                        break;
        //                                    }
        //                                    if(isset($_product_data->param[$_param_key]) && $_product_data->param[$_param_key] == $_option_key) {
        //                                        $_temp[] = $_product_data;
        //                                    }
        //                                }
        //                                if(is_null($_response['view']['data'][$_param_key][$_option_key]['found']) && count($_temp) && $_base_modification_param_id == $_param_key) {
        //                                    $_response['view']['data'][$_param_key][$_option_key]['found'] = array_shift($_temp);
        //                                }
        //                            }
        //                        }
        //                    }
        //                }
        //            } else {
        //                $_response = NULL;
        //            }
        //
        //            return $_response;
        //        }

    }

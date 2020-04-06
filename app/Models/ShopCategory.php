<?php

    namespace App\Models;

    use App\Library\BaseModel;
    use Illuminate\Pagination\Paginator;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;

    class ShopCategory extends BaseModel
    {
        protected $table = 'shop_categories';
        protected $guarded = [];
        public $classIndex = 'shop_category';
        public $filter_request = NULL;
        public $sub_categories = NULL;

        public function __construct()
        {
            parent::__construct();
        }

        /**
         * Other
         */

        public function _load()
        {
            $entity = clone $this;
            // Cache::forget("{$this->classIndex}_{$this->id}");
            $entity = Cache::remember("{$this->classIndex}_{$this->id}", 3600, function () use ($entity) {
                $_response = new \stdClass();
                $_relation = clone $entity;
                if ($entity->relation) {
                    $_relation = self::where('id', $entity->relation)
                        ->with([
                            '_alias',
                        ])
                        ->first();
                }
                $_response->last_modified = $entity->_last_modified();
                // $_response->body = content_render($entity);
                // $_response->teaser = content_render($entity, 'teaser');
                //                $_response->background = [
                //                    'path'  => $entity->_background_asset(),
                //                    'style' => $entity->_background_style(),
                //                ];
                $_response->relation_entity = $_relation;

                return $_response;
            });
            foreach ($entity as $_key => $_data) if($_data) $this->{$_key} = $_data;
            $this->body = content_render($this);
            $this->teaser = content_render($this, 'teaser');	
            // if(request()->has('code')){
            // 	dd($this->body, $entity);
            // }
            $_templates = [
                "front.shop.category_relation_{$entity->relation_entity->id}",
                "front.shop.category_relation_{$entity->relation_entity->id}_category_{$this->id}",
                "front.shop.category_{$this->id}",
                "front.shop.category",
                'oleus.base.shop_category'
            ];
            $this->template = choice_template($_templates);

            return $this;
        }

        public function _render()
        {
            $this->_load();
            $this->set_wrap([
                'seo._title'         => $this->meta_title ?? trans('shop.page_title_catalog_default', ['title' => $this->title]),
                'seo._description'   => $this->meta_description ?? trans('shop.page_description_catalog_default', ['title' => $this->title]),
                'seo._keywords'      => $this->meta_keywords,
                'seo._robots'        => $this->meta_robots,
                'seo._last_modified' => $this->_last_modified(),
                'page._title'        => $this->title,
                'page._id'           => $this->style_id,
                'page._class'        => $this->style_class,
                //                'page._background'   => $this->_background_style(),
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
                'alias'              => $this->_alias
            ]);
            wrap()->set('breadcrumb', breadcrumb_render(['entity' => $this]));
 			
            return $this;
        }

        public function _render_ajax_command($entity = NULL)
        {
            $entity = $entity ? $entity : $this;
            $_filter = $entity->_filter();
            $_sub_categories = $entity->children;
            $_items = $entity->_items();
            $_wrap = wrap()->get();
            $_more_load = request()->get('more_load', 0);
            if ($_filter['filter']) {
                $commands[] = [
                    'command' => 'replaceWith',
                    'target'  => '.shop-category-filter-card',
                    'data'    => clear_html($_filter['filter'])
                ];
            } elseif ($_sub_categories) {
                $commands[] = [
                    'command' => 'replaceWith',
                    'target'  => '.shop-category-filter-card',
                    'data'    => clear_html(view('front.shop.parent_filter_menu', [
                        'items' => $_sub_categories
                    ])->render())
                ];
            }
            $commands[] = [
                'command' => 'replaceWith',
                'target'  => '.filter-price.sort',
                'data'    => clear_html(view('front.shop.field_catalog_sort')
                    ->render())
            ];
            if ($_more_load) {
                $commands[] = [
                    'target'  => '.last-block-pagination',
                    'command' => 'remove',
                ];
            }
            // $commands[] = $_items;
            if ($_items->isNotEmpty()) {
                $commands[] = [
                    'command' => $_more_load ? 'append' : 'html',
                    'target'  => '.grid-category-product',
                    'data'    => clear_html(view('front.shop.items_category_products', [
                        '_more_load' => FALSE,
                        'items'      => $_items,
                        'language'   => $_wrap['locale'],
                        'location'   => $_wrap['location'],
                        'currency'   => $_wrap['currency']['current']
                    ])
                        ->render())
                ];
            } else {
                $commands[] = [
                    'command' => 'html',
                    'target'  => '.grid-category-product',
                    'data'    => clear_html(view('front.shop.items_category_products', [
                        '_more_load' => FALSE,
                        'items'      => $_items,
                        'language'   => $_wrap['locale'],
                        'location'   => $_wrap['location'],
                        'currency'   => $_wrap['currency']['current']
                    ])
                        ->render())
                ];
            }
            $_current_page = $_items->currentPage();
            $_page_in_url = $_current_page && $_current_page > 1 ? "page-{$_current_page}/" : NULL;
            if ($_current_page < 2 && $entity->body) {
                $commands[] = [
                    'command' => 'html',
                    'target'  => '#shop-category-description-card',
                    'data'    => "<div class='description-seo'><div class='uk-container uk-container-large'>{$entity->body}</div></div>"
                ];
            } else {
                $commands[] = [
                    'command' => 'html',
                    'target'  => '#shop-category-description-card',
                    'data'    => ''
                ];
            }
            $commands[] = [
                'command' => 'change_url',
                'url'     => _u($entity->_alias->alias) . $_page_in_url . formalize_url_query()
            ];
            $commands[] = [
                'command' => 'change_title',
                'title'   => "{$_wrap['seo']['_title']} {$_wrap['seo']['_title_suffix']}"
            ];
            $commands[] = [
                'command' => 'replaceWith',
                'target'  => '.uk-breadcrumb',
                'data'    => clear_html(view('oleus.base.breadcrumb')->render())
            ];

            return $commands;
        }

        public function _filter()
        {
            $_response = [
                'params'                => NULL,
                'filter'                => NULL,
                'selected'              => NULL,
                'count_selected_params' => 0
            ];

            //            return $_response;

            $_params = collect([]);
            $_request_query = $this->_request_query_params();
            $_this_object = $this;
            $_primary_object = $this->relation_entity;
            $_category_params = $_primary_object->_category_params->keyBy('id');
            $_count_selected_params = 0;
            //            $_category_params = ShopParam::from('shop_params as p')
            //                ->leftJoin('shop_category_params as scp', 'scp.param_id', '=', 'p.id')
            //                ->where('scp.category_id', $_primary_object->id)
            //                ->where('p.visible_in_filter', 1)
            //                ->orderBy('scp.sort')
            //                ->get([
            //                    'scp.category_id',
            //                    'scp.sort',
            //                    'scp.modify',
            //                    'p.id',
            //                    'p.type',
            //                    'p.type_view',
            //                    'p.title',
            //                    'p.name',
            //                    'p.table as table_param',
            //                    'p.language',
            //                    'p.translate',
            //                ])
            //                ->keyBy('id');
            if ($_category_params->isNotEmpty()) {
                $_params = $_category_params->map(function ($_param) use (
                    $_primary_object,
                    $_this_object,
                    $_request_query,
                    &$_response,
                    &$_count_selected_params
                ) {
                    $_param->primary_category = $_primary_object;
                    $_param->current_category = $_this_object;
                    $_open_param = FALSE;
                    $_view_param_title = $_param->_translate_title($_this_object->language);
                    $_related_param = NULL;
                    if ($_param_options = $_param->_filter_data($_request_query, $_this_object->language)) {
                        if ($_param_options['selected']) {
                            $_count_selected_params += count($_param_options['selected']);
                            $_response['selected'][$_param->name] = [
                                'type'    => $_param->type,
                                'checked' => $_param_options['selected']
                            ];
                        }
                        $_html = [
                            'label'  => $_view_param_title,
                            'values' => []
                        ];
                        if ($_param->type == 'select') {

                            if ($_param->name == 'color') {
                                $_color_groups = collect([]);
                                foreach ($_param_options['options'] as $_option) {
                                    $_item = $_color_groups->get($_option['color_shade'], []);
                                    $_item[] = $_option;
                                    $_color_groups->put($_option['color_shade'], $_item);
                                }
                                $_html['values'][] = '<ul class="uk-subnav uk-subnav-pill color-switcher" uk-switcher="animation: uk-animation-slide-left-medium, uk-animation-slide-right-medium; swiping: false; active: -1">';
                                $_color_groups->keys()->map(function ($_color) use (&$_html) {
                                    $_html['values'][] = "<li><a href='#' rel='nofollow' class='uk-display-inline-block'><span style='display: block; width: 15px; height: 15px; background-color: {$_color};'></span></a></li>";
                                });
                                $_html['values'][] = '</ul>';
                                $_html['values'][] = '<ul class="uk-switcher color-switcher-text">';
                                $_color_groups->map(function ($_color_options) use (&$_html, $_param) {
                                    $_html['values'][] = '<li>';
                                    foreach ($_color_options as $_option) {
                                        $_checked = $_option['selected'] ? 'checked' : NULL;
                                        $_checked_class = $_checked ? ' checked' : '';
                                        $_id = $_option['style_id'] ? $_option['style_id'] : str_slug($_param->name, '-') . "-{$_option['id']}";
                                        $_class = $_option['style_class'] ? str_slug($_param->name, '-') . "-{$_option['id']} {$_option['style_class']}" : str_slug($_param->name, '-') . "-{$_option['id']}";
                                        $_option_alias = $_option['filter_params_page_alias'];
                                        if ($_checked) {
                                            $_open_param = TRUE;
                                            $_option_alias = $_option['filter_params_back_alias'] . formalize_url_query();
                                        }
                                        if ($_option['number_result'] == 0) {
                                            $_html['values'][] = "<div id='{$_id}' class='{$_class}{$_checked_class} uk-flex uk-flex-between empty'>" .
                                                "<a href='{$_option_alias}' class='uk-filter-param use-ajax uk-disabled' data-view_load='0' disabled rel=''>" .
                                                "<span class='uk-name-link'>{$_option['name']}</span></a>" .
                                                "<div class='uk-checkbox empty'></div>" .
                                                "</div>";
                                        } else {
                                            $_html['values'][] = "<div id='{$_id}' class='{$_class}{$_checked_class} uk-flex uk-flex-between'>" .
                                                "<a href='{$_option_alias}' class='uk-filter-param use-ajax' data-view_load='0' rel=''>" .
                                                "<span class='uk-name-link'>{$_option['name']}</span></a>" .
                                                "<div class='uk-checkbox'></div>" .
                                                "</div>";
                                        }
                                    }
                                    $_html['values'][] = '</li>';
                                });
                                $_html['values'][] = '</ul>';
                            } else {
                                foreach ($_param_options['options'] as $_option) {
                                    $_checked = $_option['selected'] ? 'checked' : NULL;
                                    $_checked_class = $_checked ? ' checked' : '';
                                    $_id = $_option['style_id'] ? $_option['style_id'] : str_slug($_param->name, '-') . "-{$_option['id']}";
                                    $_class = $_option['style_class'] ? str_slug($_param->name, '-') . "-{$_option['id']} {$_option['style_class']}" : str_slug($_param->name, '-') . "-{$_option['id']}";
                                    $_option_alias = $_option['filter_params_page_alias'];
                                    if ($_checked) {
                                        $_open_param = TRUE;
                                        $_option_alias = $_option['filter_params_back_alias'] . formalize_url_query();
                                    }
                                    if ($_option['number_result'] == 0) {
                                        $_html['values'][] = "<div id='{$_id}' class='{$_class}{$_checked_class} uk-flex uk-flex-between empty'>" .
                                            "<a href='{$_option_alias}' class='uk-filter-param use-ajax uk-disabled' data-view_load='0' disabled rel=''>" .
                                            "<span class='uk-name-link'>{$_option['name']}</span></a>" .
                                            "<div class='uk-checkbox empty'></div>" .
                                            "</div>";
                                    } else {
                                        $_html['values'][] = "<div id='{$_id}' class='{$_class}{$_checked_class} uk-flex uk-flex-between'>" .
                                            "<a href='{$_option_alias}' class='uk-filter-param use-ajax' data-view_load='0' rel=''>" .
                                            "<span class='uk-name-link'>{$_option['name']}</span></a>" .
                                            "<div class='uk-checkbox'></div>" .
                                            "</div>";
                                    }
                                }
                            }
                        } elseif ($_param->type == 'input_number') {
                            $_diff_data = $_param_options['options']['max_value'] - $_param_options['options']['min_value'];
                            $_label_min = $_param_options['options']['values']['slider']['min'];
                            $_label_max = $_param_options['options']['values']['slider']['max'];
                            $_slide_step = $_diff_data > 100000 ? 1000 : ($_diff_data > 10000 ? 100 : ($_diff_data > 1000 ? 10 : 1));
                            $_id = $_param_options['options']['style_id'] ? "{$_param_options['options']['style_id']}" : str_slug($_param->name, '-') . "-{$_param_options['options']['id']}";
                            $_class = $_param_options['options']['style_class'] ? str_slug($_param->name, '-') . "-{$_param_options['id']} {$_param_options['options']['style_class']}" : str_slug($_param->name, '-') . "-{$_param_options['options']['id']}";
                            $_html['values'][] = "<div id='{$_id}' class='{$_class}'><div class='uk-flex-1'>" .
                                "<div class='input-slider-values uk-flex uk-width-1-1 uk-flex-middle'><div class='uk-flex-1'>" .
                                "<span class='value-min'>{$_label_min}</span>&nbsp;-&nbsp;<span class='value-max'>{$_label_max}</span> " .
                                "<span class='suffix'>{$_param_options['options']['unit_value']}</span></div>" .
                                "<div><button type='button' data-path='' data-view_load='0' data-name='{$_param->name}' data-back_path='{$_param_options['filter_params_back_alias']}' data-use_query='{$_param_options['filter_use_query_back_alias']}' class='use-ajax'>применить</button>" .
                                "</div></div>" .
                                "<div id='{$_id}-slider' class='input-slider' data-min='" . floor($_param_options['options']['min_value']) .
                                "' data-max='" . ceil($_param_options['options']['max_value']) . "' data-step='{$_slide_step}'" .
                                " data-selected-min='" . floor($_param_options['options']['values']['slider']['min']) .
                                "' data-selected-max='" . ceil($_param_options['options']['values']['slider']['max']) . "'></div>" .
                                "</div></div>";
                        }

                        return [
                            'title'     => $_view_param_title,
                            'name'      => $_param->name,
                            'table'     => $_param->table_param,
                            'type'      => $_param->type,
                            'type_view' => $_param->type_view,
                            'open'      => $_open_param,
                            'data'      => $_param_options,
                            'alias'     => $_param_options,
                            'html'      => $_html
                        ];
                    }
                });
            }
            if ($_params->isNotEmpty()) {
                $_params = $_params->filter(function ($_param) {
                    return !is_null($_param);
                });
            }
            if ($_params->isNotEmpty()) {
                $_selected_data = NULL;
                $_selected = request()->get('price');
                $_label = trans('forms.label_shop_category_filter_input_price', [], $_this_object->language);
                $_currency = wrap()->get('currency.current');
                $_min_max_values = ShopProduct::from('shop_products as p')
                    ->leftJoin('shop_product_categories as spc', 'p.id', '=', 'spc.product_id')
                    ->where('spc.category_id', $_primary_object->id)
                    ->where('p.status', 1)
                    ->select(DB::raw('max(p.price) as max'), DB::raw('min(p.price) as min'))
                    ->first();
                if (isset($_selected['min']) && $_selected['min']) $_selected_data['min'] = $_selected['min'];
                if (isset($_selected['max']) && $_selected['max'] && !isset($_selected_data['min'])) $_selected_data['min'] = $_min_max_values->min;
                if (isset($_selected['max']) && $_selected['max']) $_selected_data['max'] = $_selected['max'];
                if (isset($_selected_data['min']) && !isset($_selected_data['max'])) $_selected_data['max'] = $_min_max_values->max;
                if ($_selected_data) $_selected_data['alias'] = _u($_this_object->_alias->alias) . formalize_url_query(NULL, 'price');
                $min = transform_price($_min_max_values->min);
                $max = transform_price($_min_max_values->max);
                if ($_selected_data) {
                    $_selected_data['unit'] = $_currency['suffix'];
                    $_response['selected']['price'] = [
                        'type'    => 'input_number',
                        'checked' => $_selected_data
                    ];
                }
                $_price = [
                    'min_value' => $min['format']['price'],
                    'max_value' => $max['format']['price'],
                    'values'    => [
                        'min'    => isset($_selected['min']) && $_selected['min'] ? $_selected['min'] : $min['format']['price'],
                        'max'    => isset($_selected['max']) && $_selected['max'] ? $_selected['max'] : $max['format']['price'],
                        'slider' => [
                            'min' => (isset($_selected['min']) && $_selected['min'] ? $_selected['min'] : $min['format']['price']),
                            'max' => (isset($_selected['max']) && $_selected['max'] ? $_selected['max'] : $max['format']['price']),
                        ]
                    ],
                ];
                $_open_param = FALSE;
                $_html = [
                    'label'  => $_label,
                    'values' => []
                ];
                $_diff_data = $_price['max_value'] - $_price['min_value'];
                $_label_min = $min;
                $_label_max = $max;
                $_shop_filter_back_alias = _u(request()->url()) . formalize_url_query(NULL, 'price');
                $_use_query = formalize_url_query(NULL, 'price') ? 1 : 0;
                $_slide_step = $_diff_data > 100000 ? 1000 : ($_diff_data > 10000 ? 100 : ($_diff_data > 1000 ? 10 : 1));
                $_html['values'][] = "<div id='price' class='price-slider'><div class='uk-flex-1'>" .
                    "<div class='input-slider-values'><div><button type='button' data-path='' data-view_load='0' data-name='price' data-back_path='{$_shop_filter_back_alias}' data-use_query='{$_use_query}' class='use-ajax'>" . trans('front.button_apply') . "</button></div></div>" .
                    "<div id='price-slider' class='input-slider' data-min='" . floor($_price['min_value']) . "' data-max='" . ceil($_price['max_value']) . "' data-step='{$_slide_step}' data-selected-min='" . floor($_price['values']['slider']['min']) . "' data-selected-max='" . ceil($_price['values']['slider']['max']) . "'><div class='input-slider-values uk-flex uk-width-1-1 uk-flex-between'><span class='value-min'>{$_label_min['format']['view_price_2']}</span><span class='value-max'>{$_label_max['format']['view_price_2']}</span></div>" .
                    "<div class='ui-slider-handle min-handle'></div><div class='ui-slider-handle max-handle'></div>" .
                    "</div></div>";
                $_params->put('price', [
                    'title' => $_label,
                    'open'  => $_open_param,
                    'data'  => $_price,
                    'html'  => $_html
                ]);
            }
            if ($_params->isNotEmpty()) {
                $_templates_page = [
                    'front.shop.filter',
                    'oleus.base.shop_category_filter'
                ];
                $_response['params'] = $_params;
                $_response['filter'] = view(choice_template($_templates_page), [
                    'item'                  => $_this_object,
                    'params'                => $_params,
                    'selected'              => $_response['selected'],
                    'count_selected_params' => $_count_selected_params
                ])
                    ->render();
            }

            return $_response;
        }

        public function _items()
        {
            $_wrap = wrap()->get();
            $_request_query = $this->_request_query_params();
            $_default_category = (bool)isset($_wrap['shop_category_default']) ?? FALSE;
            $_language = $_wrap['locale'];
            $_current_page = currentPage();
            $_sub_categories = $this->relation_entity->childrens;
            if ($_sub_categories) {
                $_sub_categories = $_sub_categories->map(function ($_category) {
                    //                    return $_category->relation_entity->status ? $_category->relation_entity->id : FALSE;
                    return $_category->id;
                })->filter(function ($_category) {
                    return $_category;
                })->toArray();
            }
            Paginator::currentPageResolver(function () use ($_current_page) {
                return $_current_page ? $_current_page : 1;
            });
            $_per_page = 7;
            $_this_object = $this;
            $_primary_object = $this->relation_entity;
            $items = ShopProduct::from('shop_products as p')
                ->leftJoin('shop_product_categories as spc', 'spc.product_id', '=', 'p.id')
                ->select([
                    'p.id',
                    'p.alias_id',
                    'p.title',
                    'p.sky',
                    'p.modification',
                    'p.modification_id',
                    'p.sub_title',
                    'p.location',
                    'p.preview_fid',
                    'p.language',
                    'p.background_fid',
                    'p.price',
                    'p.old_price',
                    'p.base_price',
                    'p.currency',
                    'p.count',
                    'p.not_limited',
                    'p.out_of_stock',
                    'p.mark_new',
                    'p.mark_hit',
                    'p.mark_discount',
                    'p.mark_elected',
                    'p.status',
                    'p.sort',
                    'p.relation',
                ])
                ->with([
                    '_alias',
                    '_preview',
                    '_background',
                    '_discount_timer'
                ])
                ->where('p.language', DEFAULT_LANGUAGE)
                //                ->where('spc.category_id', $_primary_object->id)
                ->whereNull('p.relation')
                ->where('p.status', 1);
            if ($_sub_categories) {
                $items->whereIn('spc.category_id', $_sub_categories);
            } else {
                $items->where('spc.category_id', $_primary_object->id);
            }
            if ($_default_category) $items->where('p.modification', 0);
            $items->orderBy('p.out_of_stock')
                ->orderByDesc('p.fasten_to_top');
            $_index_param = 0;
            if (is_array($_request_query)) {
                foreach ($_request_query as $_query_param => $_query_param_values) {
                    if (!str_is('*utm*', $_query_param)) {
                        ++$_index_param;
                        if (is_array($_query_param_values) && count($_query_param_values['values']) && $_query_param_values['type'] == 'data') {
                            $items->leftJoin("shop_param_{$_query_param}_data as p{$_index_param}",
                                "p{$_index_param}.product_id", '=', 'p.id')
                                ->whereIn("p{$_index_param}.option_id", $_query_param_values['values']);
                        } elseif (is_array($_query_param_values) && count($_query_param_values['values']) && $_query_param_values['type'] == 'min_max') {
                            $_min_value = isset($_query_param_values['values']['min']) && $_query_param_values['values']['min'] ? $_query_param_values['values']['min'] : NULL;
                            $_max_value = isset($_query_param_values['values']['max']) && $_query_param_values['values']['max'] ? $_query_param_values['values']['max'] : NULL;
                            if ($_query_param != 'price') {
                                $items->leftJoin("shop_param_{$_query_param}_data as p{$_index_param}",
                                    "p{$_index_param}.product_id", '=', 'p.id')
                                    ->when($_min_value, function ($query) use ($_min_value, $_index_param) {
                                        return $query->where("p{$_index_param}.value", '>=', $_min_value);
                                    })->when($_max_value, function ($query) use ($_max_value, $_index_param) {
                                        return $query->where("p{$_index_param}.value", '<=', $_max_value);
                                    });
                            } else {
                                $items->when($_min_value, function ($query, $_min_value) {
                                    return $query->where('p.base_price', '>=', $_min_value);
                                })->when($_max_value, function ($query, $_max_value) {
                                    return $query->where('p.base_price', '<=', $_max_value);
                                });
                            }
                        } elseif ($_query_param == 'sort') {
                            wrap()->set('shop_filter_sort', $_query_param_values);
                            switch ($_query_param_values) {
                                case 'price_asc':
                                    $items->orderBy('p.base_price');
                                    break;
                                case 'price_desc':
                                    $items->orderByDesc('p.base_price');
                                    break;
                                case 'name_asc':
                                    $items->orderBy('p.title');
                                    break;
                                case 'name_desc':
                                    $items->orderByDesc('p.title');
                                    break;
                                case 'popular_asc':
                                    $items->orderBy('p.mark_hit')
                                        ->orderBy('p.ordered');
                                    break;
                                case 'popular_desc':
                                    $items->orderByDesc('p.mark_hit')
                                        ->orderByDesc('p.ordered');
                                    break;
                                case 'new_asc':
                                    $items->orderBy('p.mark_new');
                                    break;
                                case 'new_desc':
                                    $items->orderByDesc('p.mark_new');
                                    break;
                                case 'discount_asc':
                                    $items->orderBy('p.mark_discount');
                                    break;
                                case 'discount_desc':
                                    $items->orderByDesc('p.mark_discount');
                                    break;
                            }
                        }
                    }
                }
            }
            $items = $items->orderBy('p.sort')
                ->remember(15)
                ->paginate($_per_page);
            if ($items->isNotEmpty() && count($items->items())) {
                $items->getCollection()->transform(function ($_product) use ($_language) {
                    if ($_language == DEFAULT_LANGUAGE) {
                        $_product->_load('short');

                        return $_product;
                    } else {
                        return shop_product_load($_product->id, $_language, 'short');
                    }
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
            } elseif ($_current_page == 2) {
                $url = trim($_current_url, '/');
                $_prev_page_link = _u($url) . $_query_string;
            }
            wrap()->set('seo._link_prev', $_prev_page_link);
            wrap()->set('seo._link_next', $_next_page_link);
            wrap()->set('seo._page_number', $_current_page);
            if ($_current_page > 1) {
                wrap()->set('seo._robots', 'noindex, nofollow');
                wrap()->set('seo._title_suffix', ' - ' . trans('others.page_full', ['page' => $_current_page]) . ' ' . wrap()->get('seo._title_suffix'));
                wrap()->set('seo._description', ($_this_object->meta_description ?? $_wrap['seo']['_description']) . ' - ' . trans('others.page_full', ['page' => $_current_page]));
                $_page_title = $_this_object->title ?? trans('shop.page_description_catalog_default', ['title' => $_this_object->title]);
                wrap()->set('page._title', $_page_title . ' - <i class="page-number">' . trans('others.page_full', ['page' => $_current_page]) . '</i>');
            }
            $_breadcrumb = breadcrumb_render(['entity' => $_this_object]);
            wrap()->set('breadcrumb', $_breadcrumb, TRUE);

            return $items;
        }

        public function _request_query_params($string = FALSE)
        {
            $_request = NULL;
            $request = request()->all();
            $_params = wrap()->get('shop_filter_params');
            if (is_array($request)) {
                foreach ($request as $_param_name => $_param_options) {
                    if (is_array($_param_options)) {
                        foreach ($_param_options as $param_option_key => $param_option_value) {
                            $request[$_param_name][$param_option_key] = (int)$param_option_value;
                        }
                    } else {
                        $request[$_param_name] = $_param_options;
                    }
                }
            }
            if ($request && is_array($_params)) {
                foreach ($_params as $_param_name => $_param_options) {
                    foreach ($_param_options as $param_option_value) {
                        if (isset($request[$_param_name]) && is_array($request[$_param_name]) && !in_array((int)$param_option_value, $request[$_param_name])) {
                            $request[$_param_name][] = $param_option_value;
                        } elseif (!isset($request[$_param_name])) {
                            $request[$_param_name][] = $param_option_value;
                        }
                    }

                }
            } elseif (is_array($_params)) {
                $request = $_params;
            }
            if ($request) {
                foreach ($request as $_param => $_values) {
                    if (is_array($_values)) {
                        foreach ($_values as $_key => $_value) if ($_value) {
                            if (!isset($_request[$_param]['type'])) $_request[$_param]['type'] = is_string($_key) ? 'min_max' : 'data';
                            $_request[$_param]['values'][$_key] = $_value;
                        }
                    } else {
                        $_request[$_param] = $_values;
                    }
                }
            }
            if ($_request && $string) {
                $_exclude_query_param = [
                    'show_more'
                ];
                $_query_string = NULL;
                foreach ($_request as $_param => $_values) {
                    if (is_array($_values) && !in_array($_param, $_exclude_query_param)) {
                        foreach ($_values as $_key => $_value) {
                            if (is_numeric($_key)) {
                                $_query_string[] = "{$_param}[]=$_value";
                            } else {
                                $_query_string[] = "{$_param}[{$_key}]=$_value";
                            }
                        }
                    } elseif (!is_null($_values) && !in_array($_param, $_exclude_query_param)) {
                        $_query_string[] = "{$_param}={$_values}";
                    }
                }
                $_request = $_query_string ? implode('&', $_query_string) : NULL;
            }

            return $_request;
        }

        public function _get_parent_category(&$_data, $category_id, $t)
        {
            //            $_category = self::find($category_id);
            $_category = $t->_par;
            $_data[] = $_category;
            if ($_category->parent_id) $this->_get_parent_category($_data, $_category->parent_id, $_category);

            return $_data;
        }

        public function _get_children_category(&$_data, $category)
        {
            $entity = $this->hasAttribute('relation_entity') ? $category->relation_entity : $category;
            $_children = self::where('parent_id', $entity->id)
                ->language(DEFAULT_LANGUAGE)
                ->orderBy('sort')
                ->get();
            //            $_language = wrap()->get('locale');
            if ($_children->isNotEmpty()) {
                $_children->each(function ($_item) use (&$_data) {
                    $_data[] = $_item;
                    $_item->_get_children_category($_data, $_item);
                });
            }

            return $_data;
        }

        public function _filter_page()
        {
            return $this->hasMany(ShopFilterParamsPage::class, 'category_id');
        }

        /**
         * Attribute
         */

        public function getOtherCategoriesAttribute()
        {
            return self::where('id', '<>', $this->id)
                ->language(DEFAULT_LANGUAGE)
                ->location(DEFAULT_LOCATION)
                ->orderBy('title')
                ->pluck('title', 'id');
        }

        public function getParentAttribute()
        {
            if ($this->parent_id) return self::find($this->parent_id);

            return NULL;
        }

        public function getParentsAttribute()
        {
            $_parents = NULL;
            if ($this->parent_id) $_parents = $this->_get_parent_category($_parents, $this->parent_id, $this);

            return $_parents ? array_reverse($_parents) : $_parents;
        }

        public function getChildrenAttribute()
        {
            $entity = $this->hasAttribute('relation_entity') ? $this->relation_entity : $this;
            $_children = self::where('parent_id', $entity->id)
                ->language($entity->language)
                ->orderBy('sort')
                ->get();
            $_language = wrap()->get('locale');
            if ($_children->isNotEmpty()) {
                $_children = $_children->map(function ($_item) use ($_language) {
                    if ($_language != DEFAULT_LANGUAGE) {
                        return shop_category_load($_item->id, $_language);
                    } else {
                        $_item->_load();

                        return $_item;
                    }
                });
            }

            return $_children;
        }

        public function getChildrensAttribute()
        {
            $_childrens = $this->_get_children_category($_childrens, $this);

            return $_childrens ? collect($_childrens) : NULL;
        }

        public function getParamsAttribute()
        {
            $_response = NULL;
            $params = NULL;
            $_category_params = NULL;
            if ($_old = old('category_params')) {
                foreach ($_old as $key => $select) {
                    if ($select['applicable']) $category_params[] = $key;
                }
            } else {
                $_category_params = ShopCategoryParam::where('category_id', $this->id)
                    ->orderBy('sort')
                    ->pluck('sort', 'param_id')
                    ->toArray();
            }
            $_params_all = ShopParam::language(DEFAULT_LANGUAGE)
                ->orderBy('title')
                ->get();
            if ($_params_all->isNotEmpty()) {
                $_response = [
                    'applicable'     => collect([]),
                    'not_applicable' => collect([])
                ];
                $_params_all->map(function ($_param) use (&$_response, $_category_params) {
                    $_applicable = is_array($_category_params) && isset($_category_params[$_param->id]) ? $_category_params[$_param->id] : NULL;
                    $_type_applicable = !is_null($_applicable) ? 'applicable' : 'not_applicable';
                    $_response[$_type_applicable]->put($_param->id, (object)[
                        'id'    => $_param->id,
                        'name'  => $_param->name,
                        'title' => $_param->title,
                        'type'  => $_param->type,
                        'sort'  => !is_null($_applicable) ? $_applicable : 0,
                    ]);
                });
                if ($_response['applicable']->isNotEmpty()) $_response['applicable'] = $_response['applicable']->sortBy('sort');
            }

            return $_response;
        }

        public function getModifyParamAttribute()
        {
            $_response = NULL;

            $_category_params = ShopCategoryParam::leftJoin('shop_params', 'shop_params.id', '=',
                'shop_category_params.param_id')
                ->where('shop_category_params.category_id', $this->id)
                ->where('shop_params.type_view', 'modify')
                ->pluck('shop_params.title', 'shop_category_params.param_id');
            if ($_category_params->isNotEmpty()) {
                $_response = [
                    'relation_params' => $_category_params,
                    'selected'        => ShopCategoryParam::where('modify', 1)
                        ->where('category_id', $this->id)
                        ->pluck('param_id')
                ];
            }

            return $_response;
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */


        public function _short_code($data = NULL, $object)
        {
            $_response = NULL;
            if (!is_null($data)) {
                switch ($object) {
                    case 'medias':
                        $_template = choice_template([
                            "front.shop.category__medias_{$this->type}_{$this->id}",
                            "front.shop.category__medias_{$this->type}",
                            "front.shop.category__medias_{$this->id}",
                            'oleus.base.entity_medias'
                        ]);

                        return view($_template, ['items' => $data])
                            ->render();
                        break;
                    case 'files':
                        $_template = choice_template([
                            "front.shop.category__files_{$this->type}_{$this->id}",
                            "front.shop.category__files_{$this->type}",
                            "front.shop.category__files_{$this->id}",
                            'oleus.base.entity_files'
                        ]);

                        return view($_template, ['items' => $data])
                            ->render();
                        break;
                }
            }

            return $_response;
        }

        //        public function _set_duplicate($language = NULL, $location = NULL)
        //        {
        //            if($language || $location) {
        //                $_exists = self::where('relation', $this->id);
        //                if($location) {
        //                    $_exists->where('location', $location);
        //                } else {
        //                    $_exists->where('location', DEFAULT_LOCATION);
        //                }
        //                if($language) {
        //                    $_exists->where('language', $language);
        //                } else {
        //                    $_exists->where('language', DEFAULT_LANGUAGE);
        //                }
        //                $_exists = $_exists->count();
        //                if($_exists == 0) {
        //                    $_parent_id = NULL;
        //                    if($this->parent_id) {
        //                        $_parent_id = self::where('relation', $this->parent_id)
        //                            ->language(($language ? $language : $this->language))
        //                            ->location(($location ? $location : $this->location))
        //                            ->value('id');
        //                        if(is_null($_parent_id)) return NULL;
        //                    }
        //                    $item = self::updateOrCreate([
        //                        'id' => NULL
        //                    ], [
        //                        'title'            => $this->title,
        //                        'sub_title'        => $this->sub_title,
        //                        'body'             => $this->body,
        //                        'icon_fid'         => $this->icon_fid,
        //                        'background_fid'   => $this->background_fid,
        //                        'meta_title'       => $this->meta_title,
        //                        'meta_keywords'    => $this->meta_keywords,
        //                        'meta_description' => $this->meta_description,
        //                        'meta_robots'      => $this->meta_robots,
        //                        'sitemap'          => $this->sitemap,
        //                        'style_id'         => $this->style_id,
        //                        'style_class'      => $this->style_class,
        //                        'language'         => $language ? $language : $this->language,
        //                        'location'         => $location ? $location : $this->location,
        //                        'status'           => $this->status,
        //                        'sort'             => $this->sort,
        //                        'access'           => $this->access,
        //                        'parent_id'        => $_parent_id,
        //                        'relation'         => $this->id,
        //                    ]);
        //                    if($this->_alias) {
        //                        $_alias = UrlAlias::updateOrCreate([
        //                            'id' => NULL,
        //                        ], [
        //                            'model_id'   => $item->id,
        //                            'model_type' => $item->getMorphClass(),
        //                            'alias'      => $this->_alias->alias,
        //                            'language'   => $item->language,
        //                            'location'   => $item->location,
        //                        ]);
        //                        $item->alias_id = $_alias->id;
        //                        $item->save();
        //                    }
        //
        //                    return $item;
        //                }
        //            }
        //
        //            return NULL;
        //        }


        public function _category_params()
        {
            $this->filter_category_id = $this->relation ? $this->relation : $this->id;

            return $this->belongsToMany(ShopParam::class, 'shop_category_params', 'category_id', 'param_id', 'filter_category_id')
                ->where('shop_params.visible_in_filter', 1)
                ->orderBy('shop_category_params.sort')
                ->addSelect([
                    'shop_category_params.category_id',
                    'shop_category_params.sort',
                    'shop_category_params.modify',
                    'shop_params.id',
                    'shop_params.type',
                    'shop_params.type_view',
                    'shop_params.title',
                    'shop_params.name',
                    'shop_params.alias_name',
                    'shop_params.table as table_param',
                    'shop_params.language',
                    'shop_params.translate',
                ])
                ->with([
                    '_items'
                ])
                ->remember(15);
        }

        public function _filter_param_options()
        {
            $this->filter_category_id = $this->relation ? $this->relation : $this->id;

            return $this->belongsToMany(ShopParamItem::class, 'shop_category_params', 'category_id', 'param_id', 'filter_category_id', 'param_id')
                ->addSelect([
                    'shop_param_items.id as option_id',
                    'shop_param_items.name as option_name',
                    'shop_param_items.type as option_type',
                    'shop_param_items.sort as option_sort',
                    'shop_param_items.visible_in_filter as option_visible_in_filter',
                    'shop_param_items.translate as option_translate',
                    'shop_param_items.param_id',
                    'shop_param_items.sort',
                    DB::raw('(select `shop_params`.`name` from `shop_params` where `shop_params`.`id` = `shop_category_params`.`param_id`) as `param_name`'),
                    DB::raw('(select `shop_params`.`title` from `shop_params` where `shop_params`.`id` = `shop_category_params`.`param_id`) as `param_title`'),
                    DB::raw('(select `shop_params`.`translate` from `shop_params` where `shop_params`.`id` = `shop_category_params`.`param_id`) as `param_translate`'),
                ])
                ->orderBy('shop_category_params.sort')
                ->remember(15);
        }

        public function _generate_meta_tags($data = NULL)
        {
            $_config = config('os_meta_tags');
            $_config_class = $_config['model'][$this->getMorphClass()] ?? NULL;
            $_language = wrap()->get('locale');
            if ($_config_class) {
                $_config_class = $_config_class[$_language] ?? $_config_class[DEFAULT_LANGUAGE];
                foreach ($_config_class as &$_item) {
                    $_item = str_replace([
                        '{title}',
                        '{options}',
                    ], [
                        $this->title,
                        '[:options]'
                    ], $_item);
                }
            }
            $_meta = [
                'title'            => isset($_config_class['title']) && $_config_class['title'] ? $_config_class['title'] : NULL,
                'meta_title'       => $this->filter_page_meta_title ? : (isset($_config_class['meta_title']) && $_config_class['meta_title'] ? $_config_class['meta_title'] : "{$this->title} [:options]"),
                'meta_description' => $this->filter_page_meta_description ? : (isset($_config_class['meta_description']) && $_config_class['meta_description'] ? $_config_class['meta_description'] : "{$this->title} [:options]"),
                'meta_keywords'    => $this->filter_page_meta_keywords ? : (isset($_config_class['meta_keywords']) && $_config_class['meta_keywords'] ? $_config_class['meta_keywords'] : NULL),
                'meta_robots'      => 'robots, follow'
            ];
            $_params_options = NULL;
            if ($this->_filter_param_options->isNotEmpty()) {
                $_params_options = $this->_filter_param_options->groupBy(function ($query) {
                    return $query->param_name;
                });
                $__params_options = collect([]);
                foreach ($_config['items'] as $_key => &$_item) {
                    $_item = array_merge($_config['default'], $_item);
                    if ($_params_options->has($_key)) $__params_options->put($_key, $_params_options->get($_key));
                }
                $_params_options = $__params_options;
            }
            foreach ($_meta as $_type => &$_string) {
                $_output = NULL;
                $_option = str_contains($_string, '[:options]');
                if ($_option && $_config && isset($_config['items']) && $data) {
                    $_options = [];
                    foreach ($data as $_options_data) {
                        $_options = array_merge($_options, $_options_data);
                    }
                    if ($_params_options) {
                        $_params_options->each(function ($_params, $_param_name) use (&$_output, $data, $_config, $_language) {
                            if (isset($data[$_param_name]) && isset($_config['items'][$_param_name])) {
                                $_needle = $data[$_param_name];
                                $_needle_config = $_config['items'][$_param_name];
                                $_options = $_params->filter(function ($_option) use ($_needle) {
                                    return in_array($_option->option_id, $_needle);
                                })->map(function ($_option) use ($_language) {
                                    $_option_name = $_option->option_name;
                                    if ($_language != DEFAULT_LANGUAGE && $_option->option_translate) {
                                        $_option_translate = unserialize($_option->option_translate);
                                        if (isset($_option_translate[$_language]) && $_option_translate[$_language]) {
                                            $_option_name = $_option_translate[$_language];
                                        }
                                    }

                                    return $_option_name;
                                })->implode($_needle_config['option_separator']);
                                $_output[] = $_needle_config['label'] . $_options;
                            } else {
                                return TRUE;
                            }
                        });
                    }
                    if (count($_output)) $_output = mb_strtolower(implode($_config['default']['item_separator'], $_output));
                    $_string = str_replace('[:options]', $_output, $_string);
                }
                if ($_type == 'meta_robots' && $_config['to_block']) {
                    foreach ($_config['to_block'] as $_option_block) {
                        if (in_array($_option_block, array_keys($data))) {
                            $_meta['meta_robots'] = 'noindex, nofollow';
                        }
                    }
                }
            }

            return $_meta;
        }


        /*
         *
         *
         *
         *
         *
         * */


        public function _filter_opt()
        {
            $_response = [
                'params'                => NULL,
                'filter'                => NULL,
                'selected'              => NULL,
                'count_selected_params' => 0
            ];
            $_params = collect([]);
            $_category_params = $this->_category_params
                ->keyBy('id');
            $_request_query = $this->filter_request;
            $_response['count_selected_params'] = $_request_query['count_selected'] ?? 0;
            if ($_category_params->isNotEmpty()) {
                $_params = $_category_params->keyBy('name')->map(function ($_param) use (
                    $_request_query,
                    &$_response
                ) {
                    $_open_param = FALSE;
                    $_selected_param = NULL;
                    $_view_param_title = $_param->_translate_title($this->language);
                    if (isset($_request_query['base'][$_param->name])) {
                        $_response['selected'][$_param->name] = [
                            'type'    => $_param->type,
                            'checked' => []
                        ];
                        $_selected_param = $_request_query['base'][$_param->name]['values'];
                    }
                    $_html = [
                        'label'  => $_view_param_title,
                        'values' => []
                    ];
                    $_options = $_param->_items()
                        ->leftJoin("{$_param->table_param} as sp", 'shop_param_items.id', '=', 'sp.option_id')
                        ->leftJoin('shop_products as p', 'p.id', '=', 'sp.product_id')
                        ->leftJoin('shop_product_categories as spc', 'p.id', '=', 'spc.product_id')
                        ->where('p.status', 1)
                        ->where('spc.category_id', $this->relation_entity->id)
                        ->where('shop_param_items.visible_in_filter', 1)
                        ->distinct()
                        ->get([
                            'shop_param_items.*',
                        ]);
                    if ($_param->type == 'select') {
                        if ($_param->name == 'color') {
                            $_color_groups = collect([]);
                            foreach ($_options as $_option) {
                                $_item = $_color_groups->get($_option->color_shade, []);
                                $_option->selected = $_selected_param && in_array($_option->id, $_selected_param) ? TRUE : FALSE;
                                $_number_request_query = $_request_query;
                                $_number_request_query['base'][$_param->name]['type'] = 'data';
                                $_number_request_query['base'][$_param->name]['values'][] = $_option->id;
                                if ($this->language != DEFAULT_LANGUAGE) {
                                    $_cat = $this->relation_entity;
                                    $_cat->sub_categories = $_cat->childrens;
                                } else {
                                    $_cat = $this;
                                }
                                $_option->number_result = $_option->_number_result_opt($_cat, $_number_request_query);
                                $_option->filter_params_page_alias = $_option->_page_alias($this, $_request_query) . formalize_url_query();
                                $_item[] = $_option;
                                $_color_groups->put($_option->color_shade, $_item);
                            }
                            $_html['values'][] = '<ul class="uk-subnav uk-subnav-pill color-switcher" uk-switcher="animation: uk-animation-slide-left-medium, uk-animation-slide-right-medium; swiping: false; active: -1">';
                            $_color_groups->keys()->map(function ($_color) use (&$_html) {
                                $_html['values'][] = "<li><a href='#' rel='nofollow' class='uk-display-inline-block'><span style='display: block; width: 15px; height: 15px; background-color: {$_color};'></span></a></li>";
                            });
                            $_html['values'][] = '</ul>';
                            $_html['values'][] = '<ul class="uk-switcher color-switcher-text">';
                            $_color_groups->map(function ($_color_options) use (&$_html, $_param, &$_response) {
                                $_html['values'][] = '<li>';
                                foreach ($_color_options as $_option) {
                                    $_option_name = $_option->_translate_name($this->language);
                                    $_checked = $_option->selected ? 'checked' : NULL;
                                    $_checked_class = $_option->selected ? ' checked' : '';
                                    $_id = $_option->style_id ? $_option->style_id : str_slug($_param->name, '-') . "-{$_option->id}";
                                    $_class = $_option->style_class ? str_slug($_param->name, '-') . "-{$_option->id} {$_option->style_class}" : str_slug($_param->name, '-') . "-{$_option->id}";
                                    $_option_alias = $_option->filter_params_page_alias;
                                    if ($_checked) {
                                        $_response['selected'][$_param->name]['checked'][] = [
                                            'name'  => $_option_name,
                                            'alias' => $_option_alias
                                        ];
                                    }
                                    if ($_checked) {
                                        $_open_param = TRUE;
                                    }
                                    if ($_option->number_result == 0) {
                                        $_html['values'][] = "<div id='{$_id}' class='{$_class}{$_checked_class} uk-flex uk-flex-between empty'>" .
                                            "<a href='{$_option_alias}' class='uk-filter-param use-ajax uk-disabled' data-view_load='0' disabled rel='nofollow'>" .
                                            "<span class='uk-name-link'>{$_option_name}</span></a>" .
                                            "<div class='uk-checkbox empty'></div>" .
                                            "</div>";
                                    } else {
                                        $_html['values'][] = "<div id='{$_id}' class='{$_class}{$_checked_class} uk-flex uk-flex-between'>" .
                                            "<a href='{$_option_alias}' class='uk-filter-param use-ajax' data-view_load='0' rel=''>" .
                                            "<span class='uk-name-link'>{$_option_name}</span></a>" .
                                            "<div class='uk-checkbox'></div>" .
                                            "</div>";
                                    }
                                }
                                $_html['values'][] = '</li>';
                            });
                            $_html['values'][] = '</ul>';
                        } else {
                            foreach ($_options as $_option) {
                                $_option->selected = $_selected_param && in_array($_option->id, $_selected_param) ? TRUE : FALSE;
                                $_number_request_query = $_request_query;
                                $_number_request_query['base'][$_param->name]['type'] = 'data';
                                $_number_request_query['base'][$_param->name]['values'][] = $_option->id;
                                if ($this->language != DEFAULT_LANGUAGE) {
                                    $_cat = $this->relation_entity;
                                    $_cat->sub_categories = $_cat->childrens;
                                } else {
                                    $_cat = $this;
                                }
                                $_option->number_result = $_option->_number_result_opt($_cat, $_number_request_query);
                                $_option->filter_params_page_alias = $_option->_page_alias($this, $_request_query) . formalize_url_query();
                                $_option_name = $_option->_translate_name($this->language);
                                $_checked = $_option->selected ? 'checked' : NULL;
                                $_checked_class = $_option->selected ? ' checked' : '';
                                $_id = $_option->style_id ? $_option->style_id : str_slug($_param->name, '-') . "-{$_option->id}";
                                $_class = $_option->style_class ? str_slug($_param->name, '-') . "-{$_option->id} {$_option->style_class}" : str_slug($_param->name, '-') . "-{$_option->id}";
                                $_option_alias = $_option->filter_params_page_alias;
                                if ($_checked) {
                                    $_response['selected'][$_param->name]['checked'][] = [
                                        'name'  => $_option_name,
                                        'alias' => $_option_alias
                                    ];
                                }
                                if ($_checked) {
                                    $_open_param = TRUE;
                                }
                                if ($_option->number_result == 0) {
                                    $_html['values'][] = "<div id='{$_id}' class='{$_class}{$_checked_class} uk-flex uk-flex-between empty'>" .
                                        "<a href='{$_option_alias}' class='uk-filter-param use-ajax uk-disabled' data-view_load='0' disabled rel='nofollow'>" .
                                        "<span class='uk-name-link'>{$_option_name}</span></a>" .
                                        "<div class='uk-checkbox empty'></div>" .
                                        "</div>";
                                } else {
                                    $_html['values'][] = "<div id='{$_id}' class='{$_class}{$_checked_class} uk-flex uk-flex-between'>" .
                                        "<a href='{$_option_alias}' class='uk-filter-param use-ajax' data-view_load='0' rel=''>" .
                                        "<span class='uk-name-link'>{$_option_name}</span></a>" .
                                        "<div class='uk-checkbox'></div>" .
                                        "</div>";
                                }
                            }
                        }

                    } elseif ($_param->type == 'input_number') {
                        // todo: надо отключить выбор поля в фильтр
                    }

                    return [
                        'title' => $_view_param_title,
                        'name'  => $_param->name,
                        'table' => $_param->table_param,
                        'type'  => $_param->type,
                        'open'  => $_open_param,
                        'html'  => $_html
                    ];
                });
            }
            if ($_params->isNotEmpty()) {
                $_selected_data = NULL;
                $_selected = request()->get('price');
                $_label = trans('forms.label_shop_category_filter_input_price', [], $this->language);
                $_currency = wrap()->get('currency.current');
                $_min_max_values = ShopProduct::from('shop_products as p')
                    ->leftJoin('shop_product_categories as spc', 'p.id', '=', 'spc.product_id')
                    ->where('spc.category_id', $this->relation_entity->id)
                    ->where('p.status', 1)
                    ->select(DB::raw('max(p.price) as max'), DB::raw('min(p.price) as min'))
                    ->remember(10)
                    ->first();
                if (isset($_selected['min']) && $_selected['min']) $_selected_data['min'] = floor($_selected['min']);
                if (isset($_selected['max']) && $_selected['max'] && !isset($_selected_data['min'])) $_selected_data['min'] = floor($_min_max_values->min);
                if (isset($_selected['max']) && $_selected['max']) $_selected_data['max'] = ceil($_selected['max']);
                if (isset($_selected_data['min']) && !isset($_selected_data['max'])) $_selected_data['max'] = ceil($_min_max_values->max);
                if ($_selected_data) $_selected_data['alias'] = _u(request()->path()) . formalize_url_query(NULL, 'price');
                $min = transform_price($_min_max_values->min);
                $max = transform_price($_min_max_values->max);
                if ($_selected_data) {
                    $_selected_data['unit'] = $_currency['suffix'];
                    $_response['selected']['price'] = [
                        'type'    => 'input_number',
                        'checked' => $_selected_data
                    ];
                }
                $_price = [
                    'min_value' => $min['format']['price'],
                    'max_value' => $max['format']['price'],
                    'values'    => [
                        'min'    => isset($_selected['min']) && $_selected['min'] ? $_selected['min'] : $min['format']['price'],
                        'max'    => isset($_selected['max']) && $_selected['max'] ? $_selected['max'] : $max['format']['price'],
                        'slider' => [
                            'min' => (isset($_selected['min']) && $_selected['min'] ? $_selected['min'] : $min['format']['price']),
                            'max' => (isset($_selected['max']) && $_selected['max'] ? $_selected['max'] : $max['format']['price']),
                        ]
                    ],
                ];
                $_open_param = FALSE;
                $_html = [
                    'label'  => $_label,
                    'values' => []
                ];
                $_diff_data = $_price['max_value'] - $_price['min_value'];
                $_label_min = $min;
                $_label_max = $max;
                $_shop_filter_back_alias = _u(request()->url()) . formalize_url_query(NULL, 'price');
                $_use_query = formalize_url_query(NULL, 'price') ? 1 : 0;
                $_slide_step = $_diff_data > 100000 ? 1000 : ($_diff_data > 10000 ? 100 : ($_diff_data > 1000 ? 10 : 1));
                $_html['values'][] = "<div id='price' class='price-slider'><div class='uk-flex-1'>" .
                    "<div class='input-slider-values'><div><button type='button' data-path='' data-view_load='0' data-name='price' data-back_path='{$_shop_filter_back_alias}' data-use_query='{$_use_query}' class='use-ajax'>" . trans('front.button_apply') . "</button></div></div>" .
                    "<div id='price-slider' class='input-slider' data-min='" . floor($_price['min_value']) . "' data-max='" . ceil($_price['max_value']) . "' data-step='{$_slide_step}' data-selected-min='" . floor($_price['values']['slider']['min']) . "' data-selected-max='" . ceil($_price['values']['slider']['max']) . "'><div class='input-slider-values uk-flex uk-width-1-1 uk-flex-between'><span class='value-min'>{$_label_min['format']['view_price_2']}</span><span class='value-max'>{$_label_max['format']['view_price_2']}</span></div>" .
                    "<div class='ui-slider-handle min-handle'></div><div class='ui-slider-handle max-handle'></div>" .
                    "</div></div>";
                $_params->put('price', [
                    'title' => $_label,
                    'open'  => $_open_param,
                    'data'  => $_price,
                    'html'  => $_html
                ]);
            }
            if ($_params->isNotEmpty()) {
                $_templates_page = [
                    'front.shop.filter',
                    'oleus.base.shop_category_filter'
                ];
                $_fitler_html = view(choice_template($_templates_page), [
                    'item'                  => $this,
                    'params'                => $_params,
                    'selected'              => $_response['selected'],
                    'count_selected_params' => $_response['count_selected_params']
                ])
                    ->render();
                $_response['params'] = $_params;
                $_response['filter'] = clear_html($_fitler_html);
            }

            return $_response;
        }

        public function _items_opt()
        {
            $_wrap = wrap()->get();
            $_language = $_wrap['locale'];
            $_request_query = $this->filter_request;
            $_request_query_count_selected = $_request_query['count_selected'] ?? 0;
            $_current_page = currentPage();
            $_categories = [];
            if ($this->language != DEFAULT_LANGUAGE) {
                $_cat = $this->relation_entity;
                $_cat->sub_categories = $_cat->childrens;
                $_categories[] = $_cat->id;
                $_sub_categories = $_cat->sub_categories;
                if ($_sub_categories) {
                    $_sub_categories = $_sub_categories->map(function ($_category) {
                        return $_category->id;
                    })->filter(function ($_category) {
                        return $_category;
                    })->toArray();
                    if (is_array($_sub_categories) && count($_sub_categories)) {
                        $_categories = array_merge($_categories, $_sub_categories);
                    }
                }
            } else {
                $_categories[] = $this->id;
                $_sub_categories = $this->sub_categories;
                if ($_sub_categories) {
                    $_sub_categories = $_sub_categories->map(function ($_category) {
                        return $_category->id;
                    })->filter(function ($_category) {
                        return $_category;
                    })->toArray();
                    if (is_array($_sub_categories) && count($_sub_categories)) {
                        $_categories = array_merge($_categories, $_sub_categories);
                    }
                }
            }
            Paginator::currentPageResolver(function () use ($_current_page) {
                return $_current_page ? $_current_page : 1;
            });
            $_per_page = 7;
            $items = ShopProduct::from('shop_products as p')
                ->leftJoin('shop_product_categories as spc', 'spc.product_id', '=', 'p.id')
                ->select([
                    'p.id',
                    'p.alias_id',
                    'p.title',
                    'p.sky',
                    'p.modification',
                    'p.modification_id',
                    'p.sub_title',
                    'p.location',
                    'p.preview_fid',
                    'p.language',
                    'p.background_fid',
                    'p.price',
                    'p.old_price',
                    'p.base_price',
                    'p.currency',
                    'p.count',
                    'p.not_limited',
                    'p.out_of_stock',
                    'p.mark_new',
                    'p.mark_hit',
                    'p.mark_discount',
                    'p.mark_elected',
                    'p.status',
                    'p.sort',
                    'p.relation',
                ])
                ->with([
                    '_alias',
                    '_preview',
                    '_background'
                ])
                ->whereNull('p.relation')
                ->where('p.status', 1)
                ->whereIn('spc.category_id', $_categories)
                ->orderBy('p.out_of_stock');
            if ($_request_query && isset($_request_query['base'])) {
                $_request_query = $_request_query['base'];
                $_index_param = 0;
                foreach ($_request_query as $_query_param => $_query_param_values) {
                    if (!str_is('*utm*', $_query_param)) {
                        ++$_index_param;
                        if (is_array($_query_param_values) && $_query_param_values['type'] == 'data' && is_array($_query_param_values['values']) && count($_query_param_values['values'])) {
                            $items->leftJoin("shop_param_{$_query_param}_data as p{$_index_param}",
                                "p{$_index_param}.product_id", '=', 'p.id')
                                ->whereIn("p{$_index_param}.option_id", $_query_param_values['values']);
                        } elseif (is_array($_query_param_values) && $_query_param_values['type'] == 'min_max' && is_array($_query_param_values['values']) && count($_query_param_values['values']) && $_query_param_values['type'] == 'min_max') {
                            $_min_value = isset($_query_param_values['values']['min']) && $_query_param_values['values']['min'] ? $_query_param_values['values']['min'] : NULL;
                            $_max_value = isset($_query_param_values['values']['max']) && $_query_param_values['values']['max'] ? $_query_param_values['values']['max'] : NULL;
                            if ($_query_param != 'price') {
                                $items->leftJoin("shop_param_{$_query_param}_data as p{$_index_param}",
                                    "p{$_index_param}.product_id", '=', 'p.id')
                                    ->when($_min_value, function ($query) use ($_min_value, $_index_param) {
                                        return $query->where("p{$_index_param}.value", '>=', $_min_value);
                                    })->when($_max_value, function ($query) use ($_max_value, $_index_param) {
                                        return $query->where("p{$_index_param}.value", '<=', $_max_value);
                                    });
                            } else {
                                $items->when($_min_value, function ($query, $_min_value) {
                                    return $query->where('p.base_price', '>=', $_min_value);
                                })->when($_max_value, function ($query, $_max_value) {
                                    return $query->where('p.base_price', '<=', $_max_value);
                                });
                            }
                        } elseif ($_query_param == 'sort') {
                            wrap()->set('shop_filter_sort', $_query_param_values['values']);
                            switch ($_query_param_values['values']) {
                                case 'price_asc':
                                    $items->orderBy('p.base_price');
                                    break;
                                case 'price_desc':
                                    $items->orderByDesc('p.base_price');
                                    break;
                                case 'name_asc':
                                    $items->orderBy('p.title');
                                    break;
                                case 'popular_asc':
                                    $items->orderBy('p.mark_hit')
                                        ->orderBy('p.ordered');
                                    break;
                            }
                        }
                    }
                }
                if (!isset($_request_query['sort'])) {
                    $items->orderBy('p.title');
                }
            } else {
                //                $items->orderByDesc('p.fasten_to_top');
                $items->orderBy('p.title');
            }
            $items = $items->orderBy('p.sort')
                ->remember(15)
                ->distinct()
                ->paginate($_per_page);
            if ($items->isNotEmpty() && count($items->items())) {
                $items->getCollection()->transform(function ($_product) use ($_language) {
                    if ($_language == DEFAULT_LANGUAGE) {
                        $_product->_load('short');

                        return $_product;
                    } else {
                        return shop_product_load($_product->id, $_language, 'short');
                    }
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
            } elseif ($_current_page == 2) {
                $url = trim($_current_url, '/');
                $_prev_page_link = _u($url) . $_query_string;
            }
            wrap()->set('seo._link_prev', $_prev_page_link);
            wrap()->set('seo._link_next', $_next_page_link);
            wrap()->set('seo._page_number', $_current_page);
            if ($_current_page > 1) {
                wrap()->set('seo._robots', 'noindex, nofollow');
                wrap()->set('seo._title_suffix', ' - ' . trans('others.page_full', ['page' => $_current_page]) . ' ' . wrap()->get('seo._title_suffix'));
                wrap()->set('seo._description', ($this->meta_description ?? $_wrap['seo']['_description']) . ' - ' . trans('others.page_full', ['page' => $_current_page]));
                $_page_title = $this->title ?? trans('shop.page_description_catalog_default', ['title' => $this->title]);
                wrap()->set('page._title', $_page_title . ' - <i class="page-number">' . trans('others.page_full', ['page' => $_current_page]) . '</i>');
            }
            if($_request_query_count_selected > 2){
                wrap()->set('seo._robots', 'noindex, nofollow');
            }
            $_breadcrumb = breadcrumb_render(['entity' => $this]);
            wrap()->set('breadcrumb', $_breadcrumb, TRUE);

            return $items;
        }

        public function _render_ajax_command_opt($entity = NULL)
        {
            $entity = $entity ? $entity : $this;
            $_filter = $entity->_filter_opt();
            $_items = $entity->_items_opt();
            $_sub_categories = $entity->children;
            $_wrap = wrap()->get();
            $_more_load = request()->get('more_load', 0);
            if ($_filter['filter']) {
                $commands[] = [
                    'command' => 'replaceWith',
                    'target'  => '.shop-category-filter-card',
                    'data'    => clear_html($_filter['filter'])
                ];
            } elseif ($_sub_categories) {
                $commands[] = [
                    'command' => 'replaceWith',
                    'target'  => '.shop-category-filter-card',
                    'data'    => clear_html(view('front.shop.parent_filter_menu', [
                        'items' => $_sub_categories
                    ])->render())
                ];
            }
            $commands[] = [
                'command' => 'replaceWith',
                'target'  => '.filter-price.sort',
                'data'    => clear_html(view('front.shop.field_catalog_sort')
                    ->render())
            ];
            if ($_more_load) {
                $commands[] = [
                    'target'  => '.last-block-pagination',
                    'command' => 'remove',
                ];
            }
            $commands[] = [
                'command' => $_more_load ? 'append' : 'html',
                'target'  => '.grid-category-product',
                'data'    => clear_html(view('front.shop.items_category_products', [
                    '_more_load' => FALSE,
                    'items'      => $_items,
                    'language'   => $_wrap['locale'],
                    'location'   => $_wrap['location'],
                    'currency'   => $_wrap['currency']['current']
                ])
                    ->render())
            ];
            $_current_page = $_items->currentPage();
            $_page_in_url = $_current_page && $_current_page > 1 ? "page-{$_current_page}/" : NULL;
            if ($_current_page < 2 && $entity->body) {
                $commands[] = [
                    'command' => 'html',
                    'target'  => '#shop-category-description-card',
                    'data'    => "<div class='description-seo'><div class='uk-container uk-container-large'>{$entity->body}</div></div>"
                ];
            } else {
                $commands[] = [
                    'command' => 'html',
                    'target'  => '#shop-category-description-card',
                    'data'    => ''
                ];
            }
            $commands[] = [
                'command' => 'change_url',
                'url'     => _u(request()->url()) . formalize_url_query()
            ];
            $commands[] = [
                'command' => 'change_title',
                'title'   => "{$_wrap['seo']['_title']} {$_wrap['seo']['_title_suffix']}"
            ];
            $commands[] = [
                'command' => 'replaceWith',
                'target'  => '.uk-breadcrumb',
                'data'    => clear_html(view('oleus.base.breadcrumb')->render())
            ];

            return $commands;
        }

        public function _par()
        {
            return $this->hasOne(self::class, 'id', 'parent_id')
                ->with([
                    '_par'
                ]);
        }
    }

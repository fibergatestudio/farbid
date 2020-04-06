<?php

    namespace App\Http\Controllers\Oleus\Shop;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\City;
    use App\Models\ShopParamItem;
    use App\Models\ShopProduct;
    use App\Models\ShopCategory;
    use App\Models\UrlAlias;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Session;
    use Validator;

    class ProductsController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_shop_products'
            ]);
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'shop_products';
            $form->permission = array_merge($form->permission, $form->relation, [
                'read'   => 'read_shop_products',
                'create' => 'create_shop_products',
                'update' => 'update_shop_products',
                'delete' => 'delete_shop_products',
            ]);
            $form->seo = TRUE;
            $_field_categories = '<div class="uk-form-row" id="form-field-categories_object"><label for="form-field-categories" class="uk-form-label">' . trans('forms.label_categories') . '<span class="uk-text-danger">*</span></label><div class="uk-form-controls"><div class="uk-inline uk-width-1-1"><div class="uk-alert uk-alert-danger uk-border-rounded">' . trans('forms.help_category_is_empty', ['link' => _l(trans('forms.button_add_category'), 'oleus.shop_categories.create')]) . '</div></div></div></div>';
            $_field_modification = NULL;
            $form->tabs[] = [
                'title'   => trans('others.tab_basic'),
                'content' => [
                    field_render('title', [
                        'label'      => trans('forms.label_name_product'),
                        'value'      => $entity->exists ? $entity->title : NULL,
                        'attributes' => [
                            'autofocus' => TRUE
                        ],
                        'required'   => TRUE
                    ]),
                    field_render('sub_title', [
                        'label' => trans('forms.label_sub_title'),
                        'value' => $entity->exists ? $entity->sub_title : NULL,
                    ]),
                    field_render('preview_fid', [
                        'type'   => 'file',
                        'label'  => trans('forms.label_preview'),
                        'allow'  => 'jpg|jpeg|gif|png',
                        'values' => $entity->exists && $entity->_preview ? [$entity->_preview] : NULL,
                    ]),
                    field_render('body', [
                        'label'      => trans('forms.label_description'),
                        'type'       => 'textarea',
                        'editor'     => TRUE,
                        'value'      => $entity->exists ? $entity->body : NULL,
                        'attributes' => [
                            'rows' => 8,
                        ]
                    ]),
                    field_render('advice', [
                        'label'      => trans('forms.label_advice'),
                        'type'       => 'textarea',
                        'editor'     => TRUE,
                        'value'      => $entity->exists ? $entity->advice : NULL,
                        'class'      => 'editor-short',
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    (!$entity->exists || !$entity->relation ? '<hr class="uk-divider-icon">' : NULL),
                    (!$entity->exists || !$entity->relation ? field_render('sort', [
                        'type'   => 'select',
                        'label'  => trans('forms.label_sort'),
                        'value'  => $entity->exists ? $entity->sort : 0,
                        'values' => sort_field(),
                        'class'  => 'uk-select2',
                    ]) : NULL),
                    (!$entity->exists || !$entity->relation ? field_render('status', [
                        'type'     => 'checkbox',
                        'label'    => trans('forms.label_publish'),
                        'selected' => $entity->exists ? $entity->status : 1
                    ]) : NULL)
                ]
            ];
            if(!$entity->exists || !$entity->relation) {
                $_currencies = wrap()->get('variables.currencies');
                $_field_currencies = collect($_currencies['currencies'])->filter(function ($_currency) {
                    return $_currency['use'];
                })->map(function ($_currency) {
                    return $_currency['full_name'];
                });
                if($_categories = $entity->categories) {
                    $_categories['all'] = $_categories['all']->map(function ($_name_category, $_id_category) {
                        $_categry_model = ShopCategory::find($_id_category);
                        if($_parents = $_categry_model->parents) {
                            $_output = NULL;
                            foreach($_parents as $_parent) {
                                $_output[] = $_parent->title;
                            }
                            $_output[] = $_name_category;

                            return implode(' / ', $_output);
                        }

                        return $_name_category;
                    });
                    $_categories['all']->prepend(trans('forms.value_choice'), 0);
                    $_field_categories = field_render('categories', [
                        'type'       => 'select',
                        'label'      => trans('forms.label_category'),
                        'value'      => $_categories['selected'],
                        'values'     => $_categories['all'],
                        'class'      => 'uk-select2 use-ajax',
                        'attributes' => [
                            'data-href' => _r('oleus.shop_products.params', ['item' => ($entity->exists ? $entity->id : NULL)]),
                        ],
                        'required'   => TRUE
                    ]);
                }
                $form->tabs[] = [
                    'title'   => trans('others.tab_product'),
                    'content' => [
                        field_render('sky', [
                            'label'    => trans('forms.label_sky'),
                            'value'    => $entity->exists ? $entity->sky : NULL,
                            'required' => TRUE,
                        ]),
                        $_field_categories,
                        field_render('marks', [
                            'type'   => 'checkboxes',
                            'label'  => trans('forms.label_marks'),
                            'value'  => $entity->exists ? $entity->marks['checked'] : NULL,
                            'values' => $entity->marks['marks'],
                        ]),
                        '<hr><h4 class="uk-margin-remove-top uk-margin-small-bottom uk-text-uppercase">' . trans('forms.label_prices') . '</h4>',
                        field_render('price', [
                            'type'       => 'number',
                            'label'      => trans('forms.label_price'),
                            'value'      => $entity->exists ? $entity->price : NULL,
                            'attributes' => [
                                'min'  => 0,
                                'step' => 0.01
                            ],
                            'required'   => TRUE,
                        ]),
                        field_render('old_price', [
                            'type'       => 'number',
                            'label'      => trans('forms.label_old_price'),
                            'value'      => $entity->exists ? $entity->old_price : NULL,
                            'attributes' => [
                                'min'  => 0,
                                'step' => 0.01
                            ]
                        ]),
                        field_render('currency', [
                            'type'   => 'select',
                            'label'  => trans('forms.label_currency'),
                            'value'  => $entity->exists ? $entity->currency : $_currencies['default_currency'],
                            'values' => $_field_currencies,
                            'class'  => 'uk-select2',
                        ]),
                        '<hr><h4 class="uk-margin-remove-top uk-margin-small-bottom uk-text-uppercase">Дисконтный таймер</h4>',
                        view('oleus.shop.product_discount_timer', compact('entity'))->render(),
                        '<hr><h4 class="uk-margin-remove-top uk-margin-small-bottom uk-text-uppercase">' . trans('forms.label_availability') . '</h4>',
                        field_render('count', [
                            'type'  => 'number',
                            'label' => trans('forms.label_count'),
                            'value' => $entity->exists ? $entity->count : NULL,
                        ]),
                        field_render('not_limited', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_limited'),
                            'selected' => $entity->exists ? $entity->not_limited : 0
                        ]),
                        field_render('out_of_stock', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_out_of_stock'),
                            'selected' => $entity->exists ? $entity->out_of_stock : 0
                        ]),
                        '<hr><h4 class="uk-margin-remove-top uk-margin-small-bottom uk-text-uppercase">' . trans('forms.label_params') . '</h4>',
                        '<div id="list-product-params" class="">' . view('oleus.shop.param_product',
                            ['params' => $entity->_params($_categories['selected'], TRUE)])
                            ->render() . '</div>',
                        '<hr><h4 class="uk-margin-remove-top uk-margin-small-bottom uk-text-uppercase">' . trans('forms.label_modifications_product') . '</h4>',
                        view('oleus.shop.product_modifications', ['modifications' => ($entity->exists ? $entity->modifications : NULL)])
                            ->render(),
                    ]
                ];
            }
            $form->tabs[] = [
                'title'   => trans('others.tab_specifications'),
                'content' => [
                    field_render('specifications', [
                        'type'    => 'table',
                        'label'   => trans('forms.label_specifications'),
                        'value'   => $entity->exists ? $entity->specifications : NULL,
                        'options' => [
                            'thead' => [
                                'Название параметра',
                                'Значение параметра'
                            ]
                        ],
                        'help'    => trans('forms.help_table_field')
                    ]),
                    //                    '<hr class="uk-divider-icon">',
                    //                    field_render('equipment', [
                    //                        'label'      => trans('forms.label_equipment'),
                    //                        'type'       => 'textarea',
                    //                        'editor'     => TRUE,
                    //                        'value'      => $entity->exists ? $entity->equipment : NULL,
                    //                        'attributes' => [
                    //                            'rows' => 8,
                    //                        ]
                    //                    ]),
                    //                    '<hr class="uk-divider-icon">',
                    //                    field_render('structural_features', [
                    //                        'label'      => trans('forms.label_structural_features'),
                    //                        'type'       => 'textarea',
                    //                        'editor'     => TRUE,
                    //                        'value'      => $entity->exists ? $entity->structural_features : NULL,
                    //                        'attributes' => [
                    //                            'rows' => 8,
                    //                        ]
                    //                    ]),
                ]
            ];
            if($entity->exists) {
                $form->tabs[] = [
                    'title'   => trans('others.tab_product_related'),
                    'content' => [
                        view('oleus.shop.product_related_items', [
                            'entity' => $entity,
                            'items'  => $entity->_related_products()
                        ])
                            ->render()
                    ]
                ];
            }
            if($entity->exists) {
                $form->tabs[] = [
                    'title'   => trans('others.tab_product_groups'),
                    'content' => [
                        view('oleus.shop.product_groups_items', [
                            'entity' => $entity,
                            'items'  => $entity->_groups_product()
                        ])
                            ->render()
                    ]
                ];
            }
            $form->tabs[] = [
                'title'   => trans('others.tab_media'),
                'content' => [
                    field_render('medias', [
                        'type'     => 'file',
                        'label'    => trans('forms.label_medias'),
                        'multiple' => TRUE,
                        'values'   => $entity->exists && ($_medias = $entity->_medias()) ? $_medias : NULL
                    ]),
                    field_render('files', [
                        'type'     => 'file',
                        'label'    => trans('forms.label_files'),
                        'multiple' => TRUE,
                        'allow'    => 'txt|doc|docx|xls|xlsx|pdf',
                        'values'   => $entity->exists && ($_files = $entity->_medias('files')) ? $_files : NULL,
                    ])
                ]
            ];
            $form->tabs[] = [
                'title'   => trans('others.tab_style'),
                'content' => [
                    field_render('style_id', [
                        'label' => trans('forms.label_style_page_id'),
                        'value' => $entity->exists ? $entity->style_id : NULL
                    ]),
                    field_render('style_class', [
                        'label' => trans('forms.label_style_page_class'),
                        'value' => $entity->exists ? $entity->style_class : NULL,
                    ]),
                    field_render('background_fid', [
                        'type'   => 'file',
                        'label'  => trans('forms.label_background_page'),
                        'allow'  => 'jpg|jpeg|gif|png|svg',
                        'values' => $entity->exists && $entity->_background ? [$entity->_background] : NULL,
                    ]),
                ]
            ];

            return $form;
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, [
                'title'      => 'required',
                'categories' => 'sometimes|required',
                'sky'        => 'sometimes|required',
                'price'      => 'sometimes|required',
            ]);
        }

        public function index()
        {
            $_filter = \request()->only([
                'title',
                'category'
            ]);
            $this->set_wrap([
                'page._title' => trans('pages.shop_products_page'),
                'seo._title'  => trans('pages.shop_products_page')
            ]);
            $items = ShopProduct::from('shop_products as p')
                ->with([
                    '_alias',
                    '_mod'
                ])
                ->where('p.language', DEFAULT_LANGUAGE)
                ->where('p.location', DEFAULT_LOCATION)
                ->where('p.modification', 0)
                ->when($_filter, function ($query) use ($_filter) {
                    if(isset($_filter['title']) && $_filter['title']) {
                        $query->where('p.title', 'like', "%{$_filter['title']}%");
                    }
                    if(isset($_filter['category']) && $_filter['category'] > 0) {
                        $query->leftJoin('shop_product_categories as cp', 'cp.product_id', '=', 'p.id');
                        $_category = ShopCategory::find($_filter['category']);
                        $_childrens = $_category->childrens;
                        if($_childrens) {
                            $query->whereIn('cp.category_id', $_childrens->keyBy('id')->keys());
                        } else {
                            $query->where('cp.category_id', $_filter['category']);
                        }
                    }elseif(isset($_filter['category']) && $_filter['category'] == -1){
                        $query->whereNotExists(function ($_query) {
                            $_query->select(DB::raw(1))
                                ->from('shop_product_categories as cp')
                                ->whereRaw('cp.product_id = p.id');
                        });
                    }
                })
                ->orderByDesc('p.status')
                ->orderBy('p.title')
                ->select([
                    'p.id',
                    'p.sky',
                    'p.title',
                    'p.modification_id',
                    'p.language',
                    'p.location',
                    'p.status',
                    'p.relation',
                    'p.alias_id',
                ])
                ->paginate(25);

            $_categories_all = ShopCategory::location(DEFAULT_LOCATION)
                ->language(DEFAULT_LANGUAGE)
                ->orderBy('title')
                ->with([
                    '_par'
                ])
                ->get([
                    'title',
                    'id',
                    'parent_id'
                ])->keyBy('id');

            $_categories_all = $_categories_all->map(function ($_category_model) {
                if($_parents = $_category_model->parents) {
                    $_output = NULL;
                    foreach($_parents as $_parent) $_output[] = mb_convert_case(mb_strtolower($_parent->title), MB_CASE_TITLE);
                    $_output[] = mb_convert_case(mb_strtolower($_category_model->title), MB_CASE_TITLE);

                    return implode(' / ', $_output);
                }

                return mb_convert_case(mb_strtolower($_category_model->title), MB_CASE_TITLE);
            })->sort()->prepend('Товары без категории', -1)->prepend('Товары из всех категорий', 0)->all();

            return view('oleus.shop.index_products', compact('items', '_categories_all'));
        }

        public function create(ShopProduct $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.shop_products_page_create'),
                'seo._title'  => trans('pages.shop_products_page_create')
            ]);
            $form = $this->_form($item);

            //            dd($item->_params([2,3]));

            return view($form->theme, compact('form', 'item'));
        }

        public function store(Request $request)
        {
            if($preview_fid = $request->input('preview_fid')) {
                $_preview_fid = array_shift($preview_fid);
                Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
            }
            if($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            if($medias = $request->input('medias')) {
                $_media = f_get(array_keys($medias));
                Session::flash('medias', json_encode($_media->toArray()));
            }
            if($files = $request->input('files')) {
                $_files = f_get(array_keys($files));
                Session::flash('files', json_encode($_files->toArray()));
            }
            $this->_validate($request);
            $_save = $request->only([
                'title',
                'sub_title',
                'body',
                'advice',
                'advice',
                'equipment',
                'structural_features',
                'status',
                'style_id',
                'style_class',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
                'sitemap',
                'sort',
                'sky',
                'price',
                'old_price',
                'currency',
                'count',
                'not_limited',
                'out_of_stock',
            ]);
            if(isset($_preview_fid)) $_save['preview_fid'] = (int)$_preview_fid['id'];
            if(isset($_background_fid)) $_save['background_fid'] = (int)$_background_fid['id'];
            $_save['language'] = DEFAULT_LANGUAGE;
            $_save['location'] = DEFAULT_LOCATION;
            $_save['location'] = DEFAULT_LOCATION;
            if($_marks = $request->get('marks')) foreach($_marks as $_mark_key => $_mark_value) $_save[$_mark_key] = 1;
            $_save['base_price'] = transform_price($_save['price'], $_save['currency'], NULL, TRUE)['format']['price'];
            $item = ShopProduct::updateOrCreate([
                'id' => NULL
            ], $_save);
            $item->update([
                'modification_id' => $item->id
            ]);
            Session::forget([
                'preview_fid',
                'background_fid',
                'medias',
                'files'
            ]);

            return redirect()
                ->route('oleus.shop_products.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.shop_product_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(ShopProduct $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.shop_products_page_edit'),
                'seo._title'  => trans('pages.shop_products_page_edit')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, ShopProduct $item)
        {
            if($preview_fid = $request->input('preview_fid')) {
                $_preview_fid = array_shift($preview_fid);
                Session::flash('preview_fid', json_encode([f_get($_preview_fid['id'])]));
            }
            if($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            if($medias = $request->input('medias')) {
                $_media = f_get(array_keys($medias));
                Session::flash('medias', json_encode($_media->toArray()));
            }
            if($files = $request->input('files')) {
                $_files = f_get(array_keys($files));
                Session::flash('files', json_encode($_files->toArray()));
            }
            $this->_validate($request);
            if($item->relation) {
                $_save = $request->only([
                    'title',
                    'sub_title',
                    'body',
                    'advice',
                    'equipment',
                    'structural_features',
                    'style_id',
                    'style_class',
                    'meta_title',
                    'meta_keywords',
                    'meta_description',
                    'meta_robots',
                    'sitemap'
                ]);
            } else {
                $_save = $request->only([
                    'title',
                    'sub_title',
                    'body',
                    'advice',
                    'equipment',
                    'structural_features',
                    'status',
                    'style_id',
                    'style_class',
                    'meta_title',
                    'meta_keywords',
                    'meta_description',
                    'meta_robots',
                    'sitemap',
                    'sort',
                    'sky',
                    'price',
                    'old_price',
                    'currency',
                    'count',
                    'not_limited',
                    'out_of_stock',
                ]);
                foreach($item->marks['marks'] as $_mark_key => $_mark_value) $_save[$_mark_key] = 0;
                if($_marks = $request->get('marks')) foreach($_marks as $_mark_key => $_mark_value) $_save[$_mark_key] = 1;
                $_price = transform_price($_save['price'], $_save['currency'], NULL, TRUE)['format']['price'];
                $_save['base_price'] = $_price;
            }
            $_save['preview_fid'] = NULL;
            $_save['background_fid'] = NULL;
            $_save['specifications'] = NULL;
            if(isset($_preview_fid)) $_save['preview_fid'] = (int)$_preview_fid['id'];
            if(isset($_background_fid)) $_save['background_fid'] = (int)$_background_fid['id'];
            $specifications = collect($request->get('specifications', []));
            if($specifications->isNotEmpty()) {
                $specifications = $specifications->filter(function ($_item) {
                    $_response = FALSE;
                    foreach($_item as $_data) if($_data) $_response = TRUE;

                    return $_response;
                });
                $_save['specifications'] = $specifications->values()->toJson();
            }
            $item->update($_save);
            Session::forget([
                'preview_fid',
                'background_fid',
                'medias',
                'files'
            ]);
            if($request->input('save_close')) {
                if($item->relation) {
                    return redirect()
                        ->route('oleus.shop_products.edit', $item->relation)
                        ->with('notice', [
                            'message' => trans('notice.shop_product_updated'),
                            'status'  => 'success'
                        ]);
                } else {
                    return redirect()
                        ->route('oleus.shop_products')
                        ->with('notice', [
                            'message' => trans('notice.shop_product_updated'),
                            'status'  => 'success'
                        ]);
                }
            }

            return redirect()
                ->route('oleus.shop_products.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.shop_product_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, ShopProduct $item)
        {
            $_relation = $item->relation;
            $item->delete();

            if($_relation) {
                return redirect()
                    ->route('oleus.shop_products.edit', $_relation)
                    ->with('notice', [
                        'message' => trans('notice.shop_product_deleted'),
                        'status'  => 'success'
                    ]);
            } else {
                return redirect()
                    ->route('oleus.shop_products')
                    ->with('notice', [
                        'message' => trans('notice.shop_product_deleted'),
                        'status'  => 'success'
                    ]);
            }
        }

        public function item(Request $request, ShopProduct $param, $action, $id = NULL)
        {
            $commands = [];
            switch($action) {
                case 'add':
                    $item = (object)[
                        'exists' => FALSE
                    ];
                    $commands[] = [
                        'command' => 'modal',
                        'data'    => view('oleus.shop.param_select_item_modal', compact('item', 'param'))
                            ->render()
                    ];
                    break;
                case 'edit':
                    $item = ShopParamItem::find($id);
                    $commands[] = [
                        'command' => 'modal',
                        'data'    => view('oleus.shop.param_select_item_modal', compact('item', 'param'))
                            ->render()
                    ];
                    break;
                case 'save':
                    $_save = $request->input('param_item');
                    if($icon = $_save['icon_fid']) {
                        $_icon = array_shift($icon);
                        Session::flash('param_item.icon_fid', json_encode([f_get($_icon['id'])]));
                    }
                    $validate_rules = [
                        'param_item.name' => 'required',
                    ];
                    $validator = Validator::make($request->all(), $validate_rules);
                    foreach($validate_rules as $field => $rule) {
                        $commands[] = [
                            'command' => 'removeClass',
                            'target'  => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                            'data'    => 'uk-form-danger'
                        ];
                    }
                    if($validator->fails()) {
                        foreach($validator->errors()->messages() as $field => $message) {
                            $commands[] = [
                                'command' => 'addClass',
                                'target'  => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                                'data'    => 'uk-form-danger'
                            ];
                        }
                        $commands[] = [
                            'command' => 'notifi',
                            'status'  => 'danger',
                            'text'    => trans('notice.errors')
                        ];
                    } else {
                        if(isset($_icon)) $_save['icon_fid'] = (int)$_icon['id'];
                        unset($_save['id']);
                        ShopParamItem::updateOrCreate([
                            'id' => NULL
                        ], $_save);
                        Session::forget([
                            'param_item.icon_fid'
                        ]);
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-param-select-items',
                            'data'    => view('oleus.shop.param_select_item', ['items' => $param->_items])
                                ->render()
                        ];
                        $commands[] = [
                            'command' => 'notifi',
                            'status'  => 'success',
                            'text'    => trans('notice.shop_param_item_created')
                        ];
                        $commands[] = [
                            'command' => 'modal_close'
                        ];
                    }
                    break;
                case 'update':
                    $_save = $request->input('param_item');
                    if(isset($_save['icon_fid']) && $icon = $_save['icon_fid']) {
                        $_icon = array_shift($icon);
                        Session::flash('param_item.icon_fid', json_encode([f_get($_icon['id'])]));
                    }
                    $validate_rules = [
                        'param_item.name' => 'required',
                    ];
                    $validator = Validator::make($request->all(), $validate_rules);
                    foreach($validate_rules as $field => $rule) {
                        $commands[] = [
                            'command' => 'removeClass',
                            'target'  => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                            'data'    => 'uk-form-danger'
                        ];
                    }
                    if($validator->fails()) {
                        foreach($validator->errors()->messages() as $field => $message) {
                            $commands[] = [
                                'command' => 'addClass',
                                'target'  => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                                'data'    => 'uk-form-danger'
                            ];
                        }
                        $commands[] = [
                            'command' => 'notifi',
                            'status'  => 'danger',
                            'text'    => trans('notice.errors')
                        ];
                    } else {
                        if(isset($_icon)) $_save['icon_fid'] = (int)$_icon['id'];
                        unset($_save['id']);
                        ShopParamItem::updateOrCreate([
                            'id' => $id
                        ], $_save);
                        Session::forget([
                            'param_item.icon_fid'
                        ]);
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-param-select-items',
                            'data'    => view('oleus.shop.param_select_item', ['items' => $param->_items])
                                ->render()
                        ];
                        $commands[] = [
                            'command' => 'notifi',
                            'status'  => 'success',
                            'text'    => trans('notice.shop_param_updated')
                        ];
                        $commands[] = [
                            'command' => 'modal_close'
                        ];
                    }
                    break;
                case 'destroy':
                    ShopParamItem::find($id)
                        ->delete();
                    DB::table($param->table)
                        ->where('option_id', $id)
                        ->delete();
                    $param_items = $param->_items;
                    if($param_items->isNotEmpty()) {
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-param-select-items',
                            'data'    => view('oleus.shop.param_select_item', ['items' => $param_items])
                                ->render()
                        ];
                    } else {
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-param-select-items',
                            'data'    => '<div class="uk-alert uk-alert-warning uk-border-rounded" uk-alert>' . trans('others.others.no_items') . '</div>'
                        ];
                    }
                    $commands[] = [
                        'command' => 'notifi',
                        'status'  => 'success',
                        'text'    => trans('notice.shop_param_deleted')
                    ];
                    $commands[] = [
                        'command' => 'modal_close'
                    ];
                    break;
            }

            return response($commands, 200);
        }

        public function relation(Request $request)
        {
            if($request->has('forms')) {
                $_forms = $request->input('forms');
                $_entity_id = $request->input('item_id');
                $_location = $request->input('location');
                $_language = $request->input('language');
                $_validate_rules = [
                    'language' => NULL
                ];
                foreach($_validate_rules as $_field => $_rule) {
                    $_field_id = str_slug($_field);
                    $commands[] = [
                        'command' => 'removeClass',
                        'target'  => "#{$_forms}-{$_field_id}",
                        'data'    => 'uk-form-danger'
                    ];
                }
                $_primary = ShopProduct::find($_entity_id);
                if($_primary->setDuplicate($_language, $_location)) {
                    $commands[] = [
                        'command' => 'notice',
                        'status'  => 'success',
                        'text'    => trans('notice.relate_item_generated')
                    ];
                    $commands[] = [
                        'command' => 'html',
                        'target'  => '#list-relation-items',
                        'data'    => view('oleus.base.forms.fields_group_relations_items', [
                            'related_items' => $_primary->related,
                            'route'         => 'shop_products',
                            'form'          => $this->_form($_primary)
                        ])
                            ->render()
                    ];
                    $commands[] = [
                        'command' => 'modal_close',
                        'target'  => '#modals-form-relate-items',
                    ];
                } else {
                    $commands[] = [
                        'command' => 'notice',
                        'status'  => 'danger',
                        'text'    => trans('notice.error_duplicate_record')
                    ];
                }
            } else {
                $_languages = fields_relate_languages_values();
                $form = parent::__form();
                $form->title = trans('forms.label_related_items');
                $form->button_name = trans('forms.button_add');
                $form->route = _r('oleus.shop_products.relation');
                $form->tabs[] = field_render('forms', [
                    'type'  => 'hidden',
                    'value' => 'relation-items',
                ]);
                $form->tabs[] = field_render('item_id', [
                    'type'  => 'hidden',
                    'value' => $request->input('id'),
                ]);
                if($_languages) {
                    $form->tabs[] = field_render('language', [
                        'type'   => 'select',
                        'id'     => 'relation-items-language',
                        'label'  => trans('forms.label_related_language'),
                        'value'  => NULL,
                        'values' => $_languages,
                        'class'  => 'uk-select2'
                    ]);
                }
                $commands[] = [
                    'command' => 'modal',
                    'options' => [
                        'id'    => 'modals-form-relate-items',
                        'class' => 'uk-margin-auto-vertical'
                    ],
                    'data'    => view('oleus.base.forms.form_modal', compact('form'))
                        ->render()
                ];
            }

            return response($commands, 200);
        }

        public function params(Request $request, ShopProduct $item)
        {
            if($_categories = $request->get('option')) {
                $_params = $item->_params($_categories, TRUE);
                $commands[] = [
                    'command' => 'html',
                    'target'  => '#list-product-params',
                    'data'    => view('oleus.shop.param_product', ['params' => $_params])
                        ->render()
                ];
            } else {
                $commands[] = [
                    'command' => 'html',
                    'target'  => '#list-product-params',
                    'data'    => view('oleus.shop.param_product', ['params' => NULL])
                        ->render()
                ];
            }

            return response($commands, 200);
        }

        public function modify(Request $request, ShopProduct $item)
        {
            if($request->has('forms')) {
                $_forms = $request->input('forms');
                $_entity = ShopProduct::find($request->input('item_id'));
                $_modification_type_mode = $request->input('modification_type_mode', 0);
                $_fields = [
                    'title',
                    'sky',
                    'price',
                    'relation_item'
                ];
                foreach($_fields as $_field) {
                    $commands[] = [
                        'command' => 'removeClass',
                        'target'  => "#{$_forms}-" . str_slug($_field, '-'),
                        'data'    => 'uk-form-danger'
                    ];
                }
                $_rules = [
                    'title'               => 'required_if:modification_type_mode,2',
                    'sky'                 => 'required_if:modification_type_mode,2',
                    'price'               => 'required_if:modification_type_mode,2',
                    'relation_item.value' => 'required_if:modification_type_mode,1'
                ];
                $validator = Validator::make($request->all(), $_rules);
                if($validator->fails()) {
                    foreach($_fields as $_field) {
                        if($_field == 'relation_item') $_field = 'relation_item.value';
                        if($validator->errors()->has($_field)) {
                            if($_field == 'relation_item.value') $_field = 'relation_item';
                            $commands[] = [
                                'command' => 'addClass',
                                'target'  => "#{$_forms}-" . str_slug($_field, '-'),
                                'data'    => 'uk-form-danger'
                            ];
                        }
                    }
                } else {
                    if($_modification_type_mode == 1) {
                        $_save = $request->only([
                            'relation_item',
                        ]);
                    } else {
                        $_save = $request->only([
                            'title',
                            'sky',
                            'price',
                            'relation.param',
                        ]);
                    }
                    if($_entity->_set_modification($_modification_type_mode, $_save)) {
                        $commands[] = [
                            'command' => 'notice',
                            'status'  => 'success',
                            'text'    => trans('notice.relate_item_generated')
                        ];
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-modifications-items',
                            'data'    => view('oleus.shop.product_modifications_items', [
                                'modifications' => $_entity->modifications
                            ])
                                ->render()
                        ];
                        $commands[] = [
                            'command' => 'modal_close',
                            'target'  => '#modals-form-modification-item',
                        ];
                    } else {
                        $commands[] = [
                            'command' => 'notice',
                            'status'  => 'danger',
                            'text'    => trans('notice.error_duplicate_record')
                        ];
                    }
                }
            } else {
                $_param = NULL;
                $_modification = $item->modifications;
                if($_modification['param']) {
                    $_param = $_modification['param']->map(function ($_p) {
                        return [
                            'id'    => $_p->id,
                            'title' => $_p->title,
                            'items' => $_p->_items->pluck('name', 'id')
                        ];
                    });
                }
                $form = parent::__form();
                $form->title = trans('forms.label_modification_product');
                $form->button_name = trans('forms.button_add');
                $form->route = _r('oleus.shop_products.modify');
                $form->tabs[] = field_render('forms', [
                    'type'  => 'hidden',
                    'value' => 'modification-product',
                ]);
                $form->tabs[] = field_render('modification_type_mode', [
                    'type'  => 'hidden',
                    'value' => 1,
                ]);
                $form->tabs[] = field_render('item_id', [
                    'type'  => 'hidden',
                    'value' => $item->id,
                ]);
                $form->tabs[] = '<ul id="tab-modification-product" class="uk-tab" uk-tab="connect: #uk-tab-modal-body; swiping: false;">' .
                    '<li class="uk-active"><a href="#" onclick="document.getElementById(\'form-field-modification-type-mode\').value = 1;">Привязка к существующему товару</a></li>' .
                    '<li><a href="#" onclick="document.getElementById(\'form-field-modification-type-mode\').value = 2;">Добавление нового товара</a></li></ul>';
                $form->tabs[] = '<ul id="uk-tab-modal-body" class="uk-switcher uk-margin">';
                $form->tabs[] = '<li class="uk-active">';
                $form->tabs[] = field_render('relation_item', [
                    'type'       => 'autocomplete',
                    'label'      => 'Название существующего товара',
                    'class'      => 'uk-autocomplete',
                    'id'         => 'modification-product-relation-item',
                    'attributes' => [
                        'data-url'   => _r('oleus.shop_products.modify_relation_item', ['item' => $item]),
                        'data-value' => 'name'
                    ],
                    'required'   => TRUE,
                ]);
                $form->tabs[] = '</li>';
                $form->tabs[] = '<li>';
                $form->tabs[] = field_render('title', [
                    'label'    => trans('forms.label_name_product'),
                    'value'    => "{$item->title} (copy)",
                    'id'       => 'modification-product-title',
                    'required' => TRUE
                ]);
                $form->tabs[] = field_render('sky', [
                    'label'    => trans('forms.label_sky'),
                    'id'       => 'modification-product-sky',
                    'required' => TRUE
                ]);
                $form->tabs[] = field_render('price', [
                    'type'       => 'number',
                    'id'         => 'modification-product-price',
                    'label'      => trans('forms.label_price'),
                    'value'      => $item->price,
                    'required'   => TRUE,
                    'attributes' => [
                        'min'  => 0,
                        'step' => 0.01
                    ]
                ]);
                if($_param) {
                    $_param->each(function ($_p) use (&$form) {
                        $form->tabs[] = field_render("relation.param.{$_p['id']}", [
                            'type'   => 'select',
                            'id'     => 'modification-product-relation-option',
                            'label'  => $_p['title'],
                            'value'  => 0,
                            'values' => $_p['items'],
                            'class'  => 'uk-select2'
                        ]);
                    });
                }
                $form->tabs[] = '</li>';
                $form->tabs[] = '</ul>';
                $commands[] = [
                    'command' => 'modal',
                    'options' => [
                        'id'    => 'modals-form-modification-item',
                        'class' => 'uk-margin-auto-vertical'
                    ],
                    'data'    => view('oleus.base.forms.form_modal', compact('form'))
                        ->render()
                ];
                $commands[] = [
                    'command' => 'easyAutocomplete',
                ];
            }

            return response($commands, 200);
        }

        public function modify_relation_item(Request $request, ShopProduct $item)
        {
            $items = [];

            if($search = $request->input('search')) {
                $_items = ShopProduct::where('modification', 0)
                    ->language(DEFAULT_LANGUAGE)
                    ->location(DEFAULT_LOCATION)
                    ->where('title', 'like', "%{$search}%")
                    ->limit(10)
                    ->get([
                        'title as name',
                        'id as data'
                    ]);
                if($_items->isNotEmpty()) {
                    $items = $_items->toArray();
                }
            }

            return response($items, 200);
        }

        public function modify_remove(Request $request, ShopProduct $item)
        {
            $_entity = ShopProduct::find($item->modification_id);
            $item->update([
                'modification'    => 0,
                'modification_id' => NULL,
            ]);
            $commands[] = [
                'command' => 'notice',
                'status'  => 'success',
                'text'    => trans('notice.relate_item_remove')
            ];
            $commands[] = [
                'command' => 'html',
                'target'  => '#list-modifications-items',
                'data'    => view('oleus.shop.product_modifications_items', [
                    'modifications' => $_entity->modifications
                ])
                    ->render()
            ];

            return response($commands, 200);
        }

        public function related_product(Request $request, ShopProduct $item, $action = 'add', $id = NULL)
        {
            $commands = [];
            if($action == 'add') {
                $commands[] = [
                    'command' => 'append',
                    'target'  => '#list-product-related-items',
                    'data'    => view('oleus.shop.product_related_item', [
                        'item'   => NULL,
                        'id'     => uniqid(),
                        'entity' => $item,
                    ])
                        ->render()
                ];
                $commands[] = [
                    'command' => 'easyAutocomplete'
                ];
                $commands[] = [
                    'command' => 'remove',
                    'target'  => '#list-product-related-items .uk-item-empty'
                ];
            } elseif($action == 'remove') {
                if($id) {
                    $commands[] = [
                        'command' => 'remove',
                        'target'  => "#product-related-item-{$id}"
                    ];
                }
            }

            return response($commands, 200);
        }

        public function product_groups(Request $request, ShopProduct $item, $action = 'add', $id = NULL)
        {
            $commands = [];
            if($action == 'add') {
                $commands[] = [
                    'command' => 'append',
                    'target'  => '#list-product-groups-items',
                    'data'    => view('oleus.shop.product_groups_item', [
                        'item'   => NULL,
                        'id'     => uniqid(),
                        'entity' => $item,
                    ])
                        ->render()
                ];
                $commands[] = [
                    'command' => 'easyAutocomplete'
                ];
                $commands[] = [
                    'command' => 'remove',
                    'target'  => '#list-product-groups-items .uk-item-empty'
                ];
            } elseif($action == 'remove') {
                if($id) {
                    $commands[] = [
                        'command' => 'remove',
                        'target'  => "#product-related-item-{$id}"
                    ];
                }
            }

            return response($commands, 200);
        }

        public function get_products(Request $request)
        {
            $items = [];
            if($_search = $request->input('search')) {
                $_items = ShopProduct::where('title', 'like', "%{$_search}%")
                    ->language()
                    ->location()
                    ->limit(10)
                    ->get();
                if($_items->count()) {
                    $_items->each(function ($item) use (&$items) {
                        $items[] = [
                            'name' => $item->title,
                            'view' => $item->status ? trans('others.status_published') : trans('others.status_unpublished'),
                            'data' => $item->id
                        ];
                    });
                }
            }

            return response($items, 200);
        }
    }

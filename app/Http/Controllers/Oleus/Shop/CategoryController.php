<?php

    namespace App\Http\Controllers\Oleus\Shop;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\City;
    use App\Models\ShopCategory;
    use App\Models\ShopParam;
    use App\Models\ShopParamItem;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Session;
    use Validator;

    class CategoryController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_shop_categories'
            ]);
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'shop_categories';
            $form->permission = array_merge($form->permission, $form->relation, [
                'read'   => 'read_shop_categories',
                'create' => 'create_shop_categories',
                'update' => 'update_shop_categories',
                'delete' => 'delete_shop_categories',
            ]);
            $form->seo = TRUE;
            $_field_parents = NULL;
            $_field_params = NULL;
            if(($_other_categories = $entity->other_categories) && (!$entity->exists || ($entity->exists && is_null($entity->relation)))) {
                $_other_categories = $_other_categories->prepend(trans('forms.value_choice'), 0);
                $_field_parents = field_render('parent_id', [
                    'type'   => 'select',
                    'label'  => trans('forms.label_parent_category'),
                    'value'  => $entity->exists ? $entity->parent_id : 0,
                    'values' => $_other_categories,
                    'class'  => 'uk-select2',
                ]);
            }
            if(!$entity->exists || ($entity->exists && is_null($entity->relation))) {
                $_field_params = [
                    'title'   => trans('others.tab_params'),
                    'content' => [
                        view('oleus.shop.param_category', [
                            'params' => $entity->params,
                            'item'   => $entity
                        ])
                            ->render()
                    ]
                ];
            }
            $form->tabs = [
                [
                    'title'   => trans('others.tab_basic'),
                    'content' => [
                        field_render('title', [
                            'label'      => trans('forms.label_name'),
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
                        field_render('icon_fid', [
                            'type'   => 'file',
                            'label'  => trans('forms.label_icon'),
                            'allow'  => 'jpg|jpeg|gif|png|svg',
                            'values' => $entity->exists && $entity->_icon ? [$entity->_icon] : NULL,
                        ]),
                        $_field_parents,
                        field_render('body', [
                            'label'      => trans('forms.label_body'),
                            'type'       => 'textarea',
                            'editor'     => TRUE,
                            'value'      => $entity->exists ? $entity->body : NULL,
                            'attributes' => [
                                'rows' => 8,
                            ]
                        ]),
                        '<hr class="uk-divider-icon">',
                        field_render('sort', [
                            'type'   => 'select',
                            'label'  => trans('forms.label_sort'),
                            'value'  => $entity->exists ? $entity->sort : 0,
                            'values' => sort_field(),
                            'class'  => 'uk-select2',
                        ]),
                        field_render('status', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_publish'),
                            'selected' => $entity->exists ? $entity->status : 1
                        ])
                    ]
                ],
                $_field_params,
                [
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
                ],
                [
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
                ]
            ];
            $form->tabs[] = [
                'title'   => 'Настройки для страниц фильтра',
                'content' => [
                    field_render('filter_page_meta_title', [
                        'label'      => 'Шаблон для TITLE',
                        'type'       => 'textarea',
                        'value'      => $entity->exists ? $entity->filter_page_meta_title : NULL,
                        'attributes' => [
                            'rows' => 3,
                        ]
                    ]),
                    field_render('filter_page_meta_description', [
                        'label'      => 'Шаблон для DESCRIPTION',
                        'type'       => 'textarea',
                        'value'      => $entity->exists ? $entity->filter_page_meta_description : NULL,
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ]),
                    field_render('filter_page_meta_keywords', [
                        'label'      => 'Шаблон для KEYWORDS',
                        'type'       => 'textarea',
                        'value'      => $entity->exists ? $entity->filter_page_meta_keywords: NULL,
                        'attributes' => [
                            'rows' => 5,
                        ]
                    ])
                ]
            ];

            return $form;
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, [
                'title' => 'required'
            ]);
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => trans('pages.shop_categories_page'),
                'seo._title'  => trans('pages.shop_categories_page')
            ]);
            $items = ShopCategory::location(DEFAULT_LOCATION)
                ->language(DEFAULT_LANGUAGE)
                ->whereNull('parent_id')
                ->orderBy('sort')
                ->get();

            return view('oleus.shop.index_categories', compact('items'));
        }

        public function create(ShopCategory $item)
        {
            $this->set_wrap([
                'page._title'   => trans('pages.shop_categories_page_create'),
                'seo._title'    => trans('pages.shop_categories_page_create'),
                'page._scripts' => [
                    [
                        'url'       => 'components/sortable/sortable.category_params.js',
                        'in_footer' => TRUE,
                    ]
                ]
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function store(Request $request)
        {
            if($icon_fid = $request->input('icon_fid')) {
                $_icon_fid = array_shift($icon_fid);
                Session::flash('icon_fid', json_encode([f_get($_icon_fid['id'])]));
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
                'status',
                'style_id',
                'style_class',
                'icon_fid',
                'background_fid',
                'parentes_id',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'filter_page_meta_title',
                'filter_page_meta_description',
                'filter_page_meta_keywords',
                'meta_robots',
                'sitemap',
                'sort',
                'parent_id'
            ]);
            if(isset($_icon_fid)) $_save['icon_fid'] = (int)$_icon_fid['id'];
            if(isset($_background_fid)) $_save['background_fid'] = (int)$_background_fid['id'];
            if(isset($_save['parent_id']) && $_save['parent_id'] == 0) $_save['parent_id'] = NULL;
            $_save['location'] = DEFAULT_LOCATION;
            $_save['language'] = DEFAULT_LANGUAGE;
            $item = ShopCategory::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'icon_fid',
                'background_fid',
                'medias',
                'files'
            ]);

            return redirect()
                ->route('oleus.shop_categories.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.shop_category_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(ShopCategory $item)
        {
            $this->set_wrap([
                'page._title'   => trans('pages.shop_categories_page_edit'),
                'seo._title'    => trans('pages.shop_categories_page_edit'),
                'page._scripts' => [
                    [
                        'url'       => 'components/sortable/sortable.category_params.js',
                        'in_footer' => TRUE,
                    ]
                ]
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, ShopCategory $item)
        {
            if($icon_fid = $request->input('icon_fid')) {
                $_icon_fid = array_shift($icon_fid);
                Session::flash('icon_fid', json_encode([f_get($_icon_fid['id'])]));
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
                'status',
                'style_id',
                'style_class',
                'icon_fid',
                'background_fid',
                'parentes_id',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'meta_robots',
                'filter_page_meta_title',
                'filter_page_meta_description',
                'filter_page_meta_keywords',
                'sitemap',
                'sort',
                'parent_id'
            ]);
            if(isset($_icon_fid)) $_save['icon_fid'] = (int)$_icon_fid['id'];
            if(isset($_background_fid)) $_save['background_fid'] = (int)$_background_fid['id'];
            if(isset($_save['parent_id']) && $_save['parent_id'] == 0) $_save['parent_id'] = NULL;
            $item->update($_save);
            Session::forget([
                'icon_fid',
                'background_fid',
                'medias',
                'files'
            ]);
            if($request->input('save_close')) {
                if($item->relation) {
                    return redirect()
                        ->route('oleus.shop_categories.edit', $item->relation)
                        ->with('notice', [
                            'message' => trans('notice.shop_category_updated'),
                            'status'  => 'success'
                        ]);
                } else {
                    return redirect()
                        ->route('oleus.shop_categories')
                        ->with('notice', [
                            'message' => trans('notice.shop_category_updated'),
                            'status'  => 'success'
                        ]);
                }
            }

            return redirect()
                ->route('oleus.shop_categories.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.shop_category_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, ShopCategory $item)
        {
            $_relation = $item->relation;
            $item->delete();

            if($_relation) {
                return redirect()
                    ->route('oleus.shop_categories.edit', $_relation)
                    ->with('notice', [
                        'message' => trans('notice.shop_category_deleted'),
                        'status'  => 'success'
                    ]);
            } else {
                return redirect()
                    ->route('oleus.shop_categories')
                    ->with('notice', [
                        'message' => trans('notice.shop_category_deleted'),
                        'status'  => 'success'
                    ]);
            }
        }

        public function item(Request $request, ShopCategory $param, $action, $id = NULL)
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
                $_alert = NULL;
                $_forms = $request->input('forms');
                $_entity_id = $request->input('item_id');
                $_location = $request->input('location');
                $_language = $request->input('language');
                $_validate_rules = [
                    'location' => NULL,
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
                if(!$_location && !$_language) {
                    $commands[] = [
                        'command' => 'notice',
                        'status'  => 'danger',
                        'text'    => trans('notice.select_one_of_the_fields')
                    ];
                    foreach($_validate_rules as $_field => $_rule) {
                        $_field_id = str_slug($_field);
                        $commands[] = [
                            'command' => 'addClass',
                            'target'  => "#{$_forms}-{$_field_id}",
                            'data'    => 'uk-form-danger'
                        ];
                    }
                } else {
                    $_primary = ShopCategory::find($_entity_id);
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
                                'route'         => 'shop_categories',
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
                            'text'    => trans('notice.error_duplicate_record_or_not_exists_parent_in_shop_category')
                        ];
                    }
                }
            } else {
                $_locations = fields_relate_locations_values();
                $_languages = fields_relate_languages_values();
                $form = parent::__form();
                $form->title = trans('forms.label_related_items');
                $form->button_name = trans('forms.button_add');
                $form->route = _r('oleus.shop_categories.relation');
                $form->tabs[] = field_render('forms', [
                    'type'  => 'hidden',
                    'value' => 'relation-items',
                ]);
                $form->tabs[] = field_render('item_id', [
                    'type'  => 'hidden',
                    'value' => $request->input('id'),
                ]);
                if($_locations) {
                    $form->tabs[] = field_render('location', [
                        'type'   => 'select',
                        'id'     => 'relation-items-location',
                        'label'  => trans('forms.label_related_location'),
                        'value'  => NULL,
                        'values' => $_locations,
                        'class'  => 'uk-select2'
                    ]);
                }
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

        public function relation_param(Request $request)
        {
            $_params = $request->get('params');
            if(count($_params)) {
                $commands[] = [
                    'command' => 'html',
                    'target'  => '#relation-category-param',
                    'data'    => '<div class="uk-alert uk-alert-warning uk-border-rounded uk-margin-remove">' . trans('forms.help_category_no_matching_relative_param') . '</div>'
                ];
                $_params_relation = ShopParam::whereIn('id', $_params)
                    ->where('type_view', 'modify')
                    ->where('type', 'select')
                    ->pluck('title', 'id');
                if($_params_relation->isNotEmpty()) {
                    $_params_relation = $_params_relation->prepend(trans('forms.value_choice'), 0);
                    $commands = [
                        [
                            'command' => 'html',
                            'target'  => '#relation-category-param',
                            'data'    => view('oleus.shop.param_category_relation_select', [
                                'relation_params' => $_params_relation,
                                'selected'        => 0
                            ])
                                ->render()
                        ]
                    ];
                }
            } else {
                $commands[] = [
                    'command' => 'html',
                    'target'  => '#relation-category-param',
                    'data'    => '<div class="uk-alert uk-alert-warning uk-border-rounded uk-margin-remove">' . trans('forms.help_category_empty_relative_param') . '</div>'
                ];
            }

            return response($commands, 200);
        }
    }

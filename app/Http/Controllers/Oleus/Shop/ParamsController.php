<?php

    namespace App\Http\Controllers\Oleus\Shop;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\City;
    use App\Models\ShopParam;
    use App\Models\ShopParamItem;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use Session;
    use Validator;

    class ParamsController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_shop_params'
            ]);
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'shop_params';
            $form->permission = array_merge($form->permission, $form->relation, [
                'read'   => 'read_shop_params',
                'create' => 'create_shop_params',
                'update' => 'update_shop_params',
                'delete' => 'delete_shop_params',
            ]);
            $form->relation = FALSE;
            $_field_type = NULL;
            $_field_name = NULL;
            if (!$entity->exists) {
                $_field_type = field_render('type', [
                    'type'   => 'select',
                    'label'  => trans('forms.label_type'),
                    'value'  => 'select',
                    'values' => [
                        'select'       => trans('forms.value_field_select'),
                        'input_number' => trans('forms.value_field_input_number'),
                        'input_text'   => trans('forms.value_field_input_text'),
                    ],
                    'class'  => 'uk-select2'
                ]);
            }
            $_field_visible_in_filter = NULL;
            if ($entity->exists && is_null($entity->relation) && in_array($entity->type, [
                    'input_number',
                    'select'
                ])) {
                $_field_visible_in_filter .= '<hr class="uk-divider-icon">';
                $_field_visible_in_filter .= field_render('visible_in_filter', [
                    'type'     => 'checkbox',
                    'label'    => trans('forms.label_visible_params_in_filter'),
                    'selected' => $entity->visible_in_filter
                ]);
            }
            $form->tabs = [
                [
                    'title'   => trans('others.tab_basic'),
                    'content' => [
                        field_render('title', [
                            'label'      => trans('forms.label_name'),
                            'value'      => $entity->title,
                            'attributes' => [
                                'autofocus' => TRUE
                            ],
                            'required'   => TRUE
                        ]),
                        field_render('name', [
                            'label'      => trans('forms.label_machine_name'),
                            'help'       => $entity->exists ? FALSE : trans('forms.help_shop_params_machine_name'),
                            'value'      => $entity->name,
                            'attributes' => [
                                'readonly' => $entity->exists ? TRUE : FALSE
                            ]
                        ]),
                        $_field_type,
                        $_field_visible_in_filter
                    ]
                ]
            ];
            if ($entity->exists) {
                $_field_params = NULL;
                if ($entity->type == 'select') {
                    if (is_null($entity->relation)) {
                        $_field_params .= field_render('type_view', [
                            'type'     => 'radio',
                            'label'    => trans('forms.label_choice_display'),
                            'selected' => $entity->type_view,
                            'values'   => [
                                'one'      => trans('forms.value_single_choice'),
                                'multiple' => trans('forms.value_multiple_choice'),
                                'modify'   => trans('forms.value_relation_choice'),
                            ],
                            'help'     => trans('forms.help_params_choice_display')
                        ]);
                    }
                    $items = $entity->_items;
                    $_field_params .= view('oleus.shop.param_select_items', compact('items', 'entity'));
                } elseif ($entity->type == 'input_number') {
                    $item = ShopParamItem::where('param_id', $entity->id)
                        ->where('type', $entity->type)
                        ->first();
                    $_field_params = view('oleus.shop.param_input_number_item', [
                        'param' => $entity,
                        'item'  => $item
                    ])
                        ->render();
                } elseif ($entity->type == 'input_text') {
                    $item = ShopParamItem::where('param_id', $entity->id)
                        ->where('type', $entity->type)
                        ->first();
                    $_field_params = view('oleus.shop.param_input_text_item', [
                        'param' => $entity,
                        'item'  => $item
                    ])
                        ->render();
                }
                $form->tabs[] = [
                    'title'   => trans('others.tab_params'),
                    'content' => [
                        $_field_params
                    ]
                ];
            }
            if ($form->translate) {
                if ($_languages = wrap()->get('languages')) {
                    $_field_translate = NULL;
                    foreach ($_languages as $_language_key => $_language_value) {
                        if ($_language_key != DEFAULT_LANGUAGE) {
                            $_field_translate[] = field_render("translate.{$_language_key}", [
                                'label'      => $_language_value['full_name'],
                                'value'      => $entity->_translate_title($_language_key),
                                'attributes' => [
                                    'placeholder' => trans('forms.label_name')
                                ],
                            ]);
                            if ($entity->exists && $entity->type != 'select') {
                                $_field_translate[] = field_render("param_item.translate.{$_language_key}", [
                                    'value'      => $entity->_translate_unit($_language_key),
                                    'attributes' => [
                                        'placeholder' => trans('forms.label_unit_value')
                                    ],
                                ]);
                            }
                        }
                    }
                    $form->tabs[] = [
                        'title'   => trans('others.tab_translate'),
                        'content' => $_field_translate
                    ];
                }
            }

            return $form;
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, [
                'title' => 'required',
                'name'  => 'sometimes|unique:shop_params,name'
            ]);
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => trans('pages.shop_params_page'),
                'seo._title'  => trans('pages.shop_params_page')
            ]);
            $items = ShopParam::language(DEFAULT_LANGUAGE)
                ->orderBy('title')
                ->paginate();

            return view('oleus.shop.index_params', compact('items'));
        }

        public function create(ShopParam $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.shop_params_page_create'),
                'seo._title'  => trans('pages.shop_params_page_create')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function store(Request $request)
        {
            $this->validate($request, [
                'title' => 'required',
                'name'  => 'sometimes|unique:shop_params,name'
            ]);
            $_save = $request->only([
                'title',
                'name',
                'type',
                'visible_in_filter'
            ]);
            $_save['language'] = DEFAULT_LANGUAGE;
            if ($_translate = $request->get('translate')) $_save['translate'] = serialize($_translate);
            $item = ShopParam::updateOrCreate([
                'id' => NULL
            ], $_save);
            $item = $item->_generate_technical_name();
            $item->alias_name = $item->_generate_technical_name($item->title, 1);
            $item->table = "shop_param_{$item->name}_data";
            $_field_type = $item->type;
            Schema::create($item->table, function ($table) use ($_field_type) {
                $table->increments('id');
                $table->integer('product_id')
                    ->unsigned();
                if ($_field_type == 'select') {
                    $table->integer('option_id')
                        ->unsigned();
                }
                if ($_field_type != 'select') {
                    $table->string('value')
                        ->nullable();
                }
                $table->foreign('product_id')
                    ->references('id')
                    ->on('shop_products')
                    ->onDelete('cascade');
                if ($_field_type == 'select') {
                    $table->foreign('option_id')
                        ->references('id')
                        ->on('shop_param_items')
                        ->onDelete('cascade');
                }
            });
            $item->save();

            return redirect()
                ->route('oleus.shop_params.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.shop_param_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(ShopParam $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.shop_params_page_edit'),
                'seo._title'  => trans('pages.shop_params_page_edit')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, ShopParam $item)
        {
            $this->validate($request, [
                'title' => 'required',
            ]);
            $_save = $request->only([
                'title',
                'visible_in_filter',
                'type_view',
            ]);
            if ($_translate = $request->get('translate')) $_save['translate'] = serialize($_translate);
            $item->update($_save);
            if ($request->input('save_close')) {
                if ($item->relation) {
                    return redirect()
                        ->route('oleus.shop_params.edit', $item->relation)
                        ->with('notice', [
                            'message' => trans('notice.shop_param_updated'),
                            'status'  => 'success'
                        ]);
                } else {
                    return redirect()
                        ->route('oleus.shop_params')
                        ->with('notice', [
                            'message' => trans('notice.shop_param_updated'),
                            'status'  => 'success'
                        ]);
                }
            }

            return redirect()
                ->route('oleus.shop_params.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.shop_param_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, ShopParam $item)
        {
            $_relation = $item->relation;
            $item->delete();

            if ($_relation) {
                return redirect()
                    ->route('oleus.shop_params.edit', $_relation)
                    ->with('notice', [
                        'message' => trans('notice.shop_param_deleted'),
                        'status'  => 'success'
                    ]);
            } else {
                return redirect()
                    ->route('oleus.shop_params')
                    ->with('notice', [
                        'message' => trans('notice.shop_param_deleted'),
                        'status'  => 'success'
                    ]);
            }
        }

        public function item(Request $request, ShopParam $param, $action, $id = NULL)
        {
            $commands = [];
            switch ($action) {
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
                    if ($icon = $_save['icon_fid']) {
                        $_icon = array_shift($icon);
                        Session::flash('param_item.icon_fid', json_encode([f_get($_icon['id'])]));
                    }
                    $validate_rules = [
                        'param_item.name' => 'required',
                    ];
                    $validator = Validator::make($request->all(), $validate_rules);
                    foreach ($validate_rules as $field => $rule) {
                        $commands[] = [
                            'command' => 'removeClass',
                            'target'  => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                            'data'    => 'uk-form-danger'
                        ];
                    }
                    if ($validator->fails()) {
                        foreach ($validator->errors()->messages() as $field => $message) {
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
                        if (isset($_icon)) $_save['icon_fid'] = (int)$_icon['id'];
                        if (USE_MULTI_LANGUAGE) {
                            $_save['translate'] = $_save['translate'] ? serialize($_save['translate']) : NULL;
                        }
                        unset($_save['id']);
                        $_save['alias_name'] = $param->_generate_technical_name($_save['name']);
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
                    if (isset($_save['icon_fid']) && $icon = $_save['icon_fid']) {
                        $_icon = array_shift($icon);
                        Session::flash('param_item.icon_fid', json_encode([f_get($_icon['id'])]));
                    }
                    $validate_rules = [
                        'param_item.name' => 'required',
                    ];
                    $validator = Validator::make($request->all(), $validate_rules);
                    foreach ($validate_rules as $field => $rule) {
                        $commands[] = [
                            'command' => 'removeClass',
                            'target'  => '#' . str_slug('form-field-' . str_replace('.', '_', $field), '-'),
                            'data'    => 'uk-form-danger'
                        ];
                    }
                    if ($validator->fails()) {
                        foreach ($validator->errors()->messages() as $field => $message) {
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
                        if (isset($_icon)) $_save['icon_fid'] = (int)$_icon['id'];
                        if (USE_MULTI_LANGUAGE) {
                            $_save['translate'] = $_save['translate'] ? serialize($_save['translate']) : NULL;
                        }
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
                    if ($param_items->isNotEmpty()) {
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
    }

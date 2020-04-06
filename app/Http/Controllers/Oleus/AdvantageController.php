<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\Advantage;
    use App\Models\AdvantageItems;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;
    use Validator;

    class AdvantageController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_advantages'
            ]);
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->theme = 'oleus.base.forms.form';
            $form->route_tag = 'advantages';
            $form->permission = array_merge($form->permission, [
                'read'   => 'read_advantages',
                'create' => 'create_advantages',
                'update' => 'update_advantages',
                'delete' => 'delete_advantages',
            ]);
            $form->relation = array_merge($form->relation, [
                'view_link' => FALSE,
            ]);
            $form->tabs = [
                [
                    'title'   => trans('others.tab_basic'),
                    'content' => [
                        field_render('title', [
                            'label'      => trans('forms.label_title'),
                            'value'      => $entity->exists ? $entity->title : NULL,
                            'attributes' => [
                                'autofocus' => TRUE
                            ],
                            'required'   => TRUE
                        ]),
                        field_render('sub_title', [
                            'label' => trans('forms.label_sub_title'),
                            'value' => $entity->exists ? $entity->sub_title : NULL
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
                        '<hr class="uk-divider-icon">',
                        field_render('position', [
                            'type'     => 'select',
                            'label'    => trans('forms.label_description_position'),
                            'values'   => [
                                'under' => trans('forms.value_under_list'),
                                'above' => trans('forms.value_above_list'),
                            ],
                            'selected' => $entity->exists ? $entity->position : 'under',
                            'class'    => 'uk-select2',
                            'help'     => trans('forms.help_description_position_advantages')
                        ]),
                        field_render('hidden_title', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_hidden_title'),
                            'selected' => $entity->exists ? $entity->hidden_title : 0
                        ]),
                        field_render('status', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_visible_advantage'),
                            'name'     => 'status',
                            'selected' => $entity->exists ? $entity->status : 1
                        ])
                    ]
                ],
                [
                    'title'   => trans('others.tab_style'),
                    'content' => [
                        field_render('style_id', [
                            'label' => trans('forms.label_style_id'),
                            'value' => $entity->exists ? $entity->style_id : NULL
                        ]),
                        field_render('style_class', [
                            'label' => trans('forms.label_style_class'),
                            'value' => $entity->exists ? $entity->style_class : NULL,
                        ]),
                        field_render('background_fid', [
                            'type'   => 'file',
                            'label'  => trans('forms.label_background_block'),
                            'allow'  => 'jpg|jpeg|gif|png|svg',
                            'values' => $entity->exists && $entity->_background ? [$entity->_background] : NULL,
                        ]),
                    ],
                ],
            ];
            if($entity->exists) {
                $form->tabs[] = [
                    'title'   => trans('others.tab_advantage_items'),
                    'content' => [
                        'section' => view('oleus.advantages.items', [
                            'items'  => $entity->_items,
                            'entity' => $entity
                        ])->render()
                    ]
                ];
            }

            return $form;
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, [
                'title' => 'required',
            ]);
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => trans('pages.advantages_page'),
                'seo._title'  => trans('pages.advantages_page')
            ]);
            $items = Advantage::location(DEFAULT_LOCATION)
                ->language(DEFAULT_LANGUAGE)
                ->paginate();

            return view('oleus.advantages.index', compact('items'));
        }

        public function create(Advantage $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.advantages_page_create'),
                'seo._title'  => trans('pages.advantages_page_create')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function store(Request $request)
        {
            if($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            $this->_validate($request);
            $_save = $request->only([
                'title',
                'sub_title',
                'body',
                'position',
                'hidden_title',
                'status',
                'sort',
                'style_id',
                'style_class',
                'background_fid',
            ]);
            if(isset($_background_fid)) $_save['background_fid'] = (int)$_background_fid['id'];
            $_save['language'] = DEFAULT_LANGUAGE;
            $_save['location'] = DEFAULT_LOCATION;
            $item = Advantage::updateOrCreate([
                'id' => NULL
            ], $_save);
            Session::forget([
                'background_fid'
            ]);

            return redirect()
                ->route('oleus.advantages.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.advantage_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(Advantage $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.advantages_page_edit'),
                'seo._title'  => trans('pages.advantages_page_edit')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, Advantage $item)
        {
            if($background_fid = $request->input('background_fid')) {
                $_background_fid = array_shift($background_fid);
                Session::flash('background_fid', json_encode([f_get($_background_fid['id'])]));
            }
            $this->_validate($request);
            $_save = $request->only([
                'title',
                'sub_title',
                'body',
                'hidden_title',
                'position',
                'status',
                'style_id',
                'style_class',
                'background_fid',
            ]);
            if(isset($_background_fid)) $_save['background_fid'] = (int)$_background_fid['id'];
            $item->update($_save);
            Session::forget([
                'background_fid'
            ]);
            if($request->input('save_close')) {
                if($item->relation) {
                    return redirect()
                        ->route('oleus.advantages.edit', $item->relation)
                        ->with('notice', [
                            'message' => trans('notice.advantage_updated'),
                            'status'  => 'success'
                        ]);
                } else {
                    return redirect()
                        ->route('oleus.advantages')
                        ->with('notice', [
                            'message' => trans('notice.advantage_updated'),
                            'status'  => 'success'
                        ]);
                }
            }

            return redirect()
                ->route('oleus.advantages.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.advantage_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, Advantage $item)
        {
            $_relation = $item->relation;
            $item->delete();

            if($_relation) {
                return redirect()
                    ->route('oleus.advantages.edit', $_relation)
                    ->with('notice', [
                        'message' => trans('notice.advantage_deleted'),
                        'status'  => 'success'
                    ]);
            } else {
                return redirect()
                    ->route('oleus.advantages')
                    ->with('notice', [
                        'message' => trans('notice.advantage_deleted'),
                        'status'  => 'success'
                    ]);
            }
        }

        public function item(Request $request, Advantage $advantages, $action, $id = NULL)
        {
            $commands = [];
            switch($action) {
                case 'add':
                    $item = (object)[
                        'exists' => FALSE
                    ];
                    $commands[] = [
                        'command' => 'modal',
                        'data'    => view('oleus.advantages.item_modal', compact('item', 'advantages'))
                            ->render()
                    ];
                    break;
                case 'edit':
                    $item = AdvantageItems::find($id);
                    $commands[] = [
                        'command' => 'modal',
                        'data'    => view('oleus.advantages.item_modal', compact('item', 'advantages'))
                            ->render()
                    ];
                    break;
                case 'save':
                    $_save = $request->input('advantage_item');
                    if($icon = $_save['icon_fid']) {
                        $_icon = array_shift($icon);
                        Session::flash('advantage_item.icon_fid', json_encode([f_get($_icon['id'])]));
                    }
                    $validate_rules = [
                        'advantage_item.title' => 'required',
                        'advantage_item.body'  => 'required'
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
                        $_save['advantage_id'] = $advantages->id;
                        if(isset($_icon)) $_save['icon_fid'] = (int)$_icon['id'];
                        $_save['language'] = config('app.locale');
                        AdvantageItems::updateOrCreate([
                            'id' => NULL
                        ], $_save);
                        Session::forget([
                            'advantage_item.icon_fid'
                        ]);
                        $advantages_items = $advantages->_items;
                        $advantages_items->map(function ($_item) use (&$items_output) {
                            $items_output .= view('oleus.advantages.item', ['item' => $_item])
                                ->render();
                        });
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-advantages-items',
                            'data'    => $items_output
                        ];
                        $commands[] = [
                            'command' => 'notifi',
                            'status'  => 'success',
                            'text'    => trans('notice.advantage_item_created')
                        ];
                        $commands[] = [
                            'command' => 'modal_close'
                        ];
                    }
                    break;
                case 'update':
                    $_save = $request->input('advantage_item');
                    if($icon = $_save['icon_fid']) {
                        $_icon = array_shift($icon);
                        Session::flash('advantage_item.icon_fid', json_encode([f_get($_icon['id'])]));
                    }
                    $validate_rules = [
                        'advantage_item.title' => 'required',
                        'advantage_item.body'  => 'required'
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
                        AdvantageItems::updateOrCreate([
                            'id' => $_save['id']
                        ], $_save);
                        Session::forget([
                            'advantage_item.icon_fid'
                        ]);
                        $advantages_items = $advantages->_items;
                        $advantages_items->map(function ($_item) use (&$items_output) {
                            $items_output .= view('oleus.advantages.item', ['item' => $_item])
                                ->render();
                        });
                        $commands[] = [
                            'command' => 'html',
                            'target'  => '#list-advantages-items',
                            'data'    => $items_output
                        ];
                        $commands[] = [
                            'command' => 'notifi',
                            'status'  => 'success',
                            'text'    => trans('notice.advantage_item_created')
                        ];
                        $commands[] = [
                            'command' => 'modal_close'
                        ];
                    }
                    break;
                case 'destroy':
                    AdvantageItems::find($id)
                        ->delete();
                    $advantages_items = $advantages->_items;
                    $advantages_items->map(function ($_item) use (&$items_output) {
                        $items_output .= view('oleus.advantages.item', ['item' => $_item])
                            ->render();
                    });
                    $commands[] = [
                        'command' => 'html',
                        'target'  => '#list-advantages-items',
                        'data'    => $items_output
                    ];
                    $commands[] = [
                        'command' => 'notifi',
                        'status'  => 'success',
                        'text'    => trans('notice.advantage_item_created')
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
                    $_primary = Advantage::find($_entity_id);
                    if($_primary->_set_duplicate($_language, $_location)) {
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
                                'route'         => 'advantages',
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
                }
            } else {
                $_locations = fields_relate_locations_values();
                $_languages = fields_relate_languages_values();
                $form = parent::__form();
                $form->title = trans('forms.label_related_items');
                $form->button_name = trans('forms.button_add');
                $form->route = _r('oleus.advantages.relation');
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
    }

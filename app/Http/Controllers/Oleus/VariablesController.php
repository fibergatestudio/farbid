<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\Variable;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;

    class VariablesController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_variables'
            ]);
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, [
                'key'   => 'sometimes|required|unique:variables|regex:/^[a-zA-Z0-9_-]+$/u',
                'title' => 'sometimes|required',
                'value' => 'required',
            ]);
        }

        public function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'variables';
            $form->permission = array_merge($form->permission, [
                'read'   => 'read_variables',
                'create' => 'create_variables',
                'update' => 'update_variables',
                'delete' => 'delete_variables',
            ]);
            $form->relation = array_merge($form->relation, [
                'view_link'   => FALSE,
                'view_status' => FALSE,
            ]);
            $form->tabs = [
                [
                    'title'   => trans('others.tab_basic'),
                    'content' => [
                        ((!$entity->exists || ($entity->exists && is_null($entity->relation))) ? field_render('title', [
                            'label'    => trans('forms.label_variables_name'),
                            'value'    => $entity->exists ? $entity->title : NULL,
                            'help'     => trans('forms.help_name_of_variable'),
                            'required' => TRUE
                        ]) : NULL),
                        ((!$entity->exists || ($entity->exists && is_null($entity->relation))) ? field_render('key', [
                            'label'      => trans('forms.label_machine_name'),
                            'value'      => $entity->exists ? $entity->key : NULL,
                            'attributes' => [
                                'autofocus' => TRUE,
                                'readonly'  => $entity->exists ? TRUE : FALSE
                            ],
                            'help'       => $entity->exists ? NULL : trans('forms.help_machine_name'),
                            'required'   => !$entity->exists ? TRUE : FALSE
                        ]) : NULL),
                        field_render('value', [
                            'label'      => trans('forms.label_value_of_variable'),
                            'type'       => 'textarea',
                            'class'      => 'uk-codeMirror',
                            'value'      => $entity->exists ? $entity->data : NULL,
                            'attributes' => [
                                'rows' => 12,
                            ],
                            'help'       => trans('forms.help_value_of_variable'),
                            'required'   => TRUE
                        ]),
                        field_render('comment', [
                            'label'      => trans('forms.label_comment'),
                            'type'       => 'textarea',
                            'value'      => $entity->exists ? $entity->comment : NULL,
                            'attributes' => [
                                'rows' => 4,
                            ],
                            'help'       => trans('forms.help_comment_of_variable')
                        ]),
                        field_render('do', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_do_code'),
                            'selected' => $entity->exists ? $entity->do : 0
                        ])
                    ]
                ]
            ];

            return $form;
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => trans('pages.variables_page'),
                'seo._title'  => trans('pages.variables_page')
            ]);
            $items = Variable::location(DEFAULT_LOCATION)
                ->language(DEFAULT_LANGUAGE)
                ->orderBy('title')
                ->paginate();

            return view('oleus.variable.index', compact('items'));
        }

        public function create(Variable $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.variables_page_create'),
                'seo._title'  => trans('pages.variables_page_create')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function store(Request $request)
        {
            $this->_validate($request);
            $_save = $request->only([
                'key',
                'title',
                'value',
                'comment',
                'do',
            ]);
            $_save['language'] = DEFAULT_LANGUAGE;
            $_save['location'] = DEFAULT_LOCATION;
            $_save['value'] = serialize($_save['value']);
            $item = Variable::updateOrCreate([
                'id' => NULL
            ], $_save);

            return redirect()
                ->route('oleus.variables.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.variable_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(Variable $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.variables_page_create'),
                'seo._title'  => trans('pages.variables_page_create')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, Variable $item)
        {
            $request->offsetUnset('key');
            $this->_validate($request);
            $_save = $request->only([
                'title',
                'value',
                'comment',
                'do',
            ]);
            $_save['value'] = serialize($_save['value']);
            $item->update($_save);
            if($request->input('save_close')) {
                if($item->relation) {
                    return redirect()->route('oleus.variables.edit', $item->relation)
                        ->with('notice', [
                            'message' => trans('notice.variable_updated'),
                            'status'  => 'success',
                        ]);
                } else {
                    return redirect()->route('oleus.variables')
                        ->with('notice', [
                            'message' => trans('notice.variable_updated'),
                            'status'  => 'success',
                        ]);
                }
            }

            return redirect()
                ->route('oleus.variables.edit', [
                    'id' => $item->id
                ])
                ->with('notice', [
                    'message' => trans('notice.variable_updated'),
                    'status'  => 'success',
                ]);
        }

        public function destroy(Request $request, Variable $item)
        {
            $_relation = $item->relation;
            $item->delete();

            if($_relation) {
                return redirect()
                    ->route('oleus.variables.edit', $_relation)
                    ->with('notice', [
                        'message' => trans('notice.variable_deleted'),
                        'status'  => 'success'
                    ]);
            } else {
                return redirect()
                    ->route('oleus.variables')
                    ->with('notice', [
                        'message' => trans('notice.variable_deleted'),
                        'status'  => 'success'
                    ]);
            }
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
                    $_primary = Variable::find($_entity_id);
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
                                'route'         => 'variables',
                                'form' => $this->_form($_primary)
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
                $form->route = _r('oleus.variables.relation');
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

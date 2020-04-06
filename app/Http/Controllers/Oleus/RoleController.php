<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\Permission;
    use App\Models\Role;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;

    class RoleController extends Controller
    {
        use Authorizable;
        use Dashboard;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_roles'
            ]);
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'roles';
            $form->relation = FALSE;
            $form->permission = array_merge($form->permission, [
                'read'   => 'read_roles',
                'create' => 'create_roles',
                'update' => 'update_roles',
                'delete' => 'delete_roles',
            ]);
            $form->tabs = [
                [
                    'title'   => trans('others.tab_basic'),
                    'content' => [
                        field_render('name', [
                            'label'      => trans('forms.label_machine_name'),
                            'value'      => $entity->exists ? $entity->name : NULL,
                            'required'   => TRUE,
                            'attributes' => $entity->exists ? ['disabled' => TRUE] : ['autofocus' => TRUE],
                        ]),
                        field_render('display_name', [
                            'label'    => trans('forms.label_name'),
                            'value'    => $entity->exists ? trans($entity->display_name) : NULL,
                            'required' => TRUE,
                        ])
                    ]
                ]
            ];
            if($entity->exists) {
                $_permissions = Permission::all();
                $_permissions = $_permissions->keyBy('name')->map(function ($_permission) {
                    return trans($_permission['display_name']);
                });
                $form->tabs[] = [
                    'title'   => trans('others.tab_permissions'),
                    'content' => [
                        field_render('permissions', [
                            'label'    => trans('forms.label_permissions'),
                            'type'     => 'checkboxes',
                            'selected' => $entity->exists ? $entity->permissions->pluck('name')->toArray() : NULL,
                            'values'   => $_permissions,
                        ])
                    ],
                ];
            }

            return $form;
        }

        protected function _list()
        {
            $_items = collect([]);
            $user = wrap()->get('user');
            $_roles = Role::with('permissions')
                ->orderBy('display_name')
                ->paginate();
            if($_roles) {
                $_items = $_roles->map(function ($_role) use ($user) {
                    return [
                        'input'        => '<input type="checkbox" name="" class="uk-checkbox uk-border-rounded">',
                        'id'           => "<div class='uk-text-center uk-text-bold'>{$_role->id}</div>",
                        'name'         => $_role->name,
                        'display_name' => trans($_role->display_name),
                        'permissions'  => "<div class='uk-text-center uk-text-bold uk-teal-text text-darken-2'>{$_role->permissions->count()}</div>",
                        'button'       => $user->hasPermissionTo('update_roles') ? _l('', 'oleus.roles.edit', [
                            'p' => [
                                'id' => $_role->id
                            ],
                            'a' => [
                                'class'   => 'uk-button-icon uk-button uk-button-primary uk-waves uk-button-small uk-border-rounded uk-box-shadow-small',
                                'uk-icon' => 'icon: ui_mode_edit'
                            ]
                        ]) : ''
                    ];
                });
            }

            return $this->render_items([
                'buttons'        => $user->hasPermissionTo('create_roles') ? [
                    _l(trans('forms.button_add'), 'oleus.roles.create', [
                        'a' => [
                            'class' => 'uk-button uk-button-success uk-waves uk-border-rounded uk-box-shadow-small'
                        ]
                    ])
                ] : [],
                'headers'        => [
                    [
                        'class' => 'uk-width-expand',
                    ],
                    [
                        'class' => 'uk-width-xsmall uk-text-center',
                        'data'  => 'ID',
                    ],
                    [
                        'class' => 'uk-width-medium',
                        'data'  => trans('forms.label_machine_name'),
                    ],
                    [
                        'data' => trans('forms.label_name'),
                    ],
                    [
                        'class' => 'uk-width-expand uk-text-center',
                        'data'  => '<span uk-icon="icon: ui_fiber_pin" title="' . trans('forms.label_count_permissions') . '">',
                    ],
                    [
                        'class' => 'uk-width-expand'
                    ]
                ],
                'items'          => $_items,
                'filteredFields' => [
                    'name',
                    'display_name'
                ],
                'apiPath'        => '/api/v1/roles',
                'pagination'     => [
                    'total'       => $_roles->total(),
                    'currentPage' => $_roles->currentPage(),
                    'lastPage'    => $_roles->lastPage(),
                    'to'          => $_roles->lastItem(),
                    'perPage'     => $_roles->perPage()
                ]
            ]);
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => trans('pages.roles_page'),
                'seo._title'  => trans('pages.roles_page')
            ]);
            $items = Role::paginate();

            return view('oleus.roles.index', compact('items'));
        }

        public function create(Role $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.roles_page_create'),
                'seo._title'  => trans('pages.roles_page_create')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function store(Request $request)
        {
            $this->validate($request, [
                'name'         => 'required|alpha_dash|unique:roles|max:191',
                'display_name' => 'required|max:191',
            ]);
            $_save = $request->only([
                'name',
                'display_name',
            ]);
            $_save['guard_name'] = Role::$defaultGuardName;
            $item = Role::updateOrCreate([
                'id' => NULL
            ], $_save);

            return redirect()
                ->route('oleus.roles.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.role_created'),
                    'status'  => 'success',
                ]);
        }

        public function edit(Role $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.roles_page_edit'),
                'seo._title'  => trans('pages.roles_page_edit')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, Role $item)
        {
            $this->validate($request, [
                'permissions'  => 'required|array',
                'display_name' => 'required|max:191',
            ]);
            $_save = $request->only([
                'display_name',
            ]);
            if($item->id > 2) {
                $item->update($_save);
            } else {
                $item->save();
            }
            if($request->input('save_close')) {
                return redirect()->route('oleus.roles')
                    ->with('notice', [
                        'message' => trans('notice.role_updated'),
                        'status'  => 'success',
                    ]);
            }

            return redirect()->route('oleus.roles.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.role_updated'),
                    'status'  => 'success',
                ]);
        }

        public function destroy(Role $item)
        {
            $item->delete();

            return redirect()
                ->route('oleus.roles')
                ->with('notice', [
                    'message' => trans('common::notice.deleted'),
                    'status'  => 'success',
                ]);
        }

        public function api_response_items(Request $request)
        {
            $user = $request->user();
            $_items = NULL;
            if($_query_string = $request->input('query_string')) {
                $_query_string_trans = search_by_key_trans($_query_string, 'roles_and_permission');
                $_roles = Role::with('permissions')
                    ->where('name', 'like', "%{$_query_string}%");
                if(is_string($_query_string_trans)) {
                    $_roles->orWhere('display_name', 'like', "%{$_query_string_trans}%")
                        ->orderByRaw("CASE WHEN (display_name LIKE '{$_query_string_trans}%') THEN 0 WHEN (name LIKE '{$_query_string}%') THEN 1 WHEN (display_name LIKE '%{$_query_string_trans}%') THEN 2 WHEN (name LIKE '%{$_query_string}%') THEN 3 ELSE 4 END");
                } elseif(is_array($_query_string_trans)) {
                    $_roles->orWhereIn('display_name', $_query_string_trans)
                        ->orderByRaw("CASE WHEN (name LIKE '{$_query_string}%') THEN 0 WHEN (name LIKE '%{$_query_string}%') THEN 1 ELSE 2 END");
                }
                $_roles = $_roles->paginate();
            } else {
                $_roles = Role::with('permissions')
                    ->orderBy('display_name')
                    ->paginate();
            }
            if($_roles) {
                $_items = $_roles->map(function ($_role) use ($user) {
                    return [
                        'input'        => '<input type="checkbox" name="" class="uk-checkbox uk-border-rounded">',
                        'id'           => "<div class='uk-text-center uk-text-bold'>{$_role->id}</div>",
                        'name'         => $_role->name,
                        'display_name' => trans($_role->display_name),
                        'permissions'  => "<div class='uk-text-center uk-text-bold uk-teal-text text-darken-2'>{$_role->permissions->count()}</div>",
                        'button'       => $user->hasPermissionTo('update_roles') ? _l('', 'oleus.roles.edit', [
                            'p' => [
                                'id' => $_role->id
                            ],
                            'a' => [
                                'class'   => 'uk-button-icon uk-button uk-button-primary uk-waves uk-button-small uk-border-rounded uk-box-shadow-small',
                                'uk-icon' => 'icon: ui_mode_edit'
                            ]
                        ]) : ''
                    ];
                });
            }

            return [
                'items'      => $_items,
                'pagination' => [
                    'total'       => $_roles->total(),
                    'currentPage' => $_roles->currentPage(),
                    'lastPage'    => $_roles->lastPage(),
                    'to'          => $_roles->lastItem(),
                    'perPage'     => $_roles->perPage()
                ]
            ];
        }
    }

<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\Role;
    use App\User;
    use Carbon\Carbon;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;

    class UserController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_users'
            ]);
        }

        protected function _form($entity)
        {
            $_roles = Role::getAll();
            $form = parent::__form();
            $form->route_tag = 'users';
            $form->relation = FALSE;
            $form->permission = array_merge($form->permission, [
                'read'   => 'read_users',
                'create' => 'create_users',
                'update' => 'update_users',
                'delete' => 'delete_users',
            ]);
            $_field_blocked = NULL;
            if($entity->exists) {
                if(request()->user()->can('create_roles', 'update_roles')) {
                    $_field_blocked .= field_render('blocked', [
                        'type'     => 'checkbox',
                        'label'    => trans('forms.label_blocked'),
                        'selected' => $entity->exists && $entity->blocked ? $entity->blocked : 0,
                        'value'    => 1,
                        'help'     => trans('forms.help_blocked_user_account'),
                    ]);
                }
            }
            $form->tabs = [
                [
                    'title'   => trans('others.tab_basic'),
                    'content' => [
                        field_render('name', [
                            'label'      => trans('forms.label_login'),
                            'value'      => $entity->exists ? $entity->name : NULL,
                            'attributes' => [
                                'autofocus'
                            ],
                            'required'   => TRUE
                        ]),
                        field_render('email', [
                            'type'     => 'email',
                            'label'    => trans('forms.label_email'),
                            'value'    => $entity->exists ? $entity->email : NULL,
                            'required' => TRUE,
                        ]),
                        field_render('password', [
                            'type'     => 'password_confirmation',
                            'label'    => trans('forms.label_password'),
                            'value'    => $entity ? $entity->password : NULL,
                            'required' => TRUE,
                        ]),
                        field_render('role', [
                            'type'     => 'select',
                            'label'    => trans('forms.label_role'),
                            'value'    => $entity->exists ? $entity->getRoleNames()->first() : 'user',
                            'values'   => $_roles->pluck('display_name', 'name')->toArray(),
                            'class'    => 'uk-select2',
                            'required' => TRUE
                        ]),
                        '<hr class="uk-divider-icon">',
                        field_render('active', [
                            'type'     => 'checkbox',
                            'label'    => trans('forms.label_unlocked'),
                            'selected' => $entity->exists ? $entity->active : 0,
                            'value'    => 1,
                            'help'     => trans('forms.help_active_user_account'),
                        ]),
                        $_field_blocked
                    ],
                ],
                [
                    'title'   => trans('others.tab_profile'),
                    'content' => [
                        field_render('avatar_fid', [
                            'type'   => 'file',
                            'label'  => trans('forms.label_avatar'),
                            'allow'  => 'jpg|jpeg|gif|png|svg',
                            'values' => $entity->exists && $entity->_profile->_avatar ? [$entity->_profile->_avatar] : NULL,
                        ]),
                        field_render('last_name', [
                            'label' => trans('forms.label_last_name'),
                            'value' => $entity->exists ? $entity->_profile->last_name : NULL
                        ]),
                        field_render('first_name', [
                            'label' => trans('forms.label_first_name'),
                            'value' => $entity->exists ? $entity->_profile->first_name : NULL
                        ]),
                        field_render('phone', [
                            'label' => trans('forms.label_phone'),
                            'value' => $entity->exists ? $entity->_profile->phone : NULL,
                            'class' => 'uk-phone-mask'
                        ]),
                        field_render('sex', [
                            'type'   => 'radio',
                            'label'  => trans('forms.label_sex'),
                            'value'  => $entity->exists ? $entity->_profile->sex : 'male',
                            'values' => [
                                'male'   => trans('forms.label_sex_male'),
                                'female' => trans('forms.label_sex_female'),
                            ]
                        ]),
                        field_render('birthday', [
                            'label' => trans('forms.label_birthday'),
                            'value' => $entity->exists && $entity->_profile->birthday ? $entity->_profile->birthday->format('d.m.Y') : NULL,
                            'class' => 'uk-datepicker'
                        ]),
                        '<hr>',
                        field_render('city_delivery', [
                            'label' => trans('forms.label_city_delivery'),
                            'value' => $entity->exists ? $entity->_profile->city_delivery : NULL,
                        ]),
                        field_render('address_delivery', [
                            'label' => trans('forms.label_address_delivery'),
                            'value' => $entity->exists ? $entity->_profile->address_delivery : NULL,
                        ]),
                        '<hr>',
                        field_render('comment', [
                            'type'       => 'textarea',
                            'label'      => trans('forms.label_comment'),
                            'value'      => $entity->exists ? $entity->_profile->comment : NULL,
                            'attributes' => [
                                'rows' => 5,
                            ]
                        ]),
                    ]
                ]
            ];

            return $form;
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => trans('pages.users_page'),
                'seo._title'  => trans('pages.users_page')
            ]);
            $items = User::paginate();

            return view('oleus.users.index', compact('items'));
        }

        public function create(User $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.users_page_create'),
                'seo._title'  => trans('pages.users_page_create')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function store(Request $request)
        {
            if($avatar_fid = $request->input('avatar_fid')) {
                $_avatar_fid = array_shift($avatar_fid);
                Session::flash('avatar_fid', json_encode([f_get($_avatar_fid['id'])]));
            }
            $this->validate($request, [
                'name'     => 'required|alpha_dash|max:255',
                'email'    => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:6|confirmed',
                'role'     => 'required',
            ]);
            $_save = $request->only([
                'name',
                'email',
                'password',
                'active',
            ]);
            $_save['language'] = config('app.locale');
            $_save['password'] = bcrypt($_save['password']);
            $item = User::updateOrCreate([
                'id' => NULL
            ], $_save);
            $item->syncRoles($request->input('role'));
            $_profile_save['uid'] = $item->id;
            $_profile_save['birthday'] = ($_birthday = $request->input('birthday')) ? Carbon::parse($_birthday) : NULL;
            if(isset($_avatar_fid)) $_profile_save['avatar_fid'] = (int)$_avatar_fid['id'];
            Session::forget([
                'avatar_fid',
            ]);

            return redirect()
                ->route('oleus.users.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.user_created'),
                    'status'  => 'success'
                ]);
        }

        public function edit(User $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.users_page_edit'),
                'seo._title'  => trans('pages.users_page_edit')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, User $item)
        {
            if($avatar_fid = $request->input('avatar_fid')) {
                $_avatar_fid = array_shift($avatar_fid);
                Session::flash('avatar_fid', json_encode([f_get($_avatar_fid['id'])]));
            }
            $this->validate($request, [
                'name'     => 'required|alpha_dash|max:255',
                'email'    => 'required|email|max:255',
                'password' => 'required_with:password_confirmed|confirmed',
                'role'     => 'required',
            ]);
            $_save = $request->only([
                'name',
                'email',
                'active',
                'blocked',
            ]);
            if($_password = $request->input('password')) $_save['password'] = bcrypt($_password);
            $item->update($_save);
            Session::forget([
                'avatar_fid',
            ]);
            if($request->input('save_close')) {
                return redirect()
                    ->route('oleus.users')
                    ->with('notice', [
                        'message' => trans('notice.user_updated'),
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->route('oleus.users.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.user_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, User $item)
        {
            if($item->blocked) {
                return redirect()
                    ->route('oleus.users.edit', [
                        'id' => $item->id
                    ])
                    ->with('notice', [
                        'message' => trans('notice.action_is_blocked'),
                        'status'  => 'warning'
                    ]);
            }
            $item->delete();

            return redirect()
                ->route('oleus.users')
                ->with('notice', [
                    'message' => trans('notice.user_deleted'),
                    'status'  => 'success'
                ]);
        }
    }

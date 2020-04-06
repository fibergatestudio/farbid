<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Models\Profile;
    use App\Notifications\MailActivateUser;
    use App\User;
    use Illuminate\Auth\Events\Registered;
    use Illuminate\Foundation\Auth\RegistersUsers;
    use Illuminate\Http\Request;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Notification;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\Validator;

    class RegisterController extends Controller
    {
        use RegistersUsers;
        use Notifiable;

        protected $redirectTo = '/';

        public function __construct()
        {
            parent::__construct();
            $this->middleware('guest');
        }

        protected function validator($data)
        {
            return Validator::make($data, [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);
        }

        public function showRegistrationForm()
        {
            if($item = page_render('register')) {
                wrap()->set('page._class', 'uk-height-1-1', TRUE);

                return view('auth.register', compact('item'));
            }
            abort(404);
        }

        protected function create($data)
        {
            $_user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => bcrypt($data['password']),
                'api_token' => str_random(60)
            ]);
            Profile::create([
                'uid' => $_user->id
            ]);
            $_user->syncRoles(['user']);

            return $_user;
        }

        public function register(Request $request)
        {
            $_form = $request->get('forms', 'form-account-register');
            $_box = $request->get('box', 'account-modal-box');
            $_ajax_request = $request->get('ajax', 0);
            if($_ajax_request) {
                $_rules = [
                    'email'    => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:6',
                ];
                foreach($_rules as $_field => $_rule) {
                    $commands[] = [
                        'command' => 'removeClass',
                        'target'  => "#{$_form}-{$_field}",
                        'data'    => 'uk-form-danger'
                    ];
                    if($_field == 'password') {
                        $commands[] = [
                            'command' => 'removeClass',
                            'target'  => "#{$_form}-{$_field}_confirmation",
                            'data'    => 'uk-form-danger'
                        ];
                    }
                }
                $validator = Validator::make($request->all(), $_rules);
                if($validator->fails()) {
                    foreach($validator->errors()->messages() as $_field => $_message) {
                        $commands[] = [
                            'command' => 'addClass',
                            'target'  => "#{$_form}-{$_field}",
                            'data'    => 'uk-form-danger'
                        ];
                        if($_field == 'password') {
                            $commands[] = [
                                'command' => 'addClass',
                                'target'  => "#{$_form}-{$_field}-confirmation",
                                'data'    => 'uk-form-danger'
                            ];
                        }
                    }
                    $commands[] = [
                        'command'  => 'notice',
                        'text'     => 'Не все поля в форме заполнены корректно.<br>Проверте правильность введенных данных',
                        'status'   => 'danger',
                        'position' => 'top-center'
                    ];
                } else {
                    $_name = explode('@', $request->get('email'));
                    $request->request->add([
                        'name' => $_name[0]
                    ]);
                    event(new Registered($user = $this->create($request->all())));
                    $this->registered($request, $user);
                    $theme = "front.forms.account_login";
                    if($_box != 'account-modal-box') $theme = "front.forms.account_mobile_login";
                    $commands[] = [
                        'command' => 'analytics_fbq',
                        'data'    => [
                            'event' => 'USER_HAS_REGISTERED'
                        ]
                    ];
                    $commands[] = [
                        'target'  => "#{$_box}",
                        'command' => 'html',
                        'data'    => view($theme)
                            ->render()
                    ];
                    $commands[] = [
                        'command' => 'modal',
                        'data'    => clear_html(view('front.modals.alert', [
                                'title' => variable('alert_modal_after_register_user_title'),
                                'alert' => variable('alert_modal_after_register_user_content'),
                            ]
                        )->render()),
                        'options' => [
                            'id'    => 'modal-alert',
                            'class' => 'uk-margin-auto-vertical uk-border-rounded alert-success'
                        ]
                    ];
                }

                return response($commands, 200);
            } else {
                $this->validator($request->all())->validate();
                event(new Registered($user = $this->create($request->all())));

                Session::flash('modal', [
                    'message' => clear_html(view('front.modals.alert', [
                            'title' => variable('alert_modal_after_register_user_title'),
                            'alert' => variable('alert_modal_after_register_user_content'),
                        ]
                    )->render()),
                    'status'  => 'success'
                ]);

                return $this->registered($request, $user)
                    ? : redirect($this->redirectPath());
            }
        }

        protected function registered(Request $request, $user)
        {
            Notification::route('mail', $user->email)
                ->notify(new MailActivateUser($user));
            Auth::logout();
        }
    }

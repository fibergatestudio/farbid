<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Models\ShopProductSearchHistory;
    use App\Notifications\MailActivateUser;
    use App\User;
    use Illuminate\Foundation\Auth\AuthenticatesUsers;
    use Illuminate\Http\Request;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Notification;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Validation\ValidationException;
    use Validator;

    class LoginController extends Controller
    {
        use AuthenticatesUsers;
        use Notifiable;

        protected $redirectTo = 'account';

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'guest'
            ])
                ->except('logout');
        }

        public function redirectTo()
        {
            return (request()->user() && request()->user()->can('access_dashboard')) ? 'oleus' : 'account';
        }

        public function username()
        {
            return 'login_or_email';
        }

        public function showLoginForm()
        {
            //            Session::flash('modal', [
            //                'message' => clear_html(view('front.modals.alert', ['alert' => 'asdasdas', 'title' => 'asdas'])->render()),
            //            ]);
            if($item = page_render('login')) {
                wrap()->set('page._class', 'uk-height-1-1', TRUE);

                return view('auth.login', compact('item'));
            }
            abort(404);
        }

        public function login(Request $request)
        {
            $_ajax_request = $request->get('ajax', 0);
            $_form = $request->get('forms', 'form-account-login');
            $_box = $request->get('box', 'account-modal-box');
            $request->request->add([
                'email' => $this->userEmail($request->input($this->username()))
            ]);
            if($_ajax_request) {
                $_rules = [
                    $this->username() => 'required|string',
                    'password'        => 'required|string',
                ];
                foreach($_rules as $_field => $_rule) {
                    $commands[] = [
                        'command' => 'removeClass',
                        'target'  => "#{$_form}-{$_field}",
                        'data'    => 'uk-form-danger'
                    ];
                }
                $validator = Validator::make($request->all(), $_rules);
                if($validator->fails()) {
                    foreach($validator->errors()->messages() as $_field => $_message) {
                        $commands[] = [
                            'command' => 'addClass',
                            'target'  => "#{$_form}-{$_field}",
                            'data'    => 'uk-form-danger'
                        ];
                    }
                    $commands[] = [
                        'command'  => 'notice',
                        'text'     => 'Не все поля в форме заполнены корректно.<br>Проверте правильность введенных данных',
                        'status'   => 'danger',
                        'position' => 'top-center'
                    ];

                    return response($commands, 200);
                }
            } else {
                $this->validateLogin($request);
            }
            if($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);
                if($_ajax_request) {
                    $commands[] = [
                        'command' => 'clearForm',
                        'form'    => $_form,
                    ];
                    $commands[] = [
                        'command'  => 'notice',
                        'text'     => 'Слишком много попыток входа в систему.<br/>Повторите попытку позже',
                        'status'   => 'danger',
                        'position' => 'top-center'
                    ];

                    return response($commands, 200);

                } else {
                    return $this->sendLockoutResponse($request);
                }
            }
            if($this->attemptLogin($request)) {
                $_notice = NULL;
                if($request->user()->blocked == 1) {
                    $_notice = trans('notice.user_account_blocked');
                } elseif($request->user()->active == 0) {
                    $_notice = trans('notice.user_account_not_active');
                }
                if($_notice) {
                    Auth::logout();
                    if($_ajax_request) {
                        $commands[] = [
                            'command' => 'clearForm',
                            'form'    => $_form,
                        ];
                        $commands[] = [
                            'command'  => 'notice',
                            'text'     => $_notice,
                            'status'   => 'danger',
                            'position' => 'top-center'
                        ];

                        return response($commands, 200);
                    } else {
                        throw ValidationException::withMessages([
                            $this->username() => [$_notice],
                        ]);
                    }
                }
                if($_ajax_request) {
                    $commands[] = [
                        'command' => 'reload',
                    ];

                    return response($commands, 200);
                } else {

                    return $this->sendLoginResponse($request);
                }
            }
            $this->incrementLoginAttempts($request);
            if($_ajax_request) {
                $_notice = trans('notice.user_account_not_exists');
                $commands[] = [
                    'command' => 'addClass',
                    'target'  => "#{$_form}-login-or-email",
                    'data'    => 'uk-form-danger'
                ];
                $commands[] = [
                    'command'  => 'notice',
                    'text'     => $_notice,
                    'status'   => 'danger',
                    'position' => 'top-center'
                ];

                return response($commands, 200);
            } else {
                return $this->sendFailedLoginResponse($request);
            }
        }

        protected function credentials(Request $request)
        {
            return $request->only('email', 'password');
        }

        protected function sendFailedLoginResponse(Request $request)
        {
            throw ValidationException::withMessages([
                $this->username() => [trans('notice.user_account_not_exists')],
            ]);
        }

        protected function userEmail($variable = NULL)
        {
            if($variable) return (str_is('*@*', $variable)) ? User::whereEmail($variable)->value('email') : User::whereName($variable)->value('email');

            return NULL;
        }

        protected function authenticated(Request $request, $user)
        {
            $user->update(['api_token' => str_random(60)]);
            ShopProductSearchHistory::setHistory();
        }

        public function logout(Request $request)
        {
            $_user = $request->user();
            $_user->update(['api_token' => NULL]);
            Auth::logout();

            return redirect('/');
        }
    }

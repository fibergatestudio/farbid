<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
    use Illuminate\Http\Request;
    use Validator;
    use Illuminate\Support\Facades\Password;

    class ForgotPasswordController extends Controller
    {
        use SendsPasswordResetEmails;

        public function __construct()
        {
            parent::__construct();
            $this->middleware('guest');
        }

        public function showLinkRequestForm()
        {
            if($item = page_render('password_reset')) {
                wrap()->set('page._class', 'uk-height-1-1', TRUE);

                return view('auth.passwords.email', compact('item'));
            }
            abort(404);
        }

        public function sendResetLinkEmail(Request $request)
        {
            $_ajax_request = $request->get('ajax', 0);
            $_form = $request->get('forms', 'form-account-reset-password');
            $_box = $request->get('box', 'account-modal-box');
            if($_ajax_request) {
                $_rules = [
                    'email' => 'required|email',
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
                } else {
                    $response = $this->broker()->sendResetLink(
                        $request->only('email')
                    );
                    if($response == Password::RESET_LINK_SENT) {
                        $theme = "front.forms.account_login";
                        if($_box != 'account-modal-box') $theme = "front.forms.account_mobile_login";
                        $commands[] = [
                            'target'  => "#{$_box}",
                            'command' => 'html',
                            'data'    => view($theme)
                                ->render()
                        ];
                        $commands[] = [
                            'command' => 'modal',
                            'data'    => clear_html(view('front.modals.alert', [
                                    'title' => variable('alert_modal_reset_password_title'),
                                    'alert' => variable('alert_modal_reset_password_content'),
                                ]
                            )->render()),
                            'options' => [
                                'id'    => 'modal-alert',
                                'class' => 'uk-margin-auto-vertical uk-border-rounded alert-success'
                            ]
                        ];
                    } else {
                        $_notice = trans('notice.user_account_not_exists');
                        $commands[] = [
                            'command' => 'addClass',
                            'target'  => "#{$_form}-email",
                            'data'    => 'uk-form-danger'
                        ];
                        $commands[] = [
                            'command'  => 'notice',
                            'text'     => $_notice,
                            'status'   => 'danger',
                            'position' => 'top-center'
                        ];
                    }
                }

                return response($commands, 200);
            } else {
                $this->validateEmail($request);

                $response = $this->broker()->sendResetLink(
                    $request->only('email')
                );

                return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($response)
                    : $this->sendResetLinkFailedResponse($request, $response);
            }
        }

        protected function sendResetLinkResponse($response)
        {
            return redirect()
                ->to('/')
                ->with('modal', [
                    'message' => clear_html(view('front.modals.alert', [
                            'title' => variable('alert_modal_reset_password_title'),
                            'alert' => variable('alert_modal_reset_password_content'),
                        ]
                    )->render()),
                    'status'  => 'success'
                ]);
        }

        protected function sendResetLinkFailedResponse(Request $request, $response)
        {
            return back()->withErrors(
                ['email' => trans($response)]
            );
        }

    }

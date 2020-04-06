<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use Illuminate\Auth\Events\PasswordReset;
    use Illuminate\Foundation\Auth\ResetsPasswords;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Str;

    class ResetPasswordController extends Controller
    {
        use ResetsPasswords;

        protected $redirectTo = 'login';

        public function __construct()
        {
            parent::__construct();
            $this->middleware('guest');
        }

        public function showResetForm(Request $request, $token = NULL)
        {
            if($item = page_render('password_reset')) {
                wrap()->set('page._class', 'uk-height-1-1', TRUE);

                return view('auth.passwords.reset')->with([
                    'item'  => $item,
                    'token' => $token,
                    'email' => $request->email
                ]);
            };
            abort(404);
        }

        protected function resetPassword($user, $password)
        {
            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60));
            $user->save();
            event(new PasswordReset($user));
        }

        protected function sendResetResponse($response)
        {
            return redirect($this->redirectPath())
                ->with('modal', [
                    'message' => clear_html(view('front.modals.alert', [
                            'title' => variable('alert_modal_after_reset_password_title'),
                            'alert' => variable('alert_modal_after_reset_password_content'),
                        ]
                    )->render()),
                    'status'  => 'success'
                ]);
        }
    }

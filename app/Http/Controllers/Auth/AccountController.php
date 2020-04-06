<?php

    namespace App\Http\Controllers\Auth;

    use App\Http\Controllers\Controller;
    use App\Library\Frontend;
    use App\User;
    use Illuminate\Foundation\Auth\AuthenticatesUsers;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Validation\ValidationException;
    use Validator;

    class AccountController extends Controller
    {
        use Frontend;

        public function __construct()
        {
            parent::__construct();
        }

        public function index()
        {
            $this->middleware([
                'web',
                'auth'
            ]);
            $item = Auth::user();
            if(is_null($item)) {
                return redirect()
                    ->to('/')
                    ->with('modal', [
                        'message' => clear_html(view('front.modals.alert', [
                            'title' => 'Внимание',
                            'alert' => 'Авторизируйтесь для входа в кабинет.',
                        ])->render()),
                        'status'  => 'warning'
                    ]);
            }
            $this->set_wrap([
                'seo._title'  => $item->_profile->full_name,
                'seo._robots' => 'noindex, nofollow',
                'page._title' => $item->_profile->full_name,
                'breadcrumb'  => breadcrumb_render(['entity' => $item])
            ]);

            return view('auth.account.index', compact('item'));
        }

        public function activate(Request $request, $token)
        {
            $_user = User::where('api_token', $token)
                ->where('active', 0)
                ->first();
            if($_user) {
                $_user->update([
                    'active'    => 1,
                    'api_token' => NULL,
                ]);
                Auth::login($_user);

                return redirect()
                    ->to('account')
                    ->with('modal', [
                        'message' => clear_html(view('front.modals.alert', [
                                'title' => variable('alert_modal_account_activated_title'),
                                'alert' => variable('alert_modal_account_activated_content'),
                            ]
                        )->render()),
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->to('/')
                ->with('modal', [
                    'message' => clear_html(view('front.modals.alert', [
                            'title' => variable('alert_modal_account_is_not_exists_title'),
                            'alert' => variable('alert_modal_account_is_not_exists_content'),
                        ]
                    )->render()),
                    'status'  => 'warning'
                ]);
        }
    }
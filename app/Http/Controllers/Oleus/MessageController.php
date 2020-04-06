<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\Callback;
    use App\Models\City;
    use App\Models\Node;
    use App\Models\Page;
    use App\Models\Profile;
    use App\Models\Service;
    use App\Models\ServiceOrder;
    use App\Models\ServicePrice;
    use App\Models\Message;
    use App\Models\ShopProductDiscountTimer;
    use App\User;
    use Carbon;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;
    use View;

    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;
    use Validator;

    class MessageController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();

        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'message';
            $form->relation = FALSE;
            $form->permission = [
                'read'   => FALSE,
                'create' => FALSE,
                'update' => FALSE,
                'delete' => FALSE,
            ];

            $form->tabs = [
                [
                    'title'   => trans('others.tab_info'),
                    'content' => [
                        field_render('name', [
                            'label'      => trans('forms.label_modal_user_name'),
                            'value'      => $entity->name,
                            'attributes' => [
                                'disabled' => TRUE
                            ]
                        ]),
                        field_render('email', [
                            'label'      => trans('forms.label_modal_user_email'),
                            'value'      => $entity->email ?? trans('others.not_indicated'),
                            'attributes' => [
                                'disabled' => TRUE
                            ]
                        ]),
                        field_render('theme', [
                            'label'      => trans('forms.label_subject'),
                            'value'      => $entity->theme,
                            'attributes' => [
                                'disabled' => TRUE
                            ]
                        ]),
                        field_render('comment', [
                            'label'      => trans('forms.label_comment'),
                            'type'       => 'textarea',
                            'value'      => $entity->comment,
                            'attributes' => [
                                'rows' => 3,
                            ]
                        ])
                    ]
                ],
            ];

            return $form;
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, []);
        }

        public function index()
        {
            $_date = new Carbon\Carbon('+10 days');

            $_pdt = ShopProductDiscountTimer::where('finish_date', '<=', $_date->toDateTimeString())
                ->get();

            $_pdt->map(function($_item){
                $_item->_deactivate();
            });
            dd($_pdt);


            $this->set_wrap([
                'page._title' => trans('pages.message'),
                'seo._title'  => trans('pages.message')
            ]);
            $items = Message::orderBy('status')
                ->orderByDesc('created_at')
                ->paginate();

            return view('oleus.message.index_form_buy_one_click', compact('items'));
        }

        public function edit(Message $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.message_show'),
                'seo._title'  => trans('pages.message_show')
            ]);
            $form = $this->_form($item);
            $item->update([
                'status' => 1
            ]);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, Message $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.message_show'),
                'seo._title'  => trans('pages.message_show')
            ]);
            $item->update([
                'comment' => $request->get('comment')
            ]);

            if ($request->input('save_close')) {
                return redirect()
                    ->route('oleus.massage')
                    ->with('notice', [
                        'message' => trans('notice.message_updated'),
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->route('oleus.message.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.message_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, Callback $item)
        {
            $item->delete();

            return redirect()
                ->route('oleus.services')
                ->with('notice', [
                    'message' => trans('notice.shop_application_deleted'),
                    'status'  => 'success'
                ]);
        }
    }

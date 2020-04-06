<?php

    namespace App\Http\Controllers\Oleus\Shop;

    use App\Http\Controllers\Controller;
    use App\Library\Dashboard;
    use App\Models\City;
    use App\Models\ShopCategory;
    use App\Models\ShopOrder;
    use App\Models\ShopParam;
    use App\Models\ShopParamItem;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Session;
    use Validator;

    class OrderController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_shop_categories'
            ]);
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'shop_orders';
            $form->relation = FALSE;
            $form->permission = array_merge($form->permission, [
                'read'   => 'read_shop_order',
                'create' => FALSE,
                'update' => 'update_shop_order',
                'delete' => 'delete_shop_order',
            ]);
            foreach(config('os_shop.status_orders') as $_order_status_key => $_order_status_data) {
                $_status[$_order_status_key] = trans($_order_status_data);
            }
            $_tab_info[] = field_render('created_at', [
                'label'      => trans('shop.form_label_ordered_date'),
                'value'      => $entity->created_at->format('d/m/Y H:i'),
                'attributes' => [
                    'readonly' => TRUE
                ]
            ]);
            if($entity->user_id) {
                $_tab_info[] = field_render('name', [
                    'label'      => trans('forms.label_modal_user_name'),
                    'value'      => $entity->user_name ?? trans('others.not_indicated'),
                    'attributes' => [
                        'readonly' => TRUE
                    ]
                ]);
                $_tab_info[] = field_render('email', [
                    'label'      => trans('forms.label_modal_user_email'),
                    'value'      => $entity->user_email ?? trans('others.not_indicated'),
                    'attributes' => [
                        'readonly' => TRUE
                    ]
                ]);
            } else {
                $_tab_info[] = field_render('name', [
                    'label'      => 'Имя клиента',
                    'value'      => $entity->name ?? trans('others.not_indicated'),
                    'attributes' => [
                        'readonly' => TRUE
                    ]
                ]);
                $_tab_info[] = field_render('email', [
                    'label'      => trans('forms.label_modal_user_email'),
                    'value'      => $entity->email ?? trans('others.not_indicated'),
                    'attributes' => [
                        'readonly' => TRUE
                    ]
                ]);
            }
            $_tab_info[] = field_render('phone', [
                'label'      => trans('forms.label_modal_user_phone'),
                'value'      => $entity->phone,
                'attributes' => [
                    'readonly' => TRUE
                ]
            ]);
            $_tab_info[] = field_render('status', [
                'type'   => 'select',
                'label'  => trans('forms.label_status'),
                'value'  => $entity->status,
                'class'  => 'uk-select2',
                'values' => $_status,
            ]);
            $_tab_info[] = field_render('comment', [
                'label'      => trans('forms.label_comment'),
                'type'       => 'textarea',
                'value'      => $entity->comment,
                'attributes' => [
                    'rows' => 3,
                ]
            ]);
            $_tab_delivery_payment[] = field_render('delivery', [
                'label'      => 'Способ доставки',
                'value'      => trans('shop.delivery_type_' . $entity->delivery),
                'attributes' => [
                    'readonly' => TRUE
                ]
            ]);
            if($entity->delivery == 2) {
                $_tab_delivery_payment[] = field_render('address', [
                    'type'       => 'textarea',
                    'label'      => 'Адрес доставки',
                    'value'      => $entity->address,
                    'attributes' => [
                        'readonly' => TRUE,
                        'rows'     => 5
                    ]
                ]);
            }
            if($entity->delivery == 3) {
                $_tab_delivery_payment[] = field_render('address', [
                    'type'       => 'textarea',
                    'label'      => 'Отделение доставки',
                    'value'      => $entity->address,
                    'attributes' => [
                        'readonly' => TRUE,
                        'rows'     => 5
                    ]
                ]);
            }
            $_tab_delivery_payment[] = '<hr>';
            $_tab_delivery_payment[] = field_render('payment', [
                'label'      => 'Способ оплаты',
                'value'      => trans('shop.payment_type_' . $entity->payment),
                'attributes' => [
                    'readonly' => TRUE
                ]
            ]);
            $form->tabs = [
                [
                    'title'   => trans('others.tab_info'),
                    'content' => $_tab_info
                ],
                [
                    'title'   => 'Оптала и доставка',
                    'content' => $_tab_delivery_payment
                ],
                [
                    'title'   => trans('others.tab_order_list'),
                    'content' => [
                        view('oleus.shop.show_order', ['order' => $entity->info])
                    ]
                ]
            ];

            return $form;
        }

        protected function _validate(Request $request)
        {
            $this->validate($request, [
                'title' => 'required'
            ]);
        }

        public function index()
        {
            $this->set_wrap([
                'page._title' => trans('pages.shop_order_page'),
                'seo._title'  => trans('pages.shop_order_page')
            ]);
            $items = ShopOrder::orderBy('status')
                ->where('data', '<>', 'a:0:{}')
                ->orderBy('created_at', 'desk')
                ->get();

            return view('oleus.shop.index_orders', compact('items'));
        }

        public function edit(ShopOrder $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.shop_order_edit'),
                'seo._title'  => trans('pages.shop_order_edit')
            ]);
            $form = $this->_form($item);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, ShopOrder $item)
        {
            $_save = $request->only([
                'status',
                'comment',
            ]);
            $item->update($_save);
            if($request->input('save_close')) {
                return redirect()
                    ->route('oleus.shop_orders')
                    ->with('notice', [
                        'message' => trans('notice.shop_order_updated'),
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->route('oleus.shop_orders.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.shop_order_updated'),
                    'status'  => 'success'
                ]);
        }

        public function destroy(Request $request, ShopOrder $item)
        {
            $item->delete();

            return redirect()
                ->route('oleus.shop_orders')
                ->with('notice', [
                    'message' => trans('notice.shop_order_deleted'),
                    'status'  => 'success'
                ]);
        }
    }

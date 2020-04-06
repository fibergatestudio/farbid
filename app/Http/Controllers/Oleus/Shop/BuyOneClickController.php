<?php

    namespace App\Http\Controllers\Oleus\Shop;

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
    use App\Models\ShopBuyOneClick;
    use App\User;
    use Carbon;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Session;
    use View;

    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;
    use Validator;

    class BuyOneClickController extends Controller
    {
        use Dashboard;
        use Authorizable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard',
                'permission:read_shop_buy_one_click'
            ]);
        }

        protected function _form($entity)
        {
            $form = parent::__form();
            $form->route_tag = 'shop_products_form_buy_one_click';
            $form->relation = FALSE;
            $form->permission = array_merge($form->permission, [
                'read'   => 'read_shop_buy_one_click',
                'create' => FALSE,
                'update' => 'update_shop_buy_one_click',
                'delete' => 'delete_shop_buy_one_click',
            ]);
            $form->tabs = [
                [
                    'title'   => trans('others.tab_info'),
                    'content' => [
                        '<div class="uk-form-row">' .
                        '<div uk-grid class="uk-grid-small">' .
                        '<div class="uk-width-1-3">' .
                        $entity->_product->_preview_asset('thumb_shop_product', ['only_way' => FALSE]) .
                        '</div>' .
                        '<div class="uk-width-2-3">' .
                        '<dl class="uk-description-list uk-description-list-divider">' .
                        '<dt class="uk-text-muted">' . trans('forms.label_name_product') . '</dt>' .
                        '<dd class="uk-text-bold">' .
                        _l($entity->_product->title, 'oleus.shop_products.edit', [
                            'p' => ['id' => $entity->_product->id],
                            'a' => ['target' => '_blank']
                        ]) .
                        '<dt class="uk-text-muted">' . trans('forms.label_sky') . '</dt>' .
                        '<dd class="uk-text-bold">' . $entity->_product->sky . '</dd>' .
                        '<dt class="uk-text-muted">' . trans('forms.label_price') . '</dt>' .
                        '<dd class="uk-text-bold">' . $entity->_product->price . ' ' . $entity->_product->currency . '</dd>' .
                        '<dt class="uk-text-muted">' . trans('forms.label_count') . '</dt>' .
                        '<dd class="uk-text-bold">' . $entity->_product->count . '</dd>' .
                        '</dd>' .
                        '</dl>' .
                        '</div>' .
                        '</div>',
                        '<hr class="uk-divider-icon">',
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
                        field_render('phone', [
                            'label'      => trans('forms.label_modal_user_phone'),
                            'value'      => $entity->phone,
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
            $this->set_wrap([
                'page._title' => trans('pages.shop_form_buy_one_click'),
                'seo._title'  => trans('pages.shop_form_buy_one_click')
            ]);
            $items = ShopBuyOneClick::orderBy('status')
                ->orderByDesc('created_at')
                ->paginate();

            return view('oleus.shop.index_form_buy_one_click', compact('items'));
        }

        public function edit(ShopBuyOneClick $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.shop_form_buy_one_click_show'),
                'seo._title'  => trans('pages.shop_form_buy_one_click_show')
            ]);
            $form = $this->_form($item);
            $item->update([
                'status' => 1
            ]);

            return view($form->theme, compact('form', 'item'));
        }

        public function update(Request $request, ShopBuyOneClick $item)
        {
            $this->set_wrap([
                'page._title' => trans('pages.shop_form_buy_one_click_show'),
                'seo._title'  => trans('pages.shop_form_buy_one_click_show')
            ]);
            $item->update([
                'comment' => $request->get('comment')
            ]);

            if($request->input('save_close')) {
                return redirect()
                    ->route('oleus.shop_products_form_buy_one_click')
                    ->with('notice', [
                        'message' => trans('notice.shop_application_updated'),
                        'status'  => 'success'
                    ]);
            }

            return redirect()
                ->route('oleus.shop_products_form_buy_one_click.edit', ['id' => $item->id])
                ->with('notice', [
                    'message' => trans('notice.shop_application_updated'),
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

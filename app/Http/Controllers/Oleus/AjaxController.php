<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Library\ECommerce;
    use App\Library\liqPay;
    use App\Library\NovaPoshta;
    use App\Models\Application;
    use App\Models\File;
    use App\Models\Profile;
    use App\Models\Reviews;
    use App\Models\Search;
    use App\Models\ShopBasket;
    use App\Models\ShopBuyOneClick;
    use App\Models\ShopOrder;
    use App\Models\ShopProduct;
    use App\Models\ShopProductDesires;
    use App\Models\ShopProductDiscountTimer;
    use App\Models\ShopProductFavorites;
    use App\Models\ShopProductGroups;
    use App\Models\ShopProductSearchHistory;
    use App\Models\UrlAlias;
    use App\Notifications\MailActivateUser;
    use App\Notifications\MailShopBuyOneClick;
    use App\Notifications\MailShopOrder;
    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Notification;
    use Illuminate\Support\Facades\Storage;
    use Session;
    use Validator;

    class AjaxController extends Controller
    {
        use Notifiable;

        public $front_language;
        public $front_location;

        public function __construct(Request $request)
        {
            parent::__construct();
        }

        /**
         * Account
         */
        public function show_account_form(Request $request)
        {
            $_form = $request->input('form');
            $_box = $request->get('box', 'account-modal-box');
            $theme = "front.forms.account_{$_form}";
            if ($_box != 'account-modal-box') $theme = "front.forms.account_mobile_{$_form}";
            $commands[] = [
                'target'  => "#{$_box}",
                'command' => 'html',
                'data'    => view($theme)
                    ->render()
            ];

            return response($commands, 200);
        }

        public function submit_account_edit_form(Request $request)
        {
            if ($request->has('forms')) {
                $_forms = $request->input('forms');
                $_request = $request->all();
                $_validate_rules = [
                    'email'    => 'required|email|max:255',
                    'password' => 'required_with:password_confirmed|confirmed',
                ];
                $_validator = Validator::make($_request, $_validate_rules);
                foreach ($_validate_rules as $_field => $_rule) {
                    $commands[] = [
                        'command' => 'removeClass',
                        'target'  => "#{$_forms}-{$_field}",
                        'data'    => 'uk-form-danger'
                    ];
                    if ($_field == 'password') {
                        $commands[] = [
                            'command' => 'removeClass',
                            'target'  => "#{$_forms}-{$_field}_confirmation",
                            'data'    => 'uk-form-danger'
                        ];
                    }
                }
                if ($_validator->fails()) {
                    foreach ($_validator->errors()->messages() as $_field => $_message) {
                        $commands[] = [
                            'command' => 'addClass',
                            'target'  => "#{$_forms}-{$_field}",
                            'data'    => 'uk-form-danger'
                        ];
                        if ($_field == 'password') {
                            $commands[] = [
                                'command' => 'addClass',
                                'target'  => "#{$_forms}-{$_field}_confirmation",
                                'data'    => 'uk-form-danger'
                            ];
                        }
                    }
                    $commands[] = [
                        'command' => 'modal',
                        'data'    => view('front.partials.modal_alert', [
                            'body' => __('Не все поля в форме заполнены корректно.<br>Проверьте правильность введенных данных'),
                        ])->render(),
                        'options' => [
                            'id'    => 'modal-alert',
                            'class' => 'alert-danger uk-border-rounded'
                        ]
                    ];
                } else {
                    $_user_id = $request->get('user_id');
                    $_save_email = $request->get('email');
                    if (User::where('email', $_save_email)
                        ->where('id', '<>', $_user_id)
                        ->count()) {
                        $commands[] = [
                            'command' => 'addClass',
                            'target'  => "#{$_forms}-email",
                            'data'    => 'uk-form-danger'
                        ];
                        $commands[] = [
                            'command' => 'modal',
                            'data'    => view('front.partials.modal_alert', [
                                'body' => __('Аккаунт с таким Email уже зарегистрирован в системе'),
                            ])->render(),
                            'options' => [
                                'id'    => 'modal-alert',
                                'class' => 'alert-danger uk-border-rounded'
                            ]
                        ];
                    } else {
                        $_user = User::find($_user_id);
                        $_save_password = $request->get('password');
                        if ($_user->email != $_save_email) $_user->update(['email' => $_save_email]);
                        if ($_save_password) $_user->update(['password' => bcrypt($_save_password)]);
                        $_save_profile = $request->only([
                            'last_name',
                            'first_name',
                            'phone',
                            'city_delivery',
                            'address_delivery',
                            'avatar_fid'
                        ]);
                        $_user->_profile->update($_save_profile);
                        $commands[] = [
                            'command' => 'modal',
                            'data'    => view('front.partials.modal_alert', [
                                'body' => __('Информация профиля пользователя обновлена'),
                            ])->render(),
                            'options' => [
                                'id'    => 'modal-alert',
                                'class' => 'alert-success uk-border-rounded'
                            ]
                        ];
                    }
                }
            }

            return response($commands, 200);
        }

        public function upload_account_avatar(Request $request)
        {
            if ($request->hasFile('file')) {
                $_file = $request->file('file');
                $_file_mime_type = $_file->getClientMimeType();
                $_file_extension = $_file->getClientOriginalExtension();
                $_file_size = $_file->getClientSize();
                $_file_name = str_slug(basename($_file->getClientOriginalName(), ".{$_file_extension}")) . '-' . uniqid() . ".{$_file_extension}";
                Storage::disk('uploads')
                    ->put($_file_name, file_get_contents($_file->getRealPath()));
                $_avatar = File::updateOrCreate([
                    'id' => NULL
                ], [
                    'filename' => $_file_name,
                    'filemime' => $_file_mime_type,
                    'filesize' => $_file_size,
                ]);

                return response([
                    'path'    => image_render($_avatar, 'account_avatar', ['only_way' => TRUE]),
                    'file_id' => $_avatar->id
                ], 200);
            } else {
                $_message = view('front.partials.modal_alert', [
                    'body' => __('Не получилось загрузить файл.'),
                ])->render();

                return response($_message, 422);
            }
        }

        public function submit_account_login(Request $request)
        {
            $_forms = $request->get('form_id');
            $_validate_rules = [
                'email'    => 'required|string|email',
                'password' => 'required|string|min:6',
            ];
            $validator = Validator::make($request->all(), $_validate_rules);
            foreach ($_validate_rules as $_field => $_rule) {
                $commands[] = [
                    'command' => 'removeClass',
                    'target'  => "#{$_forms}-{$_field}",
                    'data'    => 'uk-form-danger'
                ];
            }
            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $_field => $_message) {
                    $commands[] = [
                        'command' => 'addClass',
                        'target'  => "#{$_forms}-{$_field}",
                        'data'    => 'uk-form-danger'
                    ];
                }
            } else {
                if (Auth::attempt([
                    'email'    => $request->get('email'),
                    'password' => $request->get('password')
                ])) {
                    $_notice = NULL;
                    $_user = Auth::user();
                    if ($_user->blocked == 1) {
                        $_notice = __('Аккаунт пользователя заблокирован администратором.');
                    } elseif ($_user->active == 0) {
                        $_notice = __('Аккаунт пользователя не активирован.<br>При регистрации в письме Вы получали инструкцию для активации аккаунта.');
                    }
                    if ($_notice) {
                        Auth::logout();
                        $commands[] = [
                            'command'  => 'notice',
                            'text'     => $_notice,
                            'status'   => 'danger',
                            'position' => 'top-center'
                        ];
                    } else {
                        $commands[] = [
                            'command' => 'reload'
                        ];
                    }
                } else {
                    $_notice = __('Аккаунт пользователя не найден.<br>Проверьте правильность ввода данных.');
                    $commands[] = [
                        'command' => 'addClass',
                        'target'  => "#{$_forms}-email",
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
        }

        /**
         * Shop
         */
        public function add_product_desires(Request $request)
        {
            $_wrap = wrap()->get();
            $_auth_user = $_wrap['user'];
            $language = $_wrap['locale'];
            if ($_auth_user) {
                if ($_product = $request->get('product')) {
                    if ($_my_desires = ShopProductDesires::where('product_id', $_product)
                        ->where('user_id', $_auth_user->id)
                        ->first()) {
                        $_my_desires->delete();
                        $commands[] = [
                            'command'  => 'notice',
                            'text'     => __('Товар удален из "Мои желания"'),
                            'status'   => 'danger',
                            'position' => 'top-center'
                        ];
                        if ($request->get('refresh', 0)) {
                            $_desires = $_auth_user->_my_desires;
                            $commands[] = [
                                'command' => 'replaceWith',
                                'target'  => '#desires-box-items',
                                'data'    => view('auth.account.partials.desires_items', compact('language', '_desires'))
                                    ->render()
                            ];
                        }
                    } else {
                        ShopProductDesires::updateOrCreate([
                            'id' => NULL
                        ], [
                            'product_id' => $_product,
                            'user_id'    => $_auth_user->id
                        ]);
                        $commands[] = [
                            'command'  => 'notice',
                            'text'     => __('Товар добавлен в "Мои желания"'),
                            'status'   => 'success',
                            'position' => 'top-center'
                        ];
                    }
                } else {

                }
            } else {
                $commands[] = [
                    'command' => 'modal',
                    'data'    => clear_html(view('front.modals.alert', [
                            'title' => variable('alert_modal_action_blocked_by_anonymous_title'),
                            'alert' => variable('alert_modal_action_blocked_by_anonymous_content'),
                        ]
                    )->render()),
                    'options' => [
                        'id'    => 'modal-alert',
                        'class' => 'uk-margin-auto-vertical uk-border-rounded alert-warning'
                    ]
                ];
            }

            return response($commands, 200);
        }

        public function submit_shop_but_one_click(Request $request)
        {
            if ($request->has('forms')) {
                $_forms = $request->input('forms');
                $_validate_rules = [
                    'name'  => 'required',
                    'phone' => 'required|regex:/^\+38 \(\d{3}\) \d{3} \d{4}$/',
                ];
                $_request = $request->all();
                if ($request->has('email') && is_null($_request['email'])) {
                    unset($_request['email']);
                }
                $validator = Validator::make($_request, $_validate_rules);
                foreach ($_validate_rules as $_field => $_rule) {
                    $commands[] = [
                        'command' => 'removeClass',
                        'target'  => "#{$_forms}-{$_field}",
                        'data'    => 'uk-form-danger'
                    ];
                }
                if ($validator->fails()) {
                    foreach ($validator->errors()->messages() as $_field => $_message) {
                        $commands[] = [
                            'command' => 'addClass',
                            'target'  => "#{$_forms}-{$_field}",
                            'data'    => 'uk-form-danger'
                        ];
                    }
                } else {
                    $_save = $request->only([
                        'name',
                        'email',
                        'phone',
                        'product_id',
                    ]);
                    $order = ShopBuyOneClick::updateOrCreate([
                        'id' => NULL
                    ], $_save);
                    $commands[] = [
                        'command' => 'clearForm',
                        'form'    => $_forms
                    ];
                    if ($request->input('modal')) {
                        $commands[] = [
                            'command' => 'modal_close',
                            'target'  => "#{$_forms}"
                        ];
                    }
                    Notification::route('mail', env('MAIL_USER_TO_ADDRESS'))
                        ->notify(new MailShopBuyOneClick($order));
                    $_EC = ECommerce::purchase_buy_one_click($order);
                    $commands[] = [
                        'command' => 'ecommerce',
                        'event'   => 'purchase',
                        'data'    => json_decode($_EC)
                    ];
                    $commands[] = [
                        'command' => 'analytics_gtag',
                        'data'    => [
                            'event'        => 'ORDER',
                            'category'     => 'SHOP',
                            'event_action' => 'SEND_BUY_ONE_CLICK',
                        ]
                    ];
                    $commands[] = [
                        'command' => 'modal',
                        'data'    => clear_html(view('front.modals.alert', [
                                'title' => variable('alert_modal_buy_one_click_form_title'),
                                'alert' => variable('alert_modal_buy_one_click_form_content'),
                            ]
                        )->render()),
                        'options' => [
                            'id'    => 'modal-alert',
                            'class' => 'uk-margin-auto-vertical uk-border-rounded alert-success'
                        ]
                    ];
                }
            } else {
                $_product = $request->input('product');
                $commands[] = [
                    'command' => 'modal',
                    'options' => [
                        'id'    => 'modal-one-click',
                        'class' => 'uk-margin-auto-vertical callback'
                    ],
                    'data'    => view('front.modals.buy_one_click_application', [
                        'product' => $_product,
                    ])
                        ->render()
                ];
            }

            return response($commands, 200);
        }

        public function submit_shop_basket_action(Request $request)
        {
            $commands = [];
            $entity = NULL;
            $entity_type = 'product';
            $_action = $request->get('action', 'add');
            $_product = $request->get('product');
            $_count = $request->get('count', 1);
            $_product_group = $request->get('product_group', 0);
            if ($_product_group) {
                $entity = ShopProductGroups::find($_product);
                $entity_type = 'product_group';
            } else {
                $entity = ShopProduct::find($_product);
            }
            if ($entity) {
                switch ($_action) {
                    case 'up':
                        $_count = (int)Session::get("basket.{$entity_type}.{$entity->id}.count") + 1;
                        if (($entity_type == 'product' && (!$entity->out_of_stock && ($entity->not_limited || $entity->count >= $_count))) || $entity_type == 'product_group') {
                            Session::put("basket.{$entity_type}.{$entity->id}.count", $_count);
                            $commands[] = [
                                'command' => 'replaceWith',
                                'target'  => '#basket-inside-items',
                                'data'    => view('front.shop.items_basket', [
                                    'items' => ShopBasket::get_basket()
                                ])
                                    ->render()
                            ];
                        } else {
                            $commands[] = [
                                'command' => 'modal',
                                'options' => [
                                    'id'    => 'modal-alert',
                                    'class' => 'uk-margin-auto-vertical uk-border-rounded alert-danger'
                                ],
                                'data'    => clear_html(view('front.modals.alert', [
                                    'title' => variable('alert_modal_basket_no_up_product_title'),
                                    'alert' => variable('alert_modal_basket_no_up_product_content')
                                ])
                                    ->render())
                            ];
                        }
                        break;
                    case 'down':
                        $_count = (int)Session::get("basket.{$entity_type}.{$entity->id}.count") - 1;
                        if ($_count >= 1) {
                            Session::put("basket.{$entity_type}.{$entity->id}.count", $_count);
                            $commands[] = [
                                'command' => 'replaceWith',
                                'target'  => '#basket-inside-items',
                                'data'    => view('front.shop.items_basket', [
                                    'items' => ShopBasket::get_basket()
                                ])
                                    ->render()
                            ];
                        } else {
                            $commands[] = [
                                'command' => 'modal',
                                'options' => [
                                    'id'    => 'modal-alert',
                                    'class' => 'uk-margin-auto-vertical uk-border-rounded alert-danger'
                                ],
                                'data'    => view('front.modals.alert', [
                                    'title' => variable('alert_modal_basket_no_down_product_title'),
                                    'alert' => variable('alert_modal_basket_no_down_product_content')
                                ])
                                    ->render()
                            ];
                        }
                        break;
                    case 'remove':
                        if (Session::has("basket.{$entity_type}.{$entity->id}")) {
                            Session::forget("basket.{$entity_type}.{$entity->id}");
                            if (!count(Session::get("basket.{$entity_type}"))) Session::forget("basket.{$entity_type}");
                            if (!count(Session::get('basket'))) {
                                Session::forget('basket');
                                $commands[] = [
                                    'command' => 'removeClass',
                                    'target'  => '#link-basket',
                                    'data'    => 'no-empty'
                                ];
                                $commands[] = [
                                    'command' => 'replaceWith',
                                    'target'  => '#shop-basket-inside-card-items',
                                    'data'    => view('front.shop.inside_basket', [
                                        'items' => ShopBasket::get_basket()
                                    ])
                                        ->render()
                                ];
                            } else {
                                $commands[] = [
                                    'command' => 'replaceWith',
                                    'target'  => '#basket-inside-items',
                                    'data'    => view('front.shop.items_basket', [
                                        'items' => ShopBasket::get_basket()
                                    ])
                                        ->render()
                                ];
                            }
                            $_EC = ECommerce::view_item($entity);
                            $commands[] = [
                                'command' => 'ecommerce',
                                'event'   => 'remove_from_cart',
                                'data'    => json_decode($_EC)
                            ];
                        } else {
                            $commands[] = [
                                'command' => 'modal',
                                'options' => [
                                    'id'    => 'modal-alert',
                                    'class' => 'uk-margin-auto-vertical uk-modal-body'
                                ],
                                'data'    => view('front.modals.alert', [
                                    'title'   => variable('alert_modal_basket_no_remove_product_title'),
                                    'content' => variable('alert_modal_basket_no_remove_product_content')
                                ])
                                    ->render()
                            ];
                        }
                        break;
                    default:
                        if (Session::has("basket.{$entity_type}.{$entity->id}")) {
                            $_count = (int)Session::get("basket.{$entity_type}.{$entity->id}.count") + $_count;
                            if (($entity_type == 'product' && (!$entity->out_of_stock && ($entity->not_limited || $entity->count >= $_count))) || $entity_type == 'product_group') {
                                Session::put("basket.{$entity_type}.{$entity->id}.count", $_count);
                            } else {
                                $commands[] = [
                                    'command' => 'modal',
                                    'options' => [
                                        'id'    => 'modal-alert',
                                        'class' => 'uk-margin-auto-vertical uk-border-rounded alert-danger'
                                    ],
                                    'data'    => view('front.modals.alert', [
                                        'title' => variable('alert_modal_basket_no_up_product_title'),
                                        'alert' => variable('alert_modal_basket_no_up_product_content')
                                    ])
                                        ->render()
                                ];
                            }
                        } else {
                            if (($entity_type == 'product' && ((!$entity->out_of_stock && ($entity->not_limited || $entity->count >= $_count)))) || $entity_type == 'product_group') {
                                if ($entity_type == 'product_group') {
                                    Session::put("basket.{$entity_type}.{$entity->id}", [
                                        'product_group' => $entity->id,
                                        'count'         => $_count
                                    ]);
                                } else {
                                    Session::put("basket.{$entity_type}.{$entity->id}", [
                                        'product' => $entity->id,
                                        'count'   => $_count
                                    ]);
                                }
                                $_link_to_basket = '<a href="' . _u(wrap()->get('pages.shop_basket')->_alias->alias) . '" class="link-go-to-basket">' . __('Перейти в корзину') . '</a>';
                                $commands[] = [
                                    'command' => 'modal',
                                    'options' => [
                                        'id'    => 'modal-alert',
                                        'class' => 'uk-margin-auto-vertical uk-border-rounded alert-success'
                                    ],
                                    'data'    => view('front.modals.alert', [
                                        'title' => variable('alert_modal_basket_add_product_title'),
                                        'alert' => variable(($entity_type == 'product' ? 'alert_modal_basket_add_product_content' : 'alert_modal_basket_add_product_group_content'), [
                                            'product'        => $entity->title ?? NULL,
                                            'link_to_basket' => $_link_to_basket
                                        ])
                                    ])
                                        ->render()
                                ];
                                $commands[] = [
                                    'command' => 'addClass',
                                    'target'  => '#link-basket',
                                    'data'    => 'no-empty'
                                ];
                                $_EC = ECommerce::view_item($entity);
                                $commands[] = [
                                    'command' => 'ecommerce',
                                    'event'   => 'add_to_cart',
                                    'data'    => json_decode($_EC)
                                ];
                            } else {
                                $commands[] = [
                                    'command' => 'modal',
                                    'options' => [
                                        'id'    => 'modal-alert',
                                        'class' => 'uk-margin-auto-vertical uk-border-rounded alert-danger'
                                    ],
                                    'data'    => view('front.modals.alert', [
                                        'title' => variable('alert_modal_basket_no_add_product_title'),
                                        'alert' => variable('alert_modal_basket_no_add_product_content')
                                    ])
                                        ->render()
                                ];
                            }
                        }
                        break;
                }
            }
            $_basket = ShopBasket::get_basket();
            $_count = $_basket->get('count', 0);
            $commands[] = [
                'command' => 'replaceWith',
                'target'  => '#card-basket-inside',
                'data'    => view('front.shop.basket.small_cart_inside', [
                    '_count'  => $_count,
                    '_basket' => $_basket
                ])
                    ->render()
            ];
            $commands[] = [
                'command' => 'html',
                'target'  => '#card-basket > a > span',
                'data'    => $_count ? '<small class="cart-notification">' . $_count . '</small>' : ''
            ];
            $commands[] = [
                'command' => 'html',
                'target'  => '#card-basket',
                'data'    => view('front.shop.basket.small_cart')
                    ->render()
            ];

            return response($commands, 200);
        }

        public function submit_shop_order(Request $request)
        {
            $_wrap = wrap()->get();
            $_forms = $request->input('forms');
            $_new_user = $request->input('new_user', 0);
            $_user = $_wrap['user'];
            $_validate_rules = [
                'name'                     => 'required|string',
                'phone'                    => 'required|string|regex:/^\+38 \(\d{3}\) \d{3} \d{4}$/',
                'delivery_address_city'    => 'required_if:delivery,2',
                'delivery_address_address' => 'required_if:delivery,2',
                'delivery_area'            => 'required_if:delivery,3',
                'delivery_city'            => 'required_if:delivery,3|required_with:delivery_area',
                'delivery_warehouses'      => 'required_if:delivery,3|required_with:delivery_area,delivery_city',
            ];
            if ($_new_user) {
                $_validate_rules['email'] = 'required|string|email';
                $_validate_rules['password'] = 'required|string|min:6';
            }
            if ($request->has('agreement')) {
                $_validate_rules['agreement'] = 'required';
            }
            $_request = $request->all();
            if ($request->has('email') && empty($_request['email'])) {
                unset($_request['email']);
            }
            $validator = Validator::make($_request, $_validate_rules);
            foreach ($_validate_rules as $_field => $_rule) {
                $commands[] = [
                    'command' => 'removeClass',
                    'target'  => "#{$_forms}-{$_field}",
                    'data'    => 'uk-form-danger'
                ];
            }
            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $_field => $_message) {
                    $commands[] = [
                        'command' => 'addClass',
                        'target'  => "#{$_forms}-{$_field}",
                        'data'    => 'uk-form-danger'
                    ];
                }
            } else {
                $_method_delivery = $request->input('delivery', 1);
                $_address_delivery = NULL;
                if ($_method_delivery == 2) {
                    $_address_delivery = $request->input('delivery_address_city') . ' ' . $request->input('delivery_address_address');
                } elseif ($_method_delivery == 3) {
                    $_request_NP = [
                        "area"       => $request->input('delivery_area'),
                        "city"       => $request->input('delivery_city'),
                        "warehouses" => $request->input('delivery_warehouses'),
                    ];
                    $_NP = new NovaPoshta();
                    $_address = $_NP->formation_address($_request_NP);
                    if ($_address) {
                        $_address_delivery = $_address['output'];
                        if ($_method_delivery == 'np_address') {
                            $__address = [];
                            if ($request->input('delivery_street')) $__address[] = $request->input('delivery_street');
                            if ($request->input('delivery_house')) $__address[] = 'будинок ' . $request->input('delivery_house');
                            if ($request->input('delivery_float')) $__address[] = 'квартира ' . $request->input('delivery_float');
                            $_address_delivery .= ', ' . implode(', ', $__address);
                        }
                    }
                }
                $_address_delivery;
                $_basket = ShopBasket::get_basket()->toArray();
                $_save = $request->only([
                    'delivery',
                    'payment',
                    'user_id',
                    'phone',
                    'name',
                    'email',
                    'password',
                    'comment',
                ]);
                if ($_new_user) {
                    if ($_email_check = User::where('email', $_save['email'])
                        ->count()) {
                        $commands[] = [
                            'command'  => 'notice',
                            'text'     => __('Пользователь с таким email уже существует в системе.<br>Воспользуйтесь формой восстановления пароля.'),
                            'status'   => 'danger',
                            'position' => 'top-center'
                        ];
                        $commands[] = [
                            'command' => 'addClass',
                            'target'  => "#{$_forms}-email",
                            'data'    => 'uk-form-danger'
                        ];

                        return response($commands, 200);
                    } else {
                        $_user = User::create([
                            'name'      => $_save['name'],
                            'email'     => $_save['email'],
                            'password'  => bcrypt($_save['password']),
                            'api_token' => str_random(60)
                        ]);
                        Profile::create([
                            'uid' => $_user->id
                        ]);
                        $_user->syncRoles(['user']);
                        $_save['user_id'] = $_user->id;
                        Notification::route('mail', $_user->email)
                            ->notify(new MailActivateUser($_user));
                        Auth::logout();
                        unset($_save['password']);
                    }
                } else {
                    unset($_save['password']);
                }
                $_save['order'] = date('d-m-Y');
                $_save['data'] = serialize($_basket);
                $_save['address'] = $_address_delivery;
                $_order = ShopOrder::updateOrCreate([
                    'id' => NULL
                ], $_save);
                $_order->update([
                    'order' => "{$_order->order}-{$_order->id}"
                ]);
                if ($_save['payment'] != 1) {
                    $_liqPay_private_key = config('os_shop.liqPay.private_key');
                    $_liqPay_public_key = config('os_shop.liqPay.public_key');
                    $liqPay = new liqPay($_liqPay_public_key, $_liqPay_private_key);
                    //                    $_pay_amount = $_basket['total']['format']['price'];
                    $_pay_amount = 1;
                    $_pay_currency = $_basket['total']['currency']['iso_code'];
                    $liqPayData = [
                        'amount'      => $_pay_amount,
                        'currency'    => 'UAH',
                        'description' => "Оформление заказа на сайте FARBID",
                        'order_id'    => $_order->order,
                        'action'      => 'pay',
                        'version'     => 3,
//                        'sandbox'     => 1,
                        'result_url'  => _r('ajax.shop.liqpay_callback')
                    ];
                    $liqPayForm = $liqPay->cnb_form($liqPayData);
                    $commands[] = [
                        'command' => 'append',
                        'target'  => 'body',
                        'data'    => "<div style='display: none;'>{$liqPayForm}</div>",
                    ];
                    $commands[] = [
                        'command' => 'eval',
                        'code'    => '$("#lp_form").submit();',
                    ];
                } else {
                    Notification::route('mail', env('MAIL_USER_TO_ADDRESS'))
                        ->notify(new MailShopOrder($_order));
                    Session::forget('basket');
                    $_thanks_page = page_load('shop_order_thanks_page', $_wrap['locale']);
                    $_thanks_page_alias = $_thanks_page->_alias;
                    $_thanks_page_alias = $_thanks_page_alias->language != DEFAULT_LANGUAGE ? "{$_thanks_page_alias->language}/{$_thanks_page_alias->alias}" : $_thanks_page_alias->alias;
                    $_EC = ECommerce::purchase($_order);
                    $commands[] = [
                        'command' => 'ecommerce',
                        'event'   => 'purchase',
                        'data'    => json_decode($_EC)
                    ];
                    $commands[] = [
                        'command' => 'analytics_gtag',
                        'data'    => [
                            'event'        => 'ORDER',
                            'category'     => 'SHOP',
                            'event_action' => 'COMPLETED_ORDER',
                        ]
                    ];
                    $commands[] = [
                        'command' => 'analytics_fbq',
                        'data'    => [
                            'event'        => 'COMPLETED_ORDER'
                        ]
                    ];
                    $commands[] = [
                        'command'  => 'redirect',
                        'time_out' => 500,
                        'url'      => _u($_thanks_page_alias),
                    ];
                }
            }

            return response($commands, 200);
        }

        public function liqPay_shop_callback(Request $request)
        {
            $_language = Session::get('location', DEFAULT_LANGUAGE);
            $_shop_basket_page = page_load('shop_basket', $_language);
            $_shop_basket_page_alias = $_shop_basket_page->_alias;
            $_shop_basket_page_alias = $_shop_basket_page_alias->language != DEFAULT_LANGUAGE ? _u("{$_shop_basket_page_alias->language}/{$_shop_basket_page_alias->alias}") : _u($_shop_basket_page_alias->alias);
            if ($request->has('data')) {
                $_data = json_decode(base64_decode($request->input('data')));
                $_order = ShopOrder::whereOrder($_data->order_id)
                    ->first();
                if ($_data->status == 'success' || $_data->status == 'sandbox' || $_data->status == 'processing' || $_data->status == 'wait_accept') {
                    if ($_order) {
                        Notification::route('mail', env('MAIL_USER_TO_ADDRESS'))
                            ->notify(new MailShopOrder($_order));
                        Session::forget('basket');
                        $_thanks_page = page_load('shop_order_thanks_page', $_language);
                        $_thanks_page_alias = $_thanks_page->_alias;
                        $_thanks_page_alias = $_thanks_page_alias->language != DEFAULT_LANGUAGE ? "{$_thanks_page_alias->language}/{$_thanks_page_alias->alias}" : $_thanks_page_alias->alias;
                        $_EC = ECommerce::purchase($_order);
                        Session::flash('commands', json_encode([
                            [
                                'command' => 'ecommerce',
                                'event'   => 'purchase',
                                'data'    => json_decode($_EC)
                            ],
                            [
                                'command' => 'analytics_gtag',
                                'data'    => [
                                    'event'        => 'ORDER',
                                    'category'     => 'SHOP',
                                    'event_action' => 'COMPLETED_ORDER',
                                ]
                            ],
                            [
                                'command' => 'analytics_fbq',
                                'data'    => [
                                    'event'        => 'COMPLETED_ORDER'
                                ]
                            ]
                        ]));

                        return redirect($_thanks_page_alias);
                    } else {
                        Session::flash('modal', [
                            'status'  => 'danger',
                            'message' => clear_html(view('front.modals.alert', [
                                'alert' => 'Во время попытки оплаты банковской картой произошла ошибка.<br>Ваш заказ не был найден в системе после ответа сервера сервиса оплаты.<br>Просьба сообщить оператору о данной ситуации для решения данного вопроса.',
                                'title' => 'Ошибка'
                            ])->render()),
                        ]);

                        return redirect($_shop_basket_page_alias);
                    }
                } else {
                    $_order->delete();

                    return redirect($_shop_basket_page_alias);
                }
            } else {
                Session::flash('modal', [
                    'status'  => 'danger',
                    'message' => clear_html(view('front.modals.alert', [
                        'alert' => 'Во время попытки оплаты банковской картой произошла ошибка.<br>Повторите попытку либо измените способ оплаты Вашего заказа.',
                        'title' => 'Ошибка'
                    ])->render()),
                ]);

                return redirect($_shop_basket_page_alias);
            }
        }

        public function np_ajax(Request $request)
        {
            $commands = [];
            $_np = new NovaPoshta();
            $_output = '';
            $_type = $request->get('type', 'area');
            $_option = $request->get('option');
            if ($_option) {
                if ($_type == 'area') {
                    $_output = '<option value="0" disabled selected>' . __('Выберите Город') . '</option>';
                    foreach ($_np->get_city($_option) as $_key_item => $_value_item) $_output .= '<option value="' . $_key_item . '">' . $_value_item . '</option>';
                    $commands[] = [
                        'command' => 'removeAttr',
                        'target'  => '#forms-shop-basket-delivery_city',
                        'attr'    => 'disabled'
                    ];
                    $commands[] = [
                        'command' => 'attr',
                        'target'  => '#forms-shop-basket-delivery_warehouses',
                        'attr'    => 'disabled',
                        'data'    => 'disabled',
                    ];
                    $commands[] = [
                        'command' => 'html',
                        'target'  => '#forms-shop-basket-delivery_city',
                        'data'    => $_output
                    ];
                    $commands[] = [
                        'command' => 'html',
                        'target'  => '#forms-shop-basket-delivery_warehouses',
                        'data'    => '<option value="0" disabled selected>' . __('Выберите отделение') . '</option>'
                    ];
                } elseif ($_type == 'city') {
                    $_output = '<option value="0" disabled selected>' . __('Выберите отделение') . '</option>';
                    $_streets_list = '[' . implode(',', $_np->get_street($_option)) . ']';
                    foreach ($_np->get_warehouses($_option) as $_key_item => $_value_item) $_output .= '<option value="' . $_key_item . '">' . $_value_item . '</option>';
                    $commands[] = [
                        'command' => 'removeAttr',
                        'target'  => '#forms-shop-basket-delivery_warehouses',
                        'attr'    => 'disabled'
                    ];
                    //                    $commands[] = [
                    //                        'command' => 'removeAttr',
                    //                        'target'  => '#forms-shop-basket-delivery_street',
                    //                        'attr'    => 'disabled'
                    //                    ];
                    //                    $commands[] = [
                    //                        'command' => 'removeAttr',
                    //                        'target'  => '#forms-shop-basket-delivery_house',
                    //                        'attr'    => 'disabled'
                    //                    ];
                    //                    $commands[] = [
                    //                        'command' => 'removeAttr',
                    //                        'target'  => '#forms-shop-basket-delivery_float',
                    //                        'attr'    => 'disabled'
                    //                    ];
                    $commands[] = [
                        'command' => 'html',
                        'target'  => '#forms-shop-basket-delivery_warehouses',
                        'data'    => $_output
                    ];
                    //                    $commands[] = [
                    //                        'command' => 'eval',
                    //                        'code'    => '$("#order-delivery-street").val("").autocomplete({source: ' . $_streets_list . '});'
                    //                    ];
                }
            }

            return response($commands, 200);
        }

        public function repeat_order(Request $request)
        {
            $commands = [];
            $_order_id = $request->get('order_id');
            if ($_order = ShopOrder::find($_order_id)) {
                $_order_data = $_order->info;
                foreach ($_order_data['items'] as $_entity) {
                    if ($_entity['type'] == 'product') {
                        if ($_item = ShopProduct::where('id', $_entity['id'])
                            ->active()
                            ->first()) {
                            if (!$_item->out_of_stock && ($_item->not_limited || $_item->count)) {
                                Session::put("basket.product.{$_item->id}", [
                                    'product' => $_item->id,
                                    'count'   => 1
                                ]);
                            }
                        }
                    } elseif ($_entity['type'] == 'product_group') {
                        if ($_item = ShopProductGroups::where('id', $_entity['id'])
                            ->first()) {
                            $_item_primary = ShopProduct::where('id', $_item->product_id)
                                ->active()
                                ->first();
                            $_item_secondary = ShopProduct::where('id', $_item->related_id)
                                ->active()
                                ->first();
                            if ($_item_primary && $_item_secondary) {
                                Session::put("basket.product_group.{$_item->id}", [
                                    'product_group' => $_item->id,
                                    'count'         => 1
                                ]);
                            }
                        }
                    }
                }
                $_basket = ShopBasket::get_basket();
                $_count = $_basket->get('count', 0);
                $commands[] = [
                    'command' => 'replaceWith',
                    'target'  => '#card-basket-inside',
                    'data'    => view('front.shop.basket.small_cart_inside', [
                        '_count'  => $_count,
                        '_basket' => $_basket
                    ])
                        ->render()
                ];
                $commands[] = [
                    'command' => 'html',
                    'target'  => '#card-basket > a > span',
                    'data'    => $_count ? '<small class="cart-notification">' . $_count . '</small>' : ''
                ];
            }

            return response($commands, 200);
        }

        public function view_orders(Request $request)
        {
            $commands = [];
            $_year = $request->get('year', Session::get('view_user_order_year', date('Y')));
            $_month = $request->get('month', Session::get('view_user_order_month', date('F')));
            Session::put('view_user_order_year', $_year);
            Session::put('view_user_order_month', $_month);
            $_user = Auth::user();
            $_orders = $_user->_orders($_year, $_month);
            $commands[] = [
                'command' => 'replaceWith',
                'target'  => '#account-orders-items-list',
                'data'    => view('auth.account.partials.orders_items', [
                    'item' => $_user
                ])
                    ->render()
            ];
            $commands[] = [
                'command' => 'replaceWith',
                'target'  => '#account-orders-sort-view',
                'data'    => view('auth.account.partials.orders_sort', [
                    'item' => $_user
                ])
                    ->render()
            ];

            return response($commands, 200);
        }

        /**
         * Search
         */
        public function submit_search(Request $request)
        {
            $_query = $request->only('string');
            $_response = [
                'content'      => '',
                'count_result' => 0
            ];
            $_search_page = wrap()->get('pages.search');
            $_language = wrap()->get('locale');
            if ($_query['string']) {
                $_items = Search::query_search($_query['string'], 'list');
                $_response['content'] = '<ul class="cart-list link-dropdown-list">';
                if (count($_items['items']) && count($_items['items']->items())) {
                    foreach ($_items['items']->items() as $_product) {
                        $_response['content'] .= view('front.shop.search_item_product', [
                            'item'     => $_product,
                            'language' => $_language
                        ])->render();
                    }
                    $_more = $_items['items']->total() - $_items['items']->perPage();
                    if ($_more > 0) {
                        $_plural = [
                            trans('others.plural_shop_product'),
                            trans('others.plural_shop_products'),
                            trans('others.plural_shop_products2'),
                        ];
                        $_more = plural_string($_more, $_plural);
                        $_response['content'] .= '<li><div class="more-search">';
                        $_response['content'] .= '<a href="' . _u($_search_page->_alias->alias) . '?query_string=' . $_query['string'] . '">' . __('Подробнее...') . ' ' . $_more . '</a >';
                        $_response['content'] .= '</div ></li >';
                    }
                    $_response['count_result'] = $_items['items']->total();
                } elseif ($_transcription = transcription_string($_query['string'])) {
                    $_response['content'] .= '<li class="search-punto">' . trans('others.search_maybe_string',
                            ['transcription' => '<a href ="javascript:void(0);" class="result-maybe">' . $_transcription . '</a >']) . '</li >';
                    $_response['content'] .= '<li class="search-no-result uk-alert uk-border-rounded uk-padding-small uk-alert-warning uk-margin-remove-bottom">' . __('Ничего не найдено') . '</li >';
                }
                $_response['content'] .= '</ul>';
            }
            $_response['content'] = view('front.partials.search_box', compact('_items', '_response'))->render();

            return response($_response, 200);
        }

        public function add_to_search_history(Request $request)
        {
            if ($_product_id = $request->get('product_id')) ShopProductSearchHistory::setHistory($_product_id);
        }

        /**
         * Language
         */
        public function selected_language(Request $request, $language)
        {
            $commands = [];
            $_alias_id = $request->get('alias_id');
            $_alias = $request->get('alias');
            $_location = Session::get('location', DEFAULT_LOCATION);
            //            $_language = Session::get('language', DEFAULT_LANGUAGE);
            $_language = $request->header('LOCALE-CODE', DEFAULT_LANGUAGE);
            if ($_language != $language) {
                //                Session::put('language', $language);
                if ($_alias_id && ($_alias = UrlAlias::find($_alias_id))) {
                    $_entity_class = $_alias->model_type;
                    if ($_alias->model_type == 'App\Models\ShopFilterParamsPage') {
                        if ($_entity_selected_params = $_entity_class::find($_alias->model_id)) {
                            $_default_language = DEFAULT_LANGUAGE;
                            $_entity = $_entity_class::where('selected_params', $_entity_selected_params->selected_params)
                                ->where(function ($query) use ($language, $_default_language) {
                                    $query->where('language', $language)
                                        ->orWhere('language', $_default_language);
                                })
                                ->orderByRaw("CASE WHEN (`language` = '{$language}') THEN 0 WHEN (`language` = '{$_default_language}') THEN 1 END")
                                ->with([
                                    '_alias'
                                ])
                                ->first();
                            if ($_entity) {
                                $commands[] = [
                                    'command' => 'redirect',
                                    'url'     => $_entity->language != DEFAULT_LANGUAGE ? _u("{$_entity->language}/{$_entity->_alias->alias}") : _u($_entity->_alias->alias)
                                ];
                            } else {
                                $commands[] = [
                                    'command' => 'redirect',
                                    'url'     => '/'
                                ];
                            }

                        }
                    } else {
                        if ($redirect_url = changed_page($_entity_class, $_alias->model_id, $language, $_location)) {
                            if ($_alias_id != $redirect_url->id) {
                                $commands[] = [
                                    'command' => 'redirect',
                                    'url'     => $language != DEFAULT_LANGUAGE ? _u("{$language}/{$redirect_url->alias}") : _u($redirect_url->alias)
                                ];
                            } else {
                                $commands[] = [
                                    'command'  => 'notice',
                                    'text'     => __('Нет перевода к данному материалу'),
                                    'status'   => 'danger',
                                    'position' => 'top-center'
                                ];
                            }
                        }
                    }
                } elseif ($_alias && str_is('*account*', $_alias)) {
                    $commands[] = [
                        'command' => 'redirect',
                        'url'     => $language == DEFAULT_LANGUAGE ? 'account' : "{$language}/account"
                    ];
                } else {
                    $commands[] = [
                        'command' => 'redirect',
                        'url'     => $language == DEFAULT_LANGUAGE ? '/' : $language
                    ];
                }
            }
            //            Session::forget('language');
            if (!count($commands)) {
                $commands[] = [
                    'command' => 'reload',
                ];
            }

            //            dd($commands);

            return response($commands, 200);
        }

        /**
         * Subscribe
         */
        public function submit_subscribe_application(Request $request)
        {
            $_forms = $request->input('forms');
            $_validate_rules = [
                'email' => 'required|email'
            ];
            $_request = $request->all();
            if ($request->has('email') && is_null($_request['email'])) {
                unset($_request['email']);
            }
            $validator = Validator::make($_request, $_validate_rules);
            foreach ($_validate_rules as $_field => $_rule) {
                $commands[] = [
                    'command' => 'removeClass',
                    'target'  => "#{$_forms}-{$_field}",
                    'data'    => 'uk-form-danger'
                ];
            }
            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $_field => $_message) {
                    $commands[] = [
                        'command' => 'addClass',
                        'target'  => "#{$_forms}-{$_field}",
                        'data'    => 'uk-form-danger'
                    ];
                }
            } else {
                $_save = $request->only([
                    'email'
                ]);
                Application::updateOrCreate([
                    'id' => NULL
                ], $_save);
                $commands[] = [
                    'command' => 'analytics_gtag',
                    'data'    => [
                        'event'        => 'APPLICATION',
                        'category'     => 'SUBSCRIBE',
                        'event_action' => 'COMPLETE_APPLICATION',
                    ]
                ];
                $commands[] = [
                    'command' => 'clearForm',
                    'form'    => $_forms
                ];
                $commands[] = [
                    'command' => 'modal',
                    'data'    => clear_html(view('front.modals.alert', [
                            'title' => variable('alert_modal_buy_one_click_form_title'),
                            'alert' => variable('alert_modal_application_form_title'),
                        ]
                    )->render()),
                    'options' => [
                        'id'    => 'modal-alert',
                        'class' => 'uk-margin-auto-vertical uk-border-rounded alert-success'
                    ]
                ];
            }

            return response($commands, 200);
        }

        /**
         * Count Down
         */
        public function count_down(Request $request)
        {
            $commands = [
                'command' => 'alert',
                'data'    => NULL,
                'message' => __('Ошибка! Не удалось обновить таймер.')
            ];
            $_request_data = $request->only([
                'type',
                'id'
            ]);
            $_timer = ShopProductDiscountTimer::find($_request_data['id']);
            if ($_timer) {
                if ($_product = $_timer->_deactivate()) {
                    if ($_request_data['type'] == 'teaser') {
                        $commands = [
                            'command' => 'reload',
                            'data'    => NULL,
                            'message' => NULL
                        ];
                    } else {
                        if ($_product->status) {
                            $commands = [
                                'command' => 'reload',
                                'data'    => NULL,
                                'message' => NULL
                            ];
                        } else {
                            $commands = [
                                'command' => 'redirect',
                                'data'    => _u($_product->_category()->_alias->alias),
                                'message' => NULL
                            ];
                        }
                    }
                }
            }

            return response($commands, 200);
        }

        public function clear_cache(Request $request)
        {
            $commands[] = [
                'command' => 'notice',
                'status'  => 'success',
                'text'    => __('Весь кэш очищен')
            ];
            Cache::flush();

            return response($commands, 200);
        }
    }

<?php

    namespace App\Http\Controllers\Oleus;

    use App\Http\Controllers\Controller;
    use App\Jobs\ImportShopProducts;
    use App\Library\Dashboard;
    use App\Library\ECommerce;
    use App\Models\Callback;
    use App\Models\File;
    use App\Models\Review;
    use App\Models\Reviews;
    use App\Models\ShopBasket;
    use App\Models\ShopBuyOneClick;
    use App\Models\ShopOrder;
    use App\Models\ShopProduct;
    use App\Models\ShopProductGroups;
    use App\Models\UrlAlias;
    use App\Notifications\MailShopBuyOneClick;
    use Carbon\Carbon;
    use Illuminate\Foundation\Auth\Access\Authorizable;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Notification;
    use Illuminate\Support\Facades\Storage;

    class MainController extends Controller
    {
        use Dashboard;
        use Authorizable;
        use Notifiable;

        public function __construct()
        {
            parent::__construct();
            $this->middleware([
                'permission:access_dashboard'
            ]);
        }

        public function index()
        {

            if(request()->has('code')){
                $_order = ShopOrder::find(233);

            dd($_order->info);

                // return view('mail.order', [
                //     'subject'   => 'subject',
                //     'site_url'  => config('app.url'),
                //     'site_name' => config('os_seo.settings.ru.site_name'),
                //     'item'      => $_order
                // ]);
            }

            //
            //            //            File::checked_files();
            //
            // Artisan::call('schedule:run');
            //
            //            $_file = Storage::disk('base')->get('1C_import/price_2.csv');
            //
            //            $dataCsv = [];
            //
            //            $_rows = collect(explode("\r\n", w1251ToUtf8($_file)));
            //
            //
            //            if($_rows->isNotEmpty()) {
            //                $_add_time = 0;
            //                $_rows->chunk(100)->each(function ($_chunk) use (&$dataCsv, &$_add_time) {
            //                    $_add_time++;
            //                    $_chunk->each(function ($_line_data, $_line_id) use (&$dataCsv, $_add_time) {
            //                        if($_line_data) {
            //                            $_item = explode(';', $_line_data);
            //                            if($_item[1]) $_item[1] = preg_replace('/ {2,}/', ' ', $_item[1]);
            //                            $_save = [
            //                                'title'        => $_item[1],
            //                                'sky'          => $_item[0],
            //                                'price'        => $_item[2] ? $_item[2] : 0,
            //                                'base_price'   => $_item[2] ? $_item[2] : 0,
            //                                'count'        => NULL,
            //                                'not_limited'  => $_item[3] ? 1 : 0,
            //                                'out_of_stock' => $_item[3] ? 0 : 1,
            //                            ];
            //
            //                            $dataCsv[$_add_time][] = $_save;
            //                        }
            //                    });
            //                });
            //            }

            //            $data = fgetcsv($_file, 1000, ",");


            //            $_data_csv = [];
            //            foreach ($_file as $_line) {
            //                $_data_csv[] = str_getcsv($_line);
            //            }


            //            dd($dataCsv);


            //
            //            $import_data = [];
            //            dispatch(new ImportShopProducts($import_data));

            //            $this->add_permission();

            $this->set_wrap([
                'page._title' => trans('pages.front_page'),
                'seo._title'  => trans('pages.front_page')
            ]);

            $other['orders'] = ShopOrder::orderBy('status')
                ->where('data', '<>', 'a:0:{}')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            $other['buy_one_click'] = ShopBuyOneClick::orderByDesc('created_at')
                ->limit(10)
                ->get();

            return view('oleus.main.index', compact('other'));
        }

        public function notice()
        {
            return view('layouts.oleus');

        }

        public function add_permission()
        {
            $this->permissions = collect([
                [
                    'name'         => 'coupon_read',
                    'display_name' => 'Просмотр списка купонов',
                    'guard_name'   => 'web',
                ],
                [
                    'name'         => 'coupon_import',
                    'display_name' => 'Импорт купонов',
                    'guard_name'   => 'web',
                ]
            ]);
            $this->permissions->each(function ($_permission) {
                Permission::create($_permission);
            });
            $_role = Role::findByName('super_admin');
            $this->permissions->each(function ($_permission) use ($_role) {
                $_role->givePermissionTo($_permission['name']);
            });
        }
    }

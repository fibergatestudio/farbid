<?php

    namespace App\Providers;

    use App\Models\Advantage;
    use App\Models\Banner;
    use App\Models\Block;
    use App\Models\Callback;
    use App\Models\Menu;
    use App\Models\Message;
    use App\Models\Node;
    use App\Models\Page;
    use App\Models\Review;
    use App\Models\Role;
    use App\Models\Service;
    use App\Models\ServiceOrder;
    use App\Models\ShopBasket;
    use App\Models\ShopBuyOneClick;
    use App\Models\ShopCategory;
    use App\Models\ShopFilterParamsPage;
    use App\Models\ShopOrder;
    use App\Models\ShopParam;
    use App\Models\ShopProduct;
    use App\Models\ShopProductGroups;
    use App\Models\Slider;
    use App\Models\Variable;
    use App\User;
    use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
    use Illuminate\Support\Facades\Route;

    class RouteServiceProvider extends ServiceProvider
    {
        protected $namespace = 'App\Http\Controllers';

        public function boot()
        {
            parent::boot();

            $_languages = config('os_languages.languages');
            $_locations = config('os_contacts.cities');

            Route::pattern('id', '[0-9]+');
            Route::pattern('key', '[0-9a-zA-Z]+');
            Route::pattern('action', '(add|update|remove|save|edit)');
            Route::pattern('language', '(' . implode('|', array_keys($_languages)) . ')');
            Route::pattern('location', '(' . implode('|', array_keys($_locations)) . ')');

            Route::model('user', User::class);
            Route::model('block', Block::class);
            Route::model('banner', Banner::class);
            Route::model('advantage', Advantage::class);
            Route::model('page', Page::class);
            Route::model('node', Node::class);
            Route::model('variable', Variable::class);
            Route::model('role', Role::class);
            Route::model('menu', Menu::class);
            Route::model('service', Service::class);
            Route::model('service_order', ServiceOrder::class);
            Route::model('callback', Callback::class);
            Route::model('review', Review::class);
            Route::model('slider', Slider::class);
            Route::model('shop_param', ShopParam::class);
            Route::model('shop_category', ShopCategory::class);
            Route::model('shop_product', ShopProduct::class);
            Route::model('shop_buy_one_click', ShopBuyOneClick::class);
            Route::model('shop_filter_page', ShopFilterParamsPage::class);
            Route::model('shop_product_group', ShopProductGroups::class);
            Route::model('shop_order', ShopOrder::class);
            Route::model('message', Message::class);
        }

        public function register()
        {
            parent::register();
        }

        public function map()
        {
            $this->mapOleusRoutes();
            $this->mapApiRoutes();
            $this->mapWebRoutes();
        }

        protected function mapWebRoutes()
        {
            Route::middleware([
                'web',
                'lastModified'
            ])
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        }

        protected function mapApiRoutes()
        {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
        }

        protected function mapOleusRoutes()
        {
            Route::prefix('oleus')
                ->middleware([
                    'web',
                    'auth',
                    'permission:access_dashboard'
                ])
                ->namespace('App\Http\Controllers\Oleus')
                ->group(base_path('routes/oleus.php'));

            Route::prefix('ajax')
                ->middleware([
                    'web',
                ])
                ->namespace('App\Http\Controllers\Oleus')
                ->group(base_path('routes/callback.php'));
        }
    }

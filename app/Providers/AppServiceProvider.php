<?php

    namespace App\Providers;

    use App\Library\Wrap;
    use App\Models\Advantage;
    use App\Models\AdvantageItems;
    use App\Models\Banner;
    use App\Models\Block;
    use App\Models\Menu;
    use App\Models\Node;
    use App\Models\Page;
    use App\Models\Role;
    use App\Models\Service;
    use App\Models\ShopCategory;
    use App\Models\ShopFilterParamsPage;
    use App\Models\ShopParam;
    use App\Models\ShopParamItem;
    use App\Models\ShopProduct;
    use App\Models\Slider;
    use App\Models\SliderItems;
    use App\Observers\AdvantageItemObservers;
    use App\Observers\AdvantageObservers;
    use App\Observers\BannerObservers;
    use App\Observers\BlockObservers;
    use App\Observers\MenuObservers;
    use App\Observers\NodeObservers;
    use App\Observers\PageObservers;
    use App\Observers\RoleObservers;
    use App\Observers\ServiceObservers;
    use App\Observers\ShopCategoryObservers;
    use App\Observers\ShopFilterParamsPageObservers;
    use App\Observers\ShopParamItemObservers;
    use App\Observers\ShopParamObservers;
    use App\Observers\ShopProductObservers;
    use App\Observers\SliderItemObservers;
    use App\Observers\SliderObservers;
    use App\Observers\UserObservers;
    use App\User;
    use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
    use Illuminate\Database\Eloquent\Relations\Relation;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        public function boot()
        {
            Schema::defaultStringLength(191);

            Advantage::observe(AdvantageObservers::class);
            AdvantageItems::observe(AdvantageItemObservers::class);
            Block::observe(BlockObservers::class);
            Banner::observe(BannerObservers::class);
            Page::observe(PageObservers::class);
            Node::observe(NodeObservers::class);
            User::observe(UserObservers::class);
            Role::observe(RoleObservers::class);
            Menu::observe(MenuObservers::class);
            Service::observe(ServiceObservers::class);
            Slider::observe(SliderObservers::class);
            SliderItems::observe(SliderItemObservers::class);
            ShopParam::observe(ShopParamObservers::class);
            ShopParamItem::observe(ShopParamItemObservers::class);
            ShopCategory::observe(ShopCategoryObservers::class);
            ShopProduct::observe(ShopProductObservers::class);
            ShopFilterParamsPage::observe(ShopFilterParamsPageObservers::class);

            Relation::morphMap([]);
        }

        public function register()
        {
            if($this->app->environment() !== 'production') $this->app->register(IdeHelperServiceProvider::class);
            $this->app->singleton('wrap', function ($app) {
                return new Wrap();
            });
        }
    }

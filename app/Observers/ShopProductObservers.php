<?php

    namespace App\Observers;

    use App\Models\File;
    use App\Models\FilesReference;
    use App\Models\ShopProduct;
    use App\Models\ShopProductCategory;
    use App\Models\ShopProductDiscountTimer;
    use App\Models\ShopProductGroups;
    use App\Models\ShopProductRelated;
    use App\Models\UrlAlias;
    use Illuminate\Support\Facades\Cache;

    class ShopProductObservers
    {
        public function created(ShopProduct $item)
        {
        }

        public function saved(ShopProduct $item)
        {
            if (request()->has('url')) {
                $_founder = NULL;
//                if ($item->_related_page && ($_related_page_alias = $item->_related_page->_alias)) $_founder[] = $_related_page_alias->alias;
                $_url_alias = new UrlAlias($item, $_founder);
                $_url_alias->set();
            }
            if (request()->has('preview_fid') && ($_preview = request()->input('preview_fid'))) {
                $_preview = array_shift($_preview);
                File::where('id', $_preview['id'])
                    ->update([
                        'title'       => $_preview['title'],
                        'alt'         => $_preview['alt'],
                        'description' => $_preview['description'],
                    ]);
            }
            if (request()->has('background_fid') && ($_background = request()->input('background_fid'))) {
                $_background = array_shift($_background);
                File::where('id', $_background['id'])
                    ->update([
                        'title'       => $_background['title'],
                        'alt'         => $_background['alt'],
                        'description' => $_background['description'],
                    ]);
            }
            if(request()->has('medias')) {
                $_medias = new FilesReference($item, 'medias');
                $_medias->set();
            }
            if(request()->has('files') && request()->input('files')) {
                $_medias = new FilesReference($item, 'files');
                $_medias->set();
            }
            if (request()->has('categories')) {
                $_categories = new ShopProductCategory($item);
                $_categories->set(request()->input('categories'));
                $_categories->set_params(request()->input('params'));
            }
            if (request()->has('discount_timer')) {
                $_timer = new ShopProductDiscountTimer($item);
                $_timer->set();
            }
            $_related_products = new ShopProductRelated($item);
            $_related_products->set();
            $_product_groups = new ShopProductGroups($item);
            $_product_groups->set();
            $item->forgetCache();
        }

        public function deleting(ShopProduct $item)
        {
            if ($item->alias_id) {
                UrlAlias::where('id', $item->alias_id)
                    ->delete();
            }
            FilesReference::where('model_type', $item->getMorphClass())
                ->where('model_id', $item->id)
                ->delete();
            $_relation_items = ShopProduct::where('relation', $item->id)
                ->get();
            if ($_relation_items->isNotEmpty()) {
                $_relation_items->each(function ($_product) {
                    Cache::forget("{$_product->classIndex}_{$_product->id}");
                    UrlAlias::where('id', $_product->alias_id)
                        ->delete();
                    FilesReference::where('model_type', $_product->getMorphClass())
                        ->where('model_id', $_product->id)
                        ->delete();
                    $_product->delete();
                });
            }
            Cache::forget("{$item->classIndex}_{$item->id}");
        }
    }
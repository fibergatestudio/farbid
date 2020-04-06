<?php

    namespace App\Observers;

    use App\Models\File;
    use App\Models\FilesReference;
    use App\Models\ShopCategory;
    use App\Models\ShopCategoryParam;
    use App\Models\UrlAlias;

    class ShopCategoryObservers
    {
        public function created(ShopCategory $item)
        {
        }

        public function saved(ShopCategory $item)
        {
            if(request()->has('category_params')) {
                $_category_params = new ShopCategoryParam($item);
                $_category_params->set(request()->input('category_params'));
            }
            if(request()->has('url')) {
                $_founder = NULL;
                //                if(($_parent = $item->parent) && $_parent->_alias) $_founder[] = $_parent->_alias->alias;
                $_url_alias = new UrlAlias($item, $_founder);
                $_url_alias->set();
            }
            if(request()->has('icon_fid') && ($_icon = request()->input('icon_fid'))) {
                $_icon = array_shift($_icon);
                File::where('id', $_icon['id'])
                    ->update([
                        'title'       => $_icon['title'],
                        'alt'         => $_icon['alt'],
                        'description' => $_icon['description'],
                    ]);
            }
            if(request()->has('background_fid') && ($_background = request()->input('background_fid'))) {
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
            $item->forgetCache();
        }

        public function deleting(ShopCategory $item)
        {
            if($item->alias_id) {
                UrlAlias::where('id', $item->alias_id)
                    ->delete();
            }
            FilesReference::where('model_type', $item->getMorphClass())
                ->where('model_id', $item->id)
                ->delete();
            $_relation_items = ShopCategory::where('relation', $item->id)
                ->get();
            if($_relation_items->isNotEmpty()) {
                $_relation_items->each(function ($_category) {
                    UrlAlias::where('id', $_category->alias_id)
                        ->delete();
                    FilesReference::where('model_type', $_category->getMorphClass())
                        ->where('model_id', $_category->id)
                        ->delete();
                    $_category->delete();
                });
            }
        }
    }
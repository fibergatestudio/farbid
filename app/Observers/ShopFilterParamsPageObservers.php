<?php

    namespace App\Observers;

    use App\Models\ShopFilterParamsPage;
    use App\Models\UrlAlias;

    class ShopFilterParamsPageObservers
    {
        public function created(ShopFilterParamsPage $item)
        {
        }

        public function saved(ShopFilterParamsPage $item)
        {
            if(request()->has('url_alias')) {
                $_founder = NULL;
                if(($_parent = $item->parent) && $_parent->_alias) $_founder[] = $_parent->_alias->alias;
                $_url_alias = new UrlAlias($item, $_founder);
                $_url_alias->set(request()->input('url_alias'));
            }
        }

        public function deleting(ShopFilterParamsPage $item)
        {
            if($item->alias_id) {
                UrlAlias::where('id', $item->alias_id)
                    ->delete();
            }
        }
    }
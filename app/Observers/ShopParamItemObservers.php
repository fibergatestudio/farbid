<?php

    namespace App\Observers;

    use App\Models\ShopParamItem;

    class ShopParamItemObservers
    {
        public function created(ShopParamItem $item)
        {
        }

        public function saved(ShopParamItem $item)
        {
            if(is_null($item->relation)) {
                $_relation_items = ShopParamItem::where('relation', $item->id)
                    ->get();
                if($_relation_items->isNotEmpty()) {
                    $_relation_items->each(function ($_item) use ($item) {
                        $_item->sort = $item->sort;
                        $_item->visible_in_filter = $item->visible_in_filter;
                        $_item->save();
                    });
                }
            }
        }

        public function deleting(ShopParamItem $item)
        {
        }
    }
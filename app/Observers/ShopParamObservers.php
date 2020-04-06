<?php

    namespace App\Observers;

    use App\Models\ShopParam;
    use App\Models\ShopParamItem;
    use Illuminate\Support\Facades\Schema;

    class ShopParamObservers
    {
        public function created(ShopParam $item)
        {
        }

        public function saved(ShopParam $item)
        {
            if(request()->has('param_item') && ($_param_item = request()->input('param_item'))) {
                $_translate_request = isset($_param_item['translate']) ? $_param_item['translate'] : NULL;
                $_param_item['translate'] = NULL;
                if($_translate_request) {
                    $_translate = NULL;
                    foreach($_translate_request as $_language => $_data) if($_data) $_translate[$_language] = $_data;
                    if($_translate) $_param_item['translate'] = serialize($_translate);
                }
                ShopParamItem::updateOrCreate([
                    'param_id' => $_param_item['param_id'],
                    'type'     => $_param_item['type']
                ], $_param_item);
            }
        }

        public function deleting(ShopParam $item)
        {
            if(is_null($item->relation)) Schema::drop($item->table);
        }
    }
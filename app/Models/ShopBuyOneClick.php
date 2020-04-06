<?php

    namespace App\Models;

    use App\Library\BaseModel;

    class ShopBuyOneClick extends BaseModel
    {
        protected $table = 'shop_buy_one_click';
        protected $guarded = [];

        public function _product()
        {
            return $this->hasOne(ShopProduct::class, 'id', 'product_id')
                ->with('_alias');
        }
    }

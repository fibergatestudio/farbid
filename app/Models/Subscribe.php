<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Subscribe extends Model
    {
        protected $table = 'shop_buy_one_click';
        protected $primaryKey = 'id';
        protected $fillable = [
            'product_id',
            'name',
            'email',
            'phone',
            'comment',
            'status',
        ];

        public function _product()
        {
            return $this->hasOne(ShopProduct::class, 'id', 'product_id')
                ->with('_alias');
        }
    }

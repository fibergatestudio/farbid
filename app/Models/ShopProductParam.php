<?php
    /**
     * затирать код не стал. может пригодиться. но сейчас я данные записыав, но не использую
     * все параметры по товарам записываются в 1 таблицу
     */


    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class ShopProductParam extends Model
    {
        protected $table = 'shop_product_params',
            $primaryKey = 'id',
            $fillable = [
            'product_id',
            'param_id',
            'value',
        ];
        public $timestamps = FALSE;
    }
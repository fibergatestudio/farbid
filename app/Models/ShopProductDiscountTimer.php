<?php

    namespace App\Models;

    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;
    use Session;

    class ShopProductDiscountTimer extends Model
    {
        protected $table = 'shop_product_discount_timer';
        protected $guarded = [];
        protected $entity;
        public $timestamps = FALSE;
        protected $dates = [
            'finish_date'
        ];

        public function __construct($entity = NULL)
        {
            $this->entity = $entity;
        }

        public function _product()
        {
            return $this->belongsTo(ShopProduct::class, 'product_id', 'id')->withDefault();
        }

        public function set()
        {
            if($this->entity && request()->has('discount_timer')) {
                if($_timer = request()->get('discount_timer')) {
                    $_delete = TRUE;
                    if($_timer['use_timer']) {
                        $current = Carbon::now();
                        $finish_date = Carbon::parse($_timer['finish_date']);
                        if($current->diffInMinutes($finish_date, FALSE)) {
                            self::updateOrCreate([
                                'product_id' => $this->entity->id
                            ], [
                                'product_id'  => $this->entity->id,
                                'finish_date' => $finish_date,
                                'action'      => $_timer['action'],
                                'new_price'   => $_timer['action'] == 2 ? $_timer['new_price'] : 0
                            ]);
                            DB::table('shop_products')
                                ->where('id', $this->entity->id)
                                ->update([
                                    'mark_discount' => 1
                                ]);
                            $_delete = FALSE;
                        }
                    }
                    if($_delete) {
                        self::where('product_id', $this->entity->id)
                            ->delete();
                        // DB::table('shop_products')
                        //     ->where('id', $this->entity->id)
                        //     ->update([
                        //         'mark_discount' => 0
                        //     ]);
                    }
                }
            }

            return NULL;
        }

        public function _deactivate()
        {
            $_response = NULL;
            if($this->_product->exists) {
                switch($this->action) {
                    case 1:
                        if($_old_price = $this->_product->old_price) $this->_product->price = $_old_price;
                        $this->_product->old_price = NULL;
                        $this->_product->save();
                        break;
                    case 2:
                        if($_new_price = $this->new_price) $this->_product->price = $_new_price;
                        $this->_product->old_price = NULL;
                        $this->_product->save();
                        break;
                    default:
                        $this->_product->status = 0;
                        $this->_product->save();
                        break;
                }
                $_response = $this->_product;
            }
            $this->delete();

            return $_response;
        }

        public static function products()
        {
            $_items = self::limit(10)
                ->get();
            if($_items->isNotEmpty()) {
                $_output = NULL;
                $_items->each(function ($_item) use (&$_output) {
                    $_product = ShopProduct::find($_item->product_id);
                    if($_product) {
                        $_output .= view('front.shop.teaser_product_daily_deals', [
                            'item' => $_product
                        ])->render();
                    } else {
                        $_item->delete();
                    }
                });
                if($_output) {
                    return '<div id="daily_deals" class="owl-carousel">' . $_output . '</div>';
                }
            }

            return NULL;
        }
    }

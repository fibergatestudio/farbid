<?php

    namespace App\Models;

    use App\Library\BaseModel;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Session;

    class ShopProductSearchHistory extends BaseModel
    {
        protected $table = 'shop_product_search_history';
        protected $fillable = [
            'product_id',
            'user_id',
            'added_on'
        ];
        protected $primaryKey = 'product_id';
        public $incrementing = FALSE;
        public $timestamps = FALSE;
        public $user;
        protected $limitView = 10;
        protected $dates = [
            'added_on'
        ];
        const LIMIT_VIEW = 10;

        public static function setHistory($_product_id = NULL)
        {
            $_user = Auth::user();
            if ($_user) {
                if (Session::has('product_search_history') && $_items = Session::get('product_search_history')) {
                    $_user_id = $_user->id;
                    collect($_items)->map(function ($_product) use ($_user_id) {
                        self::updateOrCreate([
                            'product_id' => $_product->product_id,
                            'user_id'    => $_user_id,
                        ], [
                            'product_id' => $_product->product_id,
                            'user_id'    => $_user_id,
                            'added_on'   => Carbon::createFromTimestamp($_product->added_on)
                        ]);
                    });
                    Session::forget('product_search_history');
                }
                if ($_product_id) {
                    self::updateOrCreate([
                        'product_id' => $_product_id,
                        'user_id'    => $_user->id,
                    ], [
                        'product_id' => $_product_id,
                        'user_id'    => $_user->id,
                        'added_on'   => Carbon::now()
                    ]);
                }
                $_delete_items = self::where('user_id', $_user->id)
                    ->orderByDesc('added_on')
                    ->offset(self::LIMIT_VIEW)
                    ->limit(100)
                    ->get();
                if ($_delete_items->isNotEmpty()) {
                    self::whereIn('product_id', $_delete_items->pluck('product_id'))
                        ->where('user_id', $_user->id)
                        ->delete();
                }

                return self::where('user_id', $_user->id)
                    ->orderByDesc('added_on')
                    ->get();
            } else {
                $_items = Session::get('product_search_history', []);
                Session::forget('product_search_history');
                if ($_product_id && isset($_items[$_product_id])) {
                    $_items[$_product_id]->added_on = Carbon::now()->timestamp;
                } elseif ($_product_id) {
                    $_items[$_product_id] = (object)[
                        'product_id' => $_product_id,
                        'added_on'   => Carbon::now()->timestamp
                    ];
                }
                $_i = self::LIMIT_VIEW;
                $_items = collect($_items)->sortByDesc('added_on')->map(function ($_product) use (&$_i) {
                    $_i--;
                    if ($_i >= 0) {
                        Session::put("product_search_history.{$_product->product_id}", $_product);

                        return $_product;
                    }
                });

                return $_items;
            }
        }

        public static function getHistory()
        {
            $_user = Auth::user();
            $_language = wrap()->get('locale', DEFAULT_LANGUAGE);
            if ($_user) {
                $_items = ShopProduct::from('shop_products as p')
                    ->join('shop_product_search_history as h', 'h.product_id', '=', 'p.id')
                    ->with([
                        '_alias',
                        '_preview',
                        '_background',
                        '_discount_timer'
                    ])
                    ->where('h.user_id', $_user->id)
                    ->orderByDesc('added_on')
                    ->get();
                if ($_items->isNotEmpty()) {
                    $_items = $_items->map(function ($_product) use ($_language) {
                        if ($_language != $_product->language) {
                            return shop_product_load($_product->id, $_language, 'short');
                        } else {
                            $_product->_load('short');

                            return $_product;
                        }
                    });
                }

                return $_items;
            } else {
                $_items = Session::get('product_search_history', []);
                $_items = collect($_items);
                if ($_items->isNotEmpty()) {
                    $_items = $_items->map(function ($_product) {
                        return shop_product_load($_product->product_id);
                    });
                }

                return $_items;
            }
        }
    }

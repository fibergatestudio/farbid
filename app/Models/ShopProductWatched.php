<?php

    namespace App\Models;

    use App\Library\BaseModel;
    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;

    class ShopProductWatched extends BaseModel
    {
        protected $table = 'shop_product_watched';
        protected $fillable = [
            'product_id',
            'user_id',
            'added_on'
        ];
        protected $primaryKey = 'product_id';
        public $incrementing = FALSE;
        public $timestamps = FALSE;
        public $entity;
        public $user;
        protected $limitView = 10;
        protected $dates = [
            'added_on'
        ];

        public function __construct($entity = NULL)
        {
            $this->entity = $entity;
            $this->user = wrap()->get('user');
        }

        public function _set()
        {
            if($this->entity) {
                if($this->user) {
                    if($this->entity->status) {
                        if(Session::has('product_watched') && $_items = Session::get('product_viewed')) {
                            $_user_id = $this->user->id;
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
                            Session::forget('product_watched');
                        }
                        self::updateOrCreate([
                            'product_id' => $this->entity->relation_entity->id,
                            'user_id'    => $this->user->id,
                        ], [
                            'product_id' => $this->entity->relation_entity->id,
                            'user_id'    => $this->user->id,
                            'added_on'   => Carbon::now()
                        ]);
                        $_delete_items = self::where('user_id', $this->user->id)
                            ->orderByDesc('added_on')
                            ->offset($this->limitView)
                            ->limit(100)
                            ->get();
                        if($_delete_items->isNotEmpty()) {
                            self::whereIn('product_id', $_delete_items->pluck('product_id'))
                                ->where('user_id', $this->user->id)
                                ->delete();
                        }
                    }

                    return self::where('user_id', $this->user->id)
                        ->where('product_id', '<>', $this->entity->relation_entity->id)
                        ->orderByDesc('added_on')
                        ->get();
                } else {
                    $_items = Session::get('product_watched', []);
                    if($this->entity->status) {
                        Session::forget('product_watched');
                        if(isset($_items[$this->entity->relation_entity->id])) {
                            $_items[$this->entity->relation_entity->id]->added_on = Carbon::now()->timestamp;
                        } else {
                            $_items[$this->entity->relation_entity->id] = (object)[
                                'product_id' => $this->entity->relation_entity->id,
                                'added_on'   => Carbon::now()->timestamp
                            ];
                        }
                        $_i = $this->limitView+1;
                        $_items = collect($_items)->sortByDesc('added_on')->map(function ($_product) use (&$_i) {
                            $_i--;
                            if($_i >= 0) {
                                Session::put("product_watched.{$_product->product_id}", $_product);

                                return $_product;
                            }
                        })->filter(function ($_product) {
                            return !is_null($_product) && $_product->product_id != $this->entity->relation_entity->id;
                        });
                    }
                    return collect($_items);
                }
            }
        }

        public function _get()
        {
            if($this->user) {
                return self::where('user_id', $this->user->id)
                    ->with([
                        '_product'
                    ])
                    ->orderByDesc('added_on')
                    ->get();
            } else {
                $_items = Session::get('product_watched', []);

                return collect($_items)->sortByDesc('added_on');
            }
        }

        public function _product()
        {
            return $this->hasOne(ShopProduct::class, 'id', 'product_id')
                ->with([
                    '_alias',
                    '_preview',
                    '_discount_timer'
                ]);
        }
    }

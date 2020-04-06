<?php

    namespace App\Models;

    use App\Library\BaseModel;
    use App\User;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class ShopOrder extends BaseModel
    {
        protected $table = 'shop_orders';
        protected $guarded = [];

        public function __construct()
        {
            parent::__construct();
        }

        public function getUserNameAttribute()
        {
            if($this->user_id) {
                $_user = User::find($this->user_id);

                return $_user->_profile->full_name;
            } else {
                return $this->email;
            }
        }

        public function getUserEmailAttribute()
        {
            if($this->user_id) {
                $_user = User::find($this->user_id);

                return $_user->email;
            }

            return NULL;
        }

        public function getInfoAttribute()
        {
            $_response = NULL;
            if($this->data) {
                $_data = unserialize($this->data);
                if($_data){
                    foreach($_data['items'] as &$_entity) {
                        if($_entity['type'] == 'product') {
                            $_item = ShopProduct::find($_entity['id']);
                            $_entity['entity'] = $_item ?? FALSE;
                        } elseif($_entity['type'] == 'product_group') {
                            foreach($_entity['products'] as &$_product) {
                                $_item = ShopProduct::find($_product['id']);
                                $_product['entity'] = $_item ?? FALSE;
                            }
                        }
                    }
                    $_response = $_data;
                }
            }

            return $_response;
        }

        public function getAmountAttribute()
        {
            if($this->info) return $this->info['total'];

            return NULL;
        }
    }

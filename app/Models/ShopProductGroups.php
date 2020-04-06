<?php

    namespace App\Models;

    use App\Library\BaseModel;

    class ShopProductGroups extends BaseModel
    {
        protected $table = 'shop_product_groups';
        protected $guarded = [];
        public $timestamps = FALSE;
        public $entity;

        public function __construct($entity = NULL)
        {
            parent::__construct();
            $this->entity = $entity;
        }

        public function set()
        {
            if ($this->entity && request()->has('product_groups')) {
                if ($_groups = request()->get('product_groups')) {
                    $_old = self::where('product_id', $this->entity->id)
                        ->pluck('related_id');
                    foreach ($_groups as $_group) {
                        if ($_group['product_id']['value']) {
                            if ($_group['product_id']['value'] != $this->entity->id) {
                                $this->updateOrCreate([
                                    'product_id' => $this->entity->id,
                                    'related_id' => $_group['product_id']['value']
                                ], [
                                    'product_id' => $this->entity->id,
                                    'related_id' => $_group['product_id']['value'],
                                    'percent'    => $_group['percent'] > 0 && $_group['percent'] < 100 ? $_group['percent'] : 1,
                                ]);
                            }
                            $_old = $_old->filter(function ($_value) use ($_group) {
                                return $_value != $_group['product_id']['value'];
                            });
                        }
                    }
                    $_groups_delete = $_old->all();
                    if (count($_groups_delete)) {
                        self::where('product_id', $this->entity->id)
                            ->whereIn('related_id', $_groups_delete)
                            ->delete();
                    }
                } else {
                    self::where('product_id', $this->entity->id)
                        ->delete();
                }
            } elseif (request()->has('save') || request()->has('save_close')) {
                self::where('product_id', $this->entity->id)
                    ->delete();
            }

            return NULL;
        }
    }

<?php

    namespace App\Models;

    use App\Library\BaseModel;

    class ShopProductRelated extends BaseModel
    {
        protected $table = 'shop_product_related';
        protected $guarded = [];
        public $entity;
        public $timestamps = FALSE;

        public function __construct($entity = NULL)
        {
            parent::__construct();
            $this->entity = $entity;
        }

        public function set()
        {
            if ($this->entity && request()->has('related_product')) {
                if ($_related = request()->get('related_product')) {
                    $_old = self::where('product_id', $this->entity->id)
                        ->pluck('related_id');
                    foreach ($_related as $_relate) {
                        if ($_relate['value']) {
                            $this->updateOrCreate([
                                'product_id' => $this->entity->id,
                                'related_id' => $_relate['value']
                            ], [
                                'product_id' => $this->entity->id,
                                'related_id' => $_relate['value']
                            ]);
                            $_old = $_old->filter(function ($_value) use ($_relate) {
                                return $_value != $_relate['value'];
                            });
                        }
                    }
                    $_related_delete = $_old->all();
                    if (count($_related_delete)) {
                        self::where('product_id', $this->entity->id)
                            ->whereIn('related_id', $_related_delete)
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

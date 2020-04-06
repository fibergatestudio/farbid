<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class ServicePrice extends Model
    {
        protected $table = 'service_prices';
        protected $fillable = [
            'service_id',
            'title',
            'sub_title',
            'sort',
            'price',
        ];
        protected $entity;
        public $timestamps = FALSE;

        public function __construct($entity = NULL)
        {
            $this->entity = $entity;
        }

        public function set()
        {
            if($this->entity && ($_prices = request()->input('service_prices'))) {
                self::where('service_id', $this->entity->id)
                    ->delete();
                if(is_array($_prices)) {
                    foreach($_prices as $_price) {
                        if($_price['title'] && $_price['price']) {
                            $_price['service_id'] = $this->entity->id;
                            self::updateOrCreate([
                                'id' => NULL
                            ], $_price);
                        }
                    }
                }
            }

            return NULL;
        }

        public function _short_code($data)
        {
            if($data->isNotEmpty()) {
                $_template = choice_template([
                    'front.services.prices',
                    'oleus.base.service_prices'
                ]);

                return view($_template, ['prices' => $data]);
            }

            return NULL;
        }
    }

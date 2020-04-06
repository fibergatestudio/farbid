<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class ShopCategoryParam extends Model
    {
        protected $table = 'shop_category_params',
            $primaryKey = 'id',
            $fillable = [
            'category_id',
            'param_id',
            'sort',
            'modify',
        ];
        public $timestamps = FALSE;
        public $entity;

        public function __construct($entity = NULL)
        {
            $this->entity = $entity;
        }

        public function set($params)
        {
            if($this->entity) {
                $_old_params = self::where('category_id', $this->entity->id)
                    ->pluck('id', 'param_id')
                    ->toArray();
                $_modify = request()->get('category_relation_param');
                foreach($params as $_data) {
                    if($_data['applicable']) {
                        if(isset($_old_params[$_data['id']])) {
                            self::where('id', $_old_params[$_data['id']])
                                ->update([
                                    'sort'   => $_data['sort'],
                                    'modify' => is_null($_modify) ? 0 : (in_array($_data['id'], $_modify) ? 1 : 0)
                                ]);
                            unset($_old_params[$_data['id']]);
                        } else {
                            self::insert([
                                'category_id' => $this->entity->id,
                                'param_id'    => $_data['id'],
                                'sort'        => $_data['sort'],
                                'modify' => is_null($_modify) ? 0 : (in_array($_data['id'], $_modify) ? 1 : 0)
                            ]);
                        }
                    }
                }
                if($_old_params) {
                    sort($_old_params);
                    self::whereIn('id', $_old_params)
                        ->delete();
                }
            }
        }
    }

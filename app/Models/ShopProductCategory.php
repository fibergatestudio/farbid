<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;

    class ShopProductCategory extends Model
    {
        protected $table = 'shop_product_categories',
            $primaryKey = 'id',
            $fillable = [
            'product_id',
            'category_id'
        ];
        public $timestamps = FALSE;
        protected $entity;

        public function __construct($entity = NULL)
        {
            $this->entity = $entity;
        }

        public function set($categories)
        {
            if($this->entity && $categories) {
                $_old = self::where('product_id', $this->entity->id)
                    ->pluck('category_id');
                if(is_array($categories)) {
                    foreach($categories as $_category) {
                        $this->updateOrCreate([
                            'product_id'  => $this->entity->id,
                            'category_id' => $_category
                        ], [
                            'product_id'  => $this->entity->id,
                            'category_id' => $_category
                        ]);
                        $_old = $_old->filter(function ($_value) use ($_category) {
                            return $_value != $_category;
                        });
                    }
                } else {
                    $this->updateOrCreate([
                        'product_id'  => $this->entity->id,
                        'category_id' => $categories
                    ], [
                        'product_id'  => $this->entity->id,
                        'category_id' => $categories
                    ]);
                    $_old = $_old->filter(function ($_value) use ($categories) {
                        return $_value != $categories;
                    });
                }
                $_categories_delete = $_old->all();
                if(count($_categories_delete)) {
                    self::where('product_id', $this->entity->id)
                        ->whereIn('category_id', $_categories_delete)
                        ->delete();
                }
            }

            return NULL;
        }

        public function set_params($params = NULL)
        {
            if($this->entity && $params) {
                $_all_params = ShopParam::language(DEFAULT_LANGUAGE)
                    ->get();
                $item = $this->entity;
                if($_all_params->isNotEmpty()) {
                    $_all_params->each(function ($_param) use ($item, $params) {
                        if(isset($params[$_param->id])) {
                            $_exists_product_param = DB::table('shop_product_params')
                                ->where('product_id', $item->id)
                                ->where('param_id', $_param->id)
                                ->where('value', $params[$_param->id])
                                ->exists();
                            if($_param->type == 'select' && ($params[$_param->id] != 0 || is_array($params[$_param->id]))) {
                                if($_param->type_view != 'multiple') {
                                    $_exists = DB::table($_param->table)
                                        ->where('product_id', $item->id)
                                        ->first();
                                    if($_exists) {
                                        DB::table($_param->table)
                                            ->where('product_id', $item->id)
                                            ->update([
                                                'option_id' => $params[$_param->id]
                                            ]);
                                    } else {
                                        DB::table($_param->table)
                                            ->insert([
                                                'product_id' => $item->id,
                                                'option_id'  => $params[$_param->id]
                                            ]);
                                        if(!$_exists_product_param) {
                                            DB::table('shop_product_params')
                                                ->insert([
                                                    'product_id' => $item->id,
                                                    'param_id'   => $_param->id,
                                                    'value'      => $params[$_param->id]
                                                ]);
                                        }
                                    }
                                } else {
                                    $_exists = DB::table($_param->table)
                                        ->where('product_id', $item->id)
                                        ->pluck('id', 'option_id');
                                    foreach($params[$_param->id] as $_option_id) {
                                        if(isset($_exists[$_option_id])) {
                                            unset($_exists[$_option_id]);
                                        } else {
                                            DB::table($_param->table)
                                                ->insert([
                                                    'product_id' => $item->id,
                                                    'option_id'  => $_option_id
                                                ]);
                                            if(!$_exists_product_param) {
                                                DB::table('shop_product_params')
                                                    ->insert([
                                                        'product_id' => $item->id,
                                                        'param_id'   => $_param->id,
                                                        'value'      => $_option_id
                                                    ]);
                                            }
                                        }
                                    }
                                    if(count($_exists)) {
                                        DB::table($_param->table)
                                            ->whereIn('id', $_exists->values()->toArray())
                                            ->delete();
                                    }
                                }
                            } elseif(($_param->type == 'input_number' || $_param->type == 'input_text') && !is_null($params[$_param->id])) {
                                $_exists = DB::table($_param->table)
                                    ->where('product_id', $item->id)
                                    ->first();
                                if($_exists) {
                                    DB::table($_param->table)
                                        ->where('product_id', $item->id)
                                        ->update([
                                            'value' => $params[$_param->id]
                                        ]);
                                } else {
                                    DB::table($_param->table)
                                        ->insert([
                                            'product_id' => $item->id,
                                            'value'      => $params[$_param->id]
                                        ]);
                                    if(!$_exists_product_param) {
                                        DB::table('shop_product_params')
                                            ->insert([
                                                'product_id' => $item->id,
                                                'param_id'   => $_param->id,
                                                'value'      => $params[$_param->id]
                                            ]);
                                    }
                                }
                            } else {
                                DB::table($_param->table)
                                    ->where('product_id', $item->id)
                                    ->delete();
                            }
                        } else {
                            DB::table($_param->table)
                                ->where('product_id', $item->id)
                                ->delete();
                            if(isset($params[$_param->id])) {
                                DB::table('shop_product_params')
                                    ->where('param_id', $_param->id)
                                    ->where('product_id', $item->id)
                                    ->where('value', $params[$_param->id])
                                    ->delete();
                            }
                        }
                    });
                }
            }

            return NULL;
        }
    }
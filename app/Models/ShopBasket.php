<?php

    namespace App\Models;

    use App\Library\BaseModel;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;

    class ShopBasket extends BaseModel
    {
        public function __construct()
        {
            parent::__construct();
        }

        public static function get_basket()
        {
            $_response = collect([]);
            if($_basket = basket_load()) {
                $_total = 0;
                $_items = collect([]);
                $_count = 0;
                $_language = wrap()->get('locale');
                $_currency = wrap()->get('currency.current.key', config('os_currency.default_currency'));
                if(isset($_basket['product'])) {
                    foreach($_basket['product'] as $_product) {
                        $_entity = shop_product_load($_product['product'], $_language);
                        $_amount = $_entity->prices_product['price']['original']['price'] * $_product['count'];
                        $_total += $_amount;
                        $_count++;
                        $_items[$_entity->id] = [
                            'type'      => 'product',
                            'id'        => $_entity->relation_entity->id,
                            'title'     => $_entity->title,
                            'alias'     => $_entity->language != DEFAULT_LANGUAGE ? "{$_entity->language}/{$_entity->_alias->alias}" : $_entity->_alias->alias,
                            'language'  => $_entity->language,
                            'preview'   => $_entity->_preview_asset('thumb_shop_product_77', [
                                'only_way' => TRUE
                            ]),
                            'count'     => $_product['count'],
                            'price'     => $_entity->prices_product,
                            'amount'    => transform_price($_amount, $_currency),
                            'sky'       => $_entity->sky,
                            'old_price' => $_entity->old_price,
                        ];
                    }
                }
                if(isset($_basket['product_group'])) {
                    foreach($_basket['product_group'] as $_product_group) {
                        $_entity = ShopProductGroups::find($_product_group['product_group']);
                        $_primary_product = shop_product_load($_entity->product_id, $_language);
                        $_secondary_product = shop_product_load($_entity->related_id, $_language);
                        $_secondary_product->relation_entity->price = $_secondary_product->relation_entity->price-$_secondary_product->relation_entity->price * ($_entity->percent / 100);
                        $_secondary_product->discount_price_product = $_secondary_product->relation_entity->price_view;
                        $_price = $_primary_product->prices_product['price']['format']['price']+$_secondary_product->discount_price_product['price']['format']['price'];
                        $_amount = $_price * $_product_group['count'];
                        $_total += $_amount;
                        $_count++;
                        $_items[$_entity->id] = [
                            'type'     => 'product_group',
                            'id'       => $_entity->id,
                            'products' => [
                                'primary'   => [
                                    'id'        => $_primary_product->relation_entity->id,
                                    'title'     => $_primary_product->title,
                                    'alias'     => $_primary_product->language != DEFAULT_LANGUAGE ? "{$_primary_product->language}/$_primary_product->_alias->alias}" : $_primary_product->_alias->alias,
                                    'language'  => $_primary_product->language,
                                    'preview'   => $_primary_product->_preview_asset('thumb_shop_product_77', [
                                        'only_way' => TRUE
                                    ]),
                                    'price'     => $_primary_product->prices_product,
                                    'old_price' => $_primary_product->old_price,
                                    'sky'       => $_primary_product->sky
                                ],
                                'secondary' => [
                                    'id'             => $_secondary_product->relation_entity->id,
                                    'title'          => $_secondary_product->title,
                                    'alias'          => $_secondary_product->language != DEFAULT_LANGUAGE ? "{$_secondary_product->language}/{$_secondary_product->_alias->alias}" : $_secondary_product->_alias->alias,
                                    'language'       => $_secondary_product->language,
                                    'preview'        => $_secondary_product->_preview_asset('thumb_shop_product_77', [
                                        'only_way' => TRUE
                                    ]),
                                    'price'          => $_secondary_product->prices_product,
                                    'discount_price' => $_secondary_product->discount_price_product,
                                    'old_price'      => $_secondary_product->old_price,
                                    'sky'            => $_secondary_product->sky
                                ]
                            ],
                            'count'    => $_product_group['count'],
                            'amount'   => transform_price($_amount, $_currency),

                        ];
                    }
                }
                $_response = collect([
                    'items' => $_items,
                    'total' => transform_price($_total, $_currency),
                    'count' => $_count
                ]);
            }

            return $_response;
        }

    }

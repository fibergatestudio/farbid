<?php

    namespace App\Library;

    use App\Models\ShopProduct;

    class ECommerce
    {
        public static function view_item_list($items, $list = 'Category Page')
        {
            if (is_array($items) && count($items)) $items = collect($items);
            if (is_object($items) && $items->isNotEmpty()) {
                $_response['items'] = $items->map(function ($_product) use ($list) {
                    $_response_format_product = [
                        'id'    => $_product->id,
                        'name'  => $_product->title,
                        'list'  => $list,
                        'price' => $_product->price_view['price']['format']['price'],
                    ];
                    $_quantity = $_product->count ? $_product->count : NULL;
                    if ($_product->not_limited) $_quantity = NULL;
                    if ($_product->out_of_stock) $_quantity = NULL;
                    if (!is_null($_quantity) || $_quantity > 0) $_response_format_product['quantity'] = $_quantity;

                    return $_response_format_product;
                });

                return json_encode($_response);
            }

            return NULL;
        }

        public static function view_item($item)
        {
            if (is_object($item) && $item->exists) {
                $_response = [
                    'id'    => $item->id,
                    'name'  => $item->title,
                    'price' => $item->base_price,
                ];
                $_quantity = $item->count ? $item->count : NULL;
                if ($item->not_limited) $_quantity = NULL;
                if ($item->out_of_stock) $_quantity = NULL;
                if (!is_null($_quantity) || $_quantity > 0) $_response['quantity'] = $_quantity;

                return json_encode(['items' => $_response]);
            }

            return NULL;
        }

        public static function purchase($order)
        {
            $_default_language = DEFAULT_LANGUAGE;
            $_basket = unserialize($order->data);
            $_shop_name = config("os_seo.settings.{$_default_language}.site_name");
            $_delivery_type = config("os_shop.deliveries.{$order->delivery}.name");
            if ($_basket) {
                //dd($_basket);
                $_products = collect($_basket['items']);
                $_products = $_products->map(function ($_product) {
                    return [
                        'id'       => $_product['id'],
                        'name'     => $_product['title'],
                        'price'    => $_product['amount']['format']['price'],
                        'quantity' => $_product['count']
                    ];
                })->toArray();
                sort($_products);
                $_response = [
                    'transaction_id' => $order->order,
                    'affiliation'    => $_shop_name,
                    'value'          => $_basket['total']['format']['price'],
                    'currency'       => $_basket['total']['currency']['iso_code'],
                    'shipping'       => trans($_delivery_type),
                    'items'          => $_products
                ];

                return json_encode($_response);
            }

            return NULL;
        }

        public static function purchase_buy_one_click($application)
        {
            $_default_language = DEFAULT_LANGUAGE;
            $_shop_name = config("os_seo.settings.{$_default_language}.site_name");
            $_product = ShopProduct::find($application->product_id);
            if ($_product) {
                $_product_price = $_product->price_view;
                $_response = [
                    'transaction_id' => $application->created_at->format('d-m-Y') . '-' . $application->id,
                    'affiliation'    => $_shop_name,
                    'value'          => $_product_price['price']['format']['price'],
                    'currency'       => $_product_price['price']['currency']['iso_code'],
                    'shipping'       => NULL,
                    'items'          => [
                        [
                            'id'       => $_product->id,
                            'name'     => $_product->title,
                            'price'    => $_product_price['price']['format']['price'],
                            'quantity' => 1
                        ]
                    ]
                ];

                return json_encode($_response);
            }

            return NULL;
        }
    }
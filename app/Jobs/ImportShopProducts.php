<?php

    namespace App\Jobs;

    use App\Models\ShopProduct;
    use App\Models\UrlAlias;
    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\Dispatchable;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Queue\SerializesModels;
    use Illuminate\Support\Facades\DB;

    class ImportShopProducts implements ShouldQueue
    {
        use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        protected $data;

        public function __construct($import_data)
        {
            $this->data = $import_data;
        }

        public function handle()
        {
            foreach ($this->data as $_import_data) {
                $_product = ShopProduct::where('sky', $_import_data['sky'])
                    ->first();
                if ($_product) {
                    if (!$_product->mark_discount && $_product->status) {
                        $_change = FALSE;
                        if ($_product->price != $_import_data['price']) $_change = TRUE;
                        if ($_product->not_limited != $_import_data['not_limited']) $_change = TRUE;
                        if ($_product->out_of_stock != $_import_data['out_of_stock']) $_change = TRUE;
                        if ($_change) {
                            unset($_import_data['sky']);
                            unset($_import_data['title']);
                            DB::table('shop_products')
                                ->where('id', $_product->id)
                                ->update([
                                    'price'        => $_import_data['price'],
                                    'base_price'   => $_import_data['price'],
                                    'count'        => NULL,
                                    'not_limited'  => $_import_data['not_limited'],
                                    'out_of_stock' => $_import_data['out_of_stock'],
                                ]);
                        }
                    }
                } else {
                    if ($_import_data['price'] && $_import_data['out_of_stock'] == 0) {
                        $_save = array_merge($_import_data, [
                            'language' => DEFAULT_LANGUAGE,
                            'location' => DEFAULT_LOCATION,
                            'currency' => 'uah',
                            'status'   => 0,
                        ]);
                        $_product = ShopProduct::updateOrCreate([
                            'id' => NULL
                        ], $_save);
                        $_alias = new UrlAlias($_product);
                        $_alias->set();
                    }
                }
            }
        }
    }

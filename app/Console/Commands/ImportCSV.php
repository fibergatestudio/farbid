<?php

    namespace App\Console\Commands;

    use App\Jobs\ImportShopProducts;
    use Carbon\Carbon;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Storage;

    class ImportCSV extends Command
    {
        protected $signature = 'import:csv';

        protected $description = 'Import price.csv';

        public function __construct()
        {
            parent::__construct();
        }

        public function handle()
        {
            $_file_name = '1C_import/price.csv';
            if(Storage::disk('base')->has($_file_name)) {
                $_file = Storage::disk('base')->get($_file_name);
                $_rows = collect(explode("\r\n", $_file));
                // $_rows = collect(explode("\r\n", w1251ToUtf8($_file)));
                if($_rows->isNotEmpty()) {
                    $_add_time = 0;
                    $_rows->chunk(100)->each(function ($_chunk) use (&$_add_time) {
                        $_add_time++;
                        $_data_queue = [];
                        $_chunk->map(function ($_line_data, $_line_id) use (&$_data_queue) {
                            if($_line_data) {
                                $_item = explode(';', $_line_data);
                                if($_item[1]) $_item[1] = preg_replace('/ {2,}/', ' ', $_item[1]);
                                $_data_queue[] = [
                                    'title'        => $_item[1],
                                    'sky'          => $_item[0],
                                    'price'        => $_item[2] ? $_item[2] : 0,
                                    'base_price'   => $_item[2] ? $_item[2] : 0,
                                    'count'        => NULL,
                                    'not_limited'  => $_item[3] && $_item[3] == 1 ? 1 : 0,
                                    'out_of_stock' => $_item[3] && $_item[3] == 1 ? 0 : 1,
                                ];
                            }
                        });
                        $_job = (new ImportShopProducts($_data_queue));
                        dispatch($_job);
                    });
                }
                Storage::disk('base')->delete($_file_name);
            }
        }
    }

<?php

    namespace App\Console\Commands;

    use App\Models\ShopProductDiscountTimer;
    use Carbon\Carbon;
    use Illuminate\Console\Command;

    class CheckedProductDiscountTimer extends Command
    {
        protected $signature = 'product_discount_timer:checked';
        protected $description = '';

        public function __construct()
        {
            parent::__construct();
        }

        public function handle()
        {
            $_current_date = Carbon::now();
            $_items = ShopProductDiscountTimer::where('finish_date', '<=', $_current_date->toDateTimeString())
                ->get();
            if ($_items->isNotEmpty()) {
                $_items->map(function ($_item) {
                    $_item->_deactivate();
                });
            }
        }
    }

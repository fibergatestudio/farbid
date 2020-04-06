<?php

    namespace App\Console\Commands;

    use App\Jobs\ImportShopProducts;
    use Carbon\Carbon;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Storage;

    class ClearCache extends Command
    {
        protected $signature = 'clear_cache';

        public function __construct()
        {
            parent::__construct();
        }

        public function handle()
        {
            Cache::flush();
        }
    }

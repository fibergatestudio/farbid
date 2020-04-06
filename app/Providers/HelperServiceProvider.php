<?php

    namespace App\Providers;

    use App\Library\Wrap;
    use Illuminate\Support\ServiceProvider;

    class HelperServiceProvider extends ServiceProvider
    {
        public function boot()
        {
        }

        public function register()
        {
            $app_path = app_path();
            foreach(glob("{$app_path}/Helpers/*.php") as $_filename) require_once($_filename);
        }
    }

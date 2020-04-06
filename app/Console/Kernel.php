<?php

    namespace App\Console;

    use App\Console\Commands\CheckedProductDiscountTimer;
    use App\Console\Commands\ImportCSV;
    use Illuminate\Console\Scheduling\Schedule;
    use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

    class Kernel extends ConsoleKernel
    {
        protected $commands = [
            CheckedProductDiscountTimer::class,
            ImportCSV::class
        ];

        protected function schedule(Schedule $schedule)
        {
            $schedule->command('product_discount_timer:checked')
                ->everyMinute();
            $schedule->command('import:csv')
                ->everyMinute();
            $schedule->command('clear_cache')
                ->twiceDaily(1,13);
        }

        protected function commands()
        {
            $this->load(__DIR__ . '/Commands');

            require base_path('routes/console.php');
        }
    }

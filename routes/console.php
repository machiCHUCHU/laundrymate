<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\ShopUpdateCommand; 
use Illuminate\Support\Facades\Schedule; 

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('app:shop-update-command')->everyFiveSeconds();
Schedule::command('app:booking-update-command')->everyFiveSeconds();
Schedule::command('app:service-update-command')->everyFiveSeconds();
Schedule::command('app:booking-deduct')->dailyAt('00:05');
//use php artisan schedule:work for continuous background process

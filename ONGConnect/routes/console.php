<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Encerra diariamente demandas abertas cuja data_limite já passou
Schedule::command('demandas:encerrar-expiradas')->dailyAt('00:05');

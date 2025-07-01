<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // if (!Cache::has('initial_db_check')) {
        //     try {
        //         DB::select('SELECT 1');

        //         Log::channel('db_check')->info('Koneksi database awal berhasil', [
        //             'database' => config('database.connections.' . config('database.default') . '.database'),
        //             'host' => config('database.connections.' . config('database.default') . '.host'),
        //             'connection' => config('database.default'),
        //             'timestamp' => now()->toDateTimeString(),
        //             'php_version' => phpversion(),
        //             'laravel_version' => app()->version(),
        //         ]);

        //         Cache::forever('initial_db_check', true);
        //     } catch (\Exception $e) {
        //         Log::channel('db_check')->error('Koneksi database awal gagal', [
        //             'error' => $e->getMessage(),
        //             'database' => config('database.connections.' . config('database.default') . '.database'),
        //             'host' => config('database.connections.' . config('database.default') . '.host'),
        //             'connection' => config('database.default'),
        //             'timestamp' => now()->toDateTimeString(),
        //             'php_version' => phpversion(),
        //             'laravel_version' => app()->version(),
        //             'trace' => $e->getTraceAsString(),
        //         ]);

        //         Cache::forever('initial_db_check', true);
        //     }
        // }
    }
}

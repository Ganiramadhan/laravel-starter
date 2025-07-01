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
        // Periksa apakah log pemeriksaan database awal sudah dibuat
        if (!Cache::has('initial_db_check')) {
            try {
                // Coba jalankan query sederhana untuk menguji koneksi database
                DB::select('SELECT 1');

                // Catat keberhasilan
                Log::channel('db_check')->info('Koneksi database awal berhasil', [
                    'database' => config('database.connections.' . config('database.default') . '.database'),
                    'host' => config('database.connections.' . config('database.default') . '.host'),
                    'connection' => config('database.default'),
                    'timestamp' => now()->toDateTimeString(),
                    'php_version' => phpversion(),
                    'laravel_version' => app()->version(),
                ]);

                // Tandai pemeriksaan sebagai selesai
                Cache::forever('initial_db_check', true);
            } catch (\Exception $e) {
                // Catat kegagalan
                Log::channel('db_check')->error('Koneksi database awal gagal', [
                    'error' => $e->getMessage(),
                    'database' => config('database.connections.' . config('database.default') . '.database'),
                    'host' => config('database.connections.' . config('database.default') . '.host'),
                    'connection' => config('database.default'),
                    'timestamp' => now()->toDateTimeString(),
                    'php_version' => phpversion(),
                    'laravel_version' => app()->version(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Tandai pemeriksaan sebagai selesai untuk menghindari percobaan berulang
                Cache::forever('initial_db_check', true);
            }
        }
    }
}

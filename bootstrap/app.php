<?php

use App\Http\Middleware\CheckInstalled;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Schedule;
use Spatie\Permission\Middleware\PermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__ . '/../routes/channels.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tambahkan middleware global ke grup web
        $middleware->web(append: [
            CheckInstalled::class,
        ]);

        $middleware->alias([
            'check.installed' => CheckInstalled::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Jadwalkan command update status kegiatan setiap menit
        $schedule->command('kegiatan:update-status')->everyMinute();

        // Atau bisa juga menggunakan callback
        // $schedule->call(function () {
        //     \App\Models\Kegiatan::where('status', '!=', 'batal')->get()->each->updateStatusByTime();
        // })->everyMinute();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

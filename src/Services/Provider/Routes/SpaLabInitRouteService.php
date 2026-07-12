<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;

class SpaLabInitRouteService
{
    public static function registerInitRoutes(): void
    {
        Route::get('/spaReactive', function () {
            return view('spa-reactive');
        })->name('spaReactive');
    }
}
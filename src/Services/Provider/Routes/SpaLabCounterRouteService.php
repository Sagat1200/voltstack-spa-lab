<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\ConterExamplePage;

class SpaLabCounterRouteService
{
    public static function registerCounterRoutes(): void
    {
        Route::get('/counterExample', ConterExamplePage::class)
            ->name('counterExample')
            ->runtime([
                'prefetch' => true,
            ]);
    }
}
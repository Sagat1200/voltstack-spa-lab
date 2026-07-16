<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\FormExamplePage;

class SpaLabFormRouteService
{
    public static function registerFormRoutes(): void
    {
        Route::get('/formExample', FormExamplePage::class)->name('formExample')
            ->runtime([
                'prefetch' => true,
            ]);
    }
}
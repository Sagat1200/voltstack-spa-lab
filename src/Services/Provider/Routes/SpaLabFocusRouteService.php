<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Focus\FocusAltPage;
use VoltStack\SPALab\Pages\Focus\FocusPage;

class SpaLabFocusRouteService
{
    public static function registerFocusRoutes(): void
    {
        Route::get('/runtimeFocus', FocusPage::class)->name('runtimeFocus');
        Route::get('/runtimeFocusAlt', FocusAltPage::class)->name('runtimeFocusAlt');
    }
}

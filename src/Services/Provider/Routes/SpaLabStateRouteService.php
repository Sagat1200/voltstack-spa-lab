<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\State\StateAltPage;
use VoltStack\SPALab\Pages\State\StatePage;

class SpaLabStateRouteService
{
    public static function registerStateRoutes(): void
    {
        Route::get('/runtimeState', StatePage::class)->name('state');
        Route::get('/runtimeStateAlt', StateAltPage::class)->name('state-alt');
    }
}
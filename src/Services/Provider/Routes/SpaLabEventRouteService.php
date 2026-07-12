<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Events\EventsPage;

class SpaLabEventRouteService
{
    public static function registerEventRoutes(): void
    {
        Route::get('/runtimeEvents', EventsPage::class)->name('runtimeEvents');
    }
}

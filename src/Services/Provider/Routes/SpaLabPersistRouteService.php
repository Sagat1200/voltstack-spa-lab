<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Persist\PersistAltPage;
use VoltStack\SPALab\Pages\Persist\PersistBridgePage;
use VoltStack\SPALab\Pages\Persist\PersistPage;

class SpaLabPersistRouteService
{
    public static function registerPersistRoutes(): void
    {
        Route::get('/runtimePersist', PersistPage::class)->name('persist');
        Route::get('/runtimePersistBridge', PersistBridgePage::class)->name('persist-bridge');
        Route::get('/runtimePersistAlt', PersistAltPage::class)->name('persist-alt');
    }
}

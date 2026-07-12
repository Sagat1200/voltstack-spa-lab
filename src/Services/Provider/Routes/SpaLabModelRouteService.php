<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Model\ModelLocalAltPage;
use VoltStack\SPALab\Pages\Model\ModelLocalPage;
use VoltStack\SPALab\Pages\Model\ModelSyncAltPage;
use VoltStack\SPALab\Pages\Model\ModelSyncPage;

class SpaLabModelRouteService
{
    public static function registerModelRoutes(): void
    {
        Route::get('/runtimeModelLocal', ModelLocalPage::class)->name('runtimeModelLocal');
        Route::get('/runtimeModelLocalAlt', ModelLocalAltPage::class)->name('runtimeModelLocalAlt');
        Route::get('/runtimeModelSync', ModelSyncPage::class)->name('runtimeModelSync');
        Route::get('/runtimeModelSyncAlt', ModelSyncAltPage::class)->name('runtimeModelSyncAlt');
    }
}

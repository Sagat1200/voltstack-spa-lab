<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Head\RuntimeHeadAppPage;
use VoltStack\SPALab\Pages\Head\RuntimeHeadSpaAltPage;
use VoltStack\SPALab\Pages\Head\RuntimeHeadSpaPage;

class SpaLabHeadRouteService
{
    public static function registerHeadRoutes(): void
    {
        Route::get('/runtimeHead', RuntimeHeadSpaPage::class)->name('runtimeHead');
        Route::get('/runtimeHeadAlt', RuntimeHeadSpaAltPage::class)->name('runtimeHeadAlt');
        Route::get('/runtimeHeadApp', RuntimeHeadAppPage::class)->name('runtimeHeadApp');
    }
}


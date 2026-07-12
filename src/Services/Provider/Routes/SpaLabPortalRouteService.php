<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Portal\PortalAltPage;
use VoltStack\SPALab\Pages\Portal\PortalPage;

class SpaLabPortalRouteService
{
    public static function registerPortalRoutes(): void
    {
        Route::get('/runtimePortal', PortalPage::class)->name('portal');
        Route::get('/runtimePortalAlt', PortalAltPage::class)->name('portal-alt');
    }
}

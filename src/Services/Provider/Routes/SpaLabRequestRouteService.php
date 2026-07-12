<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Request\RequestLabPage;
use VoltStack\SPALab\Pages\Request\RequestLabRetryOncePage;
use VoltStack\SPALab\Pages\Request\RequestLabSlowPage;

class SpaLabRequestRouteService
{
    public static function registerRequestRoutes(): void
    {
        Route::get('/runtimeRequestLab', RequestLabPage::class)->name('request-lab');
        Route::get('/runtimeRequestLabRetryOnce', RequestLabRetryOncePage::class)->name('request-lab-retry-once');
        Route::get('/runtimeRequestLabSlow', RequestLabSlowPage::class)->name('request-lab-slow');
    }
}

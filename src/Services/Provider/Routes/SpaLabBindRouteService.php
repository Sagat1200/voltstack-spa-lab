<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Bind\BindAltPage;
use VoltStack\SPALab\Pages\Bind\BindPage;

class SpaLabBindRouteService
{
    public static function registerBindRoutes(): void
    {
        Route::get('/runtimeBind', BindPage::class)->name('runtimeBind');
        Route::get('/runtimeBindAlt', BindAltPage::class)->name('runtimeBindAlt');
    }
}
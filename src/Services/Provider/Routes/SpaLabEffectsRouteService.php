<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Effects\RuntimeEffectsD2Page;

class SpaLabEffectsRouteService
{
    public static function registerEffectsRoutes(): void
    {
        Route::get('/runtimeEffectsD2', RuntimeEffectsD2Page::class)->name('runtimeEffectsD2');
    }
}


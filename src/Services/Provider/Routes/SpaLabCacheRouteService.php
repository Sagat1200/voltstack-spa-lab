<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Cache\CacheExamplePage;
use VoltStack\SPALab\Pages\Cache\CachePage;
use VoltStack\SPALab\Pages\Cache\CacheResetPage;

class SpaLabCacheRouteService
{
    public static function registerCacheRoutes(): void
    {
        Route::get('/fragmentCache', CachePage::class)->name('fragmentCache');
        Route::get('/fragmentCacheReset', CacheResetPage::class)->name('fragmentCacheReset');
        Route::get('/cacheExample', CacheExamplePage::class)->name('cacheExample');
    }
}

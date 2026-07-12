<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Directives\AdvancedDirectivesPage;

class SpaLabDirectiveRouteService
{
    public static function registerDirectiveRoutes(): void
    {
        Route::get('/runtimeAdvancedDirectives', AdvancedDirectivesPage::class)->name('runtimeAdvancedDirectives');
    }
}

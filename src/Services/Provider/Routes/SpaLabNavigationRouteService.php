<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Navigation\DocumentReloadPage;
use VoltStack\SPALab\Pages\Navigation\PolicyPage;
use VoltStack\SPALab\Pages\Navigation\TransitionAltPage;
use VoltStack\SPALab\Pages\Navigation\TransitionPage;
use VoltStack\SPALab\Pages\Navigation\TransitionProfilePage;

class SpaLabNavigationRouteService
{
    public static function registerNavigationRoutes(): void
    {
        Route::get('/navigationPolicy', PolicyPage::class)->name('navigationPolicy');
        Route::get('/navigationDocumentReload', DocumentReloadPage::class)->name('navigationDocumentReload');
        Route::get('/navigationTransition', TransitionPage::class)->name('navigationTransition');
        Route::get('/navigationTransitionAlt', TransitionAltPage::class)->name('navigationTransitionAlt');
        Route::get('/navigationTransitionProfile', TransitionProfilePage::class)->name('navigationTransitionProfile');
    }
}

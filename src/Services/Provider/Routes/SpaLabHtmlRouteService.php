<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Services\Provider\Routes;

use Quantum\Facades\Route;
use VoltStack\SPALab\Pages\Html\HtmlAltPage;
use VoltStack\SPALab\Pages\Html\HtmlPage;

class SpaLabHtmlRouteService
{
    public static function registerHtmlRoutes(): void
    {
        Route::get('/runtimeHtml', HtmlPage::class)->name('runtimeHtml');
        Route::get('/runtimeHtmlAlt', HtmlAltPage::class)->name('runtimeHtmlAlt');
    }
}

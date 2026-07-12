<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Provider;

use Quantum\Http\Request;
use Quantum\Http\Response;
use Quantum\Facades\Route;
use Quantum\View\ViewFactory;
use VoltStack\Framework\ServiceProvider;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabBindRouteService;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabCacheRouteService;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabCounterRouteService;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabDirectiveRouteService;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabEventRouteService;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabFocusRouteService;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabFormRouteService;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabHtmlRouteService;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabModelRouteService;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabNavigationRouteService;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabPersistRouteService;
use VoltStack\SPALab\Services\Provider\Routes\SpaLabPortalRouteService;

final class SPALabServiceProvider extends ServiceProvider
{
    private const CLIENT_SCRIPT_ROUTE = '/spa-lab/scripts/SpaLab.js';

    public function register(): void
    {
        $this->app->bind('spa-lab', 'VoltStack\SPALab\SPALab');
    }

    public function boot(): void
    {
        $this->registerViewPaths();
        $this->registerClientScriptRoute();

        $enableDemoSpaRoutes = (bool) config(
            'spa-lab.enable_demo_spa_routes',
            in_array($this->app->environment(), ['local', 'testing'], true)
        );

        if ($enableDemoSpaRoutes) {
            SpaLabBindRouteService::registerBindRoutes();
            SpaLabCacheRouteService::registerCacheRoutes();
            SpaLabCounterRouteService::registerCounterRoutes();
            SpaLabDirectiveRouteService::registerDirectiveRoutes();
            SpaLabEventRouteService::registerEventRoutes();
            SpaLabFocusRouteService::registerFocusRoutes();
            SpaLabFormRouteService::registerFormRoutes();
            SpaLabHtmlRouteService::registerHtmlRoutes();
            SpaLabModelRouteService::registerModelRoutes();
            SpaLabNavigationRouteService::registerNavigationRoutes();
            SpaLabPersistRouteService::registerPersistRoutes();
            SpaLabPortalRouteService::registerPortalRoutes();
        }
    }

    private function registerClientScriptRoute(): void
    {
        Route::get(self::CLIENT_SCRIPT_ROUTE, function (Request $request): Response {
            $scriptPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'SpaLab.js';

            if (! is_file($scriptPath) || ! is_readable($scriptPath)) {
                return response('SpaLab.js not found.', 404, [
                    'Content-Type' => 'text/plain; charset=UTF-8',
                    'Cache-Control' => 'no-store',
                ]);
            }

            $lastModifiedAt = filemtime($scriptPath);
            $etag = '"spa-lab-script-' . md5($scriptPath . '|' . (string) $lastModifiedAt) . '"';

            if ($this->matchesConditionalRequest($request, $etag, $lastModifiedAt)) {
                return response('', 304, $this->scriptHeaders($etag, $lastModifiedAt));
            }

            $contents = file_get_contents($scriptPath);

            if ($contents === false) {
                return response('Unable to read SpaLab.js.', 500, [
                    'Content-Type' => 'text/plain; charset=UTF-8',
                    'Cache-Control' => 'no-store',
                ]);
            }

            return response($contents, 200, $this->scriptHeaders($etag, $lastModifiedAt));
        })->name('spa-lab.client-script');
    }

    private function registerViewPaths(): void
    {
        $resourcePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'resources';

        if (! is_dir($resourcePath)) {
            return;
        }

        $views = $this->app->make(ViewFactory::class);
        $paths = $views->paths();

        if (in_array($resourcePath, $paths, true)) {
            return;
        }

        $paths[] = $resourcePath;
        $views->setPaths($paths);
    }

    /**
     * @return array<string, string>
     */
    private function scriptHeaders(string $etag, int|false $lastModifiedAt): array
    {
        $headers = [
            'Content-Type' => 'application/javascript; charset=UTF-8',
            'Cache-Control' => 'public, max-age=0, must-revalidate',
            'ETag' => $etag,
            'X-Content-Type-Options' => 'nosniff',
        ];

        if (is_int($lastModifiedAt)) {
            $headers['Last-Modified'] = gmdate('D, d M Y H:i:s', $lastModifiedAt) . ' GMT';
        }

        return $headers;
    }

    private function matchesConditionalRequest(Request $request, string $etag, int|false $lastModifiedAt): bool
    {
        $ifNoneMatch = trim((string) $request->header('If-None-Match', ''));

        if ($ifNoneMatch !== '' && $ifNoneMatch === $etag) {
            return true;
        }

        $ifModifiedSince = trim((string) $request->header('If-Modified-Since', ''));

        if ($ifModifiedSince === '' || ! is_int($lastModifiedAt)) {
            return false;
        }

        $requestedTimestamp = strtotime($ifModifiedSince);

        return $requestedTimestamp !== false && $requestedTimestamp >= $lastModifiedAt;
    }
}
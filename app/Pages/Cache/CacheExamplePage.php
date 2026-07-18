<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Cache;

use VoltStack\Runtime\Component\Component;

final class CacheExamplePage extends Component
{
    public string $title = 'Cache Example';
    public string $subtitle = 'Laboratorio de control e invalidacion de cache SPA';

    /**
     * @var array<int, array{label: string, href: string, cache: string, description: string, expectation: string}>
     */
    public array $navigationExamples = [
        [
            'label' => 'Recarga controlada',
            'href' => '/counterExample',
            'cache' => 'reload',
            'description' => 'Omite la entrada actual y fuerza una lectura nueva desde red antes de reutilizar la ruta.',
            'expectation' => 'Debe producir un miss inicial y almacenar una respuesta fresca.',
        ],
        [
            'label' => 'Sin almacenamiento',
            'href' => '/formExample',
            'cache' => 'no-store',
            'description' => 'Evita leer y guardar cache para esa navegacion o prefetch asociado.',
            'expectation' => 'Cada visita debe resolverse sin persistir la respuesta HTML en memoria.',
        ],
        [
            'label' => 'TTL extendido',
            'href' => '/counterExample',
            'cache' => 'ttl=15s',
            'description' => 'Mantiene la entrada viva mas tiempo que el TTL global del runtime.',
            'expectation' => 'Reutiliza la respuesta durante 15 segundos salvo invalidacion explicita.',
        ],
        [
            'label' => 'Invalidacion previa',
            'href' => '/formExample',
            'cache' => 'invalidate',
            'description' => 'Borra primero la entrada de esa URL y luego resuelve la navegacion normal.',
            'expectation' => 'Debe invalidar la entrada previa y volver a poblarla si la respuesta es almacenable.',
        ],
    ];

    /** @var array<int, string> */
    public array $documentControls = [
        '<meta name="volt-cache-control" content="no-store">',
        '<meta name="volt-cache-control" content="reload ttl=15s">',
        '<meta name="volt:navigation-cache" content="invalidate">',
    ];

    /** @var array<int, string> */
    public array $runtimeEvents = [
        'volt:cache-hit',
        'volt:cache-miss',
        'volt:cache-store',
        'volt:cache-invalidate',
        'volt:cache-clear',
    ];

    /** @var array<int, string> */
    public array $manualInvalidationExamples = [
        "document.dispatchEvent(new CustomEvent('volt:navigation-cache-invalidate', {\n  detail: { url: '/formExample', reason: 'manual' },\n}));",
        "document.dispatchEvent(new CustomEvent('volt:navigation-cache-invalidate', {\n  detail: { reason: 'manual' },\n}));",
    ];
}

?>

@extends('layouts.spa')

@section('head')
@if (tailwind_vite()->isProduction())
{!! tailwind_vite()->render('resources/js/cacheExample.js') !!}
@endif
@endsection

@section('content')

<section class="p-8 rounded-2xl border shadow-2xl border-slate-800 bg-slate-900/70 shadow-slate-950/30">
    <span
        class="inline-flex rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-medium uppercase tracking-[0.2em] text-cyan-300">
        Runtime Cache Demo
    </span>

    <h1 class="mt-6 text-4xl font-semibold tracking-tight text-white">{{ $title }}</h1>
    <p class="mt-4 max-w-3xl text-base leading-7 text-slate-300">{{ $subtitle }}</p>
    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-400">
        Esta pantalla concentra ejemplos concretos del nuevo contrato de cache SPA. Cada tarjeta navega con
        <code>volt:navigate</code> y aplica una politica distinta mediante <code>volt:cache</code>.
    </p>

    <article class="overflow-hidden mt-8 rounded-2xl border border-slate-800 bg-slate-950/60">
        <div class="px-5 py-4 border-b border-slate-800">
            <h2 class="text-lg font-semibold text-white">Tabla comparativa rapida</h2>
            <p class="mt-2 text-sm leading-6 text-slate-400">
                Resumen operativo de cada modo para saber si lee cache, si guarda una nueva respuesta y si invalida la
                entrada antes de navegar.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-slate-800 text-slate-300">
                <thead class="bg-slate-900/80 text-xs uppercase tracking-[0.16em] text-slate-400">
                    <tr>
                        <th class="px-5 py-3 text-left">Modo</th>
                        <th class="px-5 py-3 text-left">Lee cache</th>
                        <th class="px-5 py-3 text-left">Guarda cache</th>
                        <th class="px-5 py-3 text-left">Invalida antes</th>
                        <th class="px-5 py-3 text-left">Uso recomendado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <tr class="bg-slate-950/30">
                        <td class="px-5 py-4 align-top"><code class="text-cyan-300">reload</code></td>
                        <td class="px-5 py-4 text-rose-300 align-top">No</td>
                        <td class="px-5 py-4 text-emerald-300 align-top">Si</td>
                        <td class="px-5 py-4 text-amber-300 align-top">Si, sobre esa URL</td>
                        <td class="px-5 py-4 align-top text-slate-400">Pantallas muy dinamicas donde primero importa
                            frescura y luego reuso.</td>
                    </tr>
                    <tr class="bg-slate-950/10">
                        <td class="px-5 py-4 align-top"><code class="text-cyan-300">no-store</code></td>
                        <td class="px-5 py-4 text-rose-300 align-top">No</td>
                        <td class="px-5 py-4 text-rose-300 align-top">No</td>
                        <td class="px-5 py-4 text-amber-300 align-top">Puede limpiar la previa</td>
                        <td class="px-5 py-4 align-top text-slate-400">Contenido sensible, efimero o donde no quieres
                            HTML persistido en memoria.</td>
                    </tr>
                    <tr class="bg-slate-950/30">
                        <td class="px-5 py-4 align-top"><code class="text-cyan-300">invalidate</code></td>
                        <td class="px-5 py-4 text-rose-300 align-top">No, en esa visita</td>
                        <td class="px-5 py-4 text-emerald-300 align-top">Si</td>
                        <td class="px-5 py-4 text-emerald-300 align-top">Si</td>
                        <td class="px-5 py-4 align-top text-slate-400">Forzar una renovacion limpia antes de repoblar
                            la URL objetivo.</td>
                    </tr>
                    <tr class="bg-slate-950/10">
                        <td class="px-5 py-4 align-top"><code class="text-cyan-300">ttl=15s</code></td>
                        <td class="px-5 py-4 text-emerald-300 align-top">Si</td>
                        <td class="px-5 py-4 text-emerald-300 align-top">Si</td>
                        <td class="px-5 py-4 align-top text-slate-400">No</td>
                        <td class="px-5 py-4 align-top text-slate-400">Rutas relativamente estables donde conviene
                            ampliar la ventana de reuso.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>

    <div class="grid gap-4 mt-8 lg:grid-cols-2">
        @foreach ($navigationExamples as $example)
        <article class="p-5 rounded-2xl border border-slate-800 bg-slate-950/60">
            <div class="flex gap-4 justify-between items-start">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ $example['label'] }}</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-400">{{ $example['description'] }}</p>
                </div>
                <code class="px-2 py-1 text-xs text-cyan-300 rounded bg-slate-900">{{ $example['cache'] }}</code>
            </div>

            <p class="mt-4 text-xs leading-6 text-slate-500">
                Resultado esperado: {{ $example['expectation'] }}
            </p>

            <div class="flex flex-wrap gap-3 mt-5">
                <a href="{{ $example['href'] }}" volt:navigate volt:prefetch="none" volt:cache="{{ $example['cache'] }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-cyan-200 rounded-lg border transition border-cyan-400/30 bg-slate-950/70 hover:border-cyan-300 hover:text-white">
                    Navegar a {{ $example['href'] }}
                </a>
                <a href="{{ $example['href'] }}" volt:navigate volt:prefetch="hover"
                    volt:cache="{{ $example['cache'] }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition border-slate-700 bg-slate-950/70 text-slate-200 hover:border-slate-500 hover:text-white">
                    Hover + prefetch
                </a>
            </div>

            <pre
                class="mt-4 overflow-x-auto rounded-xl border border-slate-800 bg-slate-900/70 p-3 text-[11px] leading-5 text-slate-400">&lt;a href="{{ $example['href'] }}" volt:navigate volt:cache="{{ $example['cache'] }}"&gt;...&lt;/a&gt;</pre>
        </article>
        @endforeach
    </div>

    <div class="mt-8 grid gap-4 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
        <article class="p-5 rounded-2xl border border-slate-800 bg-slate-950/60">
            <h2 class="text-lg font-semibold text-white">Control desde el documento destino</h2>
            <p class="mt-2 text-sm leading-6 text-slate-400">
                El runtime tambien puede ajustar la politica efectiva desde el <code>head</code> de la respuesta
                destino, usando metadatos dedicados.
            </p>

            <div class="grid gap-3 mt-4">
                @foreach ($documentControls as $control)
                <pre
                    class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-900/70 p-3 text-[11px] leading-5 text-slate-400">{{ $control }}</pre>
                @endforeach
            </div>
        </article>

        <article class="p-5 rounded-2xl border border-slate-800 bg-slate-950/60">
            <h2 class="text-lg font-semibold text-white">Eventos del runtime</h2>
            <p class="mt-2 text-sm leading-6 text-slate-400">
                Puedes observar estos eventos en el inspector global del layout o desde la consola del navegador.
            </p>

            <ul class="grid gap-2 mt-4 text-sm text-slate-300">
                @foreach ($runtimeEvents as $eventName)
                <li class="px-3 py-2 rounded-lg border border-slate-800 bg-slate-900/70">
                    <code>{{ $eventName }}</code>
                </li>
                @endforeach
            </ul>
        </article>
    </div>

    <article class="p-5 mt-8 rounded-2xl border border-slate-800 bg-slate-950/60">
        <div class="flex flex-col gap-2 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-white">Monitor en vivo de `volt:cache-*`</h2>
                <p class="mt-2 text-sm leading-6 text-slate-400">
                    Usa los enlaces de arriba y observa como cambian los contadores y el detalle del ultimo evento
                    emitido por el runtime de navegacion.
                </p>
            </div>
            <p class="text-xs text-slate-500">Los enlaces "Navegar a..." usan <code>volt:prefetch="none"</code>; el
                prefetch queda reservado a los botones "Hover + prefetch".</p>
        </div>

        <div class="grid gap-4 mt-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($runtimeEvents as $eventName)
            <article data-volt-hook-card="{{ $eventName }}"
                class="flex flex-col p-4 h-full rounded-xl border border-slate-800 bg-slate-900/70">
                <div class="flex gap-3 justify-between items-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-300">{{ $eventName }}</p>
                    <span data-volt-hook-source
                        class="inline-flex items-center rounded-full border border-slate-700 bg-slate-950/80 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-400">sin
                        source</span>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-4 text-xs">
                    <div class="p-3 rounded-lg border border-slate-800 bg-slate-950/70">
                        <span class="block text-slate-500">Veces disparado</span>
                        <strong data-volt-hook-count class="block mt-1 text-lg text-white">0</strong>
                    </div>
                    <div class="p-3 rounded-lg border border-slate-800 bg-slate-950/70">
                        <span class="block text-slate-500">Ultima vez</span>
                        <strong data-volt-hook-last class="block mt-1 text-sm text-white">-</strong>
                    </div>
                </div>
                <pre data-volt-hook-detail
                    class="mt-4 min-h-24 flex-1 overflow-x-auto rounded-lg border border-slate-800 bg-slate-950/60 p-3 text-[11px] leading-5 text-slate-400">{"esperando":"evento"}</pre>
            </article>
            @endforeach
        </div>

        <div class="mt-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-300">Log reciente de cache</h3>
                <span class="text-xs text-slate-500">Se conservan los ultimos 8 eventos visibles en esta seccion.</span>
            </div>
            <ol data-volt-hook-log data-volt-hook-log-filter="cache-only"
                class="grid gap-3 mt-4 max-h-80 overflow-y-auto pr-1"
                style="overflow-anchor:none;"></ol>
        </div>
    </article>

    <article class="p-5 mt-8 rounded-2xl border border-slate-800 bg-slate-950/60">
        <h2 class="text-lg font-semibold text-white">Invalidacion manual</h2>
        <p class="mt-2 text-sm leading-6 text-slate-400">
            Si quieres forzar limpieza selectiva o total desde frontend, puedes emitir el evento
            <code>volt:navigation-cache-invalidate</code>.
        </p>

        <div class="flex flex-wrap gap-3 mt-4">
            <button type="button" data-volt-cache-invalidate-url="/counterExample"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-cyan-200 rounded-lg border transition border-cyan-400/30 bg-slate-950/70 hover:border-cyan-300 hover:text-white">
                Invalidar /counterExample
            </button>
            <button type="button" data-volt-cache-invalidate-url="/formExample"
                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition border-slate-700 bg-slate-950/70 text-slate-200 hover:border-slate-500 hover:text-white">
                Invalidar /formExample
            </button>
            <button type="button" data-volt-cache-invalidate-all="true"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-rose-200 rounded-lg border transition border-rose-500/30 bg-rose-950/20 hover:border-rose-400 hover:text-white">
                Limpiar toda la cache SPA
            </button>
        </div>

        <p data-volt-cache-action-status class="mt-3 text-xs leading-6 text-slate-500">
            Usa estos botones para emitir el evento de invalidacion sin salir de esta pantalla.
        </p>

        <div class="grid gap-3 mt-4">
            @foreach ($manualInvalidationExamples as $snippet)
            <pre
                class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-900/70 p-3 text-[11px] leading-5 text-slate-400">{{ $snippet }}</pre>
            @endforeach
        </div>
    </article>

    <div class="flex flex-wrap gap-3 mt-8">
        <a href="{{ route('spaReactive') }}" volt:navigate volt:prefetch="none"
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
        <a href="/counterExample" volt:navigate volt:prefetch="none"
            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition border-slate-700 bg-slate-950/70 text-slate-200 hover:border-slate-500 hover:text-white">
            Ir a /counterExample
        </a>
        <a href="/formExample" volt:navigate volt:prefetch="none"
            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition border-slate-700 bg-slate-950/70 text-slate-200 hover:border-slate-500 hover:text-white">
            Ir a /formExample
        </a>
    </div>
</section>
@endsection

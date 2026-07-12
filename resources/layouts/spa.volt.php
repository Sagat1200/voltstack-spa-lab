<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" data-volt-head-key="document-charset">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" data-volt-head-key="document-viewport">
    <title>VoltStack SPA Reactive Lab</title>
    @yield('head')
    @tailwind-vite
</head>

<body data-volt-document="spa" data-volt-navigation-mode="auto" data-volt-layout="spa"
    class="min-h-screen bg-slate-950 text-slate-100">
    <div id="volt-portals-root" class="pointer-events-none fixed inset-0 z-[70]">
        <div id="volt-banners-root" class="pointer-events-none fixed inset-x-0 top-4 z-[72] flex justify-center px-4">
        </div>
        <div id="volt-modals-root" class="pointer-events-none fixed inset-0 z-[74]"></div>
        <div id="volt-drawers-root"
            class="pointer-events-none fixed inset-y-0 right-0 z-[76] flex max-w-full justify-end"></div>
    </div>
    <main class="px-6 py-10 mx-auto max-w-5xl">
        @yield('content')

        <section class="p-6 mt-10 rounded-2xl border shadow-2xl border-slate-800 bg-slate-900/70 shadow-slate-950/30">
            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(280px,0.9fr)] xl:items-start">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300">Runtime Hook Inspector</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white">Hooks en vivo del frontend reactivo</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-400">
                        Usa <code>/counterExample</code> y <code>/formExample</code> para disparar patches reactivos y
                        navega con enlaces <code>volt:navigate</code> para ver los eventos de navegacion.
                    </p>
                </div>
                <div class="p-4 text-xs leading-6 rounded-xl border border-slate-800 bg-slate-950/70 text-slate-400">
                    <p class="font-semibold uppercase tracking-[0.18em] text-slate-300">Resumen rapido</p>
                    <div class="mt-3 space-y-2">
                        <p><code>volt:before-patch</code> y <code>volt:after-patch</code> se disparan durante acciones
                            reactivas.</p>
                        <p><code>volt:before-effect</code> y <code>volt:after-effect</code> detallan cada effect
                            aplicado.</p>
                        <p><code>volt:before-enter</code>, <code>volt:after-enter</code>,
                            <code>volt:before-leave</code>, <code>volt:after-leave</code>,
                            <code>volt:before-update</code> y mas hooks por fase aparecen en tarjetas y en el log
                            reciente.
                        </p>
                        <p>El log tambien muestra eventos manuales como <code>demo.saved</code> y
                            <code>demo.counter.incremented</code> emitidos desde <code>dispatch.event</code>.
                        </p>
                        <p><code>volt:before-navigate</code> y <code>volt:navigated</code> se disparan durante SPA
                            navigation.</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 mt-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ([
                'volt:before-patch' => 'Antes de aplicar effects o fallback HTML.',
                'volt:after-patch' => 'Despues de restaurar foco, scroll y snapshot.',
                'volt:before-effect' => 'Antes de ejecutar cada effect individual.',
                'volt:after-effect' => 'Despues de resolver cada effect y su resultado.',
                'volt:before-navigate' => 'Antes de mutar el body en navegacion SPA.',
                'volt:navigated' => 'Despues de actualizar history y vista.',
                'volt:before-enter' => 'Antes de ejecutar la entrada visual del documento ya renderizado.',
                'volt:after-enter' => 'Despues de completar la entrada visual del documento destino.',
                'volt:before-leave' => 'Antes de ejecutar la salida visual previa al swap del body.',
                'volt:after-leave' => 'Despues de completar la salida visual antes del render destino.',
                ] as $hook => $description)
                <article data-volt-hook-card="{{ $hook }}"
                    class="flex flex-col p-4 h-full rounded-xl border border-slate-800 bg-slate-950/70">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-300">{{ $hook }}</p>
                    <p class="mt-2 text-sm text-slate-400">{{ $description }}</p>
                    <div class="grid grid-cols-2 gap-3 mt-4 text-xs">
                        <div class="p-3 rounded-lg border border-slate-800 bg-slate-900/70">
                            <span class="block text-slate-500">Veces disparado</span>
                            <strong data-volt-hook-count class="block mt-1 text-lg text-white">0</strong>
                        </div>
                        <div class="p-3 rounded-lg border border-slate-800 bg-slate-900/70">
                            <span class="block text-slate-500">Ultima vez</span>
                            <strong data-volt-hook-last class="block mt-1 text-sm text-white">-</strong>
                        </div>
                    </div>
                    <pre data-volt-hook-detail
                        class="mt-4 min-h-24 flex-1 overflow-x-auto rounded-lg border border-slate-800 bg-slate-900/60 p-3 text-[11px] leading-5 text-slate-400">{"esperando":"evento"}</pre>
                </article>
                @endforeach
            </div>

            <div class="mt-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-300">Log reciente</h3>
                    <span class="text-xs text-slate-500">Se conservan los ultimos 12 eventos.</span>
                </div>
                <ol data-volt-hook-log class="grid gap-3 mt-4"></ol>
            </div>
        </section>
    </main>
    <script src="{{ route('spa-lab.client-script') }}" defer></script>
</body>

</html>
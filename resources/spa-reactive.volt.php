@extends('layouts.spa')

@section('content')
<section class="p-8 rounded-2xl border shadow-2xl border-slate-800 bg-slate-900/70 shadow-slate-950/30">
    <span
        class="inline-flex rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-medium uppercase tracking-[0.2em] text-cyan-300">
        Sistema de Analisis de Runtime SPA Full Reactive
    </span>



    <div class="grid gap-5 mt-8 md:grid-cols-2 xl:grid-cols-3">
        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-cyan-400/40 hover:bg-slate-950"
            href="/counterExample" volt:navigate volt:prefetch>
            <strong class="block text-lg text-white">/counterExample</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Componente reactivo inline con <code
                    class="px-2 py-1 rounded bg-red-950 text-slate-200">volt-click</code>, <code
                    class="px-2 py-1 rounded bg-slate-800 text-slate-200">data-volt-target</code> y transiciones
                declarativas, ideal para ver <code
                    class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt-click</code>,
                hooks por effect y actualizaciones finas del contador.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-cyan-400/40 hover:bg-slate-950"
            href="/formExample" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/formExample</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Demo aislada del formulario con <code
                    class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:model</code>, <code
                    class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:submit</code>, estados
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">dirty</code>, <code
                    class="px-2 py-1 rounded bg-slate-800 text-slate-200">error</code> y <code
                    class="px-2 py-1 rounded bg-slate-800 text-slate-200">success</code>.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-cyan-400/40 hover:bg-slate-950"
            href="/cacheExample" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/cacheExample</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio visual del nuevo control de cache SPA
                con
                ejemplos de <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">reload</code>, <code
                    class="px-2 py-1 rounded bg-slate-800 text-slate-200">no-store</code>, <code
                    class="px-2 py-1 rounded bg-slate-800 text-slate-200">invalidate</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">ttl=15s</code>.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-cyan-400/40 hover:bg-slate-950"
            href="/navigationPolicy" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/navigationPolicy</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio de politicas de navegacion con
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">auto</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">spa</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">reload</code> por enlace o por
                documento.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-cyan-400/40 hover:bg-slate-950"
            href="/navigationTransition" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/navigationTransition</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Demo del nuevo flujo
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">leave -&gt; swap -&gt; enter</code> para
                navegacion SPA con politicas de transicion por enlace o por documento.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-cyan-400/40 hover:bg-slate-950"
            href="/runtimeState" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimeState</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio del nuevo
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">window.Volt.state</code> para validar
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">client state</code> por URL y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">shared state</code> global entre
                pantallas SPA, la nueva sincronizacion selectiva con el backend y el MVP de
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:text</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:show</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:if</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:for</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:class</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:attr</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:style</code>.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-sky-400/40 hover:bg-slate-950"
            href="/runtimeAdvancedDirectives" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimeAdvancedDirectives</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Demo enfocada en la nueva sintaxis del runtime:
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">??</code> para
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:text</code>, condiciones con
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">&amp;&amp;</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">||</code> y parentesis, y reglas multiples
                con <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">|</code> en
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:class</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:attr</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:style</code>.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-orange-400/40 hover:bg-slate-950"
            href="/runtimeEvents" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimeEvents</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio de eventos para validar
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:on</code> con
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">input</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">change</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">click.once</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">click.self</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">keydown.enter.prevent</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">submit.prevent</code>, junto al MVP de
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:dispatch</code> y sus
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">CustomEvent</code>.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-cyan-400/40 hover:bg-slate-950"
            href="/runtimeFocus" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimeFocus</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio para validar
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:focus</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:autofocus.when</code> con foco
                reactivo, paneles condicionales y navegación SPA hacia
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">/runtimeFocusAlt</code>.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-violet-400/40 hover:bg-slate-950"
            href="/runtimePortal" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimePortal</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio del MVP de
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:portal</code> para validar banner,
                modal y drawer portalizados hacia roots globales del layout y su remount en
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">/runtimePortalAlt</code>.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-sky-400/40 hover:bg-slate-950"
            href="/runtimeHtml" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimeHtml</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio para validar
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:html</code> con contenido
                enriquecido, reemplazo completo de subarbol y reactivacion de directivas internas hacia
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">/runtimeHtmlAlt</code>.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-emerald-400/40 hover:bg-slate-950"
            href="/runtimeBind" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimeBind</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio para validar
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:bind</code> sobre
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">value</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">checked</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">disabled</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">href</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">src</code> con navegación a
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">/runtimeBindAlt</code>.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-green-400/40 hover:bg-slate-950"
            href="/runtimeModelLocal" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimeModelLocal</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio para validar
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:model.local</code> con input,
                textarea, checkbox y select ligados solo al store frontend y navegación a
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">/runtimeModelLocalAlt</code>.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-sky-400/40 hover:bg-slate-950"
            href="/runtimeModelSync" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimeModelSync</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio para validar
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:model.sync</code> con escritura
                optimista en <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">window.Volt.state</code>,
                sincronización backend con <code
                    class="px-2 py-1 rounded bg-slate-800 text-slate-200">__volt_sync__</code> y
                navegación hacia <code
                    class="px-2 py-1 rounded bg-slate-800 text-slate-200">/runtimeModelSyncAlt</code>.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-rose-400/40 hover:bg-slate-950"
            href="/runtimeRequestLab" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimeRequestLab</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio de QA para requests del runtime con
                escenarios reproducibles de
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">timeout</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">protocol-error</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">http-error</code>,
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">network-error</code> y
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">stale</code> en acciones y navegación.
            </span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-emerald-400/40 hover:bg-slate-950"
            href="/runtimeRequestLabRetryOnce" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimeRequestLabRetryOnce</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Destino de QA para validar el nuevo
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">retry</code> automático de navegación
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">GET</code>: falla una vez con error
                transitorio y carga correctamente en el siguiente intento del runtime.</span>
        </a>

        <a class="flex h-full min-h-[220px] flex-col rounded-xl border border-slate-800 bg-slate-950/60 p-5 transition hover:border-pink-400/40 hover:bg-slate-950"
            href="/runtimePersist" volt:navigate volt:prefetch="none">
            <strong class="block text-lg text-white">/runtimePersist</strong>
            <span class="block mt-3 text-sm leading-6 text-slate-400">Laboratorio del MVP de
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">volt:persist</code> para validar que un
                nodo vivo sobreviva una pantalla intermedia sin target y se reinyecte luego en
                <code class="px-2 py-1 rounded bg-slate-800 text-slate-200">/runtimePersistAlt</code>.</span>
        </a>
    </div>

    <section class="p-6 mt-8 rounded-2xl border border-slate-800 bg-slate-950/60">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300">Guia Rapida</p>
                <h2 class="mt-2 text-xl font-semibold text-white">Cuando usar cada modo de <code>volt:cache</code></h2>
            </div>
            <a href="/cacheExample" volt:navigate volt:prefetch="none"
                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition border-slate-700 bg-slate-900/80 text-slate-200 hover:border-slate-500 hover:text-white">
                Abrir laboratorio de cache
            </a>
        </div>

        <div class="grid gap-4 mt-6 md:grid-cols-2 xl:grid-cols-4">
            <article class="p-4 rounded-xl border border-slate-800 bg-slate-900/70">
                <code class="px-2 py-1 text-xs text-cyan-300 rounded bg-slate-800">reload</code>
                <p class="mt-3 text-sm font-medium text-white">Usalo cuando necesitas frescura inmediata.</p>
                <p class="mt-2 text-sm leading-6 text-slate-400">
                    Ideal para pantallas que cambian mucho y no deben reutilizar la entrada actual antes de volver a
                    consultar la red.
                </p>
            </article>

            <article class="p-4 rounded-xl border border-slate-800 bg-slate-900/70">
                <code class="px-2 py-1 text-xs text-cyan-300 rounded bg-slate-800">no-store</code>
                <p class="mt-3 text-sm font-medium text-white">Usalo cuando no quieres persistir HTML en memoria.</p>
                <p class="mt-2 text-sm leading-6 text-slate-400">
                    Conviene para contenido muy sensible, efimero o cuando una respuesta stale puede generar confusion.
                </p>
            </article>

            <article class="p-4 rounded-xl border border-slate-800 bg-slate-900/70">
                <code class="px-2 py-1 text-xs text-cyan-300 rounded bg-slate-800">invalidate</code>
                <p class="mt-3 text-sm font-medium text-white">Usalo cuando quieres borrar primero la entrada actual.
                </p>
                <p class="mt-2 text-sm leading-6 text-slate-400">
                    Sirve para forzar una renovacion limpia de una URL concreta antes de dejar que el runtime la vuelva
                    a poblar.
                </p>
            </article>

            <article class="p-4 rounded-xl border border-slate-800 bg-slate-900/70">
                <code class="px-2 py-1 text-xs text-cyan-300 rounded bg-slate-800">ttl=15s</code>
                <p class="mt-3 text-sm font-medium text-white">Usalo cuando quieres ajustar la ventana de reuso.</p>
                <p class="mt-2 text-sm leading-6 text-slate-400">
                    Es util para rutas mas estables donde el TTL global de 5 segundos se queda corto o demasiado
                    agresivo.
                </p>
            </article>
        </div>
    </section>
</section>
@endsection
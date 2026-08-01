<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Head;

use VoltStack\Runtime\Component\Component;

final class RuntimeHeadAppPage extends Component
{
    public string $title = 'Runtime Head Lab (APP layout)';
}

?>

@extends('layouts.app')

@section('head')
<meta name="runtime-head-lab" content="app" data-volt-head-key="runtime-head-lab-meta">
<meta name="runtime-head-lab-screen" content="runtimeHeadApp" data-volt-head-key="runtime-head-lab-screen">
<script data-volt-head-key="runtime-head-lab-inline">
    window.__voltRuntimeHeadLab = window.__voltRuntimeHeadLab || {
        bootedAt: Date.now()
    };
</script>
@endsection

@section('content')
<div class="px-6 py-10 mx-auto max-w-5xl">
    <section class="p-6 rounded-2xl border border-slate-800 bg-slate-950/60 text-slate-100">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-300">Layout y Head</p>
        <h1 class="mt-2 text-3xl font-semibold text-white">{{ $title }}</h1>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-400">
            Esta página usa <code>@extends('layouts.app')</code>. Desde un documento SPA con layout <code>spa</code>,
            el runtime debe hacer fallback automático (reload) al intentar llegar aquí.
        </p>
    </section>

    <section class="p-6 mt-6 rounded-2xl border border-slate-800 bg-slate-950/60 text-slate-100">
        <h2 class="text-lg font-semibold text-white">Validación rápida</h2>
        <div class="grid gap-3 mt-4 text-sm text-slate-300 md:grid-cols-3">
            <div class="p-4 rounded-xl border border-slate-800 bg-slate-900/40">
                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">layout</span>
                <strong data-runtime-check="head-lab-layout" class="block mt-2 text-lg text-white">-</strong>
            </div>
            <div class="p-4 rounded-xl border border-slate-800 bg-slate-900/40">
                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">charset/viewport</span>
                <strong data-runtime-check="head-lab-core-meta" class="block mt-2 text-lg text-white">-</strong>
            </div>
            <div class="p-4 rounded-xl border border-slate-800 bg-slate-900/40">
                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">tailwind check</span>
                <strong data-runtime-check="head-lab-style" class="block mt-2 text-lg text-white">-</strong>
            </div>
        </div>
        <div class="inline-flex gap-3 items-center px-4 py-3 mt-5 text-sm text-emerald-100 rounded-xl border border-emerald-500/20 bg-emerald-500/10">
            <span class="inline-flex w-3 h-3 bg-emerald-400 rounded-full" data-runtime-head-style-dot></span>
            <span>Si el punto es verde y el texto dice ok, el CSS se mantuvo.</span>
        </div>
    </section>

    <section class="p-6 mt-6 rounded-2xl border border-slate-800 bg-slate-950/60 text-slate-100">
        <h2 class="text-lg font-semibold text-white">Navegar dentro de layout app</h2>
        <p class="mt-2 text-sm leading-6 text-slate-400">
            Prueba volver a home sin perder estilos (navegación SPA dentro del mismo layout).
        </p>
        <div class="flex flex-wrap gap-3 mt-4">
            <a href="/" volt:navigate
                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-xl border transition border-slate-700 bg-slate-900/60 text-slate-200 hover:border-slate-500">
                Volver a /
            </a>
            <a href="/runtimeHead" volt:navigate
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-orange-200 rounded-xl border transition border-orange-500/30 bg-orange-500/10 hover:border-orange-400/60 hover:bg-orange-500/20">
                Ir a /runtimeHead (espera reload)
            </a>
            <a href="{{ route('spaReactive') }}" volt:navigate
                class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-xl border transition border-slate-700 bg-slate-900/60 text-slate-200 hover:border-slate-500">
                Volver a /spaReactive
            </a>
        </div>
    </section>
</div>

<script>
    (() => {
        const layout = document.body ? document.body.getAttribute('data-volt-layout') : null;
        const layoutLabel = document.querySelector('[data-runtime-check="head-lab-layout"]');
        const coreMetaLabel = document.querySelector('[data-runtime-check="head-lab-core-meta"]');
        const styleLabel = document.querySelector('[data-runtime-check="head-lab-style"]');

        if (layoutLabel) {
            layoutLabel.textContent = layout || '(missing)';
        }

        if (coreMetaLabel) {
            const charset = document.querySelector('meta[data-volt-head-key="document-charset"]');
            const viewport = document.querySelector('meta[data-volt-head-key="document-viewport"]');
            coreMetaLabel.textContent = charset && viewport ? 'ok' : 'missing';
        }

        if (styleLabel) {
            const dot = document.querySelector('[data-runtime-head-style-dot]');
            if (!dot) {
                styleLabel.textContent = 'missing';
            } else {
                const background = window.getComputedStyle(dot).backgroundColor || '';
                const normalizedBackground = background.replaceAll(' ', '').toLowerCase();
                styleLabel.textContent =
                    normalizedBackground === 'rgba(0,0,0,0)' || normalizedBackground === 'transparent' ?
                    'missing' :
                    'ok';
            }
        }
    })();
</script>
@endsection
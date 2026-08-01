<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Head;

use VoltStack\Runtime\Component\Component;

final class RuntimeHeadSpaPage extends Component
{
    public string $title = 'Runtime Head Lab (SPA)';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="runtime-head-lab" content="spa" data-volt-head-key="runtime-head-lab-meta">
<meta name="runtime-head-lab-screen" content="runtimeHead" data-volt-head-key="runtime-head-lab-screen">
<script data-volt-head-key="runtime-head-lab-inline">window.__voltRuntimeHeadLab = window.__voltRuntimeHeadLab || { bootedAt: Date.now() };</script>
@endsection

@section('content')
<div class="grid gap-6" data-runtime-head-lab="true">
    <section class="rounded-2xl border border-slate-800 bg-slate-950/60 p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300">Layout y Head</p>
        <h1 class="mt-2 text-3xl font-semibold text-white">{{ $title }}</h1>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-400">
            Esta pantalla valida reconciliación de <code>head</code> en navegación SPA y el fallback automático cuando el layout cambia.
        </p>
    </section>

    <section class="rounded-2xl border border-slate-800 bg-slate-950/60 p-6">
        <h2 class="text-lg font-semibold text-white">Estado actual</h2>
        <div class="mt-4 grid gap-3 text-sm text-slate-300 md:grid-cols-2">
            <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-4">
                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">meta runtime-head-lab</span>
                <strong data-runtime-check="head-lab-meta" class="mt-2 block text-lg text-white">-</strong>
            </div>
            <div class="rounded-xl border border-slate-800 bg-slate-900/40 p-4">
                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">vite assets en head</span>
                <strong data-runtime-check="head-lab-vite-assets" class="mt-2 block text-lg text-white">-</strong>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-800 bg-slate-950/60 p-6">
        <h2 class="text-lg font-semibold text-white">Navegación SPA (mismo layout)</h2>
        <p class="mt-2 text-sm leading-6 text-slate-400">
            Navega a una variante con el mismo layout para observar que el head se sincroniza sin duplicar assets.
        </p>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="/runtimeHeadAlt" volt:navigate
                class="inline-flex items-center rounded-xl border border-cyan-500/30 bg-cyan-500/10 px-4 py-2 text-sm font-semibold text-cyan-200 transition hover:border-cyan-400/60 hover:bg-cyan-500/20">
                Ir a /runtimeHeadAlt
            </a>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-800 bg-slate-950/60 p-6">
        <h2 class="text-lg font-semibold text-white">Layout mismatch (fallback)</h2>
        <p class="mt-2 text-sm leading-6 text-slate-400">
            Si se intenta navegar a un documento con <code>data-volt-layout="app"</code>, el runtime debe hacer fallback a recarga completa.
        </p>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="/runtimeHeadApp" volt:navigate
                class="inline-flex items-center rounded-xl border border-orange-500/30 bg-orange-500/10 px-4 py-2 text-sm font-semibold text-orange-200 transition hover:border-orange-400/60 hover:bg-orange-500/20">
                Ir a /runtimeHeadApp (espera reload)
            </a>
            <button type="button" data-runtime-head-lab-action="visit-no-fallback"
                class="inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-slate-500">
                Probar visit() fallback=false
            </button>
        </div>
        <pre data-runtime-check="head-lab-visit-result"
            class="mt-4 min-h-24 overflow-x-auto rounded-xl border border-slate-800 bg-slate-950/70 p-3 text-xs leading-5 text-slate-300">{"result":"(sin ejecutar)"}</pre>
    </section>

    <section class="rounded-2xl border border-slate-800 bg-slate-950/60 p-6">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('spaReactive') }}" volt:navigate
                class="inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-slate-500">
                Volver a /spaReactive
            </a>
        </div>
    </section>
</div>
@endsection

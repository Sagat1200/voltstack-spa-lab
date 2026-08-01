<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Head;

use VoltStack\Runtime\Component\Component;

final class RuntimeHeadSpaAltPage extends Component
{
    public string $title = 'Runtime Head Lab (SPA Alt)';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="runtime-head-lab" content="spa-alt" data-volt-head-key="runtime-head-lab-meta">
<meta name="runtime-head-lab-screen" content="runtimeHeadAlt" data-volt-head-key="runtime-head-lab-screen">
@endsection

@section('content')
<div class="grid gap-6" data-runtime-head-lab="true">
    <section class="rounded-2xl border border-slate-800 bg-slate-950/60 p-6">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300">Layout y Head</p>
        <h1 class="mt-2 text-3xl font-semibold text-white">{{ $title }}</h1>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-400">
            Variante dentro del mismo layout para comprobar que el runtime actualiza el head por key sin duplicar scripts o estilos.
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
                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">charset/viewport</span>
                <strong data-runtime-check="head-lab-core-meta" class="mt-2 block text-lg text-white">-</strong>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-800 bg-slate-950/60 p-6">
        <div class="flex flex-wrap gap-3">
            <a href="/runtimeHead" volt:navigate
                class="inline-flex items-center rounded-xl border border-cyan-500/30 bg-cyan-500/10 px-4 py-2 text-sm font-semibold text-cyan-200 transition hover:border-cyan-400/60 hover:bg-cyan-500/20">
                Volver a /runtimeHead
            </a>
            <a href="{{ route('spaReactive') }}" volt:navigate
                class="inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/60 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-slate-500">
                Volver a /spaReactive
            </a>
        </div>
    </section>
</div>
@endsection

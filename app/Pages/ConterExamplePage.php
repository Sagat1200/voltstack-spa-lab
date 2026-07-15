<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages;

use VoltStack\Runtime\Component\Component;
use VoltStack\Runtime\Protocol\ActionEffectOptions;

final class ConterExamplePage extends Component
{
    public int $count = 0;

    public string $title = 'Counter Example';

    public function increment(): ActionEffectOptions
    {
        $this->count++;

        return ActionEffectOptions::make()
            ->transitions()
            ->onTarget('count')
            ->forTextUpdate()
            ->pop(220)
            ->onTarget('count')
            ->forTextUpdate()
            ->updateAs('glow', className: 'volt-transition-soft-edge')
            ->end()
            ->effects()
            ->onTarget('count')
            ->event('demo.counter.incremented', ['count' => $this->count])
            ->end();
    }

    public function decrement(): ActionEffectOptions
    {
        $this->count--;

        return ActionEffectOptions::make()
            ->onTarget('count')
            ->when('text.update')
            ->fade(180);
    }
}
?>

@extends('layouts.spa')

@section('content')
<div style="display:grid;gap:20px;max-inline-size:880px;margin:0 auto;">
    <section
        style="border:1px solid rgba(34,211,238,0.2);background:rgba(15,23,42,0.82);border-radius:18px;padding:24px;color:#e2e8f0;">
        <span
            style="display:inline-flex;padding:6px 10px;border-radius:999px;border:1px solid rgba(34,211,238,0.28);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#67e8f9;">Counter
            Demo</span>
        <h1 data-volt-target="title" data-volt-transition="fade" data-volt-transition-update="pulse"
            data-volt-transition-enter="fade" data-volt-transition-update-class="volt-transition-soft-edge"
            data-volt-transition-duration="260" style="margin:16px 0 8px;font-size:32px;line-height:1.2;">{{ $title }}
        </h1>
        <p style="margin:0;color:#94a3b8;line-height:1.7;">
            Esta pagina contiene solo la prueba del contador para aislar los hooks, transiciones y estados del runtime
            relacionados con acciones reactivas simples como <code>volt-click</code>.
        </p>

        <div style="margin-block-start:18px;display:grid;gap:10px;">
            <div volt:loading
                style="border:1px solid rgba(34,197,94,0.35);background:rgba(34,197,94,0.12);color:#dcfce7;border-radius:12px;padding:12px 14px;">
                El runtime esta procesando la ultima accion reactiva.
            </div>
            <div volt:loading.hide
                style="border:1px solid rgba(51,65,85,1);background:#020617;color:#94a3b8;border-radius:12px;padding:12px 14px;">
                Runtime en reposo. Prueba hacer click muy rapido en <code>+1</code> o <code>-1</code> para observar la
                politica de concurrencia.
            </div>
            <div volt:error
                style="border:1px solid rgba(248,113,113,0.35);background:rgba(127,29,29,0.28);color:#fecaca;border-radius:12px;padding:12px 14px;">
                La ultima request reactiva termino con error.
            </div>
            <div volt:loading="increment" volt:loading.delay="10ms" volt:loading.min-duration="700ms"
                style="border:1px solid rgba(34,211,238,0.28);background:rgba(34,211,238,0.08);color:#cffafe;border-radius:12px;padding:12px 14px;">
                Este aviso solo aparece mientras corre la accion <code>increment</code> y se mantiene visible al menos
                <code>700ms</code>.
            </div>
            <div volt:loading volt:loading.target="increment-button"
                style="border:1px solid rgba(250,204,21,0.28);background:rgba(250,204,21,0.10);color:#fef08a;border-radius:12px;padding:12px 14px;">
                Este aviso solo aparece si la request activa vino del trigger con target
                <code>increment-button</code>.
            </div>
        </div>

        <div
            style="margin-block-start:18px;display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
            <div style="border:1px solid rgba(51,65,85,1);border-radius:14px;padding:16px;background:#020617;">
                <span style="display:block;font-size:12px;color:#64748b;text-transform:uppercase;">Count</span>
                <strong data-volt-target="count" data-volt-transition="pop" data-volt-transition-update="pop"
                    data-volt-transition-update-duration="220"
                    data-volt-transition-update-class="volt-transition-soft-edge"
                    style="display:block;margin-block-start:8px;font-size:28px;color:#f8fafc;">{{ $count }}</strong>
            </div>
        </div>

        <div style="margin-block-start:18px;display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button" data-volt-target="decrement-button" volt-click="decrement"
                style="border:1px solid #334155;background:#0f172a;color:#e2e8f0;border-radius:10px;padding:10px 16px;">-1</button>
            <button type="button" data-volt-target="increment-button" volt-click="increment"
                style="border:1px solid rgba(34,197,94,0.35);background:rgba(34,197,94,0.12);color:#dcfce7;border-radius:10px;padding:10px 16px;">+1</button>
        </div>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:18px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 10px;font-size:22px;">Navegacion para hooks SPA</h2>
        <p style="margin:0 0 16px;color:#94a3b8;line-height:1.7;">
            Usa estos enlaces para cambiar entre demos sin recargar la pagina completa.
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <a href="{{ route('spaReactive') }}" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid rgba(34,211,238,0.28);background:rgba(34,211,238,0.08);color:#cffafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Volver al inicio
            </a>
            <a href="/formExample" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Ir a formulario
            </a>
            <a href="/cacheExample" volt:navigate volt:prefetch="hover"
                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition border-slate-700 bg-slate-950/70 text-slate-200 hover:border-slate-500 hover:text-white">
                Probar navegacion a /cacheExample
            </a>
        </div>
    </section>
</div>
@endsection

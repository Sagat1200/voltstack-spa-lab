<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Html;

use VoltStack\Runtime\Component\Component;
use VoltStack\Runtime\Protocol\ActionEffectOptions;

final class LargeListPage extends Component
{
    public string $title = 'Large List';

    public int $rows = 2000;

    public bool $highlight = false;

    public int $variant = 0;

    public function toggleHighlight(): ActionEffectOptions
    {
        $this->highlight = ! $this->highlight;
        $this->variant++;

        return ActionEffectOptions::make();
    }

    public function resetList(): ActionEffectOptions
    {
        $this->highlight = false;
        $this->variant++;

        return ActionEffectOptions::make();
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="large-list-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(245,158,11,0.22);background:linear-gradient(135deg,rgba(120,53,15,0.72),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#fff7ed;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(251,191,36,0.28);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#fbbf24;">Listas
            grandes</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#fdba74;line-height:1.75;max-inline-size:76ch;">
                Esta pantalla sirve para estresar el costo de <code>patch</code> y <code>payload</code> en acciones
                reactivas que afectan muchos nodos. El escenario contractual inicial usa {{ $rows }} filas.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(251,191,36,0.18);background:rgba(120,53,15,0.22);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#fbbf24;">Variante</span>
            <strong style="font-size:14px;color:#fff7ed;">{{ $variant }}</strong>
            <span style="font-size:13px;color:#fdba74;">Rutas sugeridas:
                <code>/runtimeLargeList -> /runtimeMatrix</code>.</span>
        </div>
    </section>

    <section
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
            <div style="display:grid;gap:6px;">
                <h2 style="margin:0;font-size:22px;">Acciones</h2>
                <span style="color:#94a3b8;font-size:13px;line-height:1.6;">Cada click dispara un <code>/_volt/action</code> real.</span>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:12px;">
                <button type="button" volt-click="toggleHighlight"
                    style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.18);color:#dcfce7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                    Toggle highlight
                </button>
                <button type="button" volt-click="resetList"
                    style="border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.82);color:#e2e8f0;border-radius:10px;padding:10px 14px;cursor:pointer;">
                    Reset
                </button>
            </div>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <span
                style="display:inline-flex;gap:8px;align-items:center;border:1px solid rgba(34,197,94,0.22);background:rgba(20,83,45,0.14);border-radius:999px;padding:8px 12px;color:#dcfce7;font-size:12px;">
                highlight <strong>{{ $highlight ? 'on' : 'off' }}</strong>
            </span>
            <span
                style="display:inline-flex;gap:8px;align-items:center;border:1px solid rgba(251,191,36,0.22);background:rgba(120,53,15,0.14);border-radius:999px;padding:8px 12px;color:#ffedd5;font-size:12px;">
                filas <strong>{{ $rows }}</strong>
            </span>
        </div>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#020617;border-radius:20px;padding:16px;color:#e2e8f0;">
        <div data-volt-target="large-list-root"
            style="max-block-size:560px;overflow:auto;border:1px solid rgba(51,65,85,1);border-radius:16px;background:#0b1220;">
            <ol style="margin:0;padding:0;list-style:none;">
                @for ($index = 1; $index <= $rows; $index++)
                <li
                    style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-top:1px solid rgba(51,65,85,0.7);color:#cbd5e1;{{ $highlight ? 'background:rgba(34,197,94,0.10);' : '' }}">
                    <span style="font-family:Consolas,monospace;font-size:12px;color:#94a3b8;">#{{ $index }}</span>
                    <span style="font-size:13px;">Fila {{ $index }}</span>
                    <span style="font-size:12px;color:#64748b;">v{{ $variant }}</span>
                </li>
                @endfor
            </ol>
        </div>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeMatrix" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(34,197,94,0.24);background:rgba(20,83,45,0.18);color:#dcfce7;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver a /runtimeMatrix
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection


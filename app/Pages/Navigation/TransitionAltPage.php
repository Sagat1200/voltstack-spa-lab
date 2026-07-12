<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Navigation;

use VoltStack\Runtime\Component\Component;

final class TransitionAltPage extends Component
{
    public string $title = 'Page Transition Destination';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = sprintf('dest-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="navigation-transition-dest-mode">
<meta name="volt-page-transition-profile" content="gentle" data-volt-head-key="navigation-transition-dest-profile">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1040px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(192,132,252,0.26);background:linear-gradient(135deg,rgba(59,7,100,0.84),rgba(30,27,75,0.94));border-radius:24px;padding:32px;color:#f5f3ff;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(216,180,254,0.34);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#e9d5ff;">Destination
            Policy</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#e9d5ff;line-height:1.75;max-inline-size:72ch;">
                Esta pantalla declara la politica documental de transicion con un perfil reusable. Si llegaste con un
                enlace sin politica propia, el runtime aun asi hace <code>leave</code> y <code>enter</code> usando el
                perfil <code>gentle</code>.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(216,180,254,0.18);background:rgba(46,16,101,0.26);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#f0abfc;">Request
                marker</span>
            <strong style="font-size:14px;color:#faf5ff;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#e9d5ff;">Cada entrada SPA vuelve a renderizar esta vista y conserva el
                contrato de transicion documental.</span>
        </div>
    </section>

    <section
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Politica activa</h2>
        <div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
            <article
                style="border:1px solid rgba(168,85,247,0.24);background:rgba(88,28,135,0.12);border-radius:16px;padding:18px;">
                <strong style="display:block;color:#f5d0fe;">Profile</strong>
                <span style="display:block;margin-block-start:8px;color:#e9d5ff;"><code>gentle</code></span>
            </article>
            <article
                style="border:1px solid rgba(168,85,247,0.24);background:rgba(88,28,135,0.12);border-radius:16px;padding:18px;">
                <strong style="display:block;color:#f5d0fe;">Resolved variant</strong>
                <span style="display:block;margin-block-start:8px;color:#e9d5ff;"><code>fade</code></span>
            </article>
            <article
                style="border:1px solid rgba(168,85,247,0.24);background:rgba(88,28,135,0.12);border-radius:16px;padding:18px;">
                <strong style="display:block;color:#f5d0fe;">Duration</strong>
                <span style="display:block;margin-block-start:8px;color:#e9d5ff;"><code>320ms</code></span>
            </article>
            <article
                style="border:1px solid rgba(168,85,247,0.24);background:rgba(88,28,135,0.12);border-radius:16px;padding:18px;">
                <strong style="display:block;color:#f5d0fe;">Mode</strong>
                <span style="display:block;margin-block-start:8px;color:#e9d5ff;"><code>out-in</code></span>
            </article>
        </div>
    </section>

    <section data-volt-navigation-arrival
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Tipo de llegada detectado</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Este bloque indica de forma directa si la vista actual llego por navegacion SPA o por carga completa
                del documento.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <span data-volt-arrival-kind
                style="display:inline-flex;align-items:center;border:1px solid rgba(148,163,184,0.20);background:rgba(15,23,42,0.82);color:#e2e8f0;border-radius:999px;padding:6px 10px;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;">document-load</span>
            <strong data-volt-arrival-summary style="font-size:15px;color:#f8fafc;">Carga inicial del documento</strong>
        </div>

        <pre data-volt-arrival-detail
            style="margin:0;min-block-size:120px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:14px;padding:14px;color:#cbd5e1;font-size:12px;line-height:1.7;">{"esperando":"estado de llegada"}</pre>
    </section>

    <section

        style="display:flex;flex-wrap:wrap;gap:12px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
        <a href="/navigationTransition" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(96,165,250,0.28);background:rgba(14,116,144,0.14);color:#e0f2fe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al origen con politica documental
        </a>
        <a href="/navigationTransition" volt:navigate data-volt-page-transition-profile="classic"
            style="display:inline-flex;align-items:center;border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.16);color:#dcfce7;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver con perfil classic por enlace
        </a>
    </section>
</div>
@endsection
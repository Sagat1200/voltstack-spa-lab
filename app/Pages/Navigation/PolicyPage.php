<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Navigation;

use VoltStack\Runtime\Component\Component;

final class PolicyPage extends Component
{
    public string $title = 'Navigation Policy Demo';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="navigation-mode-auto">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:960px;margin:0 auto;">
    <section
        style="border:1px solid rgba(14,165,233,0.24);background:rgba(15,23,42,0.82);border-radius:20px;padding:28px;color:#e2e8f0;">
        <span
            style="display:inline-flex;padding:6px 10px;border-radius:999px;border:1px solid rgba(14,165,233,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#7dd3fc;">Navigation
            Policy</span>
        <h1 style="margin:16px 0 10px;font-size:34px;line-height:1.1;">{{ $title }}</h1>
        <p style="margin:0;color:#94a3b8;line-height:1.75;">
            Esta pantalla concentra el nuevo contrato de politicas por ruta: <code>auto</code>,
            <code>spa</code> y <code>reload</code>. Puedes probar una politica declarada en el enlace o una politica
            declarada por el propio documento destino.
        </p>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 12px;font-size:24px;">Modo por enlace</h2>
        <p style="margin:0 0 18px;color:#94a3b8;line-height:1.7;">
            Estos enlaces deciden antes de empezar la navegacion si el runtime debe usar SPA o dejar que el navegador
            haga una recarga completa.
        </p>

        <div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
            <a href="/counterExample" volt:navigate="spa" data-runtime-check="policy-link-spa"
                style="display:grid;gap:10px;border:1px solid rgba(34,197,94,0.26);background:rgba(6,78,59,0.18);border-radius:18px;padding:18px;text-decoration:none;">
                <strong style="font-size:18px;color:#f0fdf4;">`volt:navigate="spa"`</strong>
                <span style="color:#bbf7d0;line-height:1.6;">
                    Fuerza navegacion SPA a <code>/counterExample</code> incluso si quieres dejarlo explicito en el
                    markup.
                </span>
            </a>

            <a href="/counterExample" volt:navigate="reload" volt:prefetch="none"
                data-runtime-check="policy-link-reload"
                style="display:grid;gap:10px;border:1px solid rgba(248,113,113,0.26);background:rgba(127,29,29,0.16);border-radius:18px;padding:18px;text-decoration:none;">
                <strong style="font-size:18px;color:#fff1f2;">`volt:navigate="reload"`</strong>
                <span style="color:#fecaca;line-height:1.6;">
                    Deja de interceptar la ruta y permite que el navegador haga full reload hacia
                    <code>/counterExample</code>.
                </span>
            </a>
        </div>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 12px;font-size:24px;">Modo por documento destino</h2>
        <p style="margin:0 0 18px;color:#94a3b8;line-height:1.7;">
            Esta ruta se pide primero como SPA, pero el documento responde con
            <code>&lt;meta name="volt-navigation-mode" content="reload"&gt;</code> y fuerza recarga completa.
        </p>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <a href="/navigationDocumentReload" volt:navigate volt:prefetch="none" volt:cache="no-store"
                data-runtime-check="policy-link-document-reload"
                style="display:inline-flex;align-items:center;border:1px solid rgba(250,204,21,0.26);background:rgba(113,63,18,0.20);color:#fde68a;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Probar politica documental `reload`
            </a>
            <a href="{{ route('spaReactive') }}" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Inicio Sistema SPA Full Reactive
            </a>
        </div>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 12px;font-size:24px;">Resultado esperado</h2>
        <ul style="margin:0;padding-inline-start:18px;display:grid;gap:10px;color:#94a3b8;line-height:1.7;">
            <li><code>spa</code>: deben verse <code>volt:before-navigate</code> y <code>volt:navigated</code>.</li>
            <li><code>reload</code> por enlace: el navegador cambia de pagina sin pasar por el patch del body.</li>
            <li><code>reload</code> por documento: la visita empieza como SPA, pero termina en recarga completa al leer
                la politica del destino.</li>
        </ul>
    </section>

    <section data-volt-navigation-arrival data-runtime-check="navigation-arrival-panel"
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Tipo de llegada detectado</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Este bloque ayuda a distinguir una llegada SPA de una carga documental completa al volver al
                laboratorio.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <span data-volt-arrival-kind data-runtime-check="navigation-arrival-kind"
                style="display:inline-flex;align-items:center;border:1px solid rgba(148,163,184,0.20);background:rgba(15,23,42,0.82);color:#e2e8f0;border-radius:999px;padding:6px 10px;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;">document-load</span>
            <strong data-volt-arrival-summary data-runtime-check="navigation-arrival-summary"
                style="font-size:15px;color:#f8fafc;">Carga inicial del documento</strong>
        </div>

        <pre data-volt-arrival-detail data-runtime-check="navigation-arrival-detail"
            style="margin:0;min-block-size:120px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:14px;padding:14px;color:#cbd5e1;font-size:12px;line-height:1.7;">{"esperando":"estado de llegada"}</pre>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 12px;font-size:24px;">Contrato formal</h2>
        <ul style="margin:0;padding-inline-start:18px;display:grid;gap:10px;color:#94a3b8;line-height:1.7;">
            <li>Prioridad de resolucion: politica declarada en el enlace, luego politica declarada por el documento
                destino, y finalmente <code>auto</code> como valor por defecto.</li>
            <li>Documento SPA-capable: <code>data-volt-document="spa"</code> en <code>body</code> y runtime
                cargado.</li>
            <li>Modo documental por defecto: <code>data-volt-navigation-mode="auto"</code> cuando la pagina no
                declara otra politica.</li>
            <li>Razones de fallback expuestas por el runtime: <code>layout-mismatch</code>,
                <code>document-policy-reload</code> y <code>request-error</code>.
            </li>
        </ul>
    </section>
</div>
@endsection
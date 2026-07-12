<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Navigation;

use VoltStack\Runtime\Component\Component;

final class TransitionProfilePage extends Component
{
    public string $title = 'Link Transition Profile Destination';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = sprintf('profile-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="navigation-transition-profile-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1040px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(74,222,128,0.24);background:linear-gradient(135deg,rgba(20,83,45,0.88),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#ecfdf5;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(134,239,172,0.32);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#bbf7d0;">Link
            Profile</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#d1fae5;line-height:1.75;max-inline-size:72ch;">
                Esta vista no declara politica documental de transicion. El perfil activo depende del enlace usado para
                llegar y se refleja en el inspector como <code>pageTransitionProfile</code>.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(134,239,172,0.18);background:rgba(6,78,59,0.28);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#86efac;">Request
                marker</span>
            <strong style="font-size:14px;color:#f0fdf4;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#d1fae5;">Si cambia entre entradas, la navegacion siguio siendo SPA y
                el perfil vino del enlace.</span>
        </div>
    </section>

    <section
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Como validar este destino</h2>
        <ul style="margin:0;padding-inline-start:18px;display:grid;gap:10px;color:#94a3b8;line-height:1.7;">
            <li>El documento no expone <code>volt-page-transition-profile</code> ni otras metas de transicion.</li>
            <li>El inspector debe mostrar <code>pageTransitionProfile</code> con el valor definido por el enlace.</li>
            <li>Tambien deben verse <code>pageTransition</code>, <code>pageTransitionDuration</code> y
                <code>pageTransitionMode</code> ya resueltos por el runtime.
            </li>
        </ul>
    </section>

    <section data-volt-navigation-arrival
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Tipo de llegada detectado</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Este bloque indica si la vista actual llego por navegacion SPA y expone el perfil de transicion
                resuelto por el enlace.
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
        <a href="/navigationTransition" volt:navigate data-volt-page-transition-profile="soft"
            style="display:inline-flex;align-items:center;border:1px solid rgba(96,165,250,0.28);background:rgba(14,116,144,0.14);color:#e0f2fe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver con perfil soft
        </a>
        <a href="/navigationTransition" volt:navigate data-volt-page-transition-profile="crisp"
            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.16);color:#fee2e2;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver con perfil crisp
        </a>
    </section>
</div>
@endsection
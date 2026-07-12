<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Persist;

use VoltStack\Runtime\Component\Component;

final class PersistAltPage extends Component
{
    public string $title = 'Persist Destination';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-fragment-control" content="preserve" data-volt-head-key="persist-alt-fragment-control">
<script data-volt-head-key="persist-demo-bridge">
    (() => {
        if (window.__voltPersistDemoInstalled) {
            return;
        }

        window.__voltPersistDemoInstalled = true;
        window.__voltPersistDemoState = window.__voltPersistDemoState || {
            lastNavigatedDetail: null
        };

        function renderPersistStatus() {
            const state = window.__voltPersistDemoState || {};
            const detail = state.lastNavigatedDetail && typeof state.lastNavigatedDetail === 'object' ?
                state.lastNavigatedDetail : {};

            document.querySelectorAll('[data-volt-persist-status]').forEach((panel) => {
                const finalUrl = panel.querySelector('[data-volt-persist-final-url]');
                const persisted = panel.querySelector('[data-volt-persist-count]');
                const registry = panel.querySelector('[data-volt-persist-registry]');
                const detailPre = panel.querySelector('[data-volt-persist-detail]');

                if (finalUrl) {
                    finalUrl.textContent = typeof detail.finalUrl === 'string' && detail.finalUrl !== '' ?
                        detail.finalUrl :
                        window.location.href;
                }

                if (persisted) {
                    persisted.textContent = String(
                        typeof detail.persistedFragments === 'number' ? detail.persistedFragments : 0
                    );
                }

                if (registry) {
                    registry.textContent = String(
                        typeof detail.persistentFragmentRegistrySize === 'number' ?
                        detail.persistentFragmentRegistrySize :
                        0
                    );
                }

                if (detailPre) {
                    detailPre.textContent = JSON.stringify({
                        finalUrl: typeof detail.finalUrl === 'string' ? detail.finalUrl : window
                            .location.href,
                        persistedFragments: typeof detail.persistedFragments === 'number' ? detail
                            .persistedFragments : 0,
                        persistentFragmentRegistrySize: typeof detail.persistentFragmentRegistrySize ===
                            'number' ?
                            detail.persistentFragmentRegistrySize : 0,
                        preservedFragments: typeof detail.preservedFragments === 'number' ? detail
                            .preservedFragments : 0,
                        discardedFragments: typeof detail.discardedFragments === 'number' ? detail
                            .discardedFragments : 0,
                    }, null, 2);
                }
            });
        }

        document.addEventListener('volt:navigated', (event) => {
            window.__voltRuntimePersistDemoState.lastNavigatedDetail =
                event && event.detail && typeof event.detail === 'object' ? event.detail : {};

            window.requestAnimationFrame(renderPersistStatus);
        });

        document.addEventListener('DOMContentLoaded', renderPersistStatus, {
            once: true
        });
    })();
</script>
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(59,130,246,0.24);background:linear-gradient(135deg,rgba(30,64,175,0.92),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#eff6ff;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(96,165,250,0.34);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#93c5fd;">Destino
            final</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:34px;line-height:1.1;">{{ $title }}</h1>
            <p style="margin:0;color:#bfdbfe;line-height:1.75;max-inline-size:76ch;">
                Esta pantalla vuelve a exponer las claves <code>persist-sidebar</code> y
                <code>persist-player</code>. Si llegaste desde el puente, aqui deberian reaparecer los mismos nodos
                vivos que editaste en el origen.
            </p>
        </div>
    </section>

    <section data-volt-persist-status
        style="display:grid;gap:16px;border:1px solid rgba(59,130,246,0.24);background:rgba(30,64,175,0.16);border-radius:20px;padding:24px;color:#eff6ff;">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:end;justify-content:space-between;">
            <div>
                <h2 style="margin:0 0 10px;font-size:24px;">Lectura del ultimo <code>volt:navigated</code></h2>
                <p style="margin:0;line-height:1.7;color:#bfdbfe;">
                    En una navegacion correcta desde la pantalla puente, aqui deberias ver
                    <code>persistedFragments &gt; 0</code>.
                </p>
            </div>
            <strong data-volt-persist-final-url style="font-size:12px;color:#93c5fd;">/runtimePersistAlt</strong>
        </div>

        <div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
            <article
                style="border:1px solid rgba(59,130,246,0.22);border-radius:14px;padding:14px;background:rgba(30,64,175,0.24);">
                <span style="display:block;font-size:12px;color:#bfdbfe;">Persistidos ahora</span>
                <strong data-volt-persist-count
                    style="display:block;margin-block-start:8px;font-size:30px;color:#eff6ff;">0</strong>
            </article>
            <article
                style="border:1px solid rgba(59,130,246,0.22);border-radius:14px;padding:14px;background:rgba(30,64,175,0.24);">
                <span style="display:block;font-size:12px;color:#bfdbfe;">Registry size</span>
                <strong data-volt-persist-registry
                    style="display:block;margin-block-start:8px;font-size:30px;color:#eff6ff;">0</strong>
            </article>
        </div>

        <pre data-volt-persist-detail
            style="margin:0;overflow:auto;border:1px solid rgba(59,130,246,0.22);border-radius:14px;padding:14px;background:rgba(30,64,175,0.24);color:#dbeafe;font-size:12px;line-height:1.65;">{"waiting":"volt:navigated"}</pre>
    </section>

    <section style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
        <section data-volt-persist="persist-sidebar"
            style="display:grid;gap:14px;border:1px solid rgba(16,185,129,0.28);background:rgba(6,78,59,0.18);border-radius:20px;padding:20px;color:#d1fae5;">
            <div>
                <span
                    style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(16,185,129,0.30);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#86efac;">Target
                    persist-sidebar</span>
                <h2 style="margin:12px 0 6px;font-size:24px;color:#f0fdf4;">Sidebar reinyectado</h2>
                <p style="margin:0;line-height:1.65;">
                    Si el runtime funciono bien, este bloque ya no deberia mostrar el contenido base de esta vista, sino
                    el nodo vivo que salió desde el origen.
                </p>
            </div>

            <label style="display:grid;gap:6px;">
                <span style="font-size:13px;color:#a7f3d0;">Nombre visible</span>
                <input type="text" value="Base del destino"
                    style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(16,185,129,0.28);background:#022c22;color:#f0fdf4;">
            </label>

            <div contenteditable="true"
                style="min-block-size:110px;padding:12px 14px;border-radius:12px;border:1px solid rgba(16,185,129,0.24);background:#064e3b;color:#d1fae5;line-height:1.65;">
                Este texto base deberia ser reemplazado por el bloque editado en el origen.
            </div>

            <label style="display:flex;align-items:center;gap:10px;color:#d1fae5;">
                <input type="checkbox">
                Check base del destino.
            </label>

            <details
                style="border:1px solid rgba(16,185,129,0.22);border-radius:12px;padding:12px 14px;background:#022c22;">
                <summary style="cursor:pointer;color:#d1fae5;">Panel base del destino</summary>
                <p style="margin:10px 0 0;line-height:1.6;color:#a7f3d0;">
                    Si no fue reemplazado por el nodo persistido, verias este estado base.
                </p>
            </details>

            <label style="display:grid;gap:8px;">
                <span style="font-size:13px;color:#a7f3d0;">Nivel visual</span>
                <input type="range" min="0" max="100" value="12">
            </label>
        </section>

        <section data-volt-persist="persist-player"
            style="display:grid;gap:14px;border:1px solid rgba(59,130,246,0.28);background:rgba(30,41,59,0.82);border-radius:20px;padding:20px;color:#dbeafe;">
            <div>
                <span
                    style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(59,130,246,0.30);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#93c5fd;">Target
                    persist-player</span>
                <h2 style="margin:12px 0 6px;font-size:24px;color:#eff6ff;">Player reinyectado</h2>
                <p style="margin:0;line-height:1.65;color:#bfdbfe;">
                    Este segundo target confirma que el registro puede restaurar varias claves a la vez.
                </p>
            </div>

            <label style="display:grid;gap:6px;">
                <span style="font-size:13px;color:#bfdbfe;">Track actual</span>
                <input type="text" value="Base destino player"
                    style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(59,130,246,0.28);background:#0f172a;color:#eff6ff;">
            </label>

            <label style="display:grid;gap:8px;">
                <span style="font-size:13px;color:#bfdbfe;">Posicion</span>
                <input type="range" min="0" max="100" value="9">
            </label>

            <label style="display:flex;align-items:center;gap:10px;color:#dbeafe;">
                <input type="checkbox">
                Base del destino para reproduccion.
            </label>
        </section>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimePersist" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(236,72,153,0.28);background:rgba(236,72,153,0.12);color:#fbcfe8;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al origen
        </a>
        <a href="/runtimePersistBridge" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(251,191,36,0.30);background:rgba(251,191,36,0.12);color:#fde68a;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al puente
        </a>
        <a href="/" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al inicio
        </a>
    </section>
</div>
@endsection
<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Persist;

use VoltStack\Runtime\Component\Component;

final class PersistPage extends Component
{
    public string $title = 'Persist Demo';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = sprintf('persist-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-fragment-control" content="preserve" data-volt-head-key="persist-fragment-control">
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
                const preserved = panel.querySelector('[data-volt-persist-preserved]');
                const discarded = panel.querySelector('[data-volt-persist-discarded]');
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

                if (preserved) {
                    preserved.textContent = String(
                        typeof detail.preservedFragments === 'number' ? detail.preservedFragments : 0
                    );
                }

                if (discarded) {
                    discarded.textContent = String(
                        typeof detail.discardedFragments === 'number' ? detail.discardedFragments : 0
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
            window.__voltPersistDemoState.lastNavigatedDetail =
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
        style="display:grid;gap:16px;border:1px solid rgba(244,114,182,0.24);background:linear-gradient(135deg,rgba(76,29,149,0.92),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#fdf2f8;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(244,114,182,0.34);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#f9a8d4;">Runtime
            Persist MVP</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#fbcfe8;line-height:1.75;max-inline-size:76ch;">
                Esta pantalla valida el nuevo <code>volt:persist</code>. Edita los bloques marcados, navega a una
                pantalla puente sin targets persistidos y luego a una pantalla final que vuelva a exponer las mismas
                claves. Si el MVP funciona, el nodo vivo debe reaparecer intacto.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(244,114,182,0.18);background:rgba(76,29,149,0.26);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#f9a8d4;">Request
                marker</span>
            <strong style="font-size:14px;color:#fff1f2;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#fbcfe8;">Ruta sugerida:
                <code>/runtimePersist -> /runtimePersistBridge -> /runtimePersistAlt</code>.</span>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Pasos de prueba</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                La comparacion real se hace entre los bloques persistidos y sus bloques de control no persistidos.
            </p>
        </div>
        <div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
            <div style="border:1px solid rgba(51,65,85,1);border-radius:14px;padding:16px;background:#020617;">
                <strong style="display:block;font-size:13px;color:#f8fafc;">1. Edita el sidebar</strong>
                <span style="display:block;margin-block-start:8px;font-size:14px;color:#94a3b8;">Cambia texto, check,
                    panel y
                    rango del bloque persistido.</span>
            </div>
            <div style="border:1px solid rgba(51,65,85,1);border-radius:14px;padding:16px;background:#020617;">
                <strong style="display:block;font-size:13px;color:#f8fafc;">2. Pasa por el puente</strong>
                <span style="display:block;margin-block-start:8px;font-size:14px;color:#94a3b8;">En
                    <code>/runtimePersistBridge</code> no existen esos targets.</span>
            </div>
            <div style="border:1px solid rgba(51,65,85,1);border-radius:14px;padding:16px;background:#020617;">
                <strong style="display:block;font-size:13px;color:#f8fafc;">3. Reinyecta</strong>
                <span style="display:block;margin-block-start:8px;font-size:14px;color:#94a3b8;">En
                    <code>/runtimePersistAlt</code> deben volver a aparecer con el estado vivo.</span>
            </div>
        </div>
    </section>

    <section style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
        <section data-volt-persist="persist-sidebar"
            style="display:grid;gap:14px;border:1px solid rgba(16,185,129,0.28);background:rgba(6,78,59,0.18);border-radius:20px;padding:20px;color:#d1fae5;">
            <div>
                <span
                    style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(16,185,129,0.30);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#86efac;">volt:persist="persist-sidebar"</span>
                <h2 style="margin:12px 0 6px;font-size:24px;color:#f0fdf4;">Sidebar persistido</h2>
                <p style="margin:0;line-height:1.65;">
                    Este nodo debe conservar su estado aunque la siguiente pantalla no tenga ningun target compatible.
                </p>
            </div>

            <label style="display:grid;gap:6px;">
                <span style="font-size:13px;color:#a7f3d0;">Nombre visible</span>
                <input type="text" value="Sidebar inicial"
                    style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(16,185,129,0.28);background:#022c22;color:#f0fdf4;">
            </label>

            <div contenteditable="true"
                style="min-block-size:110px;padding:12px 14px;border-radius:12px;border:1px solid rgba(16,185,129,0.24);background:#064e3b;color:#d1fae5;line-height:1.65;">
                Edita este bloque y comprueba si vuelve igual en la ruta final.
            </div>

            <label style="display:flex;align-items:center;gap:10px;color:#d1fae5;">
                <input type="checkbox" checked>
                Mantener este check activo.
            </label>

            <details open
                style="border:1px solid rgba(16,185,129,0.22);border-radius:12px;padding:12px 14px;background:#022c22;">
                <summary style="cursor:pointer;color:#d1fae5;">Panel plegable del sidebar</summary>
                <p style="margin:10px 0 0;line-height:1.6;color:#a7f3d0;">
                    Puedes cerrarlo antes de navegar. Si el nodo se reinyecta bien, debe conservar ese estado.
                </p>
            </details>

            <label style="display:grid;gap:8px;">
                <span style="font-size:13px;color:#a7f3d0;">Nivel visual</span>
                <input type="range" min="0" max="100" value="78">
            </label>
        </section>

        <section
            style="display:grid;gap:14px;border:1px solid rgba(148,163,184,0.24);background:rgba(2,6,23,0.62);border-radius:20px;padding:20px;color:#e2e8f0;">
            <div>
                <span
                    style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(148,163,184,0.26);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#cbd5e1;">Control</span>
                <h2 style="margin:12px 0 6px;font-size:24px;color:#f8fafc;">Sidebar normal</h2>
                <p style="margin:0;line-height:1.65;color:#94a3b8;">
                    Este bloque sirve como comparacion. Cualquier cambio local debe perderse tras navegar.
                </p>
            </div>

            <label style="display:grid;gap:6px;">
                <span style="font-size:13px;color:#cbd5e1;">Nombre visible</span>
                <input type="text" value="Control inicial"
                    style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid #334155;background:#020617;color:#f8fafc;">
            </label>

            <div contenteditable="true"
                style="min-block-size:110px;padding:12px 14px;border-radius:12px;border:1px solid #334155;background:#020617;color:#e2e8f0;line-height:1.65;">
                Este bloque debe resetearse cuando vuelvas a una pantalla compatible.
            </div>

            <label style="display:flex;align-items:center;gap:10px;color:#e2e8f0;">
                <input type="checkbox">
                Este check vuelve a su HTML inicial.
            </label>

            <details open style="border:1px solid #334155;border-radius:12px;padding:12px 14px;background:#020617;">
                <summary style="cursor:pointer;color:#e2e8f0;">Panel de control</summary>
                <p style="margin:10px 0 0;line-height:1.6;color:#94a3b8;">
                    Cierralo o editalo para compararlo contra el panel persistido.
                </p>
            </details>

            <label style="display:grid;gap:8px;">
                <span style="font-size:13px;color:#cbd5e1;">Nivel visual</span>
                <input type="range" min="0" max="100" value="22">
            </label>
        </section>
    </section>

    <section style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
        <section volt:persist="persist-player"
            style="display:grid;gap:14px;border:1px solid rgba(59,130,246,0.28);background:rgba(30,41,59,0.82);border-radius:20px;padding:20px;color:#dbeafe;">
            <div>
                <span
                    style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(59,130,246,0.30);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#93c5fd;">volt:persist="persist-player"</span>
                <h2 style="margin:12px 0 6px;font-size:24px;color:#eff6ff;">Mini player persistido</h2>
                <p style="margin:0;line-height:1.65;color:#bfdbfe;">
                    Segundo target persistido para validar varias claves vivas en el mismo flujo.
                </p>
            </div>

            <label style="display:grid;gap:6px;">
                <span style="font-size:13px;color:#bfdbfe;">Track actual</span>
                <input type="text" value="Track 01 - Demo persist"
                    style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(59,130,246,0.28);background:#0f172a;color:#eff6ff;">
            </label>

            <label style="display:grid;gap:8px;">
                <span style="font-size:13px;color:#bfdbfe;">Posicion</span>
                <input type="range" min="0" max="100" value="41">
            </label>

            <label style="display:flex;align-items:center;gap:10px;color:#dbeafe;">
                <input type="checkbox">
                Mantener reproduccion activa.
            </label>
        </section>

        <section
            style="display:grid;gap:14px;border:1px solid rgba(148,163,184,0.24);background:rgba(2,6,23,0.62);border-radius:20px;padding:20px;color:#e2e8f0;">
            <div>
                <span
                    style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(148,163,184,0.26);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#cbd5e1;">Control</span>
                <h2 style="margin:12px 0 6px;font-size:24px;color:#f8fafc;">Player normal</h2>
                <p style="margin:0;line-height:1.65;color:#94a3b8;">
                    Este bloque no participa en el registro persistente.
                </p>
            </div>

            <label style="display:grid;gap:6px;">
                <span style="font-size:13px;color:#cbd5e1;">Track actual</span>
                <input type="text" value="Track 00 - Control"
                    style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid #334155;background:#020617;color:#f8fafc;">
            </label>

            <label style="display:grid;gap:8px;">
                <span style="font-size:13px;color:#cbd5e1;">Posicion</span>
                <input type="range" min="0" max="100" value="8">
            </label>

            <label style="display:flex;align-items:center;gap:10px;color:#e2e8f0;">
                <input type="checkbox">
                Reproduccion local de control.
            </label>
        </section>
    </section>

    <section data-volt-persist-status
        style="display:grid;gap:16px;border:1px solid rgba(236,72,153,0.22);background:rgba(80,7,36,0.16);border-radius:20px;padding:24px;color:#fdf2f8;">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:end;justify-content:space-between;">
            <div>
                <h2 style="margin:0 0 10px;font-size:24px;">Estado observado del runtime</h2>
                <p style="margin:0;line-height:1.7;color:#fbcfe8;">
                    Este panel se alimenta del detalle de <code>volt:navigated</code> para mostrar cuantas instancias
                    persistidas se restauraron y cuantas siguen registradas.
                </p>
            </div>
            <strong data-volt-persist-final-url style="font-size:12px;color:#f9a8d4;">/runtimePersist</strong>
        </div>

        <div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));">
            <article
                style="border:1px solid rgba(236,72,153,0.24);border-radius:14px;padding:14px;background:rgba(80,7,36,0.28);">
                <span style="display:block;font-size:12px;color:#f9a8d4;">Persistidos ahora</span>
                <strong data-volt-persist-count
                    style="display:block;margin-block-start:8px;font-size:30px;color:#fff1f2;">0</strong>
            </article>
            <article
                style="border:1px solid rgba(236,72,153,0.24);border-radius:14px;padding:14px;background:rgba(80,7,36,0.28);">
                <span style="display:block;font-size:12px;color:#f9a8d4;">Registry size</span>
                <strong data-volt-persist-registry
                    style="display:block;margin-block-start:8px;font-size:30px;color:#fff1f2;">0</strong>
            </article>
            <article
                style="border:1px solid rgba(236,72,153,0.24);border-radius:14px;padding:14px;background:rgba(80,7,36,0.28);">
                <span style="display:block;font-size:12px;color:#f9a8d4;">PreservedFragments</span>
                <strong data-volt-persist-preserved
                    style="display:block;margin-block-start:8px;font-size:30px;color:#fff1f2;">0</strong>
            </article>
            <article
                style="border:1px solid rgba(236,72,153,0.24);border-radius:14px;padding:14px;background:rgba(80,7,36,0.28);">
                <span style="display:block;font-size:12px;color:#f9a8d4;">DiscardedFragments</span>
                <strong data-volt-persist-discarded
                    style="display:block;margin-block-start:8px;font-size:30px;color:#fff1f2;">0</strong>
            </article>
        </div>

        <pre data-volt-persist-detail
            style="margin:0;overflow:auto;border:1px solid rgba(236,72,153,0.24);border-radius:14px;padding:14px;background:rgba(80,7,36,0.28);color:#fbcfe8;font-size:12px;line-height:1.65;">{"waiting":"volt:navigated"}</pre>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimePersistBridge" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(236,72,153,0.28);background:rgba(236,72,153,0.12);color:#fbcfe8;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Ir a la pantalla puente
        </a>
        <a href="/runtimePersistAlt" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(59,130,246,0.28);background:rgba(59,130,246,0.12);color:#dbeafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Ir directo al destino final
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection
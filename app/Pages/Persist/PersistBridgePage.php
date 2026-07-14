<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Persist;

use VoltStack\Runtime\Component\Component;

final class PersistBridgePage extends Component
{
    public string $title = 'Persist Bridge';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-fragment-control" content="preserve" data-volt-head-key="persist-bridge-fragment-control">
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
<div style="display:grid;gap:20px;max-inline-size:980px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(251,191,36,0.24);background:linear-gradient(135deg,rgba(120,53,15,0.88),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#fffbeb;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(251,191,36,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#fde68a;">Pantalla
            puente</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:34px;line-height:1.1;">{{ $title }}</h1>
            <p style="margin:0;color:#fde68a;line-height:1.75;max-inline-size:72ch;">
                Esta ruta no expone ningun target con <code>volt:persist</code>. Si vienes desde
                <code>/runtimePersist</code>, el registro interno debe conservar las instancias vivas sin reinyectarlas
                todavia. El paso decisivo es continuar luego hacia <code>/runtimePersistAlt</code>.
            </p>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Que deberia pasar aqui</h2>
        <ul style="margin:0;padding-inline-start:20px;display:grid;gap:10px;color:#cbd5e1;line-height:1.7;">
            <li>Los nodos persistidos salen del DOM visible porque esta pantalla no tiene claves compatibles.</li>
            <li>El valor de <code>persistedFragments</code> en la llegada deberia ser <code>0</code>.</li>
            <li>El valor de <code>persistentFragmentRegistrySize</code> deberia seguir siendo mayor que <code>0</code>
                si vienes desde una ruta que ya capturo instancias persistidas.</li>
        </ul>
    </section>

    <section data-volt-persist-status
        style="display:grid;gap:16px;border:1px solid rgba(251,191,36,0.24);background:rgba(120,53,15,0.18);border-radius:20px;padding:24px;color:#fffbeb;">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:end;justify-content:space-between;">
            <div>
                <h2 style="margin:0 0 10px;font-size:24px;">Estado del registro persistente</h2>
                <p style="margin:0;line-height:1.7;color:#fde68a;">
                    Este panel toma el detalle de <code>volt:navigated</code>. Aqui deberias ver el registry vivo aunque
                    no se haya reinyectado ningun nodo en esta pantalla.
                </p>
            </div>
            <strong data-volt-persist-final-url style="font-size:12px;color:#fde68a;">/runtimePersistBridge</strong>
        </div>

        <div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
            <article
                style="border:1px solid rgba(251,191,36,0.22);border-radius:14px;padding:14px;background:rgba(120,53,15,0.24);">
                <span style="display:block;font-size:12px;color:#fde68a;">Persistidos ahora</span>
                <strong data-volt-persist-count
                    style="display:block;margin-block-start:8px;font-size:30px;color:#fffbeb;">0</strong>
            </article>
            <article
                style="border:1px solid rgba(251,191,36,0.22);border-radius:14px;padding:14px;background:rgba(120,53,15,0.24);">
                <span style="display:block;font-size:12px;color:#fde68a;">Registry size</span>
                <strong data-volt-persist-registry
                    style="display:block;margin-block-start:8px;font-size:30px;color:#fffbeb;">0</strong>
            </article>
        </div>

        <pre data-volt-persist-detail
            style="margin:0;overflow:auto;border:1px solid rgba(251,191,36,0.22);border-radius:14px;padding:14px;background:rgba(120,53,15,0.24);color:#fef3c7;font-size:12px;line-height:1.65;">{"waiting":"volt:navigated"}</pre>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimePersistAlt" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(251,191,36,0.30);background:rgba(251,191,36,0.12);color:#fde68a;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Continuar al destino final
        </a>
        <a href="/runtimePersist" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(236,72,153,0.28);background:rgba(236,72,153,0.12);color:#fbcfe8;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al origen
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection

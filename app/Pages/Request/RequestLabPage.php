<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Request;

use RuntimeException;
use VoltStack\Runtime\Component\Component;
use VoltStack\Runtime\Protocol\ActionEffectOptions;

final class RequestLabPage extends Component
{
    public string $title = 'Request Lab';

    public string $requestMarker;

    public string $lastActionStatus = 'Aun no se ejecuto ninguna action controlada.';

    public string $lastActionAt = 'Sin request reactiva.';

    public function mount(): void
    {
        $this->requestMarker = sprintf('request-lab-%s', substr((string) microtime(true), -6));
    }

    public function fastAction(): ActionEffectOptions
    {
        $this->lastActionStatus = 'fastAction respondio sin demora.';
        $this->lastActionAt = date('H:i:s');

        return ActionEffectOptions::make();
    }

    public function slowAction(): ActionEffectOptions
    {
        usleep(1_500_000);

        $this->lastActionStatus = 'slowAction completo despues de 1500ms.';
        $this->lastActionAt = date('H:i:s');

        return ActionEffectOptions::make();
    }

    public function protocolValidationFailure(): ActionEffectOptions
    {
        $this->validate([
            'labProbe' => '',
        ], [
            'labProbe' => ['required', 'string', 'min:3'],
        ]);

        return ActionEffectOptions::make();
    }

    public function protocolExceptionFailure(): ActionEffectOptions
    {
        throw new RuntimeException('Runtime QA forced server exception.');
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="runtime-request-lab-mode">
<script data-volt-head-key="runtime-request-lab-spa-bridge">
(() => {
    if (window.__spaLabRequestLabSpaBridgeInstalled) {
        if (window.__spaLabRequestLab && typeof window.__spaLabRequestLab.syncVisibleState === 'function') {
            window.setTimeout(() => window.__spaLabRequestLab.syncVisibleState(), 0);
        }
        return;
    }

    window.__spaLabRequestLabSpaBridgeInstalled = true;

    function bootRequestLabInlineScript() {
        if (window.__spaLabRequestLab && typeof window.__spaLabRequestLab.syncVisibleState === 'function') {
            window.__spaLabRequestLab.syncVisibleState();
            return;
        }

        const inlineBootstrap = document.querySelector('script[data-runtime-request-lab-bootstrap="true"]');

        if (!inlineBootstrap) {
            return;
        }

        try {
            const runtimeBootstrap = document.createElement('script');
            runtimeBootstrap.type = 'text/javascript';
            runtimeBootstrap.setAttribute('data-runtime-request-lab-executed', 'true');
            runtimeBootstrap.textContent = inlineBootstrap.textContent;
            document.body.appendChild(runtimeBootstrap);
            runtimeBootstrap.remove();
        } catch (error) {
            console.error('RequestLab SPA bootstrap failed.', error);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        window.setTimeout(bootRequestLabInlineScript, 0);
    });
    document.addEventListener('volt:navigated', () => {
        window.setTimeout(bootRequestLabInlineScript, 0);
    });
    window.setTimeout(bootRequestLabInlineScript, 0);
})();
</script>
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(125,211,252,0.26);background:linear-gradient(135deg,rgba(8,47,73,0.9),rgba(15,23,42,0.96));border-radius:24px;padding:32px;color:#e0f2fe;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(125,211,252,0.3);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#7dd3fc;">Runtime
            Request QA</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#bae6fd;line-height:1.75;max-inline-size:78ch;">
                Esta pantalla concentra escenarios reproducibles para validar <code>timeout</code>,
                <code>protocol-error</code>, <code>http-error</code>, <code>network-error</code> y
                <code>stale</code> sin depender de hacks manuales en DevTools.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(125,211,252,0.18);background:rgba(8,47,73,0.34);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#7dd3fc;">Request
                marker</span>
            <strong style="font-size:14px;color:#f0f9ff;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#bae6fd;">
                Usa <code>/runtimeRequestLabSlow</code> como destino lento y <code>/runtimeRequestLabMissing</code>
                como ruta inexistente para pruebas de navegacion.
            </span>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Acciones reactivas controladas</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Estas acciones usan el endpoint reactivo normal. Puedes dispararlas con un timeout bajo o apuntarlas a
                un endpoint roto para forzar errores de red.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <button type="button" volt-click="fastAction" data-volt-target="fast-action-button"
                style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.28);color:#dcfce7;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Fast action
            </button>
            <button type="button" volt-click="slowAction" data-volt-target="slow-action-button"
                data-volt-request-timeout="120ms"
                style="border:1px solid rgba(250,204,21,0.3);background:rgba(113,63,18,0.28);color:#fef3c7;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Timeout action (120ms)
            </button>
            <button type="button" volt-click="protocolValidationFailure" data-volt-target="validation-action-button"
                style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.24);color:#fee2e2;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Protocol error por validacion
            </button>
            <button type="button" volt-click="protocolExceptionFailure" data-volt-target="exception-action-button"
                style="border:1px solid rgba(244,114,182,0.28);background:rgba(131,24,67,0.24);color:#fce7f3;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Protocol error por excepcion
            </button>
            <button type="button" data-volt-target="abort-action-scenario-button"
                onclick="(function(button){var root=button.closest('[data-volt-root]'); if(!root){return;} var slow=root.querySelector('[data-volt-target=&quot;slow-action-button&quot;]'); var fast=root.querySelector('[data-volt-target=&quot;fast-action-button&quot;]'); if(!slow||!fast){return;} slow.click(); window.setTimeout(function(){ fast.click(); }, 40);})(this)"
                style="border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.28);color:#bae6fd;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Abort previous action
            </button>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <button type="button"
                onclick="if(window.__spaLabRequestLab && typeof window.__spaLabRequestLab.setBrokenActionEndpoint === 'function'){ window.__spaLabRequestLab.setBrokenActionEndpoint(this); }"
                style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.22);color:#dbeafe;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Preparar endpoint roto
            </button>
            <button type="button"
                onclick="if(window.__spaLabRequestLab && typeof window.__spaLabRequestLab.restoreActionEndpoint === 'function'){ window.__spaLabRequestLab.restoreActionEndpoint(this); }"
                style="border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.52);color:#e2e8f0;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Restaurar endpoint normal
            </button>
            <span style="font-size:13px;color:#94a3b8;line-height:1.6;">
                Para forzar <code>network-error</code> en action: pulsa <code>Preparar endpoint roto</code> y luego
                ejecuta <code>Fast action</code>.
            </span>
        </div>

        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(71,85,105,1);background:#020617;border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#94a3b8;">Endpoint action
                actual</span>
            <strong data-runtime-check="action-endpoint-status"
                style="font-size:14px;color:#e2e8f0;">/_volt/action</strong>
            <span data-runtime-check="action-retry-guidance" style="font-size:13px;color:#cbd5e1;line-height:1.6;">
                Si una action falla por red o protocolo, el runtime no la reintenta solo: corrige la causa y vuelve a
                dispararla manualmente.
            </span>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));align-items:start;">
            <article data-runtime-check="action-retry-policy-card"
                style="display:grid;gap:10px;border:1px solid rgba(248,113,113,0.2);background:rgba(127,29,29,0.18);border-radius:16px;padding:16px;color:#fee2e2;">
                <strong>Actions POST</strong>
                <span data-runtime-check="action-retry-policy-value" style="font-size:18px;color:#fecaca;">Sin retry
                    automatico</span>
                <p style="margin:0;line-height:1.7;">
                    Se evita el replay implicito de side effects. Si ves <code>network-error</code>,
                    <code>protocol-error</code> o <code>timeout</code>, la recuperacion actual es manual.
                </p>
            </article>
            <article data-runtime-check="navigation-retry-policy-card"
                style="display:grid;gap:10px;border:1px solid rgba(74,222,128,0.2);background:rgba(20,83,45,0.18);border-radius:16px;padding:16px;color:#dcfce7;">
                <strong>Navegacion GET</strong>
                <span data-runtime-check="navigation-retry-policy-value" style="font-size:18px;color:#bbf7d0;">Retry
                    opt-in seguro</span>
                <p style="margin:0;line-height:1.7;">
                    El boton <code>Retry navigation once</code> usa <code>window.Volt.visit(..., { retry })</code>
                    para absorber fallos transitorios sin asumir replay automatico en acciones.
                </p>
            </article>
            <article data-runtime-check="network-status-card"
                style="display:grid;gap:10px;border:1px solid rgba(56,189,248,0.2);background:rgba(8,47,73,0.18);border-radius:16px;padding:16px;color:#e0f2fe;">
                <strong>Conectividad del navegador</strong>
                <span data-runtime-check="network-status-label" style="font-size:18px;color:#7dd3fc;">Online</span>
                <span data-runtime-check="network-status-detail" style="font-size:13px;color:#bae6fd;">navigator.onLine
                    = true</span>
                <p style="margin:0;line-height:1.7;">
                    Este monitor solo hace visible el estado del navegador. El modo offline, snapshots y queued
                    actions siguen pendientes como bloque futuro del roadmap.
                </p>
            </article>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <div volt:loading
                style="border:1px solid rgba(34,211,238,0.28);background:rgba(8,47,73,0.3);color:#bae6fd;border-radius:999px;padding:8px 12px;font-size:13px;">
                Procesando request reactiva...
            </div>
            <div volt:success
                style="border:1px solid rgba(74,222,128,0.24);background:rgba(20,83,45,0.24);color:#dcfce7;border-radius:999px;padding:8px 12px;font-size:13px;">
                Request completada correctamente.
            </div>
            <div volt:error volt:error.timeout="3s"
                style="border:1px solid rgba(248,113,113,0.24);background:rgba(127,29,29,0.22);color:#fee2e2;border-radius:999px;padding:8px 12px;font-size:13px;">
                La request reactiva termino con error.
            </div>
        </div>

        <div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));align-items:start;">
            <article
                style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;color:#cbd5e1;">
                <strong>Ultimo evento</strong>
                <span data-runtime-check="request-last-event" style="font-size:15px;color:#f8fafc;">Sin eventos
                    todavia</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;color:#cbd5e1;">
                <strong>Outcome visible</strong>
                <span data-runtime-check="request-last-outcome" style="font-size:15px;color:#f8fafc;">sin outcome</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;color:#cbd5e1;">
                <strong>Target observado</strong>
                <span data-runtime-check="request-last-target" style="font-size:15px;color:#f8fafc;">sin target</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;color:#cbd5e1;">
                <strong>Retry observado</strong>
                <span data-runtime-check="request-last-retry-count" style="font-size:15px;color:#f8fafc;">0</span>
            </article>
        </div>

        <div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));align-items:start;">
            <article
                style="display:grid;gap:10px;border:1px solid rgba(56,189,248,0.2);background:rgba(8,47,73,0.2);border-radius:16px;padding:16px;color:#e0f2fe;">
                <strong>Estado del servidor</strong>
                <div style="display:grid;gap:8px;">
                    <span>Ultimo estado:</span>
                    <strong data-volt-target="lab-action-status">{{ $lastActionStatus }}</strong>
                </div>
                <div style="display:grid;gap:8px;">
                    <span>Ultima marca temporal:</span>
                    <strong data-volt-target="lab-action-at">{{ $lastActionAt }}</strong>
                </div>
            </article>
            <article
                style="display:grid;gap:10px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;color:#cbd5e1;">
                <strong>Guia rapida</strong>
                <ul style="margin:0;padding-inline-start:18px;display:grid;gap:8px;line-height:1.6;">
                    <li><code>Timeout action</code> debe registrar <code>timeout</code>.</li>
                    <li><code>Protocol error por validacion</code> debe registrar <code>protocol-error</code>.</li>
                    <li><code>Preparar endpoint roto</code> + <code>Fast action</code> debe registrar
                        <code>network-error</code>.
                    </li>
                    <li><code>Abort previous action</code> debe registrar <code>volt:request-abort</code> y dejar
                        visible el resultado final de <code>Fast action</code>.</li>
                </ul>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(71,85,105,1);background:#020617;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
            <div style="display:grid;gap:8px;">
                <h2 style="margin:0;font-size:24px;">Panel unificado de resiliencia</h2>
                <p style="margin:0;color:#94a3b8;line-height:1.7;max-inline-size:78ch;">
                    Este panel normaliza la lectura de <code>retry</code>, <code>abort</code>, <code>stale</code>,
                    <code>network-error</code>, <code>timeout</code> y otros errores del lab usando el mismo lenguaje
                    visual, sin depender del log reciente.
                </p>
            </div>
            <button type="button"
                onclick="if(window.__spaLabRequestLab && typeof window.__spaLabRequestLab.clearResiliencePanel === 'function'){ window.__spaLabRequestLab.clearResiliencePanel(); }"
                style="border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.82);color:#e2e8f0;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Limpiar panel
            </button>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));align-items:start;">
            <article
                style="display:grid;gap:8px;border:1px solid rgba(56,189,248,0.2);background:rgba(8,47,73,0.18);border-radius:14px;padding:14px;">
                <strong>Ultimo escenario</strong>
                <span data-runtime-check="resilience-current-scenario" style="font-size:18px;color:#e0f2fe;">sin
                    incidentes</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.2);background:rgba(120,53,15,0.18);border-radius:14px;padding:14px;">
                <strong>Outcome</strong>
                <span data-runtime-check="resilience-current-outcome" style="font-size:18px;color:#fde68a;">sin
                    dato</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(16,185,129,0.2);background:rgba(6,95,70,0.18);border-radius:14px;padding:14px;">
                <strong>Scope</strong>
                <span data-runtime-check="resilience-current-scope" style="font-size:18px;color:#d1fae5;">sin
                    scope</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(217,70,239,0.2);background:rgba(88,28,135,0.16);border-radius:14px;padding:14px;">
                <strong>Status</strong>
                <span data-runtime-check="resilience-current-status" style="font-size:18px;color:#f5d0fe;">sin
                    dato</span>
            </article>
        </div>

        <div
            style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:16px;padding:16px;color:#cbd5e1;">
            <span data-runtime-check="resilience-current-target"
                style="font-size:13px;color:#93c5fd;line-height:1.7;">target = sin dato</span>
            <span data-runtime-check="resilience-current-message"
                style="font-size:13px;color:#cbd5e1;line-height:1.7;">mensaje = sin dato</span>
            <span data-runtime-check="resilience-current-final-url"
                style="font-size:13px;color:#93c5fd;line-height:1.7;">finalUrl = sin dato</span>
            <span data-runtime-check="resilience-current-captured-at"
                style="font-size:13px;color:#94a3b8;line-height:1.7;">capturado en = sin dato</span>
        </div>

        <div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));align-items:start;">
            <article data-runtime-check="resilience-scenario-card-retry"
                style="display:grid;gap:8px;border:1px solid rgba(74,222,128,0.2);background:rgba(20,83,45,0.16);border-radius:14px;padding:14px;">
                <strong style="color:#bbf7d0;">Retry</strong>
                <span data-runtime-check="resilience-scenario-retry"
                    style="font-size:14px;color:#dcfce7;">pendiente</span>
            </article>
            <article data-runtime-check="resilience-scenario-card-abort"
                style="display:grid;gap:8px;border:1px solid rgba(56,189,248,0.2);background:rgba(8,47,73,0.18);border-radius:14px;padding:14px;">
                <strong style="color:#7dd3fc;">Abort</strong>
                <span data-runtime-check="resilience-scenario-abort"
                    style="font-size:14px;color:#e0f2fe;">pendiente</span>
            </article>
            <article data-runtime-check="resilience-scenario-card-stale"
                style="display:grid;gap:8px;border:1px solid rgba(163,230,53,0.2);background:rgba(63,98,18,0.18);border-radius:14px;padding:14px;">
                <strong style="color:#d9f99d;">Stale</strong>
                <span data-runtime-check="resilience-scenario-stale"
                    style="font-size:14px;color:#ecfccb;">pendiente</span>
            </article>
            <article data-runtime-check="resilience-scenario-card-network-error"
                style="display:grid;gap:8px;border:1px solid rgba(59,130,246,0.2);background:rgba(30,64,175,0.18);border-radius:14px;padding:14px;">
                <strong style="color:#bfdbfe;">Network error</strong>
                <span data-runtime-check="resilience-scenario-network-error"
                    style="font-size:14px;color:#dbeafe;">pendiente</span>
            </article>
            <article data-runtime-check="resilience-scenario-card-timeout"
                style="display:grid;gap:8px;border:1px solid rgba(250,204,21,0.2);background:rgba(113,63,18,0.18);border-radius:14px;padding:14px;">
                <strong style="color:#fde68a;">Timeout</strong>
                <span data-runtime-check="resilience-scenario-timeout"
                    style="font-size:14px;color:#fef3c7;">pendiente</span>
            </article>
            <article data-runtime-check="resilience-scenario-card-protocol-error"
                style="display:grid;gap:8px;border:1px solid rgba(244,114,182,0.2);background:rgba(131,24,67,0.16);border-radius:14px;padding:14px;">
                <strong style="color:#f9a8d4;">Protocol error</strong>
                <span data-runtime-check="resilience-scenario-protocol-error"
                    style="font-size:14px;color:#fce7f3;">pendiente</span>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Navegacion controlada</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Estos botones usan <code>window.Volt.visit(...)</code> con <code>fallback:false</code> para que el
                runtime capture el error sin recarga completa.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <button type="button"
                onclick="window.Volt.visit('/runtimeRequestLabSlow', { timeout: 120, fallback: false });"
                style="border:1px solid rgba(250,204,21,0.3);background:rgba(113,63,18,0.28);color:#fef3c7;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Timeout navigation
            </button>
            <button type="button" onclick="window.Volt.visit('/runtimeRequestLabMissing', { fallback: false });"
                style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.24);color:#fee2e2;border-radius:12px;padding:10px 16px;cursor:pointer;">
                HTTP error navigation
            </button>
            <button type="button"
                onclick="window.Volt.visit('http://127.0.0.1:9/runtimeRequestLab', { timeout: 1200, fallback: false });"
                style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.22);color:#dbeafe;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Network error navigation
            </button>
            <button type="button"
                onclick="if(window.__spaLabRequestLab && typeof window.__spaLabRequestLab.runStaleNavigationScenario === 'function'){ window.__spaLabRequestLab.runStaleNavigationScenario(); }"
                data-volt-target="stale-navigation-button"
                style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.28);color:#dcfce7;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Stale navigation
            </button>
            <button type="button"
                onclick="if(window.__spaLabRequestLab && typeof window.__spaLabRequestLab.runAbortNavigationScenario === 'function'){ window.__spaLabRequestLab.runAbortNavigationScenario(); }"
                data-volt-target="abort-navigation-button"
                style="border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.28);color:#bae6fd;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Abort previous navigation
            </button>
            <button type="button"
                onclick="if(window.__spaLabRequestLab && typeof window.__spaLabRequestLab.runRetryNavigationScenario === 'function'){ window.__spaLabRequestLab.runRetryNavigationScenario(); }"
                style="border:1px solid rgba(134,239,172,0.28);background:rgba(22,101,52,0.22);color:#dcfce7;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Retry navigation once
            </button>
        </div>

        <div
            style="display:grid;gap:10px;border:1px solid rgba(71,85,105,1);background:#020617;border-radius:16px;padding:16px;color:#cbd5e1;">
            <strong>Rutas usadas por el laboratorio</strong>
            <ul style="margin:0;padding-inline-start:18px;display:grid;gap:8px;line-height:1.6;">
                <li><code>/runtimeRequestLabSlow</code>: responde lento para timeout/stale.</li>
                <li><code>/runtimeRequestLabRetryOnce</code>: falla una vez y luego responde bien para validar retry.
                </li>
                <li><code>/runtimeRequestLabMissing</code>: se deja sin ruta a proposito para producir <code>404</code>.
                </li>
                <li><code>http://127.0.0.1:9/runtimeRequestLab</code>: puerto cerrado para forzar
                    <code>network-error</code>.
                </li>
                <li><code>Stale navigation</code>: neutraliza el aborto de una visita lenta solo dentro del lab para
                    hacer visible <code>volt:request-stale</code> de forma determinista.</li>
                <li><code>Abort previous navigation</code>: inicia una visita lenta y la reemplaza con una nueva para
                    hacer visible <code>volt:request-abort</code>.</li>
            </ul>
        </div>

        <div
            style="display:grid;gap:14px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;color:#cbd5e1;">
            <strong style="color:#f8fafc;">Resumen persistido de lifecycle de navegacion</strong>
            <div
                style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));align-items:start;">
                <article
                    style="display:grid;gap:8px;border:1px solid rgba(56,189,248,0.2);background:rgba(8,47,73,0.18);border-radius:14px;padding:14px;">
                    <strong>Evento</strong>
                    <span data-runtime-check="nav-lifecycle-event" style="font-size:15px;color:#e0f2fe;">sin
                        resumen</span>
                </article>
                <article
                    style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.2);background:rgba(120,53,15,0.18);border-radius:14px;padding:14px;">
                    <strong>Outcome</strong>
                    <span data-runtime-check="nav-lifecycle-outcome" style="font-size:15px;color:#fde68a;">sin
                        dato</span>
                </article>
                <article
                    style="display:grid;gap:8px;border:1px solid rgba(16,185,129,0.2);background:rgba(6,95,70,0.18);border-radius:14px;padding:14px;">
                    <strong>Target</strong>
                    <span data-runtime-check="nav-lifecycle-target" style="font-size:15px;color:#d1fae5;">sin
                        target</span>
                </article>
                <article
                    style="display:grid;gap:8px;border:1px solid rgba(217,70,239,0.2);background:rgba(88,28,135,0.16);border-radius:14px;padding:14px;">
                    <strong>Status</strong>
                    <span data-runtime-check="nav-lifecycle-status" style="font-size:15px;color:#f5d0fe;">sin
                        dato</span>
                </article>
            </div>
            <span data-runtime-check="nav-lifecycle-message"
                style="font-size:13px;color:#cbd5e1;line-height:1.7;">mensaje sin dato</span>
            <span data-runtime-check="nav-lifecycle-final-url"
                style="font-size:13px;color:#93c5fd;line-height:1.7;">finalUrl = sin dato</span>
            <span data-runtime-check="nav-lifecycle-captured-at"
                style="font-size:13px;color:#94a3b8;line-height:1.7;">capturado en = sin dato</span>
        </div>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeEvents" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.18);color:#bae6fd;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver a runtimeEvents
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
<script data-runtime-request-lab-bootstrap="true">
window.__spaLabRequestLab = window.__spaLabRequestLab || {};
window.__spaLabRequestLab.defaultActionEndpoint = '/_volt/action';
window.__spaLabRequestLab.retrySummaryStorageKey = 'volt.requestLab.lastNavigationRetry';
window.__spaLabRequestLab.navigationLifecycleStorageKey = 'volt.requestLab.lastNavigationLifecycle';
window.__spaLabRequestLab.resilienceSummaryStorageKey = 'volt.requestLab.lastResilienceSummary';
window.__spaLabRequestLab.resilienceHistoryStorageKey = 'volt.requestLab.resilienceHistory';

window.__spaLabRequestLab.updateText = function(selector, value) {
    var element = document.querySelector(selector);
    if (element) {
        element.textContent = value;
    }
};

window.__spaLabRequestLab.writeRetrySummary = function(payload) {
    if (typeof sessionStorage === 'undefined') {
        return;
    }

    try {
        sessionStorage.setItem(
            window.__spaLabRequestLab.retrySummaryStorageKey,
            JSON.stringify(payload)
        );
    } catch (error) {
        return;
    }
};

window.__spaLabRequestLab.clearRetrySummary = function() {
    if (typeof sessionStorage === 'undefined') {
        return;
    }

    try {
        sessionStorage.removeItem(window.__spaLabRequestLab.retrySummaryStorageKey);
    } catch (error) {
        return;
    }
};

window.__spaLabRequestLab.writeNavigationLifecycleSummary = function(payload) {
    if (typeof sessionStorage === 'undefined') {
        return;
    }

    try {
        sessionStorage.setItem(
            window.__spaLabRequestLab.navigationLifecycleStorageKey,
            JSON.stringify(payload)
        );
    } catch (error) {
        return;
    }
};

window.__spaLabRequestLab.clearNavigationLifecycleSummary = function() {
    if (typeof sessionStorage === 'undefined') {
        return;
    }

    try {
        sessionStorage.removeItem(window.__spaLabRequestLab.navigationLifecycleStorageKey);
    } catch (error) {
        return;
    }
};

window.__spaLabRequestLab.readNavigationLifecycleSummary = function() {
    if (typeof sessionStorage === 'undefined') {
        return null;
    }

    try {
        var raw = sessionStorage.getItem(window.__spaLabRequestLab.navigationLifecycleStorageKey);
        if (!raw) {
            return null;
        }

        var parsed = JSON.parse(raw);
        return parsed && typeof parsed === 'object' ? parsed : null;
    } catch (error) {
        return null;
    }
};

window.__spaLabRequestLab.renderNavigationLifecycleSummaryCard = function() {
    var summary = window.__spaLabRequestLab.readNavigationLifecycleSummary();

    if (!document.querySelector('[data-runtime-check="nav-lifecycle-event"]')) {
        return;
    }

    if (!summary) {
        window.__spaLabRequestLab.updateText('[data-runtime-check="nav-lifecycle-event"]',
            'sin resumen persistido');
        window.__spaLabRequestLab.updateText('[data-runtime-check="nav-lifecycle-outcome"]', 'sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="nav-lifecycle-target"]', 'sin target');
        window.__spaLabRequestLab.updateText('[data-runtime-check="nav-lifecycle-status"]', 'sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="nav-lifecycle-message"]', 'mensaje sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="nav-lifecycle-final-url"]',
            'finalUrl = sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="nav-lifecycle-captured-at"]',
            'capturado en = sin dato');
        return;
    }

    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="nav-lifecycle-event"]',
        typeof summary.eventName === 'string' && summary.eventName !== '' ? summary.eventName : 'sin resumen'
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="nav-lifecycle-outcome"]',
        typeof summary.errorKind === 'string' && summary.errorKind !== '' ? summary.errorKind : 'sin dato'
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="nav-lifecycle-target"]',
        typeof summary.target === 'string' && summary.target !== '' ? summary.target : 'sin target'
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="nav-lifecycle-status"]',
        typeof summary.status === 'number' ? String(summary.status) : 'sin dato'
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="nav-lifecycle-message"]',
        typeof summary.message === 'string' && summary.message !== '' ? summary.message : 'mensaje sin dato'
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="nav-lifecycle-final-url"]',
        'finalUrl = ' + (typeof summary.finalUrl === 'string' && summary.finalUrl !== '' ? summary.finalUrl :
            'sin dato')
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="nav-lifecycle-captured-at"]',
        'capturado en = ' + (typeof summary.capturedAt === 'string' && summary.capturedAt !== '' ? summary
            .capturedAt : 'sin dato')
    );
};

window.__spaLabRequestLab.writeResilienceSummary = function(payload) {
    if (typeof sessionStorage === 'undefined') {
        return;
    }

    try {
        sessionStorage.setItem(
            window.__spaLabRequestLab.resilienceSummaryStorageKey,
            JSON.stringify(payload)
        );
    } catch (error) {
        return;
    }
};

window.__spaLabRequestLab.readResilienceSummary = function() {
    if (typeof sessionStorage === 'undefined') {
        return null;
    }

    try {
        var raw = sessionStorage.getItem(window.__spaLabRequestLab.resilienceSummaryStorageKey);
        if (!raw) {
            return null;
        }

        var parsed = JSON.parse(raw);
        return parsed && typeof parsed === 'object' ? parsed : null;
    } catch (error) {
        return null;
    }
};

window.__spaLabRequestLab.readResilienceHistory = function() {
    if (typeof sessionStorage === 'undefined') {
        return [];
    }

    try {
        var raw = sessionStorage.getItem(window.__spaLabRequestLab.resilienceHistoryStorageKey);
        if (!raw) {
            return [];
        }

        var parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
        return [];
    }
};

window.__spaLabRequestLab.writeResilienceHistory = function(history) {
    if (typeof sessionStorage === 'undefined') {
        return;
    }

    try {
        sessionStorage.setItem(
            window.__spaLabRequestLab.resilienceHistoryStorageKey,
            JSON.stringify(history)
        );
    } catch (error) {
        return;
    }
};

window.__spaLabRequestLab.clearResiliencePanel = function() {
    if (typeof sessionStorage !== 'undefined') {
        try {
            sessionStorage.removeItem(window.__spaLabRequestLab.resilienceSummaryStorageKey);
            sessionStorage.removeItem(window.__spaLabRequestLab.resilienceHistoryStorageKey);
        } catch (error) {
            // noop
        }
    }

    window.__spaLabRequestLab.renderResiliencePanel();
};

window.__spaLabRequestLab.resolveResilienceScenarioKey = function(eventName, meta, outcome) {
    if (eventName === 'volt:request-retry' && meta.type === 'navigation') {
        return 'retry';
    }

    if (eventName === 'volt:request-abort' && meta.type === 'navigation') {
        return 'abort';
    }

    if (eventName === 'volt:request-stale' && meta.type === 'navigation') {
        return 'stale';
    }

    if (eventName === 'volt:request-error' && outcome === 'network-error') {
        return 'network-error';
    }

    if (eventName === 'volt:request-error' && outcome === 'timeout') {
        return 'timeout';
    }

    if (eventName === 'volt:request-error' && outcome === 'protocol-error') {
        return 'protocol-error';
    }

    return null;
};

window.__spaLabRequestLab.recordResilienceIncident = function(eventName, meta, outcome, target) {
    var scenarioKey = window.__spaLabRequestLab.resolveResilienceScenarioKey(eventName, meta, outcome);
    var incident = {
        scenarioKey: scenarioKey,
        eventName: eventName,
        outcome: outcome,
        scope: typeof meta.type === 'string' && meta.type !== '' ? meta.type : 'unknown',
        target: target,
        message: typeof meta.message === 'string' && meta.message !== '' ? meta.message : 'sin mensaje tecnico',
        finalUrl: typeof meta.finalUrl === 'string' && meta.finalUrl !== '' ? meta.finalUrl : target,
        status: typeof meta.status === 'number' ? meta.status : null,
        capturedAt: new Date().toISOString()
    };
    var history = window.__spaLabRequestLab.readResilienceHistory();

    history.unshift(incident);
    if (history.length > 12) {
        history = history.slice(0, 12);
    }

    window.__spaLabRequestLab.writeResilienceSummary(incident);
    window.__spaLabRequestLab.writeResilienceHistory(history);
    window.__spaLabRequestLab.renderResiliencePanel();
};

window.__spaLabRequestLab.renderResilienceScenarioChip = function(scenarioKey, history) {
    var selector = '[data-runtime-check="resilience-scenario-' + scenarioKey + '"]';
    var match = null;
    var index = 0;

    for (index = 0; index < history.length; index += 1) {
        if (history[index] && history[index].scenarioKey === scenarioKey) {
            match = history[index];
            break;
        }
    }

    if (!match) {
        window.__spaLabRequestLab.updateText(selector, 'pendiente');
        return;
    }

    window.__spaLabRequestLab.updateText(
        selector,
        'observado · ' + (typeof match.outcome === 'string' && match.outcome !== '' ? match.outcome :
            'sin outcome')
    );
};

window.__spaLabRequestLab.renderResiliencePanel = function() {
    var summary = window.__spaLabRequestLab.readResilienceSummary();
    var history = window.__spaLabRequestLab.readResilienceHistory();

    if (!document.querySelector('[data-runtime-check="resilience-current-scenario"]')) {
        return;
    }

    if (!summary) {
        window.__spaLabRequestLab.updateText('[data-runtime-check="resilience-current-scenario"]',
            'sin incidentes');
        window.__spaLabRequestLab.updateText('[data-runtime-check="resilience-current-outcome"]', 'sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="resilience-current-scope"]', 'sin scope');
        window.__spaLabRequestLab.updateText('[data-runtime-check="resilience-current-status"]', 'sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="resilience-current-target"]',
            'target = sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="resilience-current-message"]',
            'mensaje = sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="resilience-current-final-url"]',
            'finalUrl = sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="resilience-current-captured-at"]',
            'capturado en = sin dato');
    } else {
        window.__spaLabRequestLab.updateText(
            '[data-runtime-check="resilience-current-scenario"]',
            typeof summary.scenarioKey === 'string' && summary.scenarioKey !== '' ? summary.scenarioKey :
            summary.eventName
        );
        window.__spaLabRequestLab.updateText(
            '[data-runtime-check="resilience-current-outcome"]',
            typeof summary.outcome === 'string' && summary.outcome !== '' ? summary.outcome : 'sin dato'
        );
        window.__spaLabRequestLab.updateText(
            '[data-runtime-check="resilience-current-scope"]',
            typeof summary.scope === 'string' && summary.scope !== '' ? summary.scope : 'sin scope'
        );
        window.__spaLabRequestLab.updateText(
            '[data-runtime-check="resilience-current-status"]',
            typeof summary.status === 'number' ? String(summary.status) : 'sin dato'
        );
        window.__spaLabRequestLab.updateText(
            '[data-runtime-check="resilience-current-target"]',
            'target = ' + (typeof summary.target === 'string' && summary.target !== '' ? summary.target :
                'sin dato')
        );
        window.__spaLabRequestLab.updateText(
            '[data-runtime-check="resilience-current-message"]',
            'mensaje = ' + (typeof summary.message === 'string' && summary.message !== '' ? summary.message :
                'sin dato')
        );
        window.__spaLabRequestLab.updateText(
            '[data-runtime-check="resilience-current-final-url"]',
            'finalUrl = ' + (typeof summary.finalUrl === 'string' && summary.finalUrl !== '' ? summary
                .finalUrl : 'sin dato')
        );
        window.__spaLabRequestLab.updateText(
            '[data-runtime-check="resilience-current-captured-at"]',
            'capturado en = ' + (typeof summary.capturedAt === 'string' && summary.capturedAt !== '' ? summary
                .capturedAt : 'sin dato')
        );
    }

    [
        'retry',
        'abort',
        'stale',
        'network-error',
        'timeout',
        'protocol-error'
    ].forEach(function(scenarioKey) {
        window.__spaLabRequestLab.renderResilienceScenarioChip(scenarioKey, history);
    });
};

window.__spaLabRequestLab.readRetrySummary = function() {
    if (typeof sessionStorage === 'undefined') {
        return null;
    }

    try {
        var raw = sessionStorage.getItem(window.__spaLabRequestLab.retrySummaryStorageKey);
        if (!raw) {
            return null;
        }

        var parsed = JSON.parse(raw);
        return parsed && typeof parsed === 'object' ? parsed : null;
    } catch (error) {
        return null;
    }
};

window.__spaLabRequestLab.renderRetrySummaryCard = function() {
    var summary = window.__spaLabRequestLab.readRetrySummary();

    if (!document.querySelector('[data-runtime-check="retry-summary-event"]')) {
        return;
    }

    if (!summary) {
        window.__spaLabRequestLab.updateText('[data-runtime-check="retry-summary-event"]',
            'sin resumen persistido');
        window.__spaLabRequestLab.updateText('[data-runtime-check="retry-summary-attempt"]', '0 / 0');
        window.__spaLabRequestLab.updateText('[data-runtime-check="retry-summary-error-kind"]', 'sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="retry-summary-status"]', 'sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="retry-summary-final-url"]',
            'finalUrl = sin dato');
        window.__spaLabRequestLab.updateText('[data-runtime-check="retry-summary-delay"]', 'retryDelayMs = 0');
        window.__spaLabRequestLab.updateText('[data-runtime-check="retry-summary-captured-at"]',
            'capturado en = sin dato');
        return;
    }

    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="retry-summary-event"]',
        typeof summary.eventName === 'string' && summary.eventName !== '' ? summary.eventName : 'sin resumen'
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="retry-summary-attempt"]',
        String(typeof summary.retryAttempt === 'number' ? summary.retryAttempt : 0) + ' / ' +
        String(typeof summary.retryAttempts === 'number' ? summary.retryAttempts : 0)
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="retry-summary-error-kind"]',
        typeof summary.errorKind === 'string' && summary.errorKind !== '' ? summary.errorKind : 'sin dato'
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="retry-summary-status"]',
        typeof summary.status === 'number' ? String(summary.status) : 'sin dato'
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="retry-summary-final-url"]',
        'finalUrl = ' + (typeof summary.finalUrl === 'string' && summary.finalUrl !== '' ? summary.finalUrl :
            'sin dato')
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="retry-summary-delay"]',
        'retryDelayMs = ' + String(typeof summary.retryDelayMs === 'number' ? summary.retryDelayMs : 0)
    );
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="retry-summary-captured-at"]',
        'capturado en = ' + (typeof summary.capturedAt === 'string' && summary.capturedAt !== '' ? summary
            .capturedAt : 'sin dato')
    );
};

window.__spaLabRequestLab.syncNetworkStatus = function() {
    var online = typeof navigator === 'undefined' ? true : navigator.onLine !== false;
    window.__spaLabRequestLab.updateText('[data-runtime-check="network-status-label"]', online ? 'Online' :
        'Offline');
    window.__spaLabRequestLab.updateText('[data-runtime-check="network-status-detail"]', 'navigator.onLine = ' + (
        online ? 'true' : 'false'));
};

window.__spaLabRequestLab.syncActionEndpointStatus = function(endpointValue) {
    var endpoint = typeof endpointValue === 'string' && endpointValue !== '' ?
        endpointValue :
        window.__spaLabRequestLab.defaultActionEndpoint;

    window.__spaLabRequestLab.updateText('[data-runtime-check="action-endpoint-status"]', endpoint);
    window.__spaLabRequestLab.updateText(
        '[data-runtime-check="action-retry-guidance"]',
        endpoint === window.__spaLabRequestLab.defaultActionEndpoint ?
        'Si una action falla por red o protocolo, el runtime no la reintenta solo: corrige la causa y vuelve a dispararla manualmente.' :
        'Endpoint roto preparado: la proxima action con error de red no se reintentara sola; restaura el endpoint y vuelve a dispararla manualmente.'
    );
};

window.__spaLabRequestLab.syncLastRequestEvent = function(eventName, detail) {
    var meta = detail && typeof detail === 'object' ? detail : {};
    var target = meta.action || meta.url || meta.finalUrl || meta.component || 'sin target';
    var outcome = meta.errorKind || meta.outcome || 'success';
    var retryCount = typeof meta.retryAttempt === 'number' ?
        String(meta.retryAttempt) + ' / ' + String(typeof meta.retryAttempts === 'number' ? meta.retryAttempts :
            0) :
        '0';

    window.__spaLabRequestLab.updateText('[data-runtime-check="request-last-event"]', eventName);
    window.__spaLabRequestLab.updateText('[data-runtime-check="request-last-outcome"]', outcome);
    window.__spaLabRequestLab.updateText('[data-runtime-check="request-last-target"]', target);
    window.__spaLabRequestLab.updateText('[data-runtime-check="request-last-retry-count"]', retryCount);

    if (
        eventName === 'volt:request-error' ||
        eventName === 'volt:request-retry' ||
        eventName === 'volt:request-abort' ||
        eventName === 'volt:request-stale'
    ) {
        window.__spaLabRequestLab.recordResilienceIncident(eventName, meta, outcome, target);
    }

    if (
        eventName === 'volt:request-retry' &&
        meta.type === 'navigation'
    ) {
        window.__spaLabRequestLab.writeRetrySummary({
            eventName: eventName,
            capturedAt: new Date().toISOString(),
            retryAttempt: typeof meta.retryAttempt === 'number' ? meta.retryAttempt : 0,
            retryAttempts: typeof meta.retryAttempts === 'number' ? meta.retryAttempts : 0,
            retryDelayMs: typeof meta.retryDelayMs === 'number' ? meta.retryDelayMs : 0,
            errorKind: typeof meta.errorKind === 'string' && meta.errorKind !== '' ? meta.errorKind :
                'unknown',
            status: typeof meta.status === 'number' ? meta.status : null,
            finalUrl: typeof meta.finalUrl === 'string' && meta.finalUrl !== '' ? meta.finalUrl : target,
            url: typeof meta.url === 'string' && meta.url !== '' ? meta.url : target
        });
        window.__spaLabRequestLab.renderRetrySummaryCard();
    }

    if (
        meta.type === 'navigation' &&
        (
            eventName === 'volt:request-retry' ||
            eventName === 'volt:request-abort' ||
            eventName === 'volt:request-stale'
        )
    ) {
        window.__spaLabRequestLab.writeNavigationLifecycleSummary({
            eventName: eventName,
            capturedAt: new Date().toISOString(),
            errorKind: typeof meta.errorKind === 'string' && meta.errorKind !== '' ? meta.errorKind :
                outcome,
            status: typeof meta.status === 'number' ? meta.status : null,
            message: typeof meta.message === 'string' && meta.message !== '' ? meta.message :
                'sin mensaje tecnico',
            finalUrl: typeof meta.finalUrl === 'string' && meta.finalUrl !== '' ? meta.finalUrl : target,
            target: target,
            url: typeof meta.url === 'string' && meta.url !== '' ? meta.url : target
        });
        window.__spaLabRequestLab.renderNavigationLifecycleSummaryCard();
    }
};

window.__spaLabRequestLab.findRequestLabRoot = function(button) {
    if (button && typeof button.closest === 'function') {
        return button.closest('[data-volt-root]');
    }

    return document.querySelector('[data-volt-root]');
};

window.__spaLabRequestLab.setBrokenActionEndpoint = function(button) {
    var root = window.__spaLabRequestLab.findRequestLabRoot(button);
    if (!root) {
        return;
    }

    root.setAttribute('data-volt-endpoint', 'http://127.0.0.1:9/_volt/action');
    window.__spaLabRequestLab.syncActionEndpointStatus('http://127.0.0.1:9/_volt/action');
};

window.__spaLabRequestLab.restoreActionEndpoint = function(button) {
    var root = window.__spaLabRequestLab.findRequestLabRoot(button);
    if (!root) {
        return;
    }

    root.setAttribute('data-volt-endpoint', window.__spaLabRequestLab.defaultActionEndpoint);
    window.__spaLabRequestLab.syncActionEndpointStatus(window.__spaLabRequestLab.defaultActionEndpoint);
};

window.__spaLabRequestLab.syncVisibleState = function() {
    var root = document.querySelector('[data-volt-root]');
    var endpoint = root && typeof root.getAttribute === 'function' ?
        root.getAttribute('data-volt-endpoint') || window.__spaLabRequestLab.defaultActionEndpoint :
        window.__spaLabRequestLab.defaultActionEndpoint;

    window.__spaLabRequestLab.syncActionEndpointStatus(endpoint);
    window.__spaLabRequestLab.syncNetworkStatus();
    window.__spaLabRequestLab.renderResiliencePanel();
    window.__spaLabRequestLab.renderRetrySummaryCard();
    window.__spaLabRequestLab.renderNavigationLifecycleSummaryCard();
};

window.__spaLabRequestLab.runStaleNavigationScenario = function() {
    if (
        !window.Volt ||
        typeof window.Volt.visit !== 'function' ||
        typeof AbortController === 'undefined' ||
        !AbortController.prototype ||
        typeof AbortController.prototype.abort !== 'function'
    ) {
        return;
    }

    var originalAbort = AbortController.prototype.abort;
    var restored = false;

    function restoreAbort() {
        if (restored) {
            return;
        }

        AbortController.prototype.abort = originalAbort;
        restored = true;
    }

    AbortController.prototype.abort = function() {
        restoreAbort();
    };

    window.__spaLabRequestLab.clearNavigationLifecycleSummary();
    window.Volt.visit('/runtimeRequestLabSlow', {
        timeout: 4000,
        fallback: false
    });
    window.setTimeout(function() {
        window.Volt.visit('/runtimeRequestLab', {
            fallback: false
        });
        window.setTimeout(restoreAbort, 0);
    }, 40);
    window.setTimeout(restoreAbort, 250);
};

window.__spaLabRequestLab.runAbortNavigationScenario = function() {
    if (
        !window.Volt ||
        typeof window.Volt.visit !== 'function'
    ) {
        return;
    }

    window.__spaLabRequestLab.clearNavigationLifecycleSummary();
    window.Volt.visit('/runtimeRequestLabSlow', {
        timeout: 4000,
        fallback: false
    });
    window.setTimeout(function() {
        window.Volt.visit('/runtimeEvents', {
            fallback: false
        });
    }, 40);
};

window.__spaLabRequestLab.runRetryNavigationScenario = function() {
    if (
        !window.Volt ||
        typeof window.Volt.visit !== 'function'
    ) {
        return;
    }

    window.__spaLabRequestLab.clearRetrySummary();
    window.__spaLabRequestLab.writeRetrySummary({
        eventName: 'pending',
        capturedAt: new Date().toISOString(),
        retryAttempt: 0,
        retryAttempts: 1,
        retryDelayMs: 120,
        errorKind: 'pending',
        status: null,
        finalUrl: '/runtimeRequestLabRetryOnce',
        url: '/runtimeRequestLabRetryOnce'
    });

    window.Volt.visit('/runtimeRequestLabRetryOnce', {
        fallback: false,
        retry: {
            attempts: 1,
            delay: 120
        }
    });
};


window.__spaLabRequestLab.handleNavigationLifecycleEvent = function(event) {
    window.__spaLabRequestLab.syncLastRequestEvent(event.type, event.detail);
};

if (!window.__spaLabRequestLab.listenersAttached) {
    document.addEventListener('volt:request-start', function(event) {
        window.__spaLabRequestLab.syncLastRequestEvent('volt:request-start', event.detail);
    });
    document.addEventListener('volt:request-retry', function(event) {
        window.__spaLabRequestLab.syncLastRequestEvent('volt:request-retry', event.detail);
    });
    document.addEventListener('volt:request-error', function(event) {
        window.__spaLabRequestLab.syncLastRequestEvent('volt:request-error', event.detail);
    });
    document.addEventListener('volt:request-abort', function(event) {
        window.__spaLabRequestLab.syncLastRequestEvent('volt:request-abort', event.detail);
    });
    document.addEventListener('volt:request-stale', function(event) {
        window.__spaLabRequestLab.syncLastRequestEvent('volt:request-stale', event.detail);
    });
    document.addEventListener('volt:navigated', function() {
        window.setTimeout(function() {
            window.__spaLabRequestLab.syncVisibleState();
        }, 0);
    });
    window.addEventListener('volt:request-retry', window.__spaLabRequestLab.handleNavigationLifecycleEvent);
    window.addEventListener('volt:request-abort', window.__spaLabRequestLab.handleNavigationLifecycleEvent);
    window.addEventListener('volt:request-stale', window.__spaLabRequestLab.handleNavigationLifecycleEvent);
    window.addEventListener('online', window.__spaLabRequestLab.syncNetworkStatus);
    window.addEventListener('offline', window.__spaLabRequestLab.syncNetworkStatus);
    window.__spaLabRequestLab.listenersAttached = true;
}

window.__spaLabRequestLab.syncVisibleState();
</script>
@endsection

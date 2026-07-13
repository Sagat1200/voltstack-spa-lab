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
                onclick="(function(button){var root=button.closest('[data-volt-root]'); if(root){root.setAttribute('data-volt-endpoint','http://127.0.0.1:9/_volt/action');}})(this)"
                style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.22);color:#dbeafe;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Preparar endpoint roto
            </button>
            <button type="button"
                onclick="(function(button){var root=button.closest('[data-volt-root]'); if(root){root.setAttribute('data-volt-endpoint','/_volt/action');}})(this)"
                style="border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.52);color:#e2e8f0;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Restaurar endpoint normal
            </button>
            <span style="font-size:13px;color:#94a3b8;line-height:1.6;">
                Para forzar <code>network-error</code> en action: pulsa <code>Preparar endpoint roto</code> y luego
                ejecuta <code>Fast action</code>.
            </span>
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
                onclick="window.Volt.visit('/runtimeRequestLabSlow', { timeout: 4000, fallback: false }); window.setTimeout(function(){ window.Volt.visit('/runtimeEvents', { fallback: false }); }, 40);"
                data-volt-target="abort-navigation-button"
                style="border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.28);color:#bae6fd;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Abort previous navigation
            </button>
            <button type="button"
                onclick="window.Volt.visit('/runtimeRequestLabRetryOnce', { fallback: false, retry: { attempts: 1, delay: 120 } });"
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
<script>
    window.__spaLabRequestLab = window.__spaLabRequestLab || {};

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
</script>
@endsection
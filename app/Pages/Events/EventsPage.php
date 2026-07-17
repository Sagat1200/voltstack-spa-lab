<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Events;

use VoltStack\Runtime\Component\Component;

final class EventsPage extends Component
{
    public string $title = 'Events Demo';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = sprintf('events-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="events-mode">
<script data-volt-head-key="events-demo-bridge">
(() => {
    if (window.__voltEventsDemoBridgeInstalled) {
        return;
    }

    window.__voltEventsDemoBridgeInstalled = true;

    function state() {
        return window.Volt && window.Volt.state ? window.Volt.state : null;
    }

    function setClient(path, value) {
        const api = state();

        if (!api) {
            return;
        }

        api.set(path, value, {
            scope: 'client'
        });
    }

    function setShared(path, value) {
        const api = state();

        if (!api) {
            return;
        }

        api.set(path, value, {
            scope: 'shared'
        });
    }

    function increment(path, scope) {
        const api = state();

        if (!api) {
            return;
        }

        const current = api.get(path, {
            scope
        });
        const nextValue = typeof current === 'number' ? current + 1 : 1;

        api.set(path, nextValue, {
            scope
        });
    }

    function reflectDispatch(event) {
        const api = state();

        if (!api) {
            return;
        }

        const detail = event && event.detail && typeof event.detail === 'object' ?
            event.detail : {};
        const originalEvent = detail.originalEvent && typeof detail.originalEvent === 'object' ?
            detail.originalEvent :
            null;
        const sourceElement = detail.sourceElement && typeof detail.sourceElement === 'object' ?
            detail.sourceElement :
            null;

        increment('events.dispatchCount', 'client');
        setClient('events.lastDispatchName', event.type);
        setClient('events.lastDirective', typeof detail.directive === 'string' ? detail.directive :
            '(sin directive)');
        setClient('events.lastOriginalType', originalEvent && typeof originalEvent.type === 'string' ? originalEvent
            .type : '(sin originalEvent)');
        setClient('events.lastSourceTag', sourceElement && sourceElement.tagName ? sourceElement.tagName
            .toLowerCase() : '(sin source)');
        setClient('events.lastScopeId', typeof detail.scopeId === 'string' ? detail.scopeId : '(sin scopeId)');
        setClient('events.lastComponent', typeof detail.component === 'string' && detail.component !== '' ? detail
            .component : '(sin component)');
        setClient('events.lastDispatchAt', new Date().toLocaleTimeString());

        if (event.type === 'demo.events.audit') {
            increment('events.auditCount', 'shared');
        }

        if (event.type === 'demo.events.submit') {
            increment('events.submitCount', 'shared');
        }

        if (event.type === 'demo.events.enter') {
            increment('events.enterCount', 'shared');
        }
    }

    [
        'demo.events.alpha',
        'demo.events.audit',
        'demo.events.submit',
        'demo.events.enter',
    ].forEach((name) => {
        document.addEventListener(name, reflectDispatch);
    });
})();
</script>
<script data-volt-head-key="events-resilience-panel">
(() => {
    function updateText(selector, value) {
        const element = document.querySelector(selector);
        if (element) {
            element.textContent = value;
        }
    }

    function readJson(key) {
        if (typeof sessionStorage === 'undefined') {
            return null;
        }

        try {
            const raw = sessionStorage.getItem(key);
            if (!raw) {
                return null;
            }

            return JSON.parse(raw);
        } catch (error) {
            return null;
        }
    }

    function readArray(key) {
        const parsed = readJson(key);
        return Array.isArray(parsed) ? parsed : [];
    }

    function renderLifecycleSummary() {
        if (!document.querySelector('[data-runtime-check="nav-lifecycle-event"]')) {
            return;
        }

        const summary = readJson('volt.requestLab.lastNavigationLifecycle');
        if (!summary || typeof summary !== 'object') {
            updateText('[data-runtime-check="nav-lifecycle-event"]', 'sin resumen persistido');
            updateText('[data-runtime-check="nav-lifecycle-outcome"]', 'sin dato');
            updateText('[data-runtime-check="nav-lifecycle-target"]', 'sin target');
            updateText('[data-runtime-check="nav-lifecycle-status"]', 'sin dato');
            updateText('[data-runtime-check="nav-lifecycle-message"]', 'mensaje sin dato');
            updateText('[data-runtime-check="nav-lifecycle-final-url"]', 'finalUrl = sin dato');
            updateText('[data-runtime-check="nav-lifecycle-captured-at"]', 'capturado en = sin dato');
            return;
        }

        updateText('[data-runtime-check="nav-lifecycle-event"]', typeof summary.eventName === 'string' && summary
            .eventName !== '' ? summary.eventName : 'sin resumen');
        updateText('[data-runtime-check="nav-lifecycle-outcome"]', typeof summary.errorKind === 'string' && summary
            .errorKind !== '' ? summary.errorKind : (typeof summary.outcome === 'string' ? summary.outcome :
                'sin dato'));
        updateText('[data-runtime-check="nav-lifecycle-target"]', typeof summary.target === 'string' && summary
            .target !== '' ? summary.target : 'sin target');
        updateText('[data-runtime-check="nav-lifecycle-status"]', typeof summary.status === 'number' ? String(
            summary.status) : 'sin dato');
        updateText('[data-runtime-check="nav-lifecycle-message"]', typeof summary.message === 'string' && summary
            .message !== '' ? summary.message : 'mensaje sin dato');
        updateText('[data-runtime-check="nav-lifecycle-final-url"]', 'finalUrl = ' + (typeof summary.finalUrl ===
            'string' && summary.finalUrl !== '' ? summary.finalUrl : 'sin dato'));
        updateText('[data-runtime-check="nav-lifecycle-captured-at"]', 'capturado en = ' + (typeof summary
            .capturedAt === 'string' && summary.capturedAt !== '' ? summary.capturedAt : 'sin dato'));
    }

    function renderScenarioChip(scenarioKey, history) {
        const match = history.find((entry) => entry && entry.scenarioKey === scenarioKey);
        if (!match) {
            updateText('[data-runtime-check="resilience-scenario-' + scenarioKey + '"]', 'pendiente');
            return;
        }

        updateText(
            '[data-runtime-check="resilience-scenario-' + scenarioKey + '"]',
            'observado · ' + (typeof match.outcome === 'string' && match.outcome !== '' ? match.outcome :
                'sin outcome')
        );
    }

    function renderIncidentSessionStatus() {
        const summary = readJson('volt.requestLab.lastResilienceSummary');
        const history = readArray('volt.requestLab.resilienceHistory');
        const badge = document.querySelector('[data-runtime-check="events-session-incidents-badge"]');
        const detail = document.querySelector('[data-runtime-check="events-session-incidents-detail"]');

        if (!badge || !detail) {
            return;
        }

        if (!summary || typeof summary !== 'object') {
            badge.textContent = 'Sin incidentes en sesion';
            badge.style.borderColor = 'rgba(148,163,184,0.28)';
            badge.style.background = 'rgba(15,23,42,0.82)';
            badge.style.color = '#cbd5e1';
            detail.textContent = 'El flujo QA puede arrancar limpio desde esta pantalla o saltar al request lab.';
            return;
        }

        const scenarioKey = typeof summary.scenarioKey === 'string' && summary.scenarioKey !== '' ? summary
            .scenarioKey : 'incidente';
        const count = Array.isArray(history) ? history.length : 0;
        badge.textContent = 'Hay incidentes en sesion';
        badge.style.borderColor = 'rgba(248,113,113,0.28)';
        badge.style.background = 'rgba(127,29,29,0.22)';
        badge.style.color = '#fee2e2';
        detail.textContent = 'Ultimo incidente: ' + scenarioKey + ' · registros persistidos: ' + String(count);
    }

    function renderResiliencePanel() {
        if (!document.querySelector('[data-runtime-check="resilience-current-scenario"]')) {
            return;
        }

        const summary = readJson('volt.requestLab.lastResilienceSummary');
        const history = readArray('volt.requestLab.resilienceHistory');

        if (!summary || typeof summary !== 'object') {
            updateText('[data-runtime-check="resilience-current-scenario"]', 'sin incidentes');
            updateText('[data-runtime-check="resilience-current-outcome"]', 'sin dato');
            updateText('[data-runtime-check="resilience-current-scope"]', 'sin scope');
            updateText('[data-runtime-check="resilience-current-status"]', 'sin dato');
            updateText('[data-runtime-check="resilience-current-target"]', 'target = sin dato');
            updateText('[data-runtime-check="resilience-current-message"]', 'mensaje = sin dato');
            updateText('[data-runtime-check="resilience-current-final-url"]', 'finalUrl = sin dato');
            updateText('[data-runtime-check="resilience-current-captured-at"]', 'capturado en = sin dato');
        } else {
            updateText(
                '[data-runtime-check="resilience-current-scenario"]',
                typeof summary.scenarioKey === 'string' && summary.scenarioKey !== '' ? summary.scenarioKey : (
                    typeof summary.eventName === 'string' ? summary.eventName : 'sin escenario')
            );
            updateText('[data-runtime-check="resilience-current-outcome"]', typeof summary.outcome === 'string' &&
                summary.outcome !== '' ? summary.outcome : 'sin dato');
            updateText('[data-runtime-check="resilience-current-scope"]', typeof summary.scope === 'string' &&
                summary.scope !== '' ? summary.scope : 'sin scope');
            updateText('[data-runtime-check="resilience-current-status"]', typeof summary.status === 'number' ?
                String(summary.status) : 'sin dato');
            updateText('[data-runtime-check="resilience-current-target"]', 'target = ' + (typeof summary.target ===
                'string' && summary.target !== '' ? summary.target : 'sin dato'));
            updateText('[data-runtime-check="resilience-current-message"]', 'mensaje = ' + (typeof summary
                .message === 'string' && summary.message !== '' ? summary.message : 'sin dato'));
            updateText('[data-runtime-check="resilience-current-final-url"]', 'finalUrl = ' + (typeof summary
                .finalUrl === 'string' && summary.finalUrl !== '' ? summary.finalUrl : 'sin dato'));
            updateText('[data-runtime-check="resilience-current-captured-at"]', 'capturado en = ' + (typeof summary
                .capturedAt === 'string' && summary.capturedAt !== '' ? summary.capturedAt : 'sin dato'));
        }

        [
            'retry',
            'abort',
            'stale',
            'network-error',
            'timeout',
            'protocol-error'
        ].forEach((scenarioKey) => renderScenarioChip(scenarioKey, history));
    }

    function clearResiliencePanel() {
        if (typeof sessionStorage === 'undefined') {
            return;
        }

        try {
            sessionStorage.removeItem('volt.requestLab.lastResilienceSummary');
            sessionStorage.removeItem('volt.requestLab.resilienceHistory');
        } catch (error) {
            return;
        }

        renderResiliencePanel();
    }

    window.__spaLabEventsResiliencePanel = window.__spaLabEventsResiliencePanel || {};
    window.__spaLabEventsResiliencePanel.render = function() {
        if (window.__spaLabRequestLab) {
            if (typeof window.__spaLabRequestLab.renderNavigationLifecycleSummaryCard === 'function') {
                window.__spaLabRequestLab.renderNavigationLifecycleSummaryCard();
            }
            if (typeof window.__spaLabRequestLab.renderResiliencePanel === 'function') {
                window.__spaLabRequestLab.renderResiliencePanel();
            }
        } else {
            renderLifecycleSummary();
            renderResiliencePanel();
        }
        renderIncidentSessionStatus();
    };
    window.__spaLabEventsResiliencePanel.clear = clearResiliencePanel;

    document.addEventListener('DOMContentLoaded', () => {
        window.__spaLabEventsResiliencePanel.render();
    });
    document.addEventListener('volt:navigated', () => {
        window.setTimeout(() => window.__spaLabEventsResiliencePanel.render(), 0);
    });
    window.setTimeout(() => window.__spaLabEventsResiliencePanel.render(), 0);
})();
</script>
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;" data-runtime-events-demo>
    <section
        style="display:grid;gap:16px;border:1px solid rgba(249,115,22,0.24);background:linear-gradient(135deg,rgba(124,45,18,0.88),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#fff7ed;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(253,186,116,0.32);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#fdba74;">Runtime
            Events MVP</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#fed7aa;line-height:1.75;max-inline-size:74ch;">
                Esta pantalla valida el MVP real de <code>volt:on</code> y <code>volt:dispatch</code>. Los eventos DOM
                mutan <code>window.Volt.state</code> directamente y los <code>CustomEvent</code> emitidos por
                <code>volt:dispatch</code> se reflejan visualmente en el inspector de abajo.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(253,186,116,0.18);background:rgba(124,45,18,0.28);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#fdba74;">Request
                marker</span>
            <strong style="font-size:14px;color:#fff7ed;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#fed7aa;">La limpieza borra state, pero un listener con
                <code>.once</code> queda consumido hasta que la pagina se vuelva a montar.</span>
            <span data-runtime-check="events-session-incidents-badge"
                style="display:inline-flex;align-items:center;border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.82);color:#cbd5e1;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">
                Sin incidentes en sesion
            </span>
            <a href="/runtimeRequestLab" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid rgba(253,186,116,0.28);background:rgba(124,45,18,0.22);color:#fff7ed;border-radius:10px;padding:10px 14px;text-decoration:none;">
                Ir a RequestLab
            </a>
            <span data-runtime-check="events-session-incidents-detail"
                style="font-size:13px;color:#fed7aa;line-height:1.7;">
                El flujo QA puede arrancar limpio desde esta pantalla o saltar al request lab.
            </span>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Reset y contexto</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Usa este reset para vaciar el laboratorio y volver a disparar los flujos. Si ya gastaste el ejemplo con
                <code>click.once</code>, hace falta recargar o volver a entrar a la ruta para rearmarlo.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <button type="button"
                volt:on="click -> state:delete client:events | click -> state:delete shared:events | click -> state:set client:events.lastAction = 'reset-state'"
                style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Limpiar state de eventos
            </button>
            <span style="color:#94a3b8;font-size:13px;">Ultima accion:</span>
            <strong volt:text="client:events.lastAction ?? 'sin acciones aun'" style="color:#f8fafc;">sin acciones
                aun</strong>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(56,189,248,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Resumen persistido de navegacion</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;max-inline-size:74ch;">
                Cuando llegas aqui desde <code>/runtimeRequestLab</code>, esta tarjeta refleja el ultimo lifecycle de
                navegacion persistido por el lab para que <code>abort</code>, <code>stale</code> o <code>retry</code>
                queden visibles sin abrir DevTools.
            </p>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));align-items:start;">
            <article
                style="display:grid;gap:8px;border:1px solid rgba(56,189,248,0.2);background:rgba(8,47,73,0.18);border-radius:14px;padding:14px;">
                <strong style="color:#7dd3fc;">Evento</strong>
                <span data-runtime-check="nav-lifecycle-event" style="font-size:15px;color:#e0f2fe;">sin resumen</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.2);background:rgba(120,53,15,0.18);border-radius:14px;padding:14px;">
                <strong style="color:#fde68a;">Outcome</strong>
                <span data-runtime-check="nav-lifecycle-outcome" style="font-size:15px;color:#fef3c7;">sin dato</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(16,185,129,0.2);background:rgba(6,95,70,0.18);border-radius:14px;padding:14px;">
                <strong style="color:#d1fae5;">Target</strong>
                <span data-runtime-check="nav-lifecycle-target" style="font-size:15px;color:#d1fae5;">sin target</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(217,70,239,0.2);background:rgba(88,28,135,0.16);border-radius:14px;padding:14px;">
                <strong style="color:#f5d0fe;">Status</strong>
                <span data-runtime-check="nav-lifecycle-status" style="font-size:15px;color:#f5d0fe;">sin dato</span>
            </article>
        </div>

        <span data-runtime-check="nav-lifecycle-message" style="font-size:13px;color:#cbd5e1;line-height:1.7;">mensaje
            sin dato</span>
        <span data-runtime-check="nav-lifecycle-final-url"
            style="font-size:13px;color:#93c5fd;line-height:1.7;">finalUrl = sin dato</span>
        <span data-runtime-check="nav-lifecycle-captured-at"
            style="font-size:13px;color:#94a3b8;line-height:1.7;">capturado en = sin dato</span>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(71,85,105,1);background:#020617;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
            <div style="display:grid;gap:8px;">
                <h2 style="margin:0;font-size:24px;">Panel unificado de resiliencia</h2>
                <p style="margin:0;color:#94a3b8;line-height:1.7;max-inline-size:76ch;">
                    Relee el ultimo incidente persistido por <code>/runtimeRequestLab</code> y marca los escenarios ya
                    observados en esta sesion.
                </p>
            </div>
            <button type="button"
                onclick="if(window.__spaLabEventsResiliencePanel && typeof window.__spaLabEventsResiliencePanel.clear === 'function'){ window.__spaLabEventsResiliencePanel.clear(); }"
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
            <article
                style="display:grid;gap:8px;border:1px solid rgba(74,222,128,0.2);background:rgba(20,83,45,0.16);border-radius:14px;padding:14px;">
                <strong style="color:#bbf7d0;">Retry</strong>
                <span data-runtime-check="resilience-scenario-retry"
                    style="font-size:14px;color:#dcfce7;">pendiente</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(56,189,248,0.2);background:rgba(8,47,73,0.18);border-radius:14px;padding:14px;">
                <strong style="color:#7dd3fc;">Abort</strong>
                <span data-runtime-check="resilience-scenario-abort"
                    style="font-size:14px;color:#e0f2fe;">pendiente</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(163,230,53,0.2);background:rgba(63,98,18,0.18);border-radius:14px;padding:14px;">
                <strong style="color:#d9f99d;">Stale</strong>
                <span data-runtime-check="resilience-scenario-stale"
                    style="font-size:14px;color:#ecfccb;">pendiente</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(59,130,246,0.2);background:rgba(30,64,175,0.18);border-radius:14px;padding:14px;">
                <strong style="color:#bfdbfe;">Network error</strong>
                <span data-runtime-check="resilience-scenario-network-error"
                    style="font-size:14px;color:#dbeafe;">pendiente</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(250,204,21,0.2);background:rgba(113,63,18,0.18);border-radius:14px;padding:14px;">
                <strong style="color:#fde68a;">Timeout</strong>
                <span data-runtime-check="resilience-scenario-timeout"
                    style="font-size:14px;color:#fef3c7;">pendiente</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(244,114,182,0.2);background:rgba(131,24,67,0.16);border-radius:14px;padding:14px;">
                <strong style="color:#f9a8d4;">Protocol error</strong>
                <span data-runtime-check="resilience-scenario-protocol-error"
                    style="font-size:14px;color:#fce7f3;">pendiente</span>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(14,165,233,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;"><code>volt:on</code> sobre eventos DOM</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Aqui se prueban acciones soportadas por el MVP: <code>state:set</code>, <code>state:toggle</code>,
                <code>state:delete</code> y <code>dispatch:*</code> desde eventos nativos con modificadores.
            </p>
        </div>

        <div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article
                style="display:grid;gap:12px;border:1px solid rgba(14,165,233,0.20);background:rgba(8,47,73,0.18);border-radius:16px;padding:18px;">
                <strong style="color:#7dd3fc;">input -&gt; state:set</strong>
                <input type="text" placeholder="Escribe para mutar client:events.draft"
                    volt:on="input -> state:set client:events.draft = $event.target.value | input -> state:set client:events.lastAction = 'input-draft'"
                    style="width:100%;border:1px solid rgba(148,163,184,0.18);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
                <span style="color:#94a3b8;">Valor actual:</span>
                <strong volt:text="client:events.draft ?? '(vacio)'" style="color:#e0f2fe;">(vacio)</strong>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.18);border-radius:16px;padding:18px;">
                <strong style="color:#e9d5ff;">change -&gt; state:set boolean</strong>
                <label style="display:flex;gap:10px;align-items:center;color:#f5d0fe;">
                    <input type="checkbox"
                        volt:on="change -> state:set shared:events.sharedReady = $event.target.checked | change -> state:set client:events.lastAction = 'change-shared-ready'">
                    Compartir shared:events.sharedReady
                </label>
                <span volt:show="shared:events.sharedReady === true"
                    style="display:inline-flex;width:max-content;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                <span volt:show.hide="shared:events.sharedReady === true"
                    style="display:inline-flex;width:max-content;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(34,197,94,0.20);background:rgba(6,78,59,0.18);border-radius:16px;padding:18px;">
                <strong style="color:#bbf7d0;">click / click.once</strong>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button"
                        volt:on="click -> state:toggle client:events.panelOpen | click -> state:set client:events.lastAction = 'toggle-panel'"
                        style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.18);color:#dcfce7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Alternar panel
                    </button>
                    <button type="button"
                        volt:on="click.once -> state:set client:events.onceFired = true | click.once -> state:set client:events.lastAction = 'click-once-fired'"
                        style="border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.18);color:#fde68a;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Disparar una sola vez
                    </button>
                </div>
                <div volt:show="client:events.panelOpen === true"
                    style="display:grid;gap:6px;border:1px solid rgba(34,197,94,0.24);background:rgba(20,83,45,0.20);border-radius:12px;padding:12px;">
                    <strong style="color:#bbf7d0;">Panel visible por state:toggle</strong>
                    <span style="color:#dcfce7;">Se oculta cuando el mismo boton vuelve a alternar el valor.</span>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    <span volt:show="client:events.onceFired === true"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">once
                        consumido</span>
                    <span volt:show.hide="client:events.onceFired === true"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.82);color:#cbd5e1;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">esperando
                        primer click</span>
                </div>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(244,114,182,0.20);background:rgba(131,24,67,0.16);border-radius:16px;padding:18px;">
                <strong style="color:#fbcfe8;">click.self y click.stop</strong>
                <div volt:on="click.self -> state:toggle client:events.selfOnly | click.self -> state:set client:events.lastAction = 'self-container'"
                    style="display:grid;gap:12px;border:1px dashed rgba(244,114,182,0.40);border-radius:14px;padding:14px;">
                    <span style="color:#f9a8d4;">Haz click en el borde vacio para activar <code>.self</code>.</span>
                    <button type="button"
                        volt:on="click.stop -> state:set client:events.lastNested = 'inner-button' | click.stop -> state:set client:events.lastAction = 'stop-inner-button'"
                        style="inline-size:max-content;border:1px solid rgba(244,114,182,0.28);background:rgba(131,24,67,0.22);color:#fce7f3;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Boton interno con stop
                    </button>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    <span volt:show="client:events.selfOnly === true"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">self
                        activo</span>
                    <span volt:show.hide="client:events.selfOnly === true"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.82);color:#cbd5e1;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">self
                        inactivo</span>
                    <strong volt:text="client:events.lastNested ?? 'sin click interno'" style="color:#fce7f3;">sin click
                        interno</strong>
                </div>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(96,165,250,0.20);background:rgba(30,64,175,0.16);border-radius:16px;padding:18px;">
                <strong style="color:#bfdbfe;">keydown.enter.prevent -&gt; dispatch</strong>
                <input type="text" placeholder="Pulsa Enter para emitir demo.events.enter"
                    volt:on="keydown.enter.prevent -> dispatch:demo.events.enter | keydown.enter.prevent -> state:set client:events.lastAction = 'keydown-enter'"
                    style="inline-size:100%;border:1px solid rgba(148,163,184,0.18);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
                <span style="color:#dbeafe;">Cada Enter incrementa un contador shared y deja huella en el inspector de
                    dispatch.</span>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(45,212,191,0.20);background:rgba(17,94,89,0.16);border-radius:16px;padding:18px;">
                <strong style="color:#99f6e4;">submit.prevent -&gt; dispatch</strong>
                <form
                    volt:on="submit.prevent -> dispatch:demo.events.submit | submit.prevent -> state:set client:events.lastAction = 'submit-demo-form'"
                    style="display:grid;gap:12px;">
                    <input type="text" placeholder="El submit no recarga la pagina"
                        style="inline-size:100%;border:1px solid rgba(148,163,184,0.18);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
                    <button type="submit"
                        style="inline-size:max-content;border:1px solid rgba(45,212,191,0.28);background:rgba(17,94,89,0.22);color:#ccfbf1;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Emitir submit custom
                    </button>
                </form>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(245,158,11,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;"><code>volt:dispatch</code> directo desde click</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Estos botones emiten <code>CustomEvent</code> al hacer click. Un bridge minimo en el
                <code>head</code> escucha esos eventos y escribe sus detalles mas utiles en el runtime state.
            </p>
        </div>

        <div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article
                style="display:grid;gap:12px;border:1px solid rgba(245,158,11,0.20);background:rgba(120,53,15,0.16);border-radius:16px;padding:18px;">
                <strong style="color:#fde68a;">Evento simple</strong>
                <button type="button" volt:dispatch="demo.events.alpha"
                    style="inline-size:max-content;border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.22);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                    Emitir demo.events.alpha
                </button>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#fef3c7;font-size:12px;line-height:1.7;">volt:dispatch="demo.events.alpha"</pre>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.18);border-radius:16px;padding:18px;">
                <strong style="color:#e9d5ff;">Multiples eventos con |</strong>
                <button type="button" volt:dispatch="demo.events.alpha | demo.events.audit"
                    style="inline-size:max-content;border:1px solid rgba(168,85,247,0.28);background:rgba(88,28,135,0.22);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                    Emitir alpha + audit
                </button>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#f5d0fe;font-size:12px;line-height:1.7;">volt:dispatch="demo.events.alpha | demo.events.audit"</pre>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(14,165,233,0.20);background:rgba(8,47,73,0.18);border-radius:16px;padding:18px;">
                <strong style="color:#7dd3fc;">Combinado con <code>volt:on</code></strong>
                <button type="button" volt:on="click -> state:set client:events.lastAction = 'pre-dispatch-combined'"
                    volt:dispatch="demo.events.alpha"
                    style="inline-size:max-content;border:1px solid rgba(14,165,233,0.28);background:rgba(8,47,73,0.22);color:#e0f2fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                    Mutar state y emitir evento
                </button>
                <span style="color:#bae6fd;">En el listener global actual, primero corre <code>volt:on</code> y luego
                    <code>volt:dispatch</code>.</span>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(148,163,184,0.20);background:rgba(15,23,42,0.82);border-radius:16px;padding:18px;">
                <strong style="color:#cbd5e1;">Elemento deshabilitado</strong>
                <button type="button" disabled volt:dispatch="demo.events.alpha"
                    style="inline-size:max-content;border:1px solid rgba(148,163,184,0.28);background:rgba(30,41,59,0.60);color:#94a3b8;border-radius:10px;padding:10px 14px;cursor:not-allowed;">
                    No deberia emitir nada
                </button>
                <span style="color:#94a3b8;">El runtime ignora <code>volt:dispatch</code> si el trigger esta
                    <code>disabled</code> o tiene <code>aria-disabled=true</code>.</span>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(59,130,246,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Inspector de estado y dispatch</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                El bloque mezcla estado client y shared para que sea facil ver que parte actualizo un evento DOM y que
                parte actualizo un <code>CustomEvent</code> despachado por el runtime.
            </p>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
            <article
                style="display:grid;gap:8px;border:1px solid rgba(14,165,233,0.20);background:rgba(8,47,73,0.18);border-radius:16px;padding:16px;">
                <strong style="color:#7dd3fc;">Client draft</strong>
                <span volt:text="client:events.draft ?? '(vacio)'" style="color:#e0f2fe;">(vacio)</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.18);border-radius:16px;padding:16px;">
                <strong style="color:#e9d5ff;">Shared ready</strong>
                <span volt:text="shared:events.sharedReady ?? false" style="color:#f5d0fe;">false</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(34,197,94,0.20);background:rgba(6,78,59,0.18);border-radius:16px;padding:16px;">
                <strong style="color:#bbf7d0;">Panel open</strong>
                <span volt:text="client:events.panelOpen ?? false" style="color:#dcfce7;">false</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(244,114,182,0.20);background:rgba(131,24,67,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#fbcfe8;">Self only</strong>
                <span volt:text="client:events.selfOnly ?? false" style="color:#fce7f3;">false</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.20);background:rgba(120,53,15,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#fde68a;">Last dispatch name</strong>
                <span volt:text="client:events.lastDispatchName ?? '(sin dispatch)'" style="color:#fef3c7;">(sin
                    dispatch)</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(45,212,191,0.20);background:rgba(17,94,89,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#99f6e4;">Last original event</strong>
                <span volt:text="client:events.lastOriginalType ?? '(sin original event)'" style="color:#ccfbf1;">(sin
                    original event)</span>
            </article>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));align-items:start;">
            <article
                style="display:grid;gap:10px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;">
                <strong style="color:#e2e8f0;">Detalle del ultimo dispatch</strong>
                <div style="display:grid;gap:8px;color:#cbd5e1;font-size:13px;line-height:1.7;">
                    <span>directive = <strong volt:text="client:events.lastDirective ?? '(sin dispatch)'"
                            style="color:#f8fafc;">(sin dispatch)</strong></span>
                    <span>source tag = <strong volt:text="client:events.lastSourceTag ?? '(sin source)'"
                            style="color:#f8fafc;">(sin source)</strong></span>
                    <span>scope id = <strong volt:text="client:events.lastScopeId ?? '(sin scopeId)'"
                            style="color:#f8fafc;">(sin scopeId)</strong></span>
                    <span>component = <strong volt:text="client:events.lastComponent ?? '(sin component)'"
                            style="color:#f8fafc;">(sin component)</strong></span>
                    <span>last at = <strong volt:text="client:events.lastDispatchAt ?? '(sin hora)'"
                            style="color:#f8fafc;">(sin hora)</strong></span>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;">
                <strong style="color:#e2e8f0;">Contadores</strong>
                <div style="display:grid;gap:8px;color:#cbd5e1;font-size:13px;line-height:1.7;">
                    <span>client dispatchCount = <strong volt:text="client:events.dispatchCount ?? 0"
                            style="color:#f8fafc;">0</strong></span>
                    <span>shared auditCount = <strong volt:text="shared:events.auditCount ?? 0"
                            style="color:#f8fafc;">0</strong></span>
                    <span>shared submitCount = <strong volt:text="shared:events.submitCount ?? 0"
                            style="color:#f8fafc;">0</strong></span>
                    <span>shared enterCount = <strong volt:text="shared:events.enterCount ?? 0"
                            style="color:#f8fafc;">0</strong></span>
                </div>
            </article>
        </div>
    </section>

    <section data-volt-efficiency-example data-runtime-efficiency-demo
        style="display:grid;gap:18px;border:1px solid rgba(34,197,94,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Pasada de eficiencia en navegador</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Este panel usa <code>window.Volt.telemetry.summary()</code>,
                <code>window.Volt.telemetry.latest()</code>,
                <code>window.Volt.components.summary()</code> y la API <code>performance</code> del navegador para dar
                un
                corte rapido de <code>boot</code>, <code>payload</code>, <code>patch</code> y roots activos.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <button type="button" data-volt-efficiency-action="refresh"
                style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.18);color:#dcfce7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Refrescar metricas
            </button>
            <button type="button" data-volt-efficiency-action="refresh-components"
                style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Refrescar roots
            </button>
            <button type="button" data-volt-efficiency-action="reset-telemetry"
                style="border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.18);color:#fde68a;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Resetear telemetria
            </button>
            <span style="color:#94a3b8;font-size:13px;">Estado:</span>
            <strong data-volt-efficiency-status style="color:#f8fafc;">boot</strong>
            <span style="color:#94a3b8;font-size:13px;">Ultima actualizacion:</span>
            <strong data-volt-efficiency-last-updated style="color:#dcfce7;">(pendiente)</strong>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
            <article data-runtime-check="efficiency-navigation-performance"
                style="display:grid;gap:8px;border:1px solid rgba(34,197,94,0.20);background:rgba(20,83,45,0.14);border-radius:16px;padding:16px;">
                <strong style="color:#bbf7d0;">Navigation timing</strong>
                <span>type = <strong data-volt-efficiency-nav-type style="color:#f0fdf4;">n/d</strong></span>
                <span>duration = <strong data-volt-efficiency-nav-duration style="color:#f0fdf4;">n/d</strong></span>
                <span>domInteractive = <strong data-volt-efficiency-nav-dom-interactive
                        style="color:#f0fdf4;">n/d</strong></span>
                <span>DCL end = <strong data-volt-efficiency-nav-dcl style="color:#f0fdf4;">n/d</strong></span>
                <span>load end = <strong data-volt-efficiency-nav-load style="color:#f0fdf4;">n/d</strong></span>
                <span>transferSize = <strong data-volt-efficiency-nav-transfer
                        style="color:#f0fdf4;">n/d</strong></span>
            </article>

            <article data-runtime-check="efficiency-runtime-asset"
                style="display:grid;gap:8px;border:1px solid rgba(59,130,246,0.20);background:rgba(30,64,175,0.14);border-radius:16px;padding:16px;">
                <strong style="color:#bfdbfe;">Runtime asset</strong>
                <span>name = <strong data-volt-efficiency-runtime-name style="color:#dbeafe;">n/d</strong></span>
                <span>duration = <strong data-volt-efficiency-runtime-duration
                        style="color:#dbeafe;">n/d</strong></span>
                <span>transferSize = <strong data-volt-efficiency-runtime-transfer
                        style="color:#dbeafe;">n/d</strong></span>
                <span>encodedBody = <strong data-volt-efficiency-runtime-body style="color:#dbeafe;">n/d</strong></span>
            </article>

            <article data-runtime-check="efficiency-runtime-overview"
                style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#f5d0fe;">Overview runtime</strong>
                <span>telemetry entries = <strong data-volt-efficiency-total-entries
                        style="color:#faf5ff;">0</strong></span>
                <span>telemetry max = <strong data-volt-efficiency-max-entries style="color:#faf5ff;">0</strong></span>
                <span>total roots = <strong data-volt-efficiency-total-roots style="color:#faf5ff;">0</strong></span>
                <span>unique components = <strong data-volt-efficiency-unique-components
                        style="color:#faf5ff;">0</strong></span>
            </article>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
            <article data-volt-efficiency-kind="navigation"
                style="display:grid;gap:8px;border:1px solid rgba(45,212,191,0.20);background:rgba(17,94,89,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#99f6e4;">Telemetry navigation</strong>
                <span>count = <strong data-volt-efficiency-count style="color:#ccfbf1;">0</strong></span>
                <span>outcomes = <strong data-volt-efficiency-outcomes style="color:#ccfbf1;">n/d</strong></span>
                <span>avg duration = <strong data-volt-efficiency-avg-duration
                        style="color:#ccfbf1;">n/d</strong></span>
                <span>max duration = <strong data-volt-efficiency-max-duration
                        style="color:#ccfbf1;">n/d</strong></span>
                <span>avg response = <strong data-volt-efficiency-avg-response
                        style="color:#ccfbf1;">n/d</strong></span>
                <span>max response = <strong data-volt-efficiency-max-response
                        style="color:#ccfbf1;">n/d</strong></span>
                <span>avg patch = <strong data-volt-efficiency-avg-patch style="color:#ccfbf1;">n/d</strong></span>
                <span>max patch = <strong data-volt-efficiency-max-patch style="color:#ccfbf1;">n/d</strong></span>
            </article>

            <article data-volt-efficiency-kind="action"
                style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.20);background:rgba(120,53,15,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#fde68a;">Telemetry action</strong>
                <span>count = <strong data-volt-efficiency-count style="color:#fef3c7;">0</strong></span>
                <span>outcomes = <strong data-volt-efficiency-outcomes style="color:#fef3c7;">n/d</strong></span>
                <span>avg duration = <strong data-volt-efficiency-avg-duration
                        style="color:#fef3c7;">n/d</strong></span>
                <span>max duration = <strong data-volt-efficiency-max-duration
                        style="color:#fef3c7;">n/d</strong></span>
                <span>avg request = <strong data-volt-efficiency-avg-request style="color:#fef3c7;">n/d</strong></span>
                <span>max request = <strong data-volt-efficiency-max-request style="color:#fef3c7;">n/d</strong></span>
                <span>avg response = <strong data-volt-efficiency-avg-response
                        style="color:#fef3c7;">n/d</strong></span>
                <span>max response = <strong data-volt-efficiency-max-response
                        style="color:#fef3c7;">n/d</strong></span>
                <span>avg patch = <strong data-volt-efficiency-avg-patch style="color:#fef3c7;">n/d</strong></span>
                <span>max patch = <strong data-volt-efficiency-max-patch style="color:#fef3c7;">n/d</strong></span>
            </article>

            <article data-volt-efficiency-kind="patch"
                style="display:grid;gap:8px;border:1px solid rgba(244,114,182,0.20);background:rgba(131,24,67,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#fbcfe8;">Telemetry patch</strong>
                <span>count = <strong data-volt-efficiency-count style="color:#fce7f3;">0</strong></span>
                <span>outcomes = <strong data-volt-efficiency-outcomes style="color:#fce7f3;">n/d</strong></span>
                <span>avg duration = <strong data-volt-efficiency-avg-duration
                        style="color:#fce7f3;">n/d</strong></span>
                <span>max duration = <strong data-volt-efficiency-max-duration
                        style="color:#fce7f3;">n/d</strong></span>
                <span>avg response = <strong data-volt-efficiency-avg-response
                        style="color:#fce7f3;">n/d</strong></span>
                <span>max response = <strong data-volt-efficiency-max-response
                        style="color:#fce7f3;">n/d</strong></span>
                <span>avg patch = <strong data-volt-efficiency-avg-patch style="color:#fce7f3;">n/d</strong></span>
                <span>max patch = <strong data-volt-efficiency-max-patch style="color:#fce7f3;">n/d</strong></span>
            </article>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));align-items:start;">
            <article
                style="display:grid;gap:10px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;">
                <strong style="color:#e2e8f0;">Latest navigation entry</strong>
                <pre data-volt-efficiency-latest="navigation"
                    style="margin:0;min-height:140px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#cbd5e1;font-size:12px;line-height:1.7;">(sin datos)</pre>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;">
                <strong style="color:#e2e8f0;">Latest action entry</strong>
                <pre data-volt-efficiency-latest="action"
                    style="margin:0;min-height:140px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#cbd5e1;font-size:12px;line-height:1.7;">(sin datos)</pre>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;">
                <strong style="color:#e2e8f0;">Latest patch entry</strong>
                <pre data-volt-efficiency-latest="patch"
                    style="margin:0;min-height:140px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#cbd5e1;font-size:12px;line-height:1.7;">(sin datos)</pre>
            </article>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
            <article
                style="display:grid;gap:10px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;">
                <strong style="color:#e2e8f0;">Runtime summary snapshot</strong>
                <pre data-volt-efficiency-summary-json data-runtime-check="efficiency-summary-json"
                    style="margin:0;min-height:180px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#cbd5e1;font-size:12px;line-height:1.7;">(sin datos)</pre>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:16px;padding:16px;">
                <strong style="color:#e2e8f0;">Active components summary</strong>
                <pre data-volt-efficiency-components-detail data-runtime-check="efficiency-components-detail"
                    style="margin:0;min-height:180px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#cbd5e1;font-size:12px;line-height:1.7;">(sin datos)</pre>
            </article>
        </div>

        <p style="margin:0;color:#94a3b8;line-height:1.7;">
            Flujo sugerido: 1) pulsa <code>Refrescar metricas</code>, 2) navega a
            <code>/runtimeModelSync</code> o <code>/runtimeState</code>, 3) genera acciones/payloads reales, 4) vuelve
            aqui por SPA y compara los cards de navigation/action/patch sin abrir aun DevTools.
        </p>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeAdvancedDirectives" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.18);color:#dbeafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Ir a /runtimeAdvancedDirectives
        </a>
        <a href="/runtimeState" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Ir a /runtimeState
        </a>
        <a href="/runtimeModelSync" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.18);color:#dbeafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Ir a /runtimeModelSync
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection
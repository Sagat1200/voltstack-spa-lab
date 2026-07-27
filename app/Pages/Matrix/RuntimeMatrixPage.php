<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Matrix;

use VoltStack\Runtime\Component\Component;

final class RuntimeMatrixPage extends Component
{
    public string $title = 'Runtime Matrix';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="runtime-matrix-mode">
<script data-volt-head-key="runtime-matrix-bridge">
    (() => {
        if (window.__spaLabRuntimeMatrixInstalled) {
            return;
        }

        window.__spaLabRuntimeMatrixInstalled = true;

        const DEBUG_URL = "http://127.0.0.1:7777/event";
        const DEBUG_SESSION_ID = "runtime-matrix-budgets";

        const DEFAULT_BUDGETS = {
            bootMs: 150,
            patchMs: 120,
            payloadActionBytes: 2 * 1024,
            navigationPayloadBytes: 50 * 1024,
            telemetryMaxEntries: 60,
        };
        const LEGACY_DEFAULT_BUDGETS = {
            bootMs: 150,
            patchMs: 120,
            payloadActionBytes: 25 * 1024,
            telemetryMaxEntries: 60,
        };
        const CONFIG_VERSION = 2;

        const MATRIX_BASELINES = [{
                id: 'boot',
                label: 'boot',
                route: '/runtimeEvents',
                flow: 'Carga inicial en frio del panel contractual de budgets.',
            },
            {
                id: 'spa',
                label: 'navegacion-spa',
                route: '/spaReactive -> /cacheExample -> /spaReactive',
                flow: 'Un salto SPA compatible ida y vuelta, sin html fallback.',
            },
            {
                id: 'action',
                label: 'action-reactiva',
                route: '/runtimeRequestLab',
                flow: 'Ejecutar Fast action y contrastar patch/payload.',
            },
            {
                id: 'model.sync',
                label: 'volt:model.sync',
                route: '/runtimeModelSync',
                flow: 'Escritura rapida para observar debounce, stale y abort.',
            }
        ];

        const MATRIX_CONDITIONS = [{
                id: 'normal',
                label: 'normal',
            },
            {
                id: 'degradada',
                label: 'degradada',
            }
        ];

        const SCENARIO_BUDGET_KEYS = {
            boot: ['boot', 'telemetry'],
            spa: ['patch', 'navigationPayload', 'telemetry'],
            action: ['patch', 'payloadAction', 'telemetry'],
            'model.sync': ['patch', 'payloadAction', 'telemetry'],
            'long-session': ['telemetry'],
        };

        const STORAGE_KEY = 'volt.runtimeMatrix.runs';
        const CONFIG_KEY = 'volt.runtimeMatrix.config';
        const CARRYOVER_KEY = 'volt.runtimeMatrix.carryover';
        const DEGRADATION_KEY = 'volt.runtimeMatrix.degradation';

        const DEGRADED_PROFILE = {
            enabled: true,
            condition: 'degradada',
            networkDelayMs: 400,
            responseDelayMs: 180,
            cpuBlockMs: 90,
            source: 'runtimeMatrix',
        };

        function reportDebug(hypothesisId, location, data) {
            fetch(DEBUG_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    sessionId: DEBUG_SESSION_ID,
                    runId: 'post-fix',
                    hypothesisId: hypothesisId,
                    location: location,
                    msg: '[DEBUG] runtime matrix snapshot',
                    data: data && typeof data === 'object' ? data : {},
                    ts: Date.now(),
                })
            }).catch(() => {});
        }

        function readJsonStorage(key) {
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

        function writeJsonStorage(key, value) {
            if (typeof sessionStorage === 'undefined') {
                return false;
            }

            try {
                sessionStorage.setItem(key, JSON.stringify(value));
                return true;
            } catch (error) {
                return false;
            }
        }

        function normalizeConfig(saved) {
            const normalized = saved && typeof saved === 'object' ? saved : {};
            const budgets = normalized.budgets && typeof normalized.budgets === 'object' ? normalized.budgets : {};
            const isLegacyConfig = normalized.version !== CONFIG_VERSION;

            const budgetValue = (key) => {
                const savedValue = typeof budgets[key] === 'number' ? budgets[key] : null;

                if (savedValue === null) {
                    return DEFAULT_BUDGETS[key];
                }

                if (isLegacyConfig &&
                    Object.prototype.hasOwnProperty.call(LEGACY_DEFAULT_BUDGETS, key) &&
                    savedValue === LEGACY_DEFAULT_BUDGETS[key]) {
                    return DEFAULT_BUDGETS[key];
                }

                return savedValue;
            };

            return {
                version: CONFIG_VERSION,
                scenario: typeof normalized.scenario === 'string' && normalized.scenario !== '' ? normalized.scenario : 'boot',
                condition: typeof normalized.condition === 'string' && normalized.condition !== '' ? normalized.condition : 'normal',
                budgets: {
                    bootMs: budgetValue('bootMs'),
                    patchMs: budgetValue('patchMs'),
                    payloadActionBytes: budgetValue('payloadActionBytes'),
                    navigationPayloadBytes: budgetValue('navigationPayloadBytes'),
                    telemetryMaxEntries: budgetValue('telemetryMaxEntries'),
                },
            };
        }

        function readConfig() {
            return normalizeConfig(readJsonStorage(CONFIG_KEY));
        }

        function writeConfig(next) {
            const config = normalizeConfig(next);
            return writeJsonStorage(CONFIG_KEY, config);
        }

        function ensureConfigIsCurrent() {
            const saved = readJsonStorage(CONFIG_KEY);
            const config = normalizeConfig(saved);

            if (!saved || typeof saved !== 'object' || saved.version !== CONFIG_VERSION) {
                writeJsonStorage(CONFIG_KEY, config);
            }

            return config;
        }

        function readRuns() {
            const raw = readJsonStorage(STORAGE_KEY);
            return Array.isArray(raw) ? raw : [];
        }

        function appendRun(entry) {
            const runs = readRuns();
            runs.push(entry);
            writeJsonStorage(STORAGE_KEY, runs);
            return runs;
        }

        function clearRuns() {
            writeJsonStorage(STORAGE_KEY, []);
            return [];
        }

        function readCarryover() {
            const raw = readJsonStorage(CARRYOVER_KEY);
            return raw && typeof raw === 'object' ? raw : null;
        }

        function readDegradationProfile() {
            const raw = readJsonStorage(DEGRADATION_KEY);
            return raw && typeof raw === 'object' ? raw : null;
        }

        function syncDegradationProfile(config) {
            const nextConfig = config && typeof config === 'object' ? config : readConfig();
            const condition = typeof nextConfig.condition === 'string' ? nextConfig.condition : 'normal';

            if (condition === 'degradada') {
                writeJsonStorage(DEGRADATION_KEY, DEGRADED_PROFILE);
                return DEGRADED_PROFILE;
            }

            if (typeof sessionStorage !== 'undefined') {
                try {
                    sessionStorage.removeItem(DEGRADATION_KEY);
                } catch (error) {
                    return null;
                }
            }

            return null;
        }

        function safeNumber(value) {
            return typeof value === 'number' && Number.isFinite(value) ? value : null;
        }

        function safeInteger(value) {
            return typeof value === 'number' && Number.isFinite(value) ? Math.round(value) : null;
        }

        function summarizeResource(match) {
            if (!window.performance || typeof window.performance.getEntriesByType !== 'function') {
                return null;
            }

            const entries = window.performance.getEntriesByType('resource')
                .filter((entry) => entry && typeof entry.name === 'string' && entry.name.includes(match));

            const latest = entries.length > 0 ? entries[entries.length - 1] : null;

            if (!latest) {
                return null;
            }

            return {
                name: latest.name,
                initiatorType: typeof latest.initiatorType === 'string' ? latest.initiatorType : null,
                durationMs: safeNumber(latest.duration ? Number(latest.duration.toFixed(2)) : null),
                transferSize: safeInteger(latest.transferSize),
                encodedBodySize: safeInteger(latest.encodedBodySize),
                decodedBodySize: safeInteger(latest.decodedBodySize),
            };
        }

        function heapSnapshot() {
            if (!window.performance || typeof window.performance !== 'object') {
                return null;
            }

            const memory = window.performance.memory && typeof window.performance.memory === 'object' ?
                window.performance.memory :
                null;

            if (!memory) {
                return null;
            }

            return {
                usedJSHeapSize: safeInteger(memory.usedJSHeapSize),
                totalJSHeapSize: safeInteger(memory.totalJSHeapSize),
                jsHeapSizeLimit: safeInteger(memory.jsHeapSizeLimit),
            };
        }

        function headSnapshot() {
            const head = document.querySelector && typeof document.querySelector === 'function' ?
                document.querySelector('head') :
                document.head;

            if (!head) {
                return null;
            }

            const queryCount = (selector) => {
                if (!head.querySelectorAll) {
                    return null;
                }

                return head.querySelectorAll(selector).length;
            };

            const managedHeadNodeKey = (node) => {
                if (!node || node.nodeType !== 1) {
                    return null;
                }

                const explicitKey = node.getAttribute ? node.getAttribute('data-volt-head-key') : null;

                if (explicitKey) {
                    return 'explicit:' + explicitKey;
                }

                const tag = node.tagName ? node.tagName.toLowerCase() : '';

                if (tag === 'meta') {
                    if (node.hasAttribute && node.hasAttribute('name')) {
                        return 'meta:name:' + (node.getAttribute('name') || '');
                    }

                    if (node.hasAttribute && node.hasAttribute('property')) {
                        return 'meta:property:' + (node.getAttribute('property') || '');
                    }

                    if (node.hasAttribute && node.hasAttribute('http-equiv')) {
                        return 'meta:http-equiv:' + (node.getAttribute('http-equiv') || '');
                    }

                    return null;
                }

                if (tag === 'link') {
                    const rel = (node.getAttribute ? (node.getAttribute('rel') || '') : '').toLowerCase();
                    const href = node.getAttribute ? (node.getAttribute('href') || '') : '';

                    if (!rel || !href) {
                        return null;
                    }

                    return 'link:' + rel + ':' + href + ':' + (node.getAttribute ? (node.getAttribute('as') || '') : '');
                }

                if (tag === 'script') {
                    const src = node.getAttribute ? (node.getAttribute('src') || '') : '';

                    if (!src) {
                        return null;
                    }

                    return 'script:' + (node.getAttribute ? (node.getAttribute('type') || '') : '') + ':' + src;
                }

                if (tag === 'style') {
                    const styleId = node.getAttribute ? (node.getAttribute('id') || '') : '';

                    if (styleId) {
                        return 'style:id:' + styleId;
                    }
                }

                return null;
            };

            const managedCount = head.children ?
                Array.from(head.children).filter((node) => !!managedHeadNodeKey(node)).length :
                null;

            return {
                total: typeof head.childElementCount === 'number' ? head.childElementCount : (head.children ? head.children.length : null),
                keyed: queryCount('[data-volt-head-key]'),
                managed: managedCount,
                unmanaged: typeof managedCount === 'number' && typeof head.childElementCount === 'number' ? head.childElementCount - managedCount : null,
                style: queryCount('style'),
                script: queryCount('script'),
                linkStylesheet: queryCount('link[rel="stylesheet"]'),
            };
        }

        function classify(value, threshold, mode) {
            if (typeof value !== 'number' || !Number.isFinite(value)) {
                return 'pendiente';
            }

            if (mode === 'lte') {
                return value <= threshold ? 'ok' : 'alerta';
            }

            return value >= threshold ? 'ok' : 'alerta';
        }

        function telemetryEntryMatches(entry, expectedType) {
            if (!entry || typeof entry !== 'object') {
                return false;
            }

            return entry.type === expectedType || entry.kind === expectedType;
        }

        function statusColor(status) {
            if (status === 'ok') {
                return '#86efac';
            }

            if (status === 'alerta') {
                return '#fca5a5';
            }

            return '#fde68a';
        }

        function scenarioBudgetKeys(scenario) {
            return Object.prototype.hasOwnProperty.call(SCENARIO_BUDGET_KEYS, scenario) ?
                SCENARIO_BUDGET_KEYS[scenario] : ['boot', 'patch', 'payloadAction', 'telemetry'];
        }

        function summarizeRunStatus(entry) {
            const scenario = entry && entry.matrix && typeof entry.matrix.scenario === 'string' ? entry.matrix.scenario : '';
            const budgets = entry && entry.budgets && typeof entry.budgets === 'object' ? entry.budgets : {};
            const keys = scenarioBudgetKeys(scenario);
            let hasPending = false;

            for (let index = 0; index < keys.length; index += 1) {
                const key = keys[index];
                const budget = budgets[key] && typeof budgets[key] === 'object' ? budgets[key] : null;
                const status = budget && typeof budget.status === 'string' ? budget.status : 'pendiente';

                if (status === 'alerta') {
                    return 'alerta';
                }

                if (status !== 'ok') {
                    hasPending = true;
                }
            }

            return hasPending ? 'pendiente' : 'ok';
        }

        function formatRunMetrics(entry) {
            const scenario = entry && entry.matrix && typeof entry.matrix.scenario === 'string' ? entry.matrix.scenario : '';
            const budgets = entry && entry.budgets && typeof entry.budgets === 'object' ? entry.budgets : {};
            const latest = entry && entry.telemetry && entry.telemetry.latest && typeof entry.telemetry.latest === 'object' ? entry.telemetry.latest : {};
            const latestNavigation = latest.navigation && typeof latest.navigation === 'object' ? latest.navigation : null;
            const metrics = [];

            if (budgets.boot && typeof budgets.boot.valueMs === 'number') {
                metrics.push('boot ' + budgets.boot.valueMs + ' ms');
            }

            if (budgets.patch && typeof budgets.patch.valueMs === 'number') {
                metrics.push('patch ' + budgets.patch.valueMs + ' ms');
            }

            if (budgets.payloadAction && typeof budgets.payloadAction.valueBytes === 'number') {
                metrics.push('payload ' + budgets.payloadAction.valueBytes + ' B');
            }

            if (budgets.navigationPayload && typeof budgets.navigationPayload.valueBytes === 'number') {
                metrics.push('navigation payload ' + budgets.navigationPayload.valueBytes + ' B');
            } else if (scenario === 'spa' && latestNavigation && typeof latestNavigation.responsePayloadBytes === 'number') {
                metrics.push('navigation payload ' + latestNavigation.responsePayloadBytes + ' B');
            }

            if (budgets.telemetry && typeof budgets.telemetry.value === 'number') {
                metrics.push('telemetry ' + budgets.telemetry.value);
            }

            return metrics.length > 0 ? metrics.join(' | ') : 'Sin metricas comparables aun.';
        }

        function renderMatrixCoverage(runs) {
            const body = document.querySelector('[data-runtime-matrix="coverage-body"]');
            const summary = document.querySelector('[data-runtime-matrix="coverage-summary"]');
            const pending = document.querySelector('[data-runtime-matrix="coverage-pending"]');
            const alerts = document.querySelector('[data-runtime-matrix="coverage-alerts"]');

            if (!body) {
                return;
            }

            body.innerHTML = '';

            const latestByCell = {};
            (Array.isArray(runs) ? runs : []).forEach((entry) => {
                const scenario = entry && entry.matrix && typeof entry.matrix.scenario === 'string' ? entry.matrix.scenario : null;
                const condition = entry && entry.matrix && typeof entry.matrix.condition === 'string' ? entry.matrix.condition : null;

                if (!scenario || !condition) {
                    return;
                }

                latestByCell[scenario + '::' + condition] = entry;
            });

            let completedCount = 0;
            let pendingCount = 0;
            let alertCount = 0;
            const totalCount = MATRIX_BASELINES.length * MATRIX_CONDITIONS.length;

            MATRIX_BASELINES.forEach((baseline) => {
                MATRIX_CONDITIONS.forEach((condition) => {
                    const key = baseline.id + '::' + condition.id;
                    const entry = Object.prototype.hasOwnProperty.call(latestByCell, key) ? latestByCell[key] : null;
                    const status = entry ? summarizeRunStatus(entry) : 'pendiente';

                    if (status === 'ok') {
                        completedCount += 1;
                    } else if (status === 'alerta') {
                        alertCount += 1;
                    } else {
                        pendingCount += 1;
                    }

                    const row = document.createElement('tr');
                    row.style.borderTop = '1px solid rgba(51,65,85,1)';

                    const latestCapture = entry && typeof entry.capturedAt === 'string' ? entry.capturedAt : 'Pendiente';
                    const metrics = entry ? formatRunMetrics(entry) : 'Sin captura para esta combinacion.';
                    const values = [
                        baseline.label,
                        condition.label,
                        baseline.route,
                        latestCapture,
                        status,
                        metrics,
                    ];

                    values.forEach((value, columnIndex) => {
                        const cell = document.createElement('td');
                        cell.textContent = typeof value === 'string' ? value : String(value);
                        cell.style.padding = '10px 12px';
                        cell.style.fontSize = '12px';
                        cell.style.lineHeight = '1.6';
                        cell.style.color = columnIndex === 4 ? statusColor(status) : '#cbd5e1';
                        row.appendChild(cell);
                    });

                    body.appendChild(row);
                });
            });

            if (summary) {
                summary.textContent = completedCount + '/' + totalCount;
                summary.style.color = completedCount === totalCount ? '#86efac' : '#f8fafc';
            }

            if (pending) {
                pending.textContent = String(pendingCount);
            }

            if (alerts) {
                alerts.textContent = String(alertCount);
            }
        }

        function captureSnapshot(reason, options) {
            const meta = options && typeof options === 'object' ? options : {};
            const config = readConfig();
            const scenario = typeof meta.scenario === 'string' && meta.scenario !== '' ? meta.scenario : config.scenario;
            const condition = typeof meta.condition === 'string' && meta.condition !== '' ? meta.condition : config.condition;
            const budgetsConfig = meta.budgets && typeof meta.budgets === 'object' ? meta.budgets : config.budgets;
            const carryover = readCarryover();
            const degradation = readDegradationProfile();

            const telemetry = window.Volt && window.Volt.telemetry ? window.Volt.telemetry : null;
            const components = window.Volt && window.Volt.components ? window.Volt.components : null;
            const busy = window.Volt && window.Volt.busy ? window.Volt.busy : null;

            const boot = telemetry && typeof telemetry.boot === 'function' ? telemetry.boot() : null;
            const telemetrySummary = telemetry && typeof telemetry.summary === 'function' ? telemetry.summary() : null;
            const telemetryLatestNavigation = telemetry && typeof telemetry.latest === 'function' ?
                telemetry.latest({
                    type: 'navigation'
                }) :
                null;
            const telemetryLatestAction = telemetry && typeof telemetry.latest === 'function' ?
                telemetry.latest({
                    type: 'action'
                }) :
                null;
            const telemetryLatestPatch = telemetry && typeof telemetry.latest === 'function' ?
                telemetry.latest({
                    type: 'patch'
                }) :
                null;
            const carryoverTelemetry = carryover && carryover.telemetry && typeof carryover.telemetry === 'object' ? carryover.telemetry : null;
            const carryoverLatest = carryoverTelemetry && carryoverTelemetry.latest && typeof carryoverTelemetry.latest === 'object' ? carryoverTelemetry.latest : null;
            const useCarryover = window.location.pathname === '/runtimeMatrix' &&
                carryover &&
                typeof carryover.path === 'string' &&
                carryover.path !== '/runtimeMatrix' &&
                (scenario === 'spa' || scenario === 'action' || scenario === 'model.sync' || scenario === 'long-session');
            const effectiveLatestNavigation = useCarryover &&
                carryoverLatest &&
                carryoverLatest.navigation &&
                telemetryEntryMatches(carryoverLatest.navigation, 'navigation') ?
                carryoverLatest.navigation :
                (telemetryEntryMatches(telemetryLatestNavigation, 'navigation') ? telemetryLatestNavigation : null);
            const effectiveLatestAction = useCarryover &&
                carryoverLatest &&
                carryoverLatest.action &&
                telemetryEntryMatches(carryoverLatest.action, 'action') ?
                carryoverLatest.action :
                (telemetryEntryMatches(telemetryLatestAction, 'action') ? telemetryLatestAction : null);
            const effectiveLatestPatch = useCarryover ?
                (scenario === 'spa' ?
                    (effectiveLatestNavigation || (carryoverLatest && telemetryEntryMatches(carryoverLatest.patch, 'patch') ? carryoverLatest.patch : null)) :
                    ((carryoverLatest && telemetryEntryMatches(carryoverLatest.patch, 'patch') ? carryoverLatest.patch : null) || effectiveLatestAction)) :
                (telemetryEntryMatches(telemetryLatestPatch, 'patch') ? telemetryLatestPatch : null);
            const effectiveTelemetrySummary = useCarryover &&
                carryoverTelemetry &&
                carryoverTelemetry.summary &&
                typeof carryoverTelemetry.summary === 'object' ?
                carryoverTelemetry.summary :
                (telemetrySummary && typeof telemetrySummary === 'object' ? telemetrySummary : null);
            const effectiveTelemetrySize = useCarryover &&
                carryoverTelemetry &&
                typeof carryoverTelemetry.size === 'number' ?
                carryoverTelemetry.size :
                (telemetry && typeof telemetry.size === 'function' ? telemetry.size() : null);

            const snapshot = {
                capturedAt: new Date().toISOString(),
                reason: typeof reason === 'string' && reason !== '' ? reason : 'manual',
                url: window.location.href,
                matrix: {
                    scenario,
                    condition,
                    budgets: budgetsConfig,
                    telemetrySource: useCarryover ? 'carryover' : 'current-page',
                    degradation: degradation && typeof degradation === 'object' ? degradation : null,
                },
                runtimeAsset: summarizeResource('/_volt/runtime.js'),
                heap: heapSnapshot(),
                head: headSnapshot(),
                busy: busy && typeof busy.current === 'function' ? busy.current() : null,
                telemetry: {
                    boot: boot && typeof boot === 'object' ? boot : null,
                    summary: effectiveTelemetrySummary,
                    latest: {
                        navigation: effectiveLatestNavigation,
                        action: effectiveLatestAction,
                        patch: effectiveLatestPatch,
                    },
                    size: effectiveTelemetrySize,
                    config: telemetry && typeof telemetry.config === 'function' ? telemetry.config() : null,
                },
                components: {
                    summary: components && typeof components.summary === 'function' ? components.summary() : null,
                    count: components && typeof components.count === 'function' ? components.count() : null,
                },
                carryover: carryover && typeof carryover === 'object' ? {
                    used: useCarryover,
                    capturedAt: typeof carryover.capturedAt === 'string' ? carryover.capturedAt : null,
                    reason: typeof carryover.reason === 'string' ? carryover.reason : null,
                    url: typeof carryover.url === 'string' ? carryover.url : null,
                    path: typeof carryover.path === 'string' ? carryover.path : null,
                } : null,
            };

            const bootMs = boot && typeof boot.totalDurationMs === 'number' ?
                boot.totalDurationMs :
                (boot && typeof boot.durationMs === 'number' ? boot.durationMs : null);
            const patchMs = effectiveLatestPatch && typeof effectiveLatestPatch.patchDurationMs === 'number' ?
                effectiveLatestPatch.patchDurationMs :
                null;
            const payloadActionBytes = (scenario === 'action' || scenario === 'model.sync') &&
                effectiveLatestAction &&
                typeof effectiveLatestAction.responsePayloadBytes === 'number' ?
                effectiveLatestAction.responsePayloadBytes :
                null;
            const navigationPayloadBytes = scenario === 'spa' &&
                effectiveLatestNavigation &&
                typeof effectiveLatestNavigation.responsePayloadBytes === 'number' ?
                effectiveLatestNavigation.responsePayloadBytes :
                null;
            const telemetrySize = effectiveTelemetrySize;

            snapshot.budgets = {
                boot: {
                    valueMs: bootMs,
                    thresholdMs: budgetsConfig.bootMs,
                    status: classify(bootMs, budgetsConfig.bootMs, 'lte'),
                },
                patch: {
                    valueMs: patchMs,
                    thresholdMs: budgetsConfig.patchMs,
                    status: classify(patchMs, budgetsConfig.patchMs, 'lte'),
                },
                payloadAction: {
                    valueBytes: payloadActionBytes,
                    thresholdBytes: budgetsConfig.payloadActionBytes,
                    status: classify(payloadActionBytes, budgetsConfig.payloadActionBytes, 'lte'),
                },
                navigationPayload: {
                    valueBytes: navigationPayloadBytes,
                    thresholdBytes: budgetsConfig.navigationPayloadBytes,
                    status: classify(navigationPayloadBytes, budgetsConfig.navigationPayloadBytes, 'lte'),
                },
                telemetry: {
                    value: telemetrySize,
                    threshold: budgetsConfig.telemetryMaxEntries,
                    status: classify(telemetrySize, budgetsConfig.telemetryMaxEntries, 'lte'),
                },
            };

            // #region debug-point A:matrix-snapshot
            reportDebug('A', 'RuntimeMatrixPage.php:captureSnapshot', {
                reason: snapshot.reason,
                url: snapshot.url,
                matrix: snapshot.matrix,
                budgets: snapshot.budgets,
                runtimeAsset: snapshot.runtimeAsset,
                heap: snapshot.heap,
                head: snapshot.head,
                telemetry: snapshot.telemetry ? {
                    size: snapshot.telemetry.size,
                    latest: snapshot.telemetry.latest,
                } : null,
            });
            // #endregion

            return snapshot;
        }

        function updateText(selector, value) {
            const element = document.querySelector(selector);

            if (!element) {
                return;
            }

            element.textContent = value;
        }

        function updateJson(selector, value) {
            const element = document.querySelector(selector);

            if (!element) {
                return;
            }

            element.textContent = JSON.stringify(value, null, 2);
        }

        function renderSnapshot(snapshot) {
            updateText('[data-runtime-matrix="captured-at"]', snapshot ? snapshot.capturedAt : '(pendiente)');

            const budgets = snapshot && snapshot.budgets ? snapshot.budgets : null;
            const matrix = snapshot && snapshot.matrix && typeof snapshot.matrix === 'object' ? snapshot.matrix : null;
            const payloadBudget = matrix && matrix.scenario === 'spa' ?
                (budgets ? budgets.navigationPayload : null) :
                (budgets ? budgets.payloadAction : null);

            updateText('[data-runtime-matrix="budget-boot"]', budgets ? budgets.boot.status : 'pendiente');
            updateText('[data-runtime-matrix="budget-patch"]', budgets ? budgets.patch.status : 'pendiente');
            updateText('[data-runtime-matrix="budget-payload"]', payloadBudget ? payloadBudget.status : 'pendiente');
            updateText('[data-runtime-matrix="budget-telemetry"]', budgets ? budgets.telemetry.status : 'pendiente');

            updateJson('[data-runtime-matrix="snapshot-json"]', snapshot || {
                waiting: 'Sin captura aun.'
            });
        }

        function downloadSnapshot(snapshot) {
            if (!snapshot) {
                return;
            }

            const content = JSON.stringify(snapshot, null, 2);
            const blob = new Blob([content], {
                type: 'application/json'
            });
            const url = URL.createObjectURL(blob);
            const anchor = document.createElement('a');
            anchor.href = url;
            const matrix = snapshot.matrix && typeof snapshot.matrix === 'object' ? snapshot.matrix : {};
            const suffix = [
                typeof matrix.scenario === 'string' ? matrix.scenario : 'snapshot',
                typeof matrix.condition === 'string' ? matrix.condition : 'normal',
            ].join('-');
            anchor.download = 'runtime-matrix-' + suffix + '-' + (snapshot.capturedAt || 'snapshot') + '.json';
            document.body.appendChild(anchor);
            anchor.click();
            anchor.remove();
            URL.revokeObjectURL(url);
        }

        function downloadAllRuns(runs) {
            const payload = Array.isArray(runs) ? runs : [];
            const content = JSON.stringify({
                exportedAt: new Date().toISOString(),
                url: window.location.href,
                runs: payload,
            }, null, 2);
            const blob = new Blob([content], {
                type: 'application/json'
            });
            const url = URL.createObjectURL(blob);
            const anchor = document.createElement('a');
            anchor.href = url;
            anchor.download = 'runtime-matrix-runs-' + (new Date().toISOString().replace(/[:.]/g, '-')) + '.json';
            document.body.appendChild(anchor);
            anchor.click();
            anchor.remove();
            URL.revokeObjectURL(url);
        }

        function resetTelemetry() {
            const telemetry = window.Volt && window.Volt.telemetry ? window.Volt.telemetry : null;

            if (!telemetry || typeof telemetry.reset !== 'function') {
                return;
            }

            telemetry.reset();
        }

        function renderRuns(runs) {
            const body = document.querySelector('[data-runtime-matrix="runs-body"]');

            if (!body) {
                return;
            }

            body.innerHTML = '';

            (Array.isArray(runs) ? runs : []).slice(-30).reverse().forEach((entry) => {
                const row = document.createElement('tr');
                row.style.borderTop = '1px solid rgba(51,65,85,1)';

                const createdAt = entry && typeof entry.capturedAt === 'string' ? entry.capturedAt : 'n/d';
                const scenario = entry && entry.matrix && typeof entry.matrix.scenario === 'string' ? entry.matrix.scenario : 'n/d';
                const condition = entry && entry.matrix && typeof entry.matrix.condition === 'string' ? entry.matrix.condition : 'n/d';
                const bootStatus = entry && entry.budgets && entry.budgets.boot ? entry.budgets.boot.status : 'pendiente';
                const patchStatus = entry && entry.budgets && entry.budgets.patch ? entry.budgets.patch.status : 'pendiente';
                const payloadStatus = scenario === 'spa' ?
                    (entry && entry.budgets && entry.budgets.navigationPayload ? entry.budgets.navigationPayload.status : 'pendiente') :
                    (entry && entry.budgets && entry.budgets.payloadAction ? entry.budgets.payloadAction.status : 'pendiente');

                const cells = [
                    createdAt,
                    scenario,
                    condition,
                    bootStatus,
                    patchStatus,
                    payloadStatus,
                ];

                cells.forEach((value) => {
                    const cell = document.createElement('td');
                    cell.textContent = typeof value === 'string' ? value : String(value);
                    cell.style.padding = '10px 12px';
                    cell.style.fontSize = '12px';
                    cell.style.color = '#cbd5e1';
                    row.appendChild(cell);
                });

                body.appendChild(row);
            });
        }

        function readFormValue(selector) {
            const element = document.querySelector(selector);
            if (!element) {
                return null;
            }
            return 'value' in element ? element.value : null;
        }

        function readNumberFormValue(selector) {
            const raw = readFormValue(selector);
            if (typeof raw !== 'string') {
                return null;
            }
            const parsed = Number(raw);
            return Number.isFinite(parsed) ? parsed : null;
        }

        function readMatrixOptionsFromForm() {
            const scenario = readFormValue('[data-runtime-matrix="scenario"]');
            const condition = readFormValue('[data-runtime-matrix="condition"]');

            const bootMs = readNumberFormValue('[data-runtime-matrix="budget-boot-ms"]');
            const patchMs = readNumberFormValue('[data-runtime-matrix="budget-patch-ms"]');
            const payloadActionBytes = readNumberFormValue('[data-runtime-matrix="budget-payload-bytes"]');
            const navigationPayloadBytes = readNumberFormValue('[data-runtime-matrix="budget-navigation-payload-bytes"]');
            const telemetryMaxEntries = readNumberFormValue('[data-runtime-matrix="budget-telemetry-max"]');

            const config = readConfig();

            return {
                scenario: typeof scenario === 'string' && scenario !== '' ? scenario : config.scenario,
                condition: typeof condition === 'string' && condition !== '' ? condition : config.condition,
                budgets: {
                    bootMs: typeof bootMs === 'number' ? bootMs : config.budgets.bootMs,
                    patchMs: typeof patchMs === 'number' ? patchMs : config.budgets.patchMs,
                    payloadActionBytes: typeof payloadActionBytes === 'number' ? payloadActionBytes : config.budgets.payloadActionBytes,
                    navigationPayloadBytes: typeof navigationPayloadBytes === 'number' ? navigationPayloadBytes : config.budgets.navigationPayloadBytes,
                    telemetryMaxEntries: typeof telemetryMaxEntries === 'number' ? telemetryMaxEntries : config.budgets.telemetryMaxEntries,
                },
            };
        }

        function hydrateFormFromConfig(config) {
            const safeConfig = config && typeof config === 'object' ? config : readConfig();
            const budgets = safeConfig.budgets && typeof safeConfig.budgets === 'object' ? safeConfig.budgets : DEFAULT_BUDGETS;

            const setValue = (selector, value) => {
                const element = document.querySelector(selector);
                if (element && 'value' in element) {
                    element.value = String(value);
                }
            };

            setValue('[data-runtime-matrix="scenario"]', safeConfig.scenario || 'boot');
            setValue('[data-runtime-matrix="condition"]', safeConfig.condition || 'normal');
            setValue('[data-runtime-matrix="budget-boot-ms"]', budgets.bootMs);
            setValue('[data-runtime-matrix="budget-patch-ms"]', budgets.patchMs);
            setValue('[data-runtime-matrix="budget-payload-bytes"]', budgets.payloadActionBytes);
            setValue('[data-runtime-matrix="budget-navigation-payload-bytes"]', budgets.navigationPayloadBytes);
            setValue('[data-runtime-matrix="budget-telemetry-max"]', budgets.telemetryMaxEntries);
        }

        function syncConfigFromForm() {
            const options = readMatrixOptionsFromForm();
            writeConfig(options);
            syncDegradationProfile(options);
            return options;
        }

        window.__spaLabRuntimeMatrix = window.__spaLabRuntimeMatrix || {};
        window.__spaLabRuntimeMatrix.capture = captureSnapshot;
        window.__spaLabRuntimeMatrix.render = function(reason) {
            const snapshot = captureSnapshot(reason || 'auto', readConfig());
            const runs = readRuns();
            window.__spaLabRuntimeMatrix.last = snapshot;
            renderSnapshot(snapshot);
            renderRuns(runs);
            renderMatrixCoverage(runs);
            return snapshot;
        };
        window.__spaLabRuntimeMatrix.reset = function() {
            resetTelemetry();
            const snapshot = window.__spaLabRuntimeMatrix.render('reset');
            return snapshot;
        };
        window.__spaLabRuntimeMatrix.export = function() {
            downloadSnapshot(window.__spaLabRuntimeMatrix.last || captureSnapshot('export'));
        };
        window.__spaLabRuntimeMatrix.exportRuns = function() {
            downloadAllRuns(readRuns());
        };
        window.__spaLabRuntimeMatrix.clearRuns = function() {
            const runs = clearRuns();
            renderRuns(runs);
            renderMatrixCoverage(runs);
        };

        document.addEventListener('click', (event) => {
            const trigger = event.target && typeof event.target.closest === 'function' ?
                event.target.closest('[data-runtime-matrix-action]') :
                null;

            if (!trigger) {
                return;
            }

            const action = trigger.getAttribute('data-runtime-matrix-action') || '';

            if (action === 'capture') {
                const options = syncConfigFromForm();
                const snapshot = captureSnapshot('manual-capture', options);
                const runs = appendRun(snapshot);
                window.__spaLabRuntimeMatrix.last = snapshot;
                renderSnapshot(snapshot);
                renderRuns(runs);
                renderMatrixCoverage(runs);
                return;
            }

            if (action === 'reset') {
                syncConfigFromForm();
                window.__spaLabRuntimeMatrix.reset();
                return;
            }

            if (action === 'export') {
                window.__spaLabRuntimeMatrix.export();
                return;
            }

            if (action === 'export-runs') {
                window.__spaLabRuntimeMatrix.exportRuns();
                return;
            }

            if (action === 'clear-runs') {
                window.__spaLabRuntimeMatrix.clearRuns();
                return;
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const config = ensureConfigIsCurrent();
            hydrateFormFromConfig(config);
            syncDegradationProfile(config);
            setTimeout(() => {
                window.__spaLabRuntimeMatrix.render('dom-ready');
            }, 50);
        });

        document.addEventListener('volt:navigated', () => {
            const config = readConfig();
            hydrateFormFromConfig(config);
            syncDegradationProfile(config);
            setTimeout(() => {
                window.__spaLabRuntimeMatrix.render('volt:navigated');
            }, 50);
        });

        document.addEventListener('input', (event) => {
            const target = event.target;

            if (!target || !target.matches || !target.matches('[data-runtime-matrix]')) {
                return;
            }

            syncConfigFromForm();
        });

        document.addEventListener('change', (event) => {
            const target = event.target;

            if (!target || !target.matches || !target.matches('[data-runtime-matrix]')) {
                return;
            }

            syncConfigFromForm();
        });

        if (document.readyState !== 'loading') {
            const config = ensureConfigIsCurrent();
            hydrateFormFromConfig(config);
            syncDegradationProfile(config);
            setTimeout(() => {
                window.__spaLabRuntimeMatrix.render('eager');
            }, 50);
        }
    })();
</script>
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;" data-runtime-matrix-page>
    <section
        style="display:grid;gap:16px;border:1px solid rgba(34,197,94,0.22);background:linear-gradient(135deg,rgba(6,78,59,0.88),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#f0fdf4;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(34,197,94,0.32);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#bbf7d0;">Matriz
            de eficiencia</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#bbf7d0;line-height:1.75;max-inline-size:76ch;">
                Esta pantalla sirve como runner manual: ejecutas escenarios en los labs (SPA, actions, model sync) y vuelves
                aqui para capturar un snapshot coherente de <code>telemetry</code>, <code>runtime asset</code>,
                <code>heap</code> (si existe), y un resumen de budgets.
            </p>
        </div>
    </section>

    <section
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
            <div style="display:grid;gap:6px;">
                <h2 style="margin:0;font-size:22px;">Acciones</h2>
                <span style="color:#94a3b8;font-size:13px;line-height:1.6;">Capturado en: <strong
                        data-runtime-matrix="captured-at" style="color:#f8fafc;">(pendiente)</strong></span>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:12px;">
                <button type="button" data-runtime-matrix-action="reset"
                    style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:10px;padding:10px 14px;cursor:pointer;">
                    Resetear telemetria
                </button>
                <button type="button" data-runtime-matrix-action="capture"
                    style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.18);color:#dcfce7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                    Capturar snapshot
                </button>
                <button type="button" data-runtime-matrix-action="export"
                    style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.18);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                    Exportar JSON
                </button>
            </div>
        </div>
    </section>

    <section
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:22px;">Contexto de corrida</h2>
        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));align-items:end;">
            <label style="display:grid;gap:8px;color:#cbd5e1;">
                <span style="font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">Escenario</span>
                <select data-runtime-matrix="scenario"
                    style="border:1px solid rgba(51,65,85,1);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
                    <option value="boot">boot</option>
                    <option value="spa">navegacion-spa</option>
                    <option value="action">action-reactiva</option>
                    <option value="model.sync">volt-model-sync</option>
                    <option value="long-session">sesion-larga</option>
                </select>
            </label>
            <label style="display:grid;gap:8px;color:#cbd5e1;">
                <span style="font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">Condición</span>
                <select data-runtime-matrix="condition"
                    style="border:1px solid rgba(51,65,85,1);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
                    <option value="normal">normal</option>
                    <option value="degradada">degradada</option>
                </select>
            </label>
            <label style="display:grid;gap:8px;color:#cbd5e1;">
                <span style="font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">Budget boot (ms)</span>
                <input data-runtime-matrix="budget-boot-ms" type="number" min="0" step="1"
                    style="border:1px solid rgba(51,65,85,1);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
            </label>
            <label style="display:grid;gap:8px;color:#cbd5e1;">
                <span style="font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">Budget patch (ms)</span>
                <input data-runtime-matrix="budget-patch-ms" type="number" min="0" step="1"
                    style="border:1px solid rgba(51,65,85,1);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
            </label>
            <label style="display:grid;gap:8px;color:#cbd5e1;">
                <span style="font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">Budget payload action (bytes)</span>
                <input data-runtime-matrix="budget-payload-bytes" type="number" min="0" step="1"
                    style="border:1px solid rgba(51,65,85,1);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
            </label>
            <label style="display:grid;gap:8px;color:#cbd5e1;">
                <span style="font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">Budget payload nav (bytes)</span>
                <input data-runtime-matrix="budget-navigation-payload-bytes" type="number" min="0" step="1"
                    style="border:1px solid rgba(51,65,85,1);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
            </label>
            <label style="display:grid;gap:8px;color:#cbd5e1;">
                <span style="font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">Budget buffer telemetry (max)</span>
                <input data-runtime-matrix="budget-telemetry-max" type="number" min="0" step="1"
                    style="border:1px solid rgba(51,65,85,1);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
            </label>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button" data-runtime-matrix-action="export-runs"
                style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.18);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Exportar corridas (JSON)
            </button>
            <button type="button" data-runtime-matrix-action="clear-runs"
                style="border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.82);color:#e2e8f0;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Limpiar historial
            </button>
        </div>
        <p style="margin:0;color:#94a3b8;font-size:12px;line-height:1.7;">
            La condición <strong style="color:#f8fafc;">degradada</strong> activa un harness reproducible del lab:
            añade latencia artificial de red y un bloqueo controlado de CPU en hooks del runtime. Nos sirve para comparar
            escenarios dentro del runner; la validación final de carga fría con throttling real de DevTools sigue siendo una pasada aparte.
        </p>
    </section>

    <section
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
            <div style="display:grid;gap:6px;">
                <h2 style="margin:0;font-size:22px;">Cobertura base 4x2</h2>
                <span style="color:#94a3b8;font-size:13px;line-height:1.6;">Combinaciones cerradas: <strong
                        data-runtime-matrix="coverage-summary" style="color:#f8fafc;">0/8</strong></span>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:12px;">
                <span
                    style="display:inline-flex;gap:8px;align-items:center;border:1px solid rgba(34,197,94,0.22);background:rgba(20,83,45,0.14);border-radius:999px;padding:8px 12px;color:#dcfce7;font-size:12px;">
                    alertas <strong data-runtime-matrix="coverage-alerts">0</strong>
                </span>
                <span
                    style="display:inline-flex;gap:8px;align-items:center;border:1px solid rgba(245,158,11,0.22);background:rgba(120,53,15,0.14);border-radius:999px;padding:8px 12px;color:#fef3c7;font-size:12px;">
                    pendientes <strong data-runtime-matrix="coverage-pending">8</strong>
                </span>
            </div>
        </div>
        <div style="overflow:auto;border:1px solid rgba(51,65,85,1);border-radius:14px;">
            <table style="width:100%;border-collapse:collapse;min-inline-size:980px;">
                <thead style="background:#0b1220;">
                    <tr>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">scenario</th>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">condition</th>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">ruta / flujo</th>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">ultima captura</th>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">estado</th>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">metricas</th>
                    </tr>
                </thead>
                <tbody data-runtime-matrix="coverage-body"></tbody>
            </table>
        </div>
    </section>

    <section
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:22px;">Historial (últimas 30)</h2>
        <div style="overflow:auto;border:1px solid rgba(51,65,85,1);border-radius:14px;">
            <table style="width:100%;border-collapse:collapse;min-inline-size:780px;">
                <thead style="background:#0b1220;">
                    <tr>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">capturedAt</th>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">scenario</th>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">condition</th>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">boot</th>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">patch</th>
                        <th style="text-align:left;padding:10px 12px;font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">payload</th>
                    </tr>
                </thead>
                <tbody data-runtime-matrix="runs-body"></tbody>
            </table>
        </div>
    </section>

    <section
        style="display:grid;gap:16px;border:1px solid rgba(59,130,246,0.22);background:rgba(15,23,42,0.92);border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:22px;">Escenarios recomendados</h2>
        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
            <a href="/spaReactive" volt:navigate
                style="display:grid;gap:10px;border:1px solid rgba(59,130,246,0.22);background:rgba(30,64,175,0.12);border-radius:18px;padding:16px;text-decoration:none;color:#dbeafe;">
                <strong style="font-size:16px;color:#bfdbfe;">Navegacion SPA</strong>
                <span style="color:#93c5fd;line-height:1.6;">Ciclo recomendado: <code>/spaReactive</code> &lt;-&gt;
                    <code>/cacheExample</code>.</span>
            </a>
            <a href="/runtimeRequestLab" volt:navigate
                style="display:grid;gap:10px;border:1px solid rgba(245,158,11,0.22);background:rgba(120,53,15,0.14);border-radius:18px;padding:16px;text-decoration:none;color:#fef3c7;">
                <strong style="font-size:16px;color:#fde68a;">Accion reactiva</strong>
                <span style="color:#fdba74;line-height:1.6;">Ejecuta <code>Fast action</code> y vuelve.</span>
            </a>
            <a href="/runtimeModelSync" volt:navigate
                style="display:grid;gap:10px;border:1px solid rgba(34,197,94,0.22);background:rgba(20,83,45,0.14);border-radius:18px;padding:16px;text-decoration:none;color:#dcfce7;">
                <strong style="font-size:16px;color:#bbf7d0;">volt:model.sync</strong>
                <span style="color:#86efac;line-height:1.6;">Escribe rapido y verifica coalesce en 1 POST
                    <code>__volt_sync__</code>.</span>
            </a>
            <a href="/runtimeEvents" volt:navigate
                style="display:grid;gap:10px;border:1px solid rgba(148,163,184,0.22);background:rgba(15,23,42,0.72);border-radius:18px;padding:16px;text-decoration:none;color:#e2e8f0;">
                <strong style="font-size:16px;color:#e2e8f0;">Budgets en vivo</strong>
                <span style="color:#94a3b8;line-height:1.6;">Contraste contra el panel de budgets actual.</span>
            </a>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <a href="/runtimeMatrix" volt:navigate volt:prefetch="none"
                style="display:inline-flex;align-items:center;border:1px solid rgba(34,197,94,0.24);background:rgba(20,83,45,0.18);color:#dcfce7;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Volver a capturar
            </a>
        </div>
    </section>

    <section
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:22px;">Budgets (snapshot)</h2>
        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
            <article
                style="display:grid;gap:8px;border:1px solid rgba(34,197,94,0.22);background:rgba(20,83,45,0.14);border-radius:16px;padding:14px;">
                <strong style="color:#bbf7d0;">boot</strong>
                <span data-runtime-matrix="budget-boot" style="font-size:14px;color:#dcfce7;">pendiente</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(59,130,246,0.22);background:rgba(30,64,175,0.14);border-radius:16px;padding:14px;">
                <strong style="color:#bfdbfe;">patch</strong>
                <span data-runtime-matrix="budget-patch" style="font-size:14px;color:#dbeafe;">pendiente</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.22);background:rgba(120,53,15,0.14);border-radius:16px;padding:14px;">
                <strong style="color:#fde68a;">payload action</strong>
                <span data-runtime-matrix="budget-payload" style="font-size:14px;color:#fef3c7;">pendiente</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.22);background:rgba(88,28,135,0.14);border-radius:16px;padding:14px;">
                <strong style="color:#f5d0fe;">buffer telemetry</strong>
                <span data-runtime-matrix="budget-telemetry" style="font-size:14px;color:#faf5ff;">pendiente</span>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:22px;">Snapshot JSON</h2>
        <pre data-runtime-matrix="snapshot-json"
            style="margin:0;min-block-size:260px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#0b1220;border-radius:14px;padding:14px;color:#cbd5e1;font-size:12px;line-height:1.7;">{"waiting":"Sin captura aun."}</pre>
    </section>
</div>
@endsection
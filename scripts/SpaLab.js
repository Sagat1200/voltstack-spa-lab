const HOOK_EVENTS = [
  'volt:before-patch',
  'volt:after-patch',
  'volt:before-effect',
  'volt:after-effect',
  'volt:before-enter',
  'volt:after-enter',
  'volt:before-update',
  'volt:after-update',
  'volt:before-move',
  'volt:after-move',
  'volt:before-leave',
  'volt:after-leave',
  'volt:request-start',
  'volt:request-finish',
  'volt:request-error',
  'volt:request-abort',
  'volt:request-stale',
  'volt:busy-start',
  'volt:busy-change',
  'volt:busy-end',
  'volt:dirty',
  'volt:clean',
  'volt:error-cleared',
  'volt:success',
  'volt:success-cleared',
  'volt:before-navigate',
  'volt:navigated',
  'volt:state-changed',
  'volt:state-cleared',
  'volt:state-scope-changed',
  'volt:state-sync',
  'volt:cache-hit',
  'volt:cache-miss',
  'volt:cache-store',
  'volt:cache-invalidate',
  'volt:cache-clear',
  'volt:fragment-preserve',
  'volt:fragment-discard',
]

const DEMO_EVENTS = [
  'demo.counter.incremented',
  'demo.saved',
]

function isCacheRuntimeEvent(eventName) {
  return eventName.startsWith('volt:cache-')
}

function isFragmentRuntimeEvent(eventName) {
  return eventName.startsWith('volt:fragment-')
}

function escapeHtml(value) {
  return String(value)
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#39;')
}

function hookSourceFromDetail(detail) {
  if (!detail || typeof detail !== 'object') {
    return null
  }

  if (typeof detail.source === 'string' && detail.source.trim() !== '') {
    return detail.source.trim()
  }

  return null
}

function hookSourceBadgeClass(source) {
  if (source === 'prefetch') {
    return 'border-cyan-500/40 bg-cyan-500/10 text-cyan-200'
  }

  if (source === 'navigate') {
    return 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
  }

  if (source === 'event' || source === 'cache-example') {
    return 'border-amber-500/40 bg-amber-500/10 text-amber-200'
  }

  return 'border-slate-700 bg-slate-950/80 text-slate-400'
}

function normalizeHookValue(value) {
  if (value === null || typeof value === 'string' || typeof value === 'number' || typeof value === 'boolean') {
    return value
  }

  if (Array.isArray(value)) {
    return value.map(normalizeHookValue)
  }

  if (value && typeof value === 'object') {
    if (value.tagName) {
      return `<${String(value.tagName).toLowerCase()}>`
    }

    const result = {}

    Object.keys(value).forEach((key) => {
      const normalized = normalizeHookValue(value[key])

      if (typeof normalized !== 'undefined') {
        result[key] = normalized
      }
    })

    return result
  }

  return undefined
}

function serializeHookDetail(detail) {
  try {
    const normalized = normalizeHookValue(detail || {})

    return JSON.stringify(normalized, null, 2)
  } catch (error) {
    return '{"error":"No se pudo serializar el detalle del hook."}'
  }
}

function formatHookTime(date) {
  return date.toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  })
}

function closestFromEventTarget(event, selector) {
  return event.target instanceof Element ? event.target.closest(selector) : null
}

const hookState = {
  counters: new Map(),
  latest: new Map(),
  logs: [],
}

const navigationArrivalState = {
  kind: 'document-load',
  summary: 'Carga inicial del documento',
  detail: {},
}

const NAVIGATION_DEBUG_STORAGE_KEY = 'volt.runtimeEvents.lastNavigationDebug'
const RUNTIME_BUSY_STORAGE_KEY = 'volt.runtimeEvents.busySnapshot'

const navigationDebugState = {
  stage: 'idle',
  outcome: 'sin dato',
  clickHref: null,
  clickText: null,
  requestId: null,
  requestUrl: null,
  finalUrl: null,
  location: null,
  scrollBefore: null,
  scrollAfter: null,
  updatedAt: null,
  detail: {
    waiting: 'Aun no hay intentos de navegacion capturados.',
  },
}

const runtimeBusyPanelState = {
  lastEvent: 'boot',
  updatedAt: null,
  snapshot: null,
  lastActiveSnapshot: null,
  lastActionActiveSnapshot: null,
  documentSnapshot: null,
  detail: {
    waiting: 'Aun no hay snapshots busy capturados.',
  },
  scheduled: false,
  pendingReason: 'boot',
  pendingDetail: {},
}

const runtimeStateExampleState = {
  lastEvent: null,
  lastRequestEvent: null,
}

const runtimeEfficiencyState = {
  lastUpdatedAt: null,
  lastReason: 'boot',
  scheduled: false,
  pendingReason: 'boot',
  pendingDetailLevel: 'full',
}

function roundMetric(value) {
  return typeof value === 'number' && Number.isFinite(value)
    ? Math.round(value * 100) / 100
    : null
}

function formatMetric(value, suffix = ' ms') {
  return typeof value === 'number' && Number.isFinite(value)
    ? `${roundMetric(value)}${suffix}`
    : 'n/d'
}

function formatBytes(value) {
  return typeof value === 'number' && Number.isFinite(value)
    ? `${Math.round(value)} B`
    : 'n/d'
}

function formatKilobytes(value) {
  return typeof value === 'number' && Number.isFinite(value)
    ? `${Math.round((value / 1024) * 100) / 100} KB`
    : 'n/d'
}

function evaluateBudgetStatus(actual, target, options = {}) {
  if (typeof actual !== 'number' || !Number.isFinite(actual)) {
    return options.pendingLabel || 'pendiente'
  }

  if (actual <= target) {
    return options.okLabel || 'ok'
  }

  return options.alertLabel || 'alerta'
}

function setBudgetCardState(card, status) {
  if (!card) {
    return
  }

  if (status === 'ok') {
    card.style.borderColor = 'rgba(34,197,94,0.34)'
    card.style.background = 'rgba(20,83,45,0.20)'
    return
  }

  if (status === 'alerta') {
    card.style.borderColor = 'rgba(239,68,68,0.38)'
    card.style.background = 'rgba(127,29,29,0.22)'
    return
  }

  card.style.borderColor = 'rgba(148,163,184,0.22)'
  card.style.background = '#020617'
}

function hasRuntimeEfficiencyExample() {
  return !!document.querySelector('[data-volt-efficiency-example]')
}

function formatOutcomes(outcomes) {
  if (!outcomes || typeof outcomes !== 'object' || Array.isArray(outcomes)) {
    return 'n/d'
  }

  const items = Object.keys(outcomes)
    .sort()
    .map((key) => `${key}:${outcomes[key]}`)

  return items.length > 0 ? items.join(', ') : 'n/d'
}

function runtimeTelemetryApi() {
  return window.Volt && window.Volt.telemetry ? window.Volt.telemetry : null
}

function runtimeComponentsApi() {
  return window.Volt && window.Volt.components ? window.Volt.components : null
}

function runtimeBusyApi() {
  return window.Volt && window.Volt.busy ? window.Volt.busy : null
}

function readDocumentBusySnapshot() {
  const element = document.body || document.documentElement

  if (!element || typeof element.getAttribute !== 'function') {
    return {
      active: false,
      kind: 'idle',
      phase: 'idle',
      source: 'idle',
      requestId: null,
      component: null,
      action: null,
      target: null,
    }
  }

  return {
    active: element.getAttribute('data-volt-busy') === 'true',
    kind: element.getAttribute('data-volt-busy-kind') || 'idle',
    phase: element.getAttribute('data-volt-busy-phase') || 'idle',
    source: element.getAttribute('data-volt-busy-source') || 'idle',
    requestId: element.getAttribute('data-volt-busy-request-id') || null,
    component: element.getAttribute('data-volt-busy-component') || null,
    action: element.getAttribute('data-volt-busy-action') || null,
    target: element.getAttribute('data-volt-busy-target') || null,
  }
}

function readRuntimeBusySnapshot() {
  const api = runtimeBusyApi()

  if (!api || typeof api.current !== 'function') {
    return readDocumentBusySnapshot()
  }

  const snapshot = api.current()
  return snapshot && typeof snapshot === 'object'
    ? normalizeHookValue(snapshot)
    : readDocumentBusySnapshot()
}

function hasRuntimeBusyPanel() {
  return !!document.querySelector('[data-runtime-busy-panel]')
}

function resolvedNavigationMetric(value, options = {}) {
  if (typeof value !== 'number' || !Number.isFinite(value)) {
    return null
  }

  if (value > 0) {
    return roundMetric(value)
  }

  const allowZero = options.allowZero === true
  return allowZero ? roundMetric(value) : null
}

function runtimeNavigationPerformanceSnapshot() {
  const entry = currentNavigationEntry()

  if (!entry) {
    return null
  }

  const loadEventEnd = resolvedNavigationMetric(entry.loadEventEnd)
  const duration = loadEventEnd !== null
    ? resolvedNavigationMetric(entry.duration) ?? loadEventEnd
    : null

  return {
    type: typeof entry.type === 'string' ? entry.type : 'navigate',
    duration: duration,
    domInteractive: resolvedNavigationMetric(entry.domInteractive),
    domContentLoaded: resolvedNavigationMetric(entry.domContentLoadedEventEnd),
    loadEventEnd: loadEventEnd,
    transferSize: typeof entry.transferSize === 'number' ? Math.round(entry.transferSize) : null,
    encodedBodySize: typeof entry.encodedBodySize === 'number' ? Math.round(entry.encodedBodySize) : null,
    decodedBodySize: typeof entry.decodedBodySize === 'number' ? Math.round(entry.decodedBodySize) : null,
  }
}

function runtimeAssetPerformanceSnapshot() {
  if (typeof window === 'undefined' || !window.performance || typeof window.performance.getEntriesByType !== 'function') {
    return null
  }

  const entries = window.performance
    .getEntriesByType('resource')
    .filter((entry) => entry && typeof entry.name === 'string' && entry.name.includes('/_volt/runtime.js'))

  if (entries.length === 0) {
    return null
  }

  const entry = entries[entries.length - 1]

  return {
    name: entry.name,
    duration: roundMetric(entry.duration),
    transferSize: typeof entry.transferSize === 'number' ? Math.round(entry.transferSize) : null,
    encodedBodySize: typeof entry.encodedBodySize === 'number' ? Math.round(entry.encodedBodySize) : null,
    decodedBodySize: typeof entry.decodedBodySize === 'number' ? Math.round(entry.decodedBodySize) : null,
    count: entries.length,
  }
}

function runtimeBootPerformanceSnapshot() {
  if (typeof window === 'undefined' || !window.Volt || !window.Volt.telemetry || typeof window.Volt.telemetry.boot !== 'function') {
    return null
  }

  const snapshot = window.Volt.telemetry.boot()

  if (!snapshot || typeof snapshot !== 'object') {
    return null
  }

  return {
    invocationCount: typeof snapshot.invocationCount === 'number' ? snapshot.invocationCount : null,
    readyState: typeof snapshot.readyState === 'string' ? snapshot.readyState : null,
    totalDurationMs: typeof snapshot.totalDurationMs === 'number' ? roundMetric(snapshot.totalDurationMs) : null,
    busyDurationMs: typeof snapshot.busyDurationMs === 'number' ? roundMetric(snapshot.busyDurationMs) : null,
    syncDurationMs: typeof snapshot.syncDurationMs === 'number' ? roundMetric(snapshot.syncDurationMs) : null,
    refreshDurationMs: typeof snapshot.refreshDurationMs === 'number' ? roundMetric(snapshot.refreshDurationMs) : null,
    viewportPrefetchDurationMs: typeof snapshot.viewportPrefetchDurationMs === 'number' ? roundMetric(snapshot.viewportPrefetchDurationMs) : null,
    heuristicPrefetchDurationMs: typeof snapshot.heuristicPrefetchDurationMs === 'number' ? roundMetric(snapshot.heuristicPrefetchDurationMs) : null,
    rootCount: typeof snapshot.rootCount === 'number' ? snapshot.rootCount : null,
    recordedAt: typeof snapshot.recordedAt === 'string' ? snapshot.recordedAt : null,
  }
}

function updateRuntimeEfficiencyKindCard(root, kind, summary) {
  root.querySelectorAll(`[data-volt-efficiency-kind="${kind}"]`).forEach((card) => {
    const countNode = card.querySelector('[data-volt-efficiency-count]')
    const outcomesNode = card.querySelector('[data-volt-efficiency-outcomes]')
    const avgDurationNode = card.querySelector('[data-volt-efficiency-avg-duration]')
    const maxDurationNode = card.querySelector('[data-volt-efficiency-max-duration]')
    const avgRequestNode = card.querySelector('[data-volt-efficiency-avg-request]')
    const maxRequestNode = card.querySelector('[data-volt-efficiency-max-request]')
    const avgResponseNode = card.querySelector('[data-volt-efficiency-avg-response]')
    const maxResponseNode = card.querySelector('[data-volt-efficiency-max-response]')
    const avgPatchNode = card.querySelector('[data-volt-efficiency-avg-patch]')
    const maxPatchNode = card.querySelector('[data-volt-efficiency-max-patch]')

    if (countNode) {
      countNode.textContent = summary ? String(summary.count) : '0'
    }

    if (outcomesNode) {
      outcomesNode.textContent = formatOutcomes(summary ? summary.outcomes : null)
    }

    if (avgDurationNode) {
      avgDurationNode.textContent = formatMetric(summary ? summary.averageDurationMs : null)
    }

    if (maxDurationNode) {
      maxDurationNode.textContent = formatMetric(summary ? summary.maxDurationMs : null)
    }

    if (avgRequestNode) {
      avgRequestNode.textContent = formatBytes(summary ? summary.averageRequestPayloadBytes : null)
    }

    if (maxRequestNode) {
      maxRequestNode.textContent = formatBytes(summary ? summary.maxRequestPayloadBytes : null)
    }

    if (avgResponseNode) {
      avgResponseNode.textContent = formatBytes(summary ? summary.averageResponsePayloadBytes : null)
    }

    if (maxResponseNode) {
      maxResponseNode.textContent = formatBytes(summary ? summary.maxResponsePayloadBytes : null)
    }

    if (avgPatchNode) {
      avgPatchNode.textContent = formatMetric(summary ? summary.averagePatchDurationMs : null)
    }

    if (maxPatchNode) {
      maxPatchNode.textContent = formatMetric(summary ? summary.maxPatchDurationMs : null)
    }
  })
}

function syncRuntimeEfficiencyExamples(reason = 'manual', detailLevel = 'full') {
  if (!hasRuntimeEfficiencyExample()) {
    return
  }

  const fullSync = detailLevel !== 'budget-only'
  const telemetry = runtimeTelemetryApi()
  const components = fullSync ? runtimeComponentsApi() : null
  const telemetrySummary = telemetry ? telemetry.summary() : null
  const latestNavigation = fullSync && telemetry ? telemetry.latest({ kind: 'navigation' }) : null
  const latestAction = fullSync && telemetry ? telemetry.latest({ kind: 'action' }) : null
  const latestPatch = fullSync && telemetry ? telemetry.latest({ kind: 'patch' }) : null
  const componentsSummary = components ? components.summary() : null
  const navigationPerformance = runtimeNavigationPerformanceSnapshot()
  const runtimeBootPerformance = runtimeBootPerformanceSnapshot()
  const runtimeAssetPerformance = runtimeAssetPerformanceSnapshot()
  const now = new Date()
  const bootBudgetTargetMs = 150
  const patchBudgetTargetMs = 120
  const actionPayloadBudgetBytes = 25 * 1024
  const bufferUsage = telemetrySummary && typeof telemetrySummary.totalEntries === 'number'
    ? telemetrySummary.totalEntries
    : null
  const bufferMax = telemetrySummary && typeof telemetrySummary.maxEntries === 'number'
    ? telemetrySummary.maxEntries
    : null
  const bootActual = runtimeBootPerformance && typeof runtimeBootPerformance.totalDurationMs === 'number'
    ? runtimeBootPerformance.totalDurationMs
    : null
  const patchActual = telemetrySummary && telemetrySummary.patch &&
    typeof telemetrySummary.patch.averagePatchDurationMs === 'number'
    ? telemetrySummary.patch.averagePatchDurationMs
    : null
  const actionPayloadActual = telemetrySummary && telemetrySummary.action &&
    typeof telemetrySummary.action.averageRequestPayloadBytes === 'number'
    ? telemetrySummary.action.averageRequestPayloadBytes
    : null
  const bufferStatus = typeof bufferUsage === 'number' && typeof bufferMax === 'number'
    ? evaluateBudgetStatus(bufferUsage, bufferMax)
    : 'pendiente'
  const bootStatus = evaluateBudgetStatus(bootActual, bootBudgetTargetMs)
  const patchStatus = evaluateBudgetStatus(patchActual, patchBudgetTargetMs)
  const payloadStatus = evaluateBudgetStatus(actionPayloadActual, actionPayloadBudgetBytes)
  const statuses = [bootStatus, patchStatus, payloadStatus, bufferStatus]
  const overallStatus = statuses.includes('alerta')
    ? 'alerta'
    : statuses.every((status) => status === 'ok')
      ? 'ok'
      : 'pendiente'
  const budgetSummary = [
    `boot=${bootStatus} (${formatMetric(bootActual)})`,
    `patch=${patchStatus} (${formatMetric(patchActual)})`,
    `payload=${payloadStatus} (${formatKilobytes(actionPayloadActual)})`,
    `buffer=${bufferStatus} (${typeof bufferUsage === 'number' && typeof bufferMax === 'number' ? `${bufferUsage}/${bufferMax}` : 'n/d'})`,
  ].join(' | ')

  runtimeEfficiencyState.lastUpdatedAt = now
  runtimeEfficiencyState.lastReason = reason

  document.querySelectorAll('[data-volt-efficiency-example]').forEach((root) => {
    const navTypeNode = root.querySelector('[data-volt-efficiency-nav-type]')
    const navDurationNode = root.querySelector('[data-volt-efficiency-nav-duration]')
    const navInteractiveNode = root.querySelector('[data-volt-efficiency-nav-dom-interactive]')
    const navDclNode = root.querySelector('[data-volt-efficiency-nav-dcl]')
    const navLoadNode = root.querySelector('[data-volt-efficiency-nav-load]')
    const navTransferNode = root.querySelector('[data-volt-efficiency-nav-transfer]')
    const runtimeNameNode = root.querySelector('[data-volt-efficiency-runtime-name]')
    const runtimeDurationNode = root.querySelector('[data-volt-efficiency-runtime-duration]')
    const runtimeTransferNode = root.querySelector('[data-volt-efficiency-runtime-transfer]')
    const runtimeBodyNode = root.querySelector('[data-volt-efficiency-runtime-body]')
    const totalEntriesNode = root.querySelector('[data-volt-efficiency-total-entries]')
    const maxEntriesNode = root.querySelector('[data-volt-efficiency-max-entries]')
    const totalRootsNode = root.querySelector('[data-volt-efficiency-total-roots]')
    const uniqueComponentsNode = root.querySelector('[data-volt-efficiency-unique-components]')
    const componentsDetailNode = root.querySelector('[data-volt-efficiency-components-detail]')
    const summaryNode = root.querySelector('[data-volt-efficiency-summary-json]')
    const navLatestNode = root.querySelector('[data-volt-efficiency-latest="navigation"]')
    const actionLatestNode = root.querySelector('[data-volt-efficiency-latest="action"]')
    const patchLatestNode = root.querySelector('[data-volt-efficiency-latest="patch"]')
    const statusNode = root.querySelector('[data-volt-efficiency-status]')
    const updatedNode = root.querySelector('[data-volt-efficiency-last-updated]')
    const budgetBootCard = root.querySelector('[data-runtime-check="efficiency-budget-boot"]')
    const budgetPatchCard = root.querySelector('[data-runtime-check="efficiency-budget-patch"]')
    const budgetPayloadCard = root.querySelector('[data-runtime-check="efficiency-budget-payload"]')
    const budgetBufferCard = root.querySelector('[data-runtime-check="efficiency-budget-buffer"]')
    const budgetBootStatusNode = root.querySelector('[data-volt-efficiency-budget-boot-status]')
    const budgetBootValueNode = root.querySelector('[data-volt-efficiency-budget-boot-value]')
    const budgetPatchStatusNode = root.querySelector('[data-volt-efficiency-budget-patch-status]')
    const budgetPatchValueNode = root.querySelector('[data-volt-efficiency-budget-patch-value]')
    const budgetPayloadStatusNode = root.querySelector('[data-volt-efficiency-budget-payload-status]')
    const budgetPayloadValueNode = root.querySelector('[data-volt-efficiency-budget-payload-value]')
    const budgetBufferStatusNode = root.querySelector('[data-volt-efficiency-budget-buffer-status]')
    const budgetBufferValueNode = root.querySelector('[data-volt-efficiency-budget-buffer-value]')
    const budgetOverallNode = root.querySelector('[data-volt-efficiency-budget-overall]')
    const budgetSummaryNode = root.querySelector('[data-volt-efficiency-budget-summary]')

    if (navTypeNode) {
      navTypeNode.textContent = navigationPerformance ? navigationPerformance.type : 'n/d'
    }

    if (navDurationNode) {
      navDurationNode.textContent = formatMetric(navigationPerformance ? navigationPerformance.duration : null)
    }

    if (navInteractiveNode) {
      navInteractiveNode.textContent = formatMetric(navigationPerformance ? navigationPerformance.domInteractive : null)
    }

    if (navDclNode) {
      navDclNode.textContent = formatMetric(navigationPerformance ? navigationPerformance.domContentLoaded : null)
    }

    if (navLoadNode) {
      navLoadNode.textContent = formatMetric(navigationPerformance ? navigationPerformance.loadEventEnd : null)
    }

    if (navTransferNode) {
      navTransferNode.textContent = formatBytes(navigationPerformance ? navigationPerformance.transferSize : null)
    }

    if (runtimeNameNode) {
      runtimeNameNode.textContent = runtimeAssetPerformance ? runtimeAssetPerformance.name : 'n/d'
    }

    if (runtimeDurationNode) {
      runtimeDurationNode.textContent = formatMetric(runtimeAssetPerformance ? runtimeAssetPerformance.duration : null)
    }

    if (runtimeTransferNode) {
      runtimeTransferNode.textContent = formatBytes(runtimeAssetPerformance ? runtimeAssetPerformance.transferSize : null)
    }

    if (runtimeBodyNode) {
      runtimeBodyNode.textContent = formatBytes(runtimeAssetPerformance ? runtimeAssetPerformance.encodedBodySize : null)
    }

    if (totalEntriesNode) {
      totalEntriesNode.textContent = telemetrySummary ? String(telemetrySummary.totalEntries) : '0'
    }

    if (maxEntriesNode) {
      maxEntriesNode.textContent = telemetrySummary ? String(telemetrySummary.maxEntries) : '0'
    }

    if (totalRootsNode) {
      totalRootsNode.textContent = componentsSummary ? String(componentsSummary.totalRoots) : '0'
    }

    if (uniqueComponentsNode) {
      uniqueComponentsNode.textContent = componentsSummary ? String(componentsSummary.uniqueComponents) : '0'
    }

    if (componentsDetailNode && fullSync) {
      componentsDetailNode.textContent = serializeHookDetail(
        componentsSummary && Array.isArray(componentsSummary.components)
          ? componentsSummary.components
          : [],
      )
    }

    if (summaryNode && fullSync) {
      summaryNode.textContent = serializeHookDetail({
        navigationPerformance,
        runtimeBootPerformance,
        runtimeAssetPerformance,
        telemetrySummary,
        componentsSummary,
      })
    }

    if (navLatestNode && fullSync) {
      navLatestNode.textContent = serializeHookDetail(latestNavigation || {
        waiting: 'Aun no hay navegaciones registradas en window.Volt.telemetry.',
      })
    }

    if (actionLatestNode && fullSync) {
      actionLatestNode.textContent = serializeHookDetail(latestAction || {
        waiting: 'Aun no hay acciones reactivas registradas en window.Volt.telemetry.',
      })
    }

    if (patchLatestNode && fullSync) {
      patchLatestNode.textContent = serializeHookDetail(latestPatch || {
        waiting: 'Aun no hay patches registrados en window.Volt.telemetry.',
      })
    }

    if (statusNode) {
      statusNode.textContent = `Actualizado por ${reason} (${overallStatus}${fullSync ? '' : ', resumido'})`
    }

    if (updatedNode) {
      updatedNode.textContent = formatHookTime(now)
    }

    if (budgetBootStatusNode) {
      budgetBootStatusNode.textContent = bootStatus
    }

    if (budgetBootValueNode) {
      budgetBootValueNode.textContent = formatMetric(bootActual)
    }

    if (budgetPatchStatusNode) {
      budgetPatchStatusNode.textContent = patchStatus
    }

    if (budgetPatchValueNode) {
      budgetPatchValueNode.textContent = formatMetric(patchActual)
    }

    if (budgetPayloadStatusNode) {
      budgetPayloadStatusNode.textContent = payloadStatus
    }

    if (budgetPayloadValueNode) {
      budgetPayloadValueNode.textContent = formatKilobytes(actionPayloadActual)
    }

    if (budgetBufferStatusNode) {
      budgetBufferStatusNode.textContent = bufferStatus
    }

    if (budgetBufferValueNode) {
      budgetBufferValueNode.textContent = typeof bufferUsage === 'number' && typeof bufferMax === 'number'
        ? `${bufferUsage} / ${bufferMax}`
        : 'n/d'
    }

    if (budgetOverallNode) {
      budgetOverallNode.textContent = overallStatus
    }

    if (budgetSummaryNode) {
      budgetSummaryNode.textContent = budgetSummary
    }

    setBudgetCardState(budgetBootCard, bootStatus)
    setBudgetCardState(budgetPatchCard, patchStatus)
    setBudgetCardState(budgetPayloadCard, payloadStatus)
    setBudgetCardState(budgetBufferCard, bufferStatus)

    updateRuntimeEfficiencyKindCard(root, 'navigation', telemetrySummary ? telemetrySummary.navigation : null)
    updateRuntimeEfficiencyKindCard(root, 'action', telemetrySummary ? telemetrySummary.action : null)
    updateRuntimeEfficiencyKindCard(root, 'patch', telemetrySummary ? telemetrySummary.patch : null)
  })
}

function scheduleRuntimeEfficiencySync(reason = 'manual', detailLevel = 'full') {
  if (!hasRuntimeEfficiencyExample()) {
    return
  }

  runtimeEfficiencyState.pendingReason = reason
  runtimeEfficiencyState.pendingDetailLevel = runtimeEfficiencyState.pendingDetailLevel === 'full' || detailLevel === 'full'
    ? 'full'
    : detailLevel

  if (runtimeEfficiencyState.scheduled) {
    return
  }

  runtimeEfficiencyState.scheduled = true

  window.requestAnimationFrame(() => {
    runtimeEfficiencyState.scheduled = false
    const nextReason = runtimeEfficiencyState.pendingReason
    const nextDetailLevel = runtimeEfficiencyState.pendingDetailLevel
    runtimeEfficiencyState.pendingReason = 'manual'
    runtimeEfficiencyState.pendingDetailLevel = 'full'
    syncRuntimeEfficiencyExamples(nextReason, nextDetailLevel)
  })
}

function registerRuntimeEfficiencyExamples() {
  document.addEventListener('click', (event) => {
    const trigger = closestFromEventTarget(event, '[data-volt-efficiency-action]')

    if (!trigger) {
      return
    }

    const action = trigger.getAttribute('data-volt-efficiency-action') || ''
    const telemetry = runtimeTelemetryApi()
    const components = runtimeComponentsApi()

    event.preventDefault()

    if (action === 'reset-telemetry' && telemetry) {
      telemetry.reset()
      scheduleRuntimeEfficiencySync('manual-reset-telemetry')
      return
    }

    if (action === 'refresh-components' && components) {
      components.refresh('efficiency-lab-manual')
      scheduleRuntimeEfficiencySync('manual-refresh-components')
      return
    }

    scheduleRuntimeEfficiencySync('manual-refresh')
  })

  ;[
    'volt:request-finish',
    'volt:after-patch',
    'volt:navigated',
    'volt:component-destroyed',
  ].forEach((eventName) => {
    document.addEventListener(eventName, () => {
      scheduleRuntimeEfficiencySync(eventName)
    })
  })

  window.addEventListener('load', () => {
    scheduleRuntimeEfficiencySync('window-load')
  })

  scheduleRuntimeEfficiencySync('boot', 'budget-only')
}

function currentNavigationEntry() {
  if (typeof window === 'undefined' || !window.performance || typeof window.performance.getEntriesByType !== 'function') {
    return null
  }

  const entries = window.performance.getEntriesByType('navigation')

  if (!Array.isArray(entries) || entries.length === 0) {
    return null
  }

  const entry = entries[entries.length - 1]
  return entry && typeof entry === 'object' ? entry : null
}

function initialNavigationArrivalState() {
  const entry = currentNavigationEntry()
  const type = entry && typeof entry.type === 'string' ? entry.type : 'navigate'

  if (type === 'reload') {
    return {
      kind: 'full-reload',
      summary: 'Full reload del documento',
      detail: {
        source: 'document',
        navigationEntryType: type,
      },
    }
  }

  if (type === 'back_forward') {
    return {
      kind: 'history-entry',
      summary: 'Entrada restaurada por historial del navegador',
      detail: {
        source: 'document',
        navigationEntryType: type,
      },
    }
  }

  return {
    kind: 'document-load',
    summary: 'Carga inicial directa del documento',
    detail: {
      source: 'document',
      navigationEntryType: type,
    },
  }
}

function navigationArrivalBadgeClass(kind) {
  if (kind === 'spa') {
    return 'border-emerald-500/40 bg-emerald-500/10 text-emerald-200'
  }

  if (kind === 'full-reload') {
    return 'border-rose-500/40 bg-rose-500/10 text-rose-200'
  }

  if (kind === 'history-entry') {
    return 'border-amber-500/40 bg-amber-500/10 text-amber-200'
  }

  return 'border-slate-700 bg-slate-950/80 text-slate-300'
}

function syncNavigationArrivalIndicators() {
  document.querySelectorAll('[data-volt-navigation-arrival]').forEach((panel) => {
    const badge = panel.querySelector('[data-volt-arrival-kind]')
    const summary = panel.querySelector('[data-volt-arrival-summary]')
    const detail = panel.querySelector('[data-volt-arrival-detail]')

    if (badge) {
      badge.textContent = navigationArrivalState.kind
      badge.className = `inline-flex items-center rounded-full border px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] ${navigationArrivalBadgeClass(navigationArrivalState.kind)}`
    }

    if (summary) {
      summary.textContent = navigationArrivalState.summary
    }

    if (detail) {
      detail.textContent = serializeHookDetail(navigationArrivalState.detail)
    }
  })
}

function updateNavigationArrival(state) {
  if (!state || typeof state !== 'object') {
    return
  }

  navigationArrivalState.kind = typeof state.kind === 'string' ? state.kind : navigationArrivalState.kind
  navigationArrivalState.summary = typeof state.summary === 'string' ? state.summary : navigationArrivalState.summary
  navigationArrivalState.detail = state.detail && typeof state.detail === 'object'
    ? state.detail
    : navigationArrivalState.detail

  syncNavigationArrivalIndicators()
}

function readPersistedNavigationDebugState() {
  if (typeof window === 'undefined' || !window.sessionStorage) {
    return null
  }

  try {
    const raw = window.sessionStorage.getItem(NAVIGATION_DEBUG_STORAGE_KEY)

    if (!raw) {
      return null
    }

    const parsed = JSON.parse(raw)
    return parsed && typeof parsed === 'object' ? parsed : null
  } catch (error) {
    return null
  }
}

function persistNavigationDebugState() {
  if (typeof window === 'undefined' || !window.sessionStorage) {
    return
  }

  try {
    window.sessionStorage.setItem(NAVIGATION_DEBUG_STORAGE_KEY, JSON.stringify(navigationDebugState))
  } catch (error) {
    // noop: the lab should stay usable even if sessionStorage is unavailable
  }
}

function setNavigationDebugState(patch) {
  if (!patch || typeof patch !== 'object') {
    return
  }

  Object.assign(navigationDebugState, patch, {
    updatedAt: new Date().toISOString(),
  })
  persistNavigationDebugState()
  syncNavigationDebugPanel()
}

function clearNavigationDebugState() {
  navigationDebugState.stage = 'idle'
  navigationDebugState.outcome = 'sin dato'
  navigationDebugState.clickHref = null
  navigationDebugState.clickText = null
  navigationDebugState.requestId = null
  navigationDebugState.requestUrl = null
  navigationDebugState.finalUrl = null
  navigationDebugState.location = window.location.pathname
  navigationDebugState.scrollBefore = null
  navigationDebugState.scrollAfter = null
  navigationDebugState.updatedAt = new Date().toISOString()
  navigationDebugState.detail = {
    cleared: true,
    message: 'Panel reiniciado manualmente.',
  }

  persistNavigationDebugState()
  syncNavigationDebugPanel()
}

function formatNavigationDebugScroll(label, value) {
  return `${label} = ${typeof value === 'number' && Number.isFinite(value) ? Math.round(value) : 'sin dato'}`
}

function syncNavigationDebugPanel() {
  document.querySelectorAll('[data-runtime-navigation-debug]').forEach((panel) => {
    const stageNode = panel.querySelector('[data-runtime-check="nav-debug-stage"]')
    const outcomeNode = panel.querySelector('[data-runtime-check="nav-debug-outcome"]')
    const hrefNode = panel.querySelector('[data-runtime-check="nav-debug-click-href"]')
    const textNode = panel.querySelector('[data-runtime-check="nav-debug-click-text"]')
    const requestNode = panel.querySelector('[data-runtime-check="nav-debug-request-id"]')
    const beforeNode = panel.querySelector('[data-runtime-check="nav-debug-scroll-before"]')
    const afterNode = panel.querySelector('[data-runtime-check="nav-debug-scroll-after"]')
    const locationNode = panel.querySelector('[data-runtime-check="nav-debug-location"]')
    const updatedNode = panel.querySelector('[data-runtime-check="nav-debug-updated-at"]')
    const detailNode = panel.querySelector('[data-runtime-check="nav-debug-detail"]')

    if (stageNode) {
      stageNode.textContent = `stage = ${navigationDebugState.stage || 'sin dato'}`
    }

    if (outcomeNode) {
      outcomeNode.textContent = `outcome = ${navigationDebugState.outcome || 'sin dato'}`
    }

    if (hrefNode) {
      hrefNode.textContent = `href = ${navigationDebugState.clickHref || 'sin href'}`
    }

    if (textNode) {
      textNode.textContent = `texto = ${navigationDebugState.clickText || 'sin texto'}`
    }

    if (requestNode) {
      requestNode.textContent = `requestId = ${navigationDebugState.requestId || 'sin requestId'}`
    }

    if (beforeNode) {
      beforeNode.textContent = formatNavigationDebugScroll('scroll.before', navigationDebugState.scrollBefore)
    }

    if (afterNode) {
      afterNode.textContent = formatNavigationDebugScroll('scroll.after', navigationDebugState.scrollAfter)
    }

    if (locationNode) {
      const location = navigationDebugState.location || window.location.pathname
      const finalUrl = navigationDebugState.finalUrl || navigationDebugState.requestUrl || 'sin finalUrl'
      locationNode.textContent = `location = ${location} | finalUrl = ${finalUrl}`
    }

    if (updatedNode) {
      updatedNode.textContent = `actualizado = ${navigationDebugState.updatedAt || 'sin dato'}`
    }

    if (detailNode) {
      detailNode.textContent = serializeStateExample(navigationDebugState.detail || {
        waiting: 'Aun no hay intentos de navegacion capturados.',
      })
    }
  })
}

function voltNavigateLinkFromEvent(event) {
  const element = event.target instanceof Element ? event.target.closest('a, area') : null

  if (!element || typeof element.hasAttribute !== 'function') {
    return null
  }

  const hasNavigateDirective = [
    'volt:navigate',
    'data-volt-navigate',
    'volt-navigate',
  ].some((attribute) => element.hasAttribute(attribute))

  return hasNavigateDirective ? element : null
}

function recordNavigationDebugFromClick(event) {
  const link = voltNavigateLinkFromEvent(event)

  if (!link) {
    return
  }

  const href = typeof link.href === 'string' && link.href !== ''
    ? link.href
    : link.getAttribute('href') || ''
  const text = typeof link.textContent === 'string'
    ? link.textContent.replace(/\s+/g, ' ').trim()
    : ''

  setNavigationDebugState({
    stage: 'click',
    outcome: 'esperando request',
    clickHref: href || null,
    clickText: text || null,
    requestId: null,
    requestUrl: href || null,
    finalUrl: null,
    location: window.location.pathname,
    scrollBefore: window.scrollY,
    scrollAfter: null,
    detail: {
      trigger: 'click',
      href: href || null,
      text: text || null,
      pathname: window.location.pathname,
      scrollY: Math.round(window.scrollY),
    },
  })
}

function applyPersistedNavigationDebugState() {
  const persisted = readPersistedNavigationDebugState()

  if (!persisted) {
    navigationDebugState.location = window.location.pathname
    syncNavigationDebugPanel()
    return
  }

  Object.assign(navigationDebugState, persisted, {
    location: typeof persisted.location === 'string' && persisted.location !== ''
      ? persisted.location
      : window.location.pathname,
  })
  syncNavigationDebugPanel()
}

function registerNavigationDebugPanel() {
  window.__spaLabNavigationDebug = window.__spaLabNavigationDebug || {}
  window.__spaLabNavigationDebug.clear = clearNavigationDebugState
  window.__spaLabNavigationDebug.syncVisibleState = syncNavigationDebugPanel

  document.addEventListener('click', (event) => {
    recordNavigationDebugFromClick(event)
  }, true)

  document.addEventListener('volt:request-start', (event) => {
    const detail = event.detail || {}

    if (detail.type !== 'navigation') {
      return
    }

    setNavigationDebugState({
      stage: 'request-start',
      outcome: 'request-start',
      requestId: detail.requestId || null,
      requestUrl: detail.url || navigationDebugState.requestUrl,
      location: window.location.pathname,
      scrollBefore: typeof navigationDebugState.scrollBefore === 'number' ? navigationDebugState.scrollBefore : window.scrollY,
      detail: normalizeHookValue(detail || {}),
    })
  })

  document.addEventListener('volt:before-navigate', (event) => {
    const detail = event.detail || {}

    setNavigationDebugState({
      stage: 'before-navigate',
      outcome: 'patching',
      requestUrl: detail.url || navigationDebugState.requestUrl,
      finalUrl: detail.finalUrl || navigationDebugState.finalUrl,
      location: window.location.pathname,
      detail: normalizeHookValue(detail || {}),
    })
  })

  document.addEventListener('volt:navigated', (event) => {
    const detail = event.detail || {}

    window.requestAnimationFrame(() => {
      setNavigationDebugState({
        stage: 'navigated',
        outcome: 'success',
        requestUrl: detail.url || navigationDebugState.requestUrl,
        finalUrl: detail.finalUrl || window.location.href,
        location: window.location.pathname,
        scrollAfter: window.scrollY,
        detail: normalizeHookValue(detail || {}),
      })
    })
  })

  ;[
    'volt:request-abort',
    'volt:request-stale',
    'volt:request-error',
    'volt:request-finish',
  ].forEach((eventName) => {
    document.addEventListener(eventName, (event) => {
      const detail = event.detail || {}

      if (detail.type !== 'navigation') {
        return
      }

      const stage = eventName.replace('volt:', '')
      const outcome = eventName === 'volt:request-finish'
        ? (detail.outcome || detail.errorKind || 'finished')
        : (detail.errorKind || detail.outcome || stage)

      setNavigationDebugState({
        stage,
        outcome,
        requestId: detail.requestId || navigationDebugState.requestId,
        requestUrl: detail.url || navigationDebugState.requestUrl,
        finalUrl: detail.finalUrl || navigationDebugState.finalUrl,
        location: window.location.pathname,
        scrollAfter: window.scrollY,
        detail: normalizeHookValue(detail || {}),
      })
    })
  })

  applyPersistedNavigationDebugState()
}


function readPersistedRuntimeBusyPanelState() {
  if (typeof window === 'undefined' || !window.sessionStorage) {
    return null
  }

  try {
    const raw = window.sessionStorage.getItem(RUNTIME_BUSY_STORAGE_KEY)

    if (!raw) {
      return null
    }

    const parsed = JSON.parse(raw)
    return parsed && typeof parsed === 'object' ? parsed : null
  } catch (error) {
    return null
  }
}

function persistRuntimeBusyPanelState() {
  if (typeof window === 'undefined' || !window.sessionStorage) {
    return
  }

  try {
    window.sessionStorage.setItem(RUNTIME_BUSY_STORAGE_KEY, JSON.stringify({
      lastEvent: runtimeBusyPanelState.lastEvent,
      updatedAt: runtimeBusyPanelState.updatedAt,
      snapshot: runtimeBusyPanelState.snapshot,
      lastActiveSnapshot: runtimeBusyPanelState.lastActiveSnapshot,
      lastActionActiveSnapshot: runtimeBusyPanelState.lastActionActiveSnapshot,
      documentSnapshot: runtimeBusyPanelState.documentSnapshot,
      detail: runtimeBusyPanelState.detail,
    }))
  } catch (error) {
    // noop: the lab should stay usable even if sessionStorage is unavailable
  }
}

function busyMirrorStatus(snapshot, documentSnapshot) {
  const current = snapshot && typeof snapshot === 'object' ? snapshot : {}
  const doc = documentSnapshot && typeof documentSnapshot === 'object' ? documentSnapshot : {}
  const fields = ['active', 'kind', 'phase', 'source', 'requestId', 'component', 'action', 'target']

  return fields.every((field) => {
    const left = Object.prototype.hasOwnProperty.call(current, field) ? current[field] : null
    const right = Object.prototype.hasOwnProperty.call(doc, field) ? doc[field] : null
    return left === right
  })
}

function isBusyActionSnapshot(snapshot) {
  return !!(
    snapshot &&
    typeof snapshot === 'object' &&
    snapshot.active === true &&
    snapshot.kind === 'action'
  )
}

function syncRuntimeBusyPanel() {
  if (!hasRuntimeBusyPanel()) {
    return
  }

  const snapshot = runtimeBusyPanelState.snapshot && typeof runtimeBusyPanelState.snapshot === 'object'
    ? runtimeBusyPanelState.snapshot
    : readRuntimeBusySnapshot()
  const documentSnapshot = runtimeBusyPanelState.documentSnapshot && typeof runtimeBusyPanelState.documentSnapshot === 'object'
    ? runtimeBusyPanelState.documentSnapshot
    : readDocumentBusySnapshot()
  const lastActiveSnapshot = runtimeBusyPanelState.lastActiveSnapshot && typeof runtimeBusyPanelState.lastActiveSnapshot === 'object'
    ? runtimeBusyPanelState.lastActiveSnapshot
    : null
  const lastActionActiveSnapshot = runtimeBusyPanelState.lastActionActiveSnapshot && typeof runtimeBusyPanelState.lastActionActiveSnapshot === 'object'
    ? runtimeBusyPanelState.lastActionActiveSnapshot
    : null
  const mirror = busyMirrorStatus(snapshot, documentSnapshot) ? 'consistente' : 'drift'

  document.querySelectorAll('[data-runtime-busy-panel]').forEach((panel) => {
    const kindNode = panel.querySelector('[data-runtime-check="busy-current-kind"] span')
    const phaseNode = panel.querySelector('[data-runtime-check="busy-current-phase"] span')
    const requestNode = panel.querySelector('[data-runtime-check="busy-current-request"] span')
    const mirrorNode = panel.querySelector('[data-runtime-check="busy-current-mirror"] span')
    const actionNode = panel.querySelector('[data-runtime-check="busy-current-action"]')
    const componentNode = panel.querySelector('[data-runtime-check="busy-current-component"]')
    const targetNode = panel.querySelector('[data-runtime-check="busy-current-target"]')
    const documentNode = panel.querySelector('[data-runtime-check="busy-document-kind"]')
    const lastActiveNode = panel.querySelector('[data-runtime-check="busy-last-active-summary"]')
    const lastActionNode = panel.querySelector('[data-runtime-check="busy-last-action-summary"]')
    const lastEventNode = panel.querySelector('[data-runtime-check="busy-last-event"]')
    const updatedNode = panel.querySelector('[data-runtime-check="busy-updated-at"]')
    const detailNode = panel.querySelector('[data-runtime-check="busy-detail"]')

    if (kindNode) {
      kindNode.textContent = `kind = ${snapshot.kind || 'idle'} (${snapshot.active ? 'active' : 'idle'})`
    }

    if (phaseNode) {
      phaseNode.textContent = `phase = ${snapshot.phase || 'idle'}`
    }

    if (requestNode) {
      requestNode.textContent = `requestId = ${snapshot.requestId || 'sin requestId'} | source = ${snapshot.source || 'idle'}`
    }

    if (mirrorNode) {
      mirrorNode.textContent = `mirror = ${mirror}`
    }

    if (actionNode) {
      actionNode.textContent = `action = ${snapshot.action || 'sin action'}`
    }

    if (componentNode) {
      componentNode.textContent = `component = ${snapshot.component || 'sin component'}`
    }

    if (targetNode) {
      targetNode.textContent = `target = ${snapshot.target || 'sin target'}`
    }

    if (documentNode) {
      documentNode.textContent = `doc.kind = ${documentSnapshot.kind || 'idle'} | doc.phase = ${documentSnapshot.phase || 'idle'}`
    }

    if (lastActiveNode) {
      lastActiveNode.textContent = lastActiveSnapshot
        ? `ultimo busy = ${lastActiveSnapshot.kind || 'sin kind'} | phase = ${lastActiveSnapshot.phase || 'sin phase'} | requestId = ${lastActiveSnapshot.requestId || 'sin requestId'} | action = ${lastActiveSnapshot.action || 'sin action'} | component = ${lastActiveSnapshot.component || 'sin component'}`
        : 'ultimo busy = sin snapshot activo persistido'
    }

    if (lastActionNode) {
      lastActionNode.textContent = lastActionActiveSnapshot
        ? `ultima accion busy = ${lastActionActiveSnapshot.kind || 'sin kind'} | phase = ${lastActionActiveSnapshot.phase || 'sin phase'} | requestId = ${lastActionActiveSnapshot.requestId || 'sin requestId'} | action = ${lastActionActiveSnapshot.action || 'sin action'} | component = ${lastActionActiveSnapshot.component || 'sin component'}`
        : 'ultima accion busy = sin snapshot de action persistido'
    }

    if (lastEventNode) {
      lastEventNode.textContent = `lastEvent = ${runtimeBusyPanelState.lastEvent || 'boot'}`
    }

    if (updatedNode) {
      updatedNode.textContent = `actualizado = ${runtimeBusyPanelState.updatedAt || 'sin dato'}`
    }

    if (detailNode) {
      detailNode.textContent = serializeStateExample(runtimeBusyPanelState.detail || {
        waiting: 'Aun no hay snapshots busy capturados.',
      })
    }
  })
}

function setRuntimeBusyPanelState(eventName, detail) {
  const normalizedDetail = normalizeHookValue(detail || {})
  const detailSnapshot = normalizedDetail && typeof normalizedDetail === 'object' && !Array.isArray(normalizedDetail) &&
    (
      Object.prototype.hasOwnProperty.call(normalizedDetail, 'active') ||
      Object.prototype.hasOwnProperty.call(normalizedDetail, 'kind') ||
      Object.prototype.hasOwnProperty.call(normalizedDetail, 'phase') ||
      Object.prototype.hasOwnProperty.call(normalizedDetail, 'source')
    )
    ? normalizedDetail
    : null

  runtimeBusyPanelState.lastEvent = typeof eventName === 'string' && eventName !== '' ? eventName : 'manual'
  runtimeBusyPanelState.updatedAt = new Date().toISOString()
  runtimeBusyPanelState.snapshot = readRuntimeBusySnapshot()
  if (detailSnapshot && detailSnapshot.active === true) {
    runtimeBusyPanelState.lastActiveSnapshot = detailSnapshot
  } else if (runtimeBusyPanelState.snapshot && runtimeBusyPanelState.snapshot.active) {
    runtimeBusyPanelState.lastActiveSnapshot = runtimeBusyPanelState.snapshot
  }
  if (isBusyActionSnapshot(detailSnapshot)) {
    runtimeBusyPanelState.lastActionActiveSnapshot = detailSnapshot
  } else if (isBusyActionSnapshot(runtimeBusyPanelState.snapshot)) {
    runtimeBusyPanelState.lastActionActiveSnapshot = runtimeBusyPanelState.snapshot
  }
  runtimeBusyPanelState.documentSnapshot = readDocumentBusySnapshot()
  runtimeBusyPanelState.detail = {
    eventName: runtimeBusyPanelState.lastEvent,
    runtimeSnapshot: runtimeBusyPanelState.snapshot,
    eventSnapshot: detailSnapshot,
    lastActiveSnapshot: runtimeBusyPanelState.lastActiveSnapshot,
    lastActionActiveSnapshot: runtimeBusyPanelState.lastActionActiveSnapshot,
    documentSnapshot: runtimeBusyPanelState.documentSnapshot,
    detail: normalizedDetail,
  }

  persistRuntimeBusyPanelState()
  syncRuntimeBusyPanel()
}

function clearRuntimeBusyPanelState() {
  runtimeBusyPanelState.lastEvent = 'manual-clear'
  runtimeBusyPanelState.updatedAt = new Date().toISOString()
  runtimeBusyPanelState.snapshot = readRuntimeBusySnapshot()
  runtimeBusyPanelState.lastActiveSnapshot = null
  runtimeBusyPanelState.lastActionActiveSnapshot = null
  runtimeBusyPanelState.documentSnapshot = readDocumentBusySnapshot()
  runtimeBusyPanelState.detail = {
    cleared: true,
    runtimeSnapshot: runtimeBusyPanelState.snapshot,
    lastActiveSnapshot: runtimeBusyPanelState.lastActiveSnapshot,
    lastActionActiveSnapshot: runtimeBusyPanelState.lastActionActiveSnapshot,
    documentSnapshot: runtimeBusyPanelState.documentSnapshot,
  }

  persistRuntimeBusyPanelState()
  syncRuntimeBusyPanel()
}

function scheduleRuntimeBusyPanelSync(reason = 'manual', detail = {}) {
  runtimeBusyPanelState.pendingReason = reason
  runtimeBusyPanelState.pendingDetail = detail

  if (runtimeBusyPanelState.scheduled) {
    return
  }

  runtimeBusyPanelState.scheduled = true

  window.requestAnimationFrame(() => {
    runtimeBusyPanelState.scheduled = false
    const nextReason = runtimeBusyPanelState.pendingReason
    const nextDetail = runtimeBusyPanelState.pendingDetail
    runtimeBusyPanelState.pendingReason = 'manual'
    runtimeBusyPanelState.pendingDetail = {}
    setRuntimeBusyPanelState(nextReason, nextDetail)
  })
}

function applyPersistedRuntimeBusyPanelState() {
  const persisted = readPersistedRuntimeBusyPanelState()

  if (!persisted) {
    scheduleRuntimeBusyPanelSync('boot')
    return
  }

  runtimeBusyPanelState.lastEvent = typeof persisted.lastEvent === 'string' ? persisted.lastEvent : 'boot'
  runtimeBusyPanelState.updatedAt = typeof persisted.updatedAt === 'string' ? persisted.updatedAt : null
  runtimeBusyPanelState.snapshot = persisted.snapshot && typeof persisted.snapshot === 'object'
    ? persisted.snapshot
    : readRuntimeBusySnapshot()
  runtimeBusyPanelState.lastActiveSnapshot = persisted.lastActiveSnapshot && typeof persisted.lastActiveSnapshot === 'object'
    ? persisted.lastActiveSnapshot
    : null
  runtimeBusyPanelState.lastActionActiveSnapshot = persisted.lastActionActiveSnapshot && typeof persisted.lastActionActiveSnapshot === 'object'
    ? persisted.lastActionActiveSnapshot
    : null
  runtimeBusyPanelState.documentSnapshot = persisted.documentSnapshot && typeof persisted.documentSnapshot === 'object'
    ? persisted.documentSnapshot
    : readDocumentBusySnapshot()
  runtimeBusyPanelState.detail = persisted.detail && typeof persisted.detail === 'object'
    ? persisted.detail
    : {
      waiting: 'Aun no hay snapshots busy capturados.',
    }

  syncRuntimeBusyPanel()
}

function registerRuntimeBusyPanel() {
  window.__spaLabRuntimeBusyPanel = window.__spaLabRuntimeBusyPanel || {}
  window.__spaLabRuntimeBusyPanel.render = function(reason = 'manual-render') {
    scheduleRuntimeBusyPanelSync(reason)
  }
  window.__spaLabRuntimeBusyPanel.clear = clearRuntimeBusyPanelState

  ;['volt:busy-start', 'volt:busy-change', 'volt:busy-end'].forEach((eventName) => {
    document.addEventListener(eventName, (event) => {
      scheduleRuntimeBusyPanelSync(eventName, event.detail || {})
    })
  })

  ;['volt:request-start', 'volt:request-finish', 'volt:navigated'].forEach((eventName) => {
    document.addEventListener(eventName, (event) => {
      scheduleRuntimeBusyPanelSync(eventName, event.detail || {})
    })
  })

  window.addEventListener('load', () => {
    scheduleRuntimeBusyPanelSync('window-load')
  })

  applyPersistedRuntimeBusyPanelState()
}
function runtimeStateApi() {
  return window.Volt && window.Volt.state ? window.Volt.state : null
}

function nextRuntimeListItem(scope, currentItems) {
  const items = Array.isArray(currentItems) ? currentItems : []
  const position = items.length + 1

  return {
    title: `${scope} item ${position}`,
    detail: `Creado en ${window.location.pathname}`,
    badge: scope === 'shared' ? 'shared' : 'client',
  }
}

function serializeStateExample(value) {
  try {
    return JSON.stringify(normalizeHookValue(value), null, 2)
  } catch (error) {
    return '{"error":"No se pudo serializar el estado runtime."}'
  }
}

function runtimeStateInput(root, name) {
  return root ? root.querySelector(`[data-volt-state-input="${name}"]`) : null
}

function setRuntimeStateInputValue(root, name, value) {
  const input = runtimeStateInput(root, name)

  if (input) {
    input.value = typeof value === 'string' ? value : ''
  }
}

function setRuntimePresetStatus(root, message) {
  const status = root ? root.querySelector('[data-runtime-preset-status]') : null

  if (status) {
    status.textContent = message
  }
}

function mergeRuntimeScopedValue(api, scope, key, patch) {
  api.update(key, (value) => Object.assign({}, value || {}, patch), {
    scope,
  })
}

function setRuntimeAdvancedPreset(api, root, preset) {
  api.clear({
    scope: 'client',
    reason: `advanced-directives-${preset}`,
  })
  api.clear({
    scope: 'shared',
    reason: `advanced-directives-${preset}`,
  })

  switch (preset) {
    case 'text-shared-fallback':
      api.merge('draft', {
        note: 'Nota shared visible',
        page: window.location.pathname,
      }, {
        scope: 'shared',
      })
      setRuntimeStateInputValue(root, 'client-note', '')
      setRuntimeStateInputValue(root, 'shared-note', 'Nota shared visible')
      setRuntimePresetStatus(
        root,
        'Preset activo: fallback shared. `volt:text` debe mostrar la nota shared porque client quedo vacio.',
      )
      return

    case 'text-client-priority':
      api.merge('draft', {
        note: 'Nota client prioritaria',
        page: window.location.pathname,
      }, {
        scope: 'client',
      })
      api.merge('draft', {
        note: 'Nota shared secundaria',
        page: window.location.pathname,
      }, {
        scope: 'shared',
      })
      setRuntimeStateInputValue(root, 'client-note', 'Nota client prioritaria')
      setRuntimeStateInputValue(root, 'shared-note', 'Nota shared secundaria')
      setRuntimePresetStatus(
        root,
        'Preset activo: prioridad client. `volt:text` debe resolver primero el valor client.',
      )
      return

    case 'compound-true':
      mergeRuntimeScopedValue(api, 'client', 'ui', {
        showClientPanel: true,
        mountClientPanel: true,
      })
      mergeRuntimeScopedValue(api, 'shared', 'ui', {
        showSharedPanel: false,
        mountSharedPanel: false,
      })
      setRuntimePresetStatus(
        root,
        'Preset activo: condicion true. `volt:show` y `volt:if` compuestos deben quedar activos en su rama positiva.',
      )
      return

    case 'compound-false':
      mergeRuntimeScopedValue(api, 'client', 'ui', {
        showClientPanel: true,
        mountClientPanel: false,
      })
      mergeRuntimeScopedValue(api, 'shared', 'ui', {
        showSharedPanel: true,
        mountSharedPanel: false,
      })
      setRuntimePresetStatus(
        root,
        'Preset activo: condicion false. Las ramas compuestas principales deben quedar inactivas o desmontadas.',
      )
      return

    case 'relational-threshold-hit':
      api.set('counter', 2, {
        scope: 'client',
      })
      api.set('counter', 1, {
        scope: 'shared',
      })
      setRuntimePresetStatus(
        root,
        'Preset activo: umbral relacional. `client:counter >= 2 && shared:counter < 3` y `client:counter >= shared:counter` deben evaluar a true.',
      )
      return

    case 'null-vs-undefined':
      api.set('edge', {
        nullValue: null,
        emptyString: '',
        zeroValue: 0,
        falseValue: false,
      }, {
        scope: 'client',
      })
      api.set('edge', {
        nullValue: null,
        emptyString: '',
        zeroValue: 0,
        falseValue: false,
      }, {
        scope: 'shared',
      })
      api.delete('edge.undefinedValue', {
        scope: 'client',
      })
      api.delete('edge.undefinedValue', {
        scope: 'shared',
      })
      setRuntimePresetStatus(
        root,
        'Preset activo: null vs undefined. Las comparaciones flexibles deben coincidir y las estrictas deben diferir.',
      )
      return

    case 'multi-rule-client':
      mergeRuntimeScopedValue(api, 'client', 'ui', {
        highlightClientCard: true,
        lockClientAction: true,
        softenClientCard: true,
      })
      mergeRuntimeScopedValue(api, 'shared', 'ui', {
        highlightSharedCard: false,
        lockSharedAction: false,
        softenSharedCard: false,
      })
      setRuntimePresetStatus(
        root,
        'Preset activo: reglas client. Deben activarse las ramas client de class, attr y style sin interferencia shared.',
      )
      return

    case 'multi-rule-shared':
      mergeRuntimeScopedValue(api, 'client', 'ui', {
        highlightClientCard: true,
        lockClientAction: true,
        softenClientCard: true,
      })
      mergeRuntimeScopedValue(api, 'shared', 'ui', {
        highlightSharedCard: true,
        lockSharedAction: true,
        softenSharedCard: true,
      })
      setRuntimePresetStatus(
        root,
        'Preset activo: reglas shared. La rama shared debe tomar el control visual y atributivo sobre los targets marcados.',
      )
      return

    default:
      setRuntimeStateInputValue(root, 'client-note', '')
      setRuntimeStateInputValue(root, 'shared-note', '')
      setRuntimePresetStatus(
        root,
        'Preset activo: ninguno. Se limpiaron los scopes client y shared para volver al baseline.',
      )
  }
}

function syncRuntimeStateExamples() {
  const api = runtimeStateApi()

  document.querySelectorAll('[data-volt-state-example]').forEach((panel) => {
    const runtimeRoot = panel.closest('[data-volt-root]')
    const clientScopeNode = panel.querySelector('[data-volt-state-client-scope]')
    const clientNode = panel.querySelector('[data-volt-state-client-snapshot]')
    const sharedNode = panel.querySelector('[data-volt-state-shared-snapshot]')
    const eventNode = panel.querySelector('[data-volt-state-last-event]')
    const requestStatusNode = panel.querySelector('[data-runtime-check="state-status-request-status"]')
    const dirtyTargetNode = panel.querySelector('[data-runtime-check="state-status-dirty-target"]')
    const successActionNode = panel.querySelector('[data-runtime-check="state-status-success-action"]')
    const successTargetNode = panel.querySelector('[data-runtime-check="state-status-success-target"]')
    const errorActionNode = panel.querySelector('[data-runtime-check="state-status-error-action"]')
    const errorTargetNode = panel.querySelector('[data-runtime-check="state-status-error-target"]')
    const errorMessageNode = panel.querySelector('[data-runtime-check="state-status-error-message"]')
    const requestEventNode = panel.querySelector('[data-runtime-check="state-status-last-request-event"]')

    if (!api) {
      if (clientScopeNode) {
        clientScopeNode.textContent = 'runtime.state no disponible'
      }

      if (clientNode) {
        clientNode.textContent = '{"error":"runtime.state no disponible"}'
      }

      if (sharedNode) {
        sharedNode.textContent = '{"error":"runtime.state no disponible"}'
      }

      if (requestStatusNode) {
        requestStatusNode.textContent = 'request-status = runtime no disponible'
      }

      if (dirtyTargetNode) {
        dirtyTargetNode.textContent = 'dirty.target = runtime no disponible'
      }

      if (successActionNode) {
        successActionNode.textContent = 'success.action = runtime no disponible'
      }

      if (successTargetNode) {
        successTargetNode.textContent = 'success.target = runtime no disponible'
      }

      if (errorActionNode) {
        errorActionNode.textContent = 'error.action = runtime no disponible'
      }

      if (errorTargetNode) {
        errorTargetNode.textContent = 'error.target = runtime no disponible'
      }

      if (errorMessageNode) {
        errorMessageNode.textContent = 'error.message = runtime no disponible'
      }

      if (requestEventNode) {
        requestEventNode.textContent = '{"error":"runtime request state no disponible"}'
      }

      return
    }

    if (clientScopeNode) {
      clientScopeNode.textContent = api.currentScope()
    }

    if (clientNode) {
      clientNode.textContent = serializeStateExample(api.snapshot({
        scope: 'client',
      }))
    }

    if (sharedNode) {
      sharedNode.textContent = serializeStateExample(api.snapshot({
        scope: 'shared',
      }))
    }

    if (eventNode) {
      eventNode.textContent = serializeStateExample(runtimeStateExampleState.lastEvent || {
        waiting: 'Aun no hay eventos de state runtime.',
      })
    }

    if (requestStatusNode) {
      requestStatusNode.textContent = `request-status = ${runtimeRoot ? (runtimeRoot.getAttribute('data-volt-request-status') || 'idle') : 'sin root'}`
    }

    if (dirtyTargetNode) {
      dirtyTargetNode.textContent = `dirty.target = ${runtimeRoot ? (runtimeRoot.getAttribute('data-volt-dirty-target') || 'sin target') : 'sin root'}`
    }

    if (successActionNode) {
      successActionNode.textContent = `success.action = ${runtimeRoot ? (runtimeRoot.getAttribute('data-volt-success-action') || 'sin action') : 'sin root'}`
    }

    if (successTargetNode) {
      successTargetNode.textContent = `success.target = ${runtimeRoot ? (runtimeRoot.getAttribute('data-volt-success-target') || 'sin target') : 'sin root'}`
    }

    if (errorActionNode) {
      errorActionNode.textContent = `error.action = ${runtimeRoot ? (runtimeRoot.getAttribute('data-volt-error-action') || 'sin action') : 'sin root'}`
    }

    if (errorTargetNode) {
      errorTargetNode.textContent = `error.target = ${runtimeRoot ? (runtimeRoot.getAttribute('data-volt-error-target') || 'sin target') : 'sin root'}`
    }

    if (errorMessageNode) {
      errorMessageNode.textContent = `error.message = ${runtimeRoot ? (runtimeRoot.getAttribute('data-volt-error-message') || 'sin error') : 'sin root'}`
    }

    if (requestEventNode) {
      requestEventNode.textContent = serializeStateExample(runtimeStateExampleState.lastRequestEvent || {
        waiting: 'Aun no hay hooks de request state.',
      })
    }
  })
}

function setRuntimeStateExampleEvent(eventName, detail) {
  runtimeStateExampleState.lastEvent = {
    event: eventName,
    detail: normalizeHookValue(detail || {}),
  }

  syncRuntimeStateExamples()
}

function setRuntimeStateRequestExampleEvent(eventName, detail) {
  runtimeStateExampleState.lastRequestEvent = {
    event: eventName,
    detail: normalizeHookValue(detail || {}),
  }

  syncRuntimeStateExamples()
}

function updateHookCard(eventName, detail, count, date) {
  document.querySelectorAll(`[data-volt-hook-card="${eventName}"]`).forEach((card) => {
    const countNode = card.querySelector('[data-volt-hook-count]')
    const lastNode = card.querySelector('[data-volt-hook-last]')
    const detailNode = card.querySelector('[data-volt-hook-detail]')
    const sourceNode = card.querySelector('[data-volt-hook-source]')
    const source = hookSourceFromDetail(detail)

    if (countNode) {
      countNode.textContent = String(count)
    }

    if (lastNode) {
      lastNode.textContent = formatHookTime(date)
    }

    if (detailNode) {
      detailNode.textContent = serializeHookDetail(detail)
    }

    if (sourceNode) {
      sourceNode.textContent = source || 'sin source'
      sourceNode.className = `inline-flex items-center rounded-full border px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] ${hookSourceBadgeClass(source)}`
    }
  })
}

function buildHookLogMarkup(eventName, detail, date) {
  const source = hookSourceFromDetail(detail)
  const sourceBadge = source
    ? `<span class="inline-flex items-center rounded-full border px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] ${hookSourceBadgeClass(source)}">${escapeHtml(source)}</span>`
    : ''

  return `
      <div class="flex gap-3 justify-between items-center">
        <div class="flex gap-2 items-center">
          <strong class="text-cyan-300">${escapeHtml(eventName)}</strong>
          ${sourceBadge}
        </div>
        <span class="text-slate-500">${formatHookTime(date)}</span>
      </div>
      <pre class="mt-2 overflow-x-auto whitespace-pre-wrap text-[11px] text-slate-400">${escapeHtml(serializeHookDetail(detail))}</pre>
    `
}

function appendHookLog(eventName, detail, date) {
  hookState.logs.unshift({
    eventName,
    detail,
    date,
  })

  while (hookState.logs.length > 20) {
    hookState.logs.pop()
  }
}

function renderHookLogs() {
  document.querySelectorAll('[data-volt-hook-log]').forEach((log) => {
    const filter = log.getAttribute('data-volt-hook-log-filter') || 'all'
    const entries = hookState.logs.filter(({ eventName }) => {
      if (filter === 'cache-only') {
        return isCacheRuntimeEvent(eventName)
      }

      if (filter === 'fragment-only') {
        return isFragmentRuntimeEvent(eventName)
      }

      return true
    }).slice(0, 12)

    log.innerHTML = ''

    entries.forEach(({ eventName, detail, date }) => {
      const item = document.createElement('li')
      item.className = 'p-3 text-xs rounded-lg border border-slate-800 bg-slate-950/70 text-slate-300'
      item.innerHTML = buildHookLogMarkup(eventName, detail, date)
      log.appendChild(item)
    })
  })
}

function syncHookInspector() {
  hookState.latest.forEach(({ detail, count, date }, eventName) => {
    updateHookCard(eventName, detail, count, date)
  })

  renderHookLogs()
}

function registerVoltHookExamples() {
  ;[...HOOK_EVENTS, ...DEMO_EVENTS].forEach((eventName) => {
    hookState.counters.set(eventName, 0)

    document.addEventListener(eventName, (event) => {
      const nextCount = (hookState.counters.get(eventName) || 0) + 1
      const now = new Date()
      const detail = event.detail || {}

      hookState.counters.set(eventName, nextCount)
      hookState.latest.set(eventName, {
        detail,
        count: nextCount,
        date: now,
      })
      appendHookLog(eventName, detail, now)
      syncHookInspector()
    })
  })

  document.addEventListener('volt:navigated', () => {
    window.requestAnimationFrame(() => {
      syncHookInspector()
    })
  })

  document.addEventListener('volt:navigated', (event) => {
    const detail = event.detail || {}

    updateNavigationArrival({
      kind: 'spa',
      summary: 'Llegada por navegacion SPA',
      detail: {
        source: 'runtime',
        trigger: 'volt:navigated',
        finalUrl: detail.finalUrl || window.location.href,
        navigationMode: detail.navigationMode || null,
        historyMode: detail.historyMode || null,
        pageTransition: detail.pageTransition || null,
        pageTransitionSource: detail.pageTransitionSource || null,
        pageTransitionMode: detail.pageTransitionMode || null,
        pageTransitionDuration: detail.pageTransitionDuration ?? null,
        pageTransitionProfile: detail.pageTransitionProfile || null,
      },
    })
  })

  document.addEventListener('volt:before-enter', (event) => {
    const detail = event.detail || {}

    if (detail.type !== 'navigation-transition' || detail.target !== 'body') {
      return
    }

    updateNavigationArrival({
      kind: 'spa',
      summary: 'Llegada SPA con transicion de entrada activa',
      detail: Object.assign({
        source: 'runtime',
        trigger: 'volt:before-enter',
        finalUrl: window.location.href,
      }, detail),
    })
  })

  updateNavigationArrival(initialNavigationArrivalState())
  syncHookInspector()
}

function registerCacheExampleControls() {
  document.addEventListener('click', (event) => {
    const trigger = closestFromEventTarget(event, '[data-volt-cache-invalidate-url], [data-volt-cache-invalidate-all]')

    if (!trigger) {
      return
    }

    const statusNodes = document.querySelectorAll('[data-volt-cache-action-status]')
    const url = trigger.getAttribute('data-volt-cache-invalidate-url')
    const clearAll = trigger.getAttribute('data-volt-cache-invalidate-all') === 'true'
    const detail = clearAll
      ? { reason: 'demo-button', source: 'cache-example' }
      : { url, reason: 'demo-button', source: 'cache-example' }

    document.dispatchEvent(new CustomEvent('volt:navigation-cache-invalidate', {
      detail,
    }))

    const message = clearAll
      ? 'Se solicito la limpieza completa de la cache SPA.'
      : `Se solicito invalidar ${url}.`

    statusNodes.forEach((node) => {
      node.textContent = message
    })
  })
}

function registerRuntimeStateExampleControls() {
  document.addEventListener('click', (event) => {
    const trigger = closestFromEventTarget(event, '[data-volt-state-action]')

    if (!trigger) {
      return
    }

    const api = runtimeStateApi()

    if (!api) {
      return
    }

    const action = trigger.getAttribute('data-volt-state-action') || ''
    const root = closestFromEventTarget(event, '[data-volt-state-example]')

    event.preventDefault()

    switch (action) {
      case 'increment-client-counter':
        api.update('counter', (value) => typeof value === 'number' ? value + 1 : 1, {
          scope: 'client',
        })
        break

      case 'increment-shared-counter':
        api.update('counter', (value) => typeof value === 'number' ? value + 1 : 1, {
          scope: 'shared',
        })
        break

      case 'save-client-note': {
        const input = root ? root.querySelector('[data-volt-state-input="client-note"]') : null
        api.merge('draft', {
          note: input ? input.value : '',
          page: window.location.pathname,
        }, {
          scope: 'client',
        })
        break
      }

      case 'save-shared-note': {
        const input = root ? root.querySelector('[data-volt-state-input="shared-note"]') : null
        api.merge('draft', {
          note: input ? input.value : '',
          page: window.location.pathname,
        }, {
          scope: 'shared',
        })
        break
      }

      case 'clear-client':
        api.clear({
          scope: 'client',
          reason: 'demo-button',
        })
        break

      case 'clear-shared':
        api.clear({
          scope: 'shared',
          reason: 'demo-button',
        })
        break

      case 'toggle-client-visibility':
        api.update('ui', (value) => Object.assign({}, value || {}, {
          showClientPanel: !(value && value.showClientPanel),
        }), {
          scope: 'client',
        })
        break

      case 'toggle-shared-visibility':
        api.update('ui', (value) => Object.assign({}, value || {}, {
          showSharedPanel: !(value && value.showSharedPanel),
        }), {
          scope: 'shared',
        })
        break

      case 'toggle-client-if':
        api.update('ui', (value) => Object.assign({}, value || {}, {
          mountClientPanel: !(value && value.mountClientPanel),
        }), {
          scope: 'client',
        })
        break

      case 'toggle-shared-if':
        api.update('ui', (value) => Object.assign({}, value || {}, {
          mountSharedPanel: !(value && value.mountSharedPanel),
        }), {
          scope: 'shared',
        })
        break

      case 'toggle-client-class':
        api.update('ui', (value) => Object.assign({}, value || {}, {
          highlightClientCard: !(value && value.highlightClientCard),
        }), {
          scope: 'client',
        })
        break

      case 'toggle-shared-class':
        api.update('ui', (value) => Object.assign({}, value || {}, {
          highlightSharedCard: !(value && value.highlightSharedCard),
        }), {
          scope: 'shared',
        })
        break

      case 'toggle-client-attr':
        api.update('ui', (value) => Object.assign({}, value || {}, {
          lockClientAction: !(value && value.lockClientAction),
        }), {
          scope: 'client',
        })
        break

      case 'toggle-shared-attr':
        api.update('ui', (value) => Object.assign({}, value || {}, {
          lockSharedAction: !(value && value.lockSharedAction),
        }), {
          scope: 'shared',
        })
        break

      case 'toggle-client-style':
        api.update('ui', (value) => Object.assign({}, value || {}, {
          softenClientCard: !(value && value.softenClientCard),
        }), {
          scope: 'client',
        })
        break

      case 'toggle-shared-style':
        api.update('ui', (value) => Object.assign({}, value || {}, {
          softenSharedCard: !(value && value.softenSharedCard),
        }), {
          scope: 'shared',
        })
        break

      case 'add-client-for-item':
        api.update('list', (value) => {
          const next = Object.assign({}, value || {})
          const items = Array.isArray(next.items) ? next.items.slice() : []
          items.push(nextRuntimeListItem('client', items))
          next.items = items
          return next
        }, {
          scope: 'client',
        })
        break

      case 'remove-client-for-item':
        api.update('list', (value) => {
          const next = Object.assign({}, value || {})
          const items = Array.isArray(next.items) ? next.items.slice(0, -1) : []
          next.items = items
          return next
        }, {
          scope: 'client',
        })
        break

      case 'add-shared-for-item':
        api.update('list', (value) => {
          const next = Object.assign({}, value || {})
          const items = Array.isArray(next.items) ? next.items.slice() : []
          items.push(nextRuntimeListItem('shared', items))
          next.items = items
          return next
        }, {
          scope: 'shared',
        })
        break

      case 'remove-shared-for-item':
        api.update('list', (value) => {
          const next = Object.assign({}, value || {})
          const items = Array.isArray(next.items) ? next.items.slice(0, -1) : []
          next.items = items
          return next
        }, {
          scope: 'shared',
        })
        break

      case 'preset-text-shared-fallback':
        setRuntimeAdvancedPreset(api, root, 'text-shared-fallback')
        break

      case 'preset-text-client-priority':
        setRuntimeAdvancedPreset(api, root, 'text-client-priority')
        break

      case 'preset-compound-true':
        setRuntimeAdvancedPreset(api, root, 'compound-true')
        break

      case 'preset-compound-false':
        setRuntimeAdvancedPreset(api, root, 'compound-false')
        break

      case 'preset-relational-threshold-hit':
        setRuntimeAdvancedPreset(api, root, 'relational-threshold-hit')
        break

      case 'preset-null-vs-undefined':
        setRuntimeAdvancedPreset(api, root, 'null-vs-undefined')
        break

      case 'preset-multi-rule-client':
        setRuntimeAdvancedPreset(api, root, 'multi-rule-client')
        break

      case 'preset-multi-rule-shared':
        setRuntimeAdvancedPreset(api, root, 'multi-rule-shared')
        break

      case 'reset-runtime-advanced-demo':
        setRuntimeAdvancedPreset(api, root, 'reset')
        break
    }

    syncRuntimeStateExamples()
  })

  document.addEventListener('volt:state-changed', (event) => {
    setRuntimeStateExampleEvent('volt:state-changed', event.detail || {})
  })

  document.addEventListener('volt:state-cleared', (event) => {
    setRuntimeStateExampleEvent('volt:state-cleared', event.detail || {})
  })

  document.addEventListener('volt:state-scope-changed', (event) => {
    setRuntimeStateExampleEvent('volt:state-scope-changed', event.detail || {})
  })

  ;[
    'volt:dirty',
    'volt:clean',
    'volt:success',
    'volt:success-cleared',
    'volt:request-error',
    'volt:error-cleared',
  ].forEach((eventName) => {
    document.addEventListener(eventName, (event) => {
      setRuntimeStateRequestExampleEvent(eventName, event.detail || {})
    })
  })

  document.addEventListener('volt:navigated', () => {
    window.requestAnimationFrame(() => {
      syncRuntimeStateExamples()
    })
  })

  syncRuntimeStateExamples()
}

function bootstrapRequestLabPage() {
  const requestLabMarker = document.querySelector('[data-runtime-check="action-endpoint-status"]')

  if (!requestLabMarker) {
    return
  }

  if (window.__spaLabRequestLab && typeof window.__spaLabRequestLab.syncVisibleState === 'function') {
    window.__spaLabRequestLab.syncVisibleState()
    return
  }

  const inlineBootstrap = document.querySelector('script[data-runtime-request-lab-bootstrap="true"]')

  if (!inlineBootstrap) {
    return
  }

  try {
    const runtimeBootstrap = document.createElement('script')
    runtimeBootstrap.type = 'text/javascript'
    runtimeBootstrap.setAttribute('data-runtime-request-lab-executed', 'true')
    runtimeBootstrap.textContent = inlineBootstrap.textContent
    document.body.appendChild(runtimeBootstrap)
    runtimeBootstrap.remove()
  } catch (error) {
    console.error('RequestLab SPA bootstrap failed from SpaLab.js.', error)
  }
}

registerVoltHookExamples()
registerNavigationDebugPanel()
registerCacheExampleControls()
registerRuntimeStateExampleControls()
registerRuntimeBusyPanel()
registerRuntimeEfficiencyExamples()
document.addEventListener('DOMContentLoaded', () => {
  window.requestAnimationFrame(() => {
    bootstrapRequestLabPage()
  })
})
document.addEventListener('volt:navigated', () => {
  window.requestAnimationFrame(() => {
    bootstrapRequestLabPage()
  })
})
bootstrapRequestLabPage()

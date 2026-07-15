# Contrato Vigente - Volt Runtime JS (SPA Lab)

Objetivo: concentrar el contrato operativo vigente del runtime (SPA documental + reactividad), con enlaces a la documentacion fuente y a las validaciones recomendadas.

Este archivo no reemplaza la bitacora. Para el historial y el roadmap detallado, ver [1-Versions.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/1-Versions.md).

## 1. Contratos Publicos Estables

### 1.1 Navegacion SPA Documental

Fuente principal: [4-Navigation-Contract.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/4-Navigation-Contract.md)

Contrato:

- un documento SPA-capable declara `data-volt-document="spa"` y expone un modo resoluble `auto|spa|reload`
- prioridad de resolucion: `enlace -> documento -> auto`
- fallback seguro en modo `auto` por razones como `layout-mismatch` y `document-reload-only`
- paginas de error se tratan como `reload-only`

### 1.2 Guia De Integracion Cliente (Patrones)

Fuente principal: [5-Client-Integration-Guide.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/5-Client-Integration-Guide.md)

Patrones documentados:

- `Controller + View` (SSR tradicional) con enlaces `volt:navigate`
- `Component` como pagina reactiva completa
- `Controller + View + islas` (componentes interactivos embebidos)
- `reload-only` por meta o por atributo documental

### 1.3 Cache SPA (Documentos HTML)

Fuente principal: [2-Versions_Cache.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/2-Versions_Cache.md)

Contrato actual:

- cache de navegacion SPA (HTML) en memoria, por pestaña
- reuse de requests en vuelo
- expiracion por TTL
- control declarativo por enlace via `volt:cache` (`no-store`, `reload`, `invalidate`, `ttl=...`, `max-age=...`)
- control por documento destino via meta tags
- observabilidad por eventos `volt:cache-hit`, `volt:cache-miss`, `volt:cache-store`, `volt:cache-invalidate` y `volt:cache-clear`
- durante `prefetch`, el runtime puede inyectar hints de `head` como:
  - `<link rel="preload" as="style" ... data-volt-prefetch-preload="...">`
  - `<link rel="modulepreload" ... data-volt-prefetch-preload="...">`

Fuera de alcance (por ahora):

- cache formal de datos desacoplado del HTML
- persistencia entre pestañas o entre recargas completas

### 1.4 Preserve Opt-In De Fragmentos SPA

Fuente principal: [1-Versions.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/1-Versions.md)

Contrato actual:

- `volt:preserve` reutiliza fragmentos top-level marcados por clave entre pantallas SPA compatibles
- acepta `data-volt-preserve`, `volt-preserve` y `volt:preserve`
- el destino debe volver a exponer la misma clave para reutilizar el nodo vivo
- la politica documental `reset` descarta los fragmentos preservados aunque la clave coincida
- emite observabilidad por `volt:fragment-preserve` y `volt:fragment-discard`

Validacion recomendada:

- [13-Volt-Preserve-Manual-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/13-Volt-Preserve-Manual-Validation.md)
- [7-Fragment-Cache-Prefetch-Manual-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/7-Fragment-Cache-Prefetch-Manual-Validation.md)

Estado de validacion:

- pasada browser del flujo `/fragmentCache -> /formExample -> /fragmentCacheReset -> /fragmentCache` ejecutada en build local, con reuse correcto del estado vivo en la ruta compatible y descarte observable por `reset`
- despues del descarte, el HTML fresco del documento `reset` pasa a ser la nueva base reutilizable si la siguiente ruta vuelve a exponer la misma clave

### 1.5 Persistencia Opt-In De Fragmentos Vivos

Fuente principal: [1-Versions.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/1-Versions.md)

Contrato actual:

- `volt:persist` reutiliza una instancia fisica del DOM por clave estable entre navegaciones SPA compatibles
- acepta `data-volt-persist`, `volt-persist` y `volt:persist`
- puede sobrevivir a una pantalla intermedia sin target compatible y reinyectarse cuando la clave reaparece
- evita duplicar instancias activas por clave y exige coincidencia de `key + tagName` en el MVP actual
- la politica documental `reset` puede descartar el registro persistido actual

Validacion recomendada:

- [12-Volt-Persist-Manual-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/12-Volt-Persist-Manual-Validation.md)

Estado de validacion:

- pasada browser del flujo `/runtimePersist -> /runtimePersistBridge -> /runtimePersistAlt -> /runtimePersist` ejecutada en build local, con `registry` estable, reinyeccion correcta y sin duplicados observados

### 1.6 Directivas Avanzadas (Validacion Manual)

Fuente: [6-Runtime-Advanced-Directives-Manual-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/6-Runtime-Advanced-Directives-Manual-Validation.md)

Alcance:

- `volt:text`, `volt:show`, `volt:if`
- `volt:class`, `volt:attr`, `volt:style`
- bordes `null` vs `undefined` y expresiones compuestas

## 2. Validacion Recomendada

### 2.1 QA Rapido End-to-End

Gate recomendado antes de nuevas features del runtime:

- [10-Manual_Runtime_QA.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/10-Manual_Runtime_QA.md)

### 2.2 Politicas de navegacion

- [8-Navigation-Policy-Manual-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/8-Navigation-Policy-Manual-Validation.md)

### 2.3 Fragment preserve/cache + Prefetch

- [7-Fragment-Cache-Prefetch-Manual-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/7-Fragment-Cache-Prefetch-Manual-Validation.md)

Estado de validacion:

- pasada browser cerrada en build local para `fragment cache SPA`, `prefetch`, `preload` y `modulepreload`

### 2.4 Preserve opt-in (`volt:preserve`)

- [13-Volt-Preserve-Manual-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/13-Volt-Preserve-Manual-Validation.md)

### 2.5 Persistencia opt-in (`volt:persist`)

- [12-Volt-Persist-Manual-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/12-Volt-Persist-Manual-Validation.md)

### 2.6 Eficiencia y telemetria

- [9-Runtime-Efficiency-Browser-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/9-Runtime-Efficiency-Browser-Validation.md)

## 3. Roadmap / Deuda Principal (donde vivir la verdad)

- Checklist completo y bitacora: [1-Versions.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/1-Versions.md)
- Cierre de migracion Full SPA: [3-Full-SPA-Reactive.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/3-Full-SPA-Reactive.md)
- Mapa ejecutivo del directorio: [00-Matriz-Ejecutiva.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/00-Matriz-Ejecutiva.md)

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

Fuera de alcance (por ahora):

- cache formal de datos desacoplado del HTML
- persistencia entre pestañas o entre recargas completas

### 1.4 Directivas Avanzadas (Validacion Manual)

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

### 2.4 Eficiencia y telemetria

- [9-Runtime-Efficiency-Browser-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/9-Runtime-Efficiency-Browser-Validation.md)

## 3. Roadmap / Deuda Principal (donde vivir la verdad)

- Checklist completo y bitacora: [1-Versions.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/1-Versions.md)
- Cierre de migracion Full SPA: [3-Full-SPA-Reactive.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/3-Full-SPA-Reactive.md)
- Mapa ejecutivo del directorio: [00-Matriz-Ejecutiva.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/00-Matriz-Ejecutiva.md)


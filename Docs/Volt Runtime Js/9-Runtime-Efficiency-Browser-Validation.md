# Runtime Efficiency - Browser Validation

## Objetivo

Validar en navegador real la pasada inicial de eficiencia del runtime usando:

- `window.Volt.telemetry`
- `window.Volt.components`
- `performance.getEntriesByType('navigation')`
- `performance.getEntriesByType('resource')`
- el laboratorio `/runtimeEvents`

## Preparacion

1. levantar la aplicacion cliente con assets frontend actualizados
2. abrir `/runtimeEvents`
3. abrir DevTools en `Network` y `Performance`
4. activar `Preserve log` en `Network`
5. confirmar que el panel `data-runtime-efficiency-demo` aparece visible

## Pantalla Relevante

- `/runtimeEvents`
  - laboratorio de hooks y eficiencia
  - markers estables:
    - `data-runtime-events-demo`
    - `data-runtime-efficiency-demo`
    - `data-runtime-check="efficiency-navigation-performance"`
    - `data-runtime-check="efficiency-runtime-asset"`
    - `data-runtime-check="efficiency-runtime-overview"`
    - `data-runtime-check="efficiency-summary-json"`
    - `data-runtime-check="efficiency-components-detail"`

## Checklist Manual

### 1. Baseline Inicial Del Documento

Accion:

- abrir `/runtimeEvents` por carga documental normal
- pulsar `Refrescar metricas`

Esperado:

- el bloque `efficiency-navigation-performance` muestra valores reales de `type`, `duration`, `domInteractive`, `DCL end` y `load end`
- `type` normalmente arranca como `navigate` o `reload`
- el bloque `efficiency-runtime-asset` muestra el recurso `/_volt/runtime.js`
- `efficiency-runtime-overview` muestra conteos de telemetry y roots activos

### 2. Confirmar Runtime Externo Y Cacheable

Accion:

- revisar `Network`
- recargar la pagina una vez

Esperado:

- aparece una request hacia `/_volt/runtime.js`
- el runtime ya no viaja inline dentro del HTML principal
- la respuesta del runtime expone headers cacheables (`Cache-Control`, `ETag`, `Last-Modified`)

### 3. Generar Telemetria De Accion Y Patch

Accion:

- en la misma pantalla, interactuar con el bloque de `volt:on`:
  - escribir en el input de draft
  - alternar el panel con `click`
  - disparar `click.once`
  - usar el input con `keydown.enter.prevent`

Esperado:

- el card `Telemetry patch` incrementa su `count`
- `Latest patch entry` deja de estar vacio
- `efficiency-summary-json` refleja nuevos datos en `telemetrySummary.patch`
- `Active components summary` sigue mostrando roots coherentes y no crece sin motivo

### 4. Generar Telemetria De Navegacion SPA

Accion:

- desde `/runtimeEvents`, navegar por SPA a:
  - `/runtimeModelSync`
  - `/runtimeState`
- volver a `/runtimeEvents` por SPA

Esperado:

- el card `Telemetry navigation` incrementa su `count`
- `Latest navigation entry` muestra `finalUrl`, `outcome`, payload y duraciones
- el panel de eficiencia sigue disponible al regresar
- el runtime asset no necesita volver a inyectarse inline en el HTML

### 5. Generar Telemetria De Payload Real

Accion:

- navegar a `/runtimeModelSync`
- editar varios campos con `volt:model.sync`
- volver a `/runtimeEvents`

Esperado:

- `Telemetry action` refleja `avg request`, `max request`, `avg response` y `avg patch`
- `Latest action entry` muestra `requestPayloadBytes`, `responsePayloadBytes` y `patchDurationMs`
- en `Network` no debe verse tormenta de requests injustificada para una sola interaccion simple

### 6. Reset Y Refresco Operativo

Accion:

- pulsar `Resetear telemetria`
- despues pulsar `Refrescar roots`

Esperado:

- `telemetry entries` vuelve a `0`
- los cards de navigation/action/patch reinician sus conteos
- `total roots` y `unique components` siguen reflejando el estado actual del DOM conectado

### 7. Verificacion Basica Con DevTools Performance

Accion:

- grabar una sesion corta en `Performance`
- repetir una navegacion SPA y una accion reactiva

Esperado:

- no aparecen long tasks recurrentes obvias durante una interaccion simple
- el costo principal observable debe concentrarse en request/patch real, no en reinyeccion completa del runtime
- el timeline debe ser consistente con las duraciones visibles en el panel de eficiencia

## Criterio De Cierre

Se puede marcar esta pasada como util cuando:

- `/runtimeEvents` permite ver `navigation`, `action` y `patch` sin abrir consola
- la request a `/_volt/runtime.js` queda observable y separada del HTML
- el panel permite detectar payloads altos, patches lentos o crecimiento raro de roots
- los datos del laboratorio son suficientes para decidir el siguiente cuello de botella real antes de optimizar a ciegas

## Corte Actual Del Bloque 4

Budgets iniciales fijados con la evidencia reciente del runner:

- `boot <= 150 ms`
- `patch <= 120 ms`
- `payload action <= 2 KB`
- `payload navigation <= 50 KB`
- `telemetry buffer <= 60`

Budgets iniciales de segunda ronda (sesion larga + cache + listas grandes):

- `cache hit ratio (navigate) >= 80%`
- `cache duplicate misses <= 0` dentro del `ttl`
- `listas grandes (2000) patch <= 400 ms`
- `listas grandes (2000) payload <= 256 KB`
- `sesion larga heap used <= 15 MB` (si el browser expone `performance.memory`)

### Protocolo Cache Hit Ratio (Warm-up)

Objetivo: medir reuso real en navegacion (click), sin contaminar el indicador con misses/hits de prefetch.

Accion:

- abrir `/runtimeMatrix`
- seleccionar escenario `cache` (condicion `normal`)
- pulsar `Resetear cache stats`
- abrir `/counterExample`
- repetir la navegacion a `/counterExample` usando el boton `Repetir /counterExample (ttl=15000ms)` (usa `volt:prefetch="none"` y `volt:cache="ttl=15000"`)
  - primer click: debe producir `miss` + `store`
  - siguientes clicks (dentro de ~15s): deben producir `hit` (navigate)
- ejecutar al menos `10` navegaciones a `/counterExample` dentro de la ventana `ttl` para estabilizar el ratio aun si el regreso al runner cuenta como un `miss`
- volver a `/runtimeMatrix` y pulsar `Capturar snapshot`

Esperado:

- `cache hit` en el snapshot debe tomar el ratio de `navigate` y quedar `>= 80%`
- `cache duplicate misses` debe permanecer en `0` dentro de la ventana `ttl`

### Protocolo Sesion Larga (Runner)

Objetivo: validar que en una sesion con muchas interacciones el runtime mantiene:

- buffer de telemetria acotado (`<= 60`)
- heap razonable (si el browser expone `performance.memory`)
- cache sin misses duplicados dentro del `ttl`
- hit ratio de cache (navigate) dentro del umbral

Accion (por condicion: `normal` y `degradada`):

- abrir `/runtimeMatrix`
- seleccionar escenario `sesion-larga`
- seleccionar condicion (`normal` o `degradada`)
- pulsar `Resetear telemetria` y `Resetear cache stats`
- abrir `/runtimeRequestLab` y ejecutar `Fast action` `80` veces
- abrir `/runtimeModelSync` y hacer `20` updates rapidos
- abrir `/counterExample` y ejecutar `1 miss + 50 hits` con `Repetir /counterExample (ttl=15000ms)`
- volver a `/runtimeMatrix` y pulsar `Capturar snapshot`

Lecturas representativas del corte:

- `boot / normal`: `~22 ms`
- `boot / degradada` con DevTools `Slow 4G`: `runtime.js` `45.6 kB` en `146 ms`; `boot = 2.6 ms`
- `action-reactiva / normal`: `patch ~14.3 ms`, `payload 689 B`
- `volt:model.sync / normal`: `patch ~12.9 ms`, `payload 699 B`
- `action-reactiva / degradada`: `patch ~106.4 ms`, `payload 689 B`
- `volt:model.sync / degradada`: `patch ~103.1 ms`, `payload 707 B`
- `navegacion-spa / degradada`: `patch ~103 ms`, `payload ~39.7 KB`
- `sesion larga / normal` (runner): `telemetry size = 60`, `heap used ~10.1 MB`, `cache hit (navigate) = 90.91%`, `dup misses = 0`
- `sesion larga / degradada` (runner): `telemetry size = 60`, `heap used ~10.1 MB`, `cache hit (navigate) = 90.91%`, `dup misses = 0`

Nota:

- la condicion `degradada` de `/runtimeMatrix` ya es un harness reproducible del lab (latencia artificial de red + bloqueo controlado de CPU en hooks runtime)
- `boot / degradada` queda cerrado con una pasada externa real de DevTools sobre `/runtimeEvents`; el `bootMs` observado (`2.6 ms`) se mantiene dentro del budget `< 150 ms` y el costo de red del asset (`146 ms`) queda separado como `runtime asset duration`

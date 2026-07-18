## Validacion Manual Rapida Del Runtime

Usar esta tabla para registrar una pasada corta de validacion funcional en navegador real sobre el runtime SPA/reactivo.

## Checklists Complementarias

Cuando el bloque activo sea `cache/preserve/persist`, complementar esta pasada corta con:

- [7-Fragment-Cache-Prefetch-Manual-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/7-Fragment-Cache-Prefetch-Manual-Validation.md)
- [13-Volt-Preserve-Manual-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/13-Volt-Preserve-Manual-Validation.md)
- [12-Volt-Persist-Manual-Validation.md](file:///c:/W4/Packages/VoltStack/app-skeleton/vendor/voltstack/spa-lab/Docs/Volt%20Runtime%20Js/12-Volt-Persist-Manual-Validation.md)

### Datos De La Ejecucion

| Campo | Valor |
| --- | --- |
| Fecha |  |
| Entorno |  |
| Branch o commit |  |
| Navegador |  |
| Tester |  |

### Matriz De Validacion

| ID | Area | Escenario | Ruta o pantalla | Esperado | Estado | Observaciones |
| --- | --- | --- | --- | --- | --- | --- |
| QA-01 | Runtime asset | El runtime se sirve como recurso externo | `/runtimeEvents` | Existe request a `/_volt/runtime.js` y no viaja inline dentro del HTML principal | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-02 | Cache HTTP | El runtime expone headers cacheables | `/_volt/runtime.js` | Respuesta con `Cache-Control`, `ETag` y `Last-Modified` | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-03 | Telemetria inicial | El panel de eficiencia refleja metricas reales | `/runtimeEvents` | Se muestran datos en `efficiency-navigation-performance`, `efficiency-runtime-asset` y `efficiency-runtime-overview` | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-04 | Accion reactiva | Una interaccion simple actualiza telemetria y patch | `/runtimeEvents` | `Telemetry patch` y `Telemetry action` incrementan y aparece `Latest patch entry` | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-05 | Model sync | Escritura rapida no genera tormenta de requests | `/runtimeModelSync` | Requests razonables, sin duplicacion injustificada ni bloqueo visible | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-06 | Navegacion SPA | Navegar entre pantallas sin recarga completa | `/runtimeEvents` -> `/runtimeModelSync` -> `/runtimeState` | La navegacion ocurre por SPA y `Telemetry navigation` incrementa | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-07 | Persistencia del runtime | El runtime no se reinyecta incorrectamente al navegar | Flujo SPA entre rutas demo | El runtime sigue operativo y no reaparece inline como parte del HTML documental | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-08 | Abort y stale | Requests concurrentes se resuelven de forma coherente | Acciones o navegaciones rapidas consecutivas | No quedan estados colgados y el resultado final visible es coherente | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-09 | Estado loading | El estado `loading` aparece y desaparece correctamente | Cualquier accion reactiva | Se activa durante la request y se limpia al finalizar | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-10 | Estado dirty | El estado `dirty` responde a cambios de entrada | Inputs con `volt:model` o `volt:model.sync` | Se activa al editar y se limpia cuando corresponde | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-11 | Estado success | El estado `success` respeta timeout y limpieza | Acciones con resultado correcto | Se activa de forma visible y luego se limpia segun politica configurada | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-12 | Estado error | El estado `error` se refleja de forma segura | Error forzado de request o protocolo | Se informa error sin romper la UI ni dejar estados inconsistentes | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-13 | Foco | El foco se preserva tras patch reactivo | Inputs y formularios | El cursor no salta de forma inesperada despues del patch | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-14 | Scroll | El scroll no se rompe durante patch o SPA | Contenido con desplazamiento | No hay saltos visuales inesperados salvo comportamiento documentado | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| QA-15 | Reset operativo | Resetear telemetria no rompe el estado del runtime | `/runtimeEvents` | `Resetear telemetria` limpia contadores y `Refrescar roots` mantiene datos coherentes | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |

### Hallazgos

| Prioridad | Hallazgo | Impacto | Reproducible | Ruta | Nota tecnica |
| --- | --- | --- | --- | --- | --- |
| Alta |  |  | `[ ] Si` `[ ] No` |  |  |
| Media |  |  | `[ ] Si` `[ ] No` |  |  |
| Baja |  |  | `[ ] Si` `[ ] No` |  |  |

### Resumen De La Pasada

| Campo | Valor |
| --- | --- |
| Total escenarios | 15 |
| OK |  |
| Parcial |  |
| Falla |  |
| Riesgo general | `[ ] Bajo` `[ ] Medio` `[ ] Alto` |
| Siguiente bloque recomendado |  |

### Decision Tecnica

- continuar sin cambios
- corregir issues criticos antes de nuevas features
- priorizar resiliencia (`timeout`, `retry`, clasificacion de errores)
- priorizar UX runtime (`focus`, `scroll`, `states`)
- priorizar performance (`payload`, `patch`, `volt.js`)

### Ejecucion Reciente 2026-06-26

Resultado validado manualmente en navegador real sobre las rutas demo actuales:

- `QA-01 Runtime asset`: `OK`
- `QA-03 Telemetria inicial`: `OK`
- `QA-04 Accion reactiva`: `OK`
- guardrails automatizados del skeleton fijan ahora la presencia del hook inspector, el panel `efficiency-*`, los snapshots `latest` y la API publica de telemetria/componentes en `/runtimeEvents`
- `QA-05 Model sync`: `OK`
- `QA-06 Navegacion SPA`: `OK`
- `QA-07 Persistencia del runtime`: `OK`
- `QA-08 Abort y stale`: `OK` para `aborted`; `stale` queda reproducible via `/runtimeRequestLab`
- `QA-09 Estado loading`: `OK`
- `QA-11 Estado success`: `OK`
- `QA-12 Estado error`: `OK`

Pasada browser adicional sobre `/runtimeEvents -> /runtimeState -> accion reactiva -> /runtimeEvents`:

- `Telemetry navigation`: `count = 2`, `outcomes = success:2`
- `Telemetry action`: `count = 1`, `outcomes = success:1`
- `Telemetry patch`: `count = 3`, `outcomes = navigation-patch:2, action-effects:1`
- `Latest navigation entry`: termina apuntando a `/runtimeEvents`
- `Latest action entry`: queda fijado en `captureSelectiveSync`
- `Latest patch entry`: termina como `navigation-patch`
- hallazgo operativo: escribir en los inputs de `/runtimeState` no alimenta por si solo `captureSelectiveSync`; primero hay que persistir el valor en `window.Volt.state`, o el selective sync reporta `Applied = 0`, `Skipped = 3` y notas `"(vacio)"`
- ajuste UX del lab: `/runtimeState` ahora expone un flujo visual de 3 pasos y un preview live desde `window.Volt.state` para que el usuario vea exactamente que llegara al backend antes del submit

Cobertura adicional del contrato de errores del runtime:

- `Telemetry navigation`: `aborted:1`, `http-error:1`, `success:4`, `timeout:1`
- `Telemetry action`: `network-error:1`, `protocol-error:1`, `timeout:1`

### Ejecucion Reciente 2026-07-13

Resultado de cierre operativo del bloque Full SPA:

- `QA-06 Navegacion SPA`: `OK`
  - flujo `/` -> `/spaReactive` -> `/counterExample` -> `/formExample` sin recarga completa
  - flujo `/` -> `/noLayoutExample` sin fallback por ausencia de `data-volt-layout`
- `Indice operativo del lab`: `OK`
  - `/spaReactive` expone accesos directos a `/cacheExample`, `/fragmentCache`, `/runtimeState`, `/runtimePersist` y `/runtimeRequestLab`
- `QA-07 Persistencia del runtime`: `OK`
  - no se detecto reinyeccion duplicada de `/_volt/runtime.js`
- `Build de produccion`: `OK`
  - `npm run build` genero `public/build/.vite/manifest.json`
  - el skeleton resolvio assets `/build/assets/*` sin depender de `@vite/client` cuando el hot reload no estaba activo

### Ejecucion Reciente 2026-07-13 - Bloque 2 Request Lifecycle

Validacion real en navegador sobre `/runtimeRequestLab`:

- `QA-08 Abort y stale`: `OK`
  - `Abort previous action` emitio `volt:request-abort` para `slowAction`
  - el estado final visible quedo en `fastAction respondio sin demora.`
  - `Abort previous navigation` emitio `volt:request-abort` para la visita a `/runtimeRequestLabSlow`
  - la segunda navegacion completo correctamente hacia `/runtimeEvents`
  - `Stale navigation` emitio `volt:request-stale` de forma determinista para la visita lenta supersedida
- `Observacion de implementacion`: `OK`
  - el laboratorio ahora expone controles explicitos para `abort` y un helper acotado al lab para reproducir `stale` sin tocar el runtime global
- `volt:error` validado con `CSRF` invalido y con laboratorio controlado en `/runtimeRequestLab`

Cobertura adicional del retry seguro en navegacion `GET`:

- ruta validada: `/runtimeRequestLabRetryOnce`
- lectura observada en `Telemetry navigation`: `count = 5`
- outcomes observados: `success:5`
- `avg duration = 213.74 ms`
- `max duration = 542.6 ms`
- `avg response = 33860 B`
- `max response = 58213 B`
- `avg patch = 21.24 ms`
- `max patch = 29 ms`
- guardrail automatizado del skeleton: el destino `/runtimeRequestLabRetryOnce` queda fijado con primer intento `500` y segundo intento `200`
- lectura operativa: resultado compatible con un fallo transitorio absorbido por el retry automatico, sin degradacion visible del patch DOM
- revalidacion browser posterior al endurecimiento de `/runtimeRequestLab`: con listener persistido en `sessionStorage`, `Retry navigation once` vuelve a confirmar `volt:request-retry` con `retryAttempt = 1`, `errorKind = http-error`, `status = 500` y navegacion final exitosa en un solo click
- cierre UX adicional: `/runtimeRequestLabRetryOnce` ahora pinta ese resumen persistido en la propia pantalla destino para que QA vea `retryAttempt`, `status`, `errorKind`, `retryDelayMs` y `finalUrl` sin abrir DevTools
- cierre UX adicional para lifecycle: `Abort previous navigation` y `Stale navigation` ahora dejan un resumen persistido visible en `/runtimeEvents` y/o `/runtimeRequestLab`, mostrando `eventName`, `errorKind`, `target`, `message` y `finalUrl` despues de navegar
- revalidacion browser del cierre lifecycle: `Stale navigation` ya muestra `volt:request-stale`, `outcome = stale` y `finalUrl = /runtimeRequestLabSlow` en `/runtimeRequestLab`; `Abort previous navigation` ya muestra `volt:request-abort`, `outcome = aborted` y mensaje de supersession en `/runtimeEvents`
- endurecimiento UX adicional del lab: `/runtimeRequestLab` ahora concentra un panel unificado de resiliencia que resume el ultimo incidente y deja marcados como observados `retry`, `abort`, `stale`, `network-error`, `timeout` y `protocol-error`; `/runtimeEvents` replica el panel para que el resumen sea visible tambien al aterrizar fuera del lab
- cierre UX de navegacion QA: `/runtimeEvents` ahora muestra un badge `incidentes en sesion` y un CTA `Ir a RequestLab`, de forma que el tester vea enseguida si hay contexto persistido y pueda saltar al laboratorio sin recordar la ruta
- cierre operativo adicional: al entrar a `/runtimeRequestLab` por SPA desde `/runtimeEvents`, `SpaLab.js` rehidrata el wiring del lab automaticamente, evitando que el CTA deje una pantalla visualmente cargada pero sin bootstrap activo
- revalidacion browser del CTA end-to-end: `runtimeEvents -> RequestLab -> Protocol error por validacion -> runtimeEvents` ya conserva `protocol-error` en `sessionStorage` y repinta el panel/badge de resiliencia al volver por SPA, con `status = 422`, `target = protocolValidationFailure` y mensaje `The given data was invalid.`
- Sprint 1 `Estados Runtime` validado en `/runtimeState`: editar `volt:model="statusProbeTitle"` activa `dirty.target = statusProbeTitle` con debounce `200ms`; `saveStatusProbe` expone `success.action = saveStatusProbe` y `success.target = state-status-form`; `failStatusProbe` expone `error.action = failStatusProbe`, `error.target = state-status-error-button` y mensaje `Server Error`
- `QA-13 Foco`: `OK` en `/runtimeFocus`
  - el input/textarea conservan el target activo tras `Disparar patch reactivo`
  - el inspector mantiene `focus-selection-range` y `focus-selection-direction` cuando existe una seleccion valida
- `QA-14 Scroll`: `OK` en `/runtimeFocus`
  - el contenedor `data-volt-preserve-scroll` restaura `focus-scroll-box-top` despues del patch
  - el mismo lab deja visible el scroll interno del control activo mediante `focus-selection-scroll-top`
- `QA-15 Budgets de eficiencia`: `OK` en `/runtimeEvents`
  - estado inicial visible sin DevTools: `boot=alerta`, `patch=pendiente`, `payload=pendiente`, `buffer=ok`
  - flujo `/runtimeEvents -> /runtimeModelSync -> /runtimeEvents` actualiza budgets con datos reales y deja `patch=ok`, `payload=ok`, `buffer=ok`
  - el refresh manual conserva el resumen contractual y no infla `telemetry entries`
- `QA-16 Boot diferido del lab de eficiencia`: `OK` en `/runtimeEvents`
  - el HTML inicial mantiene placeholders (`boot`, `(pendiente)`, `(sin datos)`) y difiere el llenado pesado a `window.load`
  - `Runtime summary snapshot`, `Active components summary` y `Latest *` aparecen despues de `load`
  - el refresh manual sigue operativo y el panel de resiliencia ya no re-renderiza dos veces en el arranque
- `QA-17 Diagnostico SPA click + scroll`: `OK` en `/runtimeEvents`
  - recorrer `/runtimeEvents -> /spaReactive -> /cacheExample -> /spaReactive -> /runtimeEvents`
  - el panel `Diagnostico de click y scroll` debe reflejar el ultimo intento SPA con `href`, `requestId`, `outcome`, `location/finalUrl`, `scroll.before` y `scroll.after`
  - el `pre` de detalle debe mostrar el payload serializado del ultimo hook relevante (`request-start`, `before-navigate`, `navigated` o `request-finish`)
- `QA-18 Latest-wins en clicks rapidos`: `OK`
  - desde `cacheExample`, hacer click rapido en `Ir a /counterExample` y enseguida en `Ir a /formExample`
  - repetir tambien el patron inverso y otras combinaciones entre `cacheExample`, `counterExample` y `formExample`
  - el ultimo click debe ganar de forma consistente; la URL final y el marker visible de la vista deben coincidir con el ultimo destino

### Cobertura Automatizada Complementaria 2026-07-13

Guardrails nuevos incorporados en `vendor/voltstack/framework/tests/Feature/SkeletonSpaRoadmapTest.php`:

- `popstate` queda fijado contra el contrato actual de `visit(window.location.href, { updateHistory: false, historyMode: "replace", fallback: false })`
- la reconciliacion de `head` queda fijada sobre claves gestionadas y reutilizacion de nodos existentes
- la no duplicacion de scripts del `head` queda fijada por la clave estable `script:type:src`
- el fallback por error HTTP de navegacion queda fijado sobre `volt:request-error` y `window.location.assign(...)`
- `volt:model` queda fijado sobre la actualizacion inmediata del `snapshot` en `data-volt-snapshot`
- `volt:model.sync` queda fijado sobre el despacho interno `__volt_sync__` con debounce del runtime
- el lifecycle de acciones queda fijado sobre `volt:request-stale`, `volt:request-abort` y resincronizacion del snapshot tras la respuesta
- las acciones reactivas quedan fijadas sin retry automatico en el runtime actual
- `accion no permitida` queda fijada como `protocol-error` semantico con codigo `runtime.action_not_allowed`

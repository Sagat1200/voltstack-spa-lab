# Volt Persist - Manual Validation

Objetivo: cerrar la validacion fina del MVP de `volt:persist` sobre navegador real, usando el flujo demo del `spa-lab` y los paneles de observabilidad ya expuestos por las rutas de persistencia.

## Alcance

Este checklist valida el contrato actual de `volt:persist`:

- reuse de la misma instancia DOM por clave estable
- supervivencia a una pantalla intermedia sin targets compatibles
- reinyeccion al reaparecer la misma clave
- no duplicacion de instancias activas
- descarte por politica documental `reset`
- continuidad de estado efimero del DOM y listeners ya montados

Fuera de alcance en esta pasada:

- persistencia entre recargas completas
- persistencia entre pestañas
- estrategias configurables tipo TTL o `keep-alive`

## Preparacion

1. Levantar el skeleton con `php volt serve`.
2. Confirmar que la navegacion SPA funciona sin 404 de assets.
3. Abrir DevTools en `Elements`, `Console` y `Network`.
4. Iniciar en:
   - `/runtimePersist`
   - flujo principal: `/runtimePersist -> /runtimePersistBridge -> /runtimePersistAlt`

## Matriz De Casos

| ID | Escenario | Pasos | Esperado | Estado | Notas |
| --- | --- | --- | --- | --- | --- |
| VP-01 | Captura inicial del sidebar persistido | En `/runtimePersist`, editar `Nombre visible`, el bloque `contenteditable`, el `checkbox`, el `details` y el `range` del sidebar persistido | Los cambios quedan visibles solo en el panel persistido; el bloque de control sigue con estado propio | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VP-02 | Captura inicial del player persistido | En `/runtimePersist`, editar el input del player persistido y mover su `range` | Los cambios quedan visibles en el player persistido y no en el bloque de control | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VP-03 | Supervivencia a pantalla puente | Navegar por SPA a `/runtimePersistBridge` | No aparecen targets persistidos en el DOM visible; el panel muestra `persistedFragments = 0` y `persistentFragmentRegistrySize > 0` si vienes del origen | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VP-04 | Reinyeccion final del sidebar | Desde `/runtimePersistBridge`, navegar a `/runtimePersistAlt` | El sidebar reinyectado conserva texto, `checkbox`, `details` y `range` del origen; no aparece el estado base del destino | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VP-05 | Reinyeccion final del player | En `/runtimePersistAlt`, revisar el player reinyectado | El player conserva el valor editado en el origen; no queda el contenido base del destino | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VP-06 | No duplicacion de instancias | En `/runtimePersistAlt`, inspeccionar el DOM y contar los nodos `persist-sidebar` y `persist-player` | Existe una sola instancia activa por clave; no hay duplicados visibles ni ocultos en el target actual | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VP-07 | Continuidad de listeners y UI viva | Tras la reinyeccion, interactuar otra vez con `details`, inputs y rangos de los bloques persistidos | Los listeners siguen funcionando; no hay nodos congelados ni UI muerta | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VP-08 | Observabilidad del panel de estado | En las tres rutas, revisar el panel `data-volt-persist-status` | `finalUrl`, `persistedFragments`, `persistentFragmentRegistrySize`, `preservedFragments` y `discardedFragments` reflejan el ultimo `volt:navigated` | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VP-09 | Compatibilidad con pantalla sin targets | Repetir varias veces `origin -> bridge -> alt -> origin` | El registry no se corrompe, la reinyeccion sigue ocurriendo y no crecen duplicados | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VP-10 | Descarte por politica `reset` | Navegar desde una ruta con `volt:persist` hacia una pantalla con politica documental `reset` si aplica en la sesion de QA, o documentar el caso como pendiente de ruta dedicada | El registro persistido se descarta y no se reinyecta en un destino que exija arranque limpio | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |

## Observaciones Operativas

- En `/runtimePersistBridge`, el comportamiento sano es:
  - `persistedFragments = 0`
  - `persistentFragmentRegistrySize > 0`
- En `/runtimePersistAlt`, el comportamiento sano es:
  - `persistedFragments > 0`
  - targets reinyectados con estado vivo
- Si el contenido base del destino sigue visible, la reinyeccion fallo o fue reemplazada por un render nuevo.
- Si aparecen dos nodos con la misma clave en el destino, hay regresion de no duplicacion.

## Señales De Regresion

- el panel de estado no cambia al navegar
- el bridge muestra `persistentFragmentRegistrySize = 0` despues de haber capturado instancias
- el destino final conserva los valores base del HTML en lugar del estado editado en el origen
- se pierden listeners o controles quedan inertizados tras la reinyeccion
- aparecen duplicados por clave

## Resultado De La Pasada

| Campo | Valor |
| --- | --- |
| Fecha | 2026-07-14 |
| Entorno | Local `php volt serve --port=8001` |
| Navegador | Browser integrado del agente |
| Build | `[ ] dev` `[x] build` |
| Total casos | 10 |
| OK | 9 |
| Parcial | 1 |
| Falla | 0 |
| Decision | `[x] cerrar item` `[ ] mantener en progreso` `[ ] corregir antes de cerrar` |

Notas de la pasada:

- flujo validado: `/runtimePersist -> /runtimePersistBridge -> /runtimePersistAlt -> /runtimePersist`
- `bridge`: `persistedFragments = 0`, `persistentFragmentRegistrySize = 2`
- `alt`: `persistedFragments = 2`, `persistentFragmentRegistrySize = 2`
- supervivencia comprobada de texto, `details`, checkbox y `range` en `persist-sidebar`, y de texto, checkbox y `range` en `persist-player`
- no se observaron duplicados por clave al reinyectar ni al volver al origen
- `VP-10` queda como caso parcial por no existir en el flujo demo actual una ruta dedicada para forzar `reset` documental especifica de `volt:persist`; el contrato actual del MVP se considera cerrado con la validacion browser del flujo soportado

## Cierre Documental

Si la pasada queda sana:

1. mover `cerrar validacion fina de volt:persist` a `[x]` en `1-Versions.md`
2. actualizar `01-Contrato-Vigente.md`
3. reflejar el cierre en `10-Manual_Runtime_QA.md`
4. actualizar `11-Matriz-Implementacion-Runtime.md`

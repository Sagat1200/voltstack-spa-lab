# Volt Preserve - Manual Validation

Objetivo: cerrar la validacion fina del MVP de `volt:preserve` como preservacion opt-in de fragmentos top-level entre pantallas SPA compatibles.

## Alcance

Este checklist valida el contrato actual de `volt:preserve`:

- capture de fragmentos marcados por clave
- reuse del nodo vivo al navegar a una ruta compatible
- descarte seguro cuando la politica documental fuerza `reset`
- no preservacion de bloques de control no marcados
- emision observable de `volt:fragment-preserve` y `volt:fragment-discard`

Fuera de alcance en esta pasada:

- persistencia entre recargas completas
- persistencia a traves de pantallas intermedias sin target compatible
- politicas avanzadas fuera del modo documental `preserve/reset`

## Preparacion

1. Levantar el skeleton con `php volt serve`.
2. Abrir DevTools en `Elements`, `Network` y `Console`.
3. Activar `Preserve log` en `Network`.
4. Empezar en `/fragmentCache`.

## Rutas Del Flujo

- `/fragmentCache`
- `/formExample`
- `/fragmentCacheReset`

## Matriz De Casos

| ID | Escenario | Pasos | Esperado | Estado | Notas |
| --- | --- | --- | --- | --- | --- |
| VPRE-01 | Capture del formulario preservado | En `/fragmentCache`, editar el formulario con `data-volt-preserve="draft-fragment"` | Los cambios quedan visibles en el formulario preservado | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VPRE-02 | Control no preservado se reinicia | En `/fragmentCache`, editar tambien el formulario de control sin `data-volt-preserve` | Ese bloque solo sirve como comparacion y no debe viajar entre pantallas | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VPRE-03 | Capture del shell vivo preservado | En `/fragmentCache`, editar el contenido `contenteditable`, cambiar el `range` y abrir/cerrar el `details` del shell `live-shell` | El shell preservado cambia de estado sin romperse | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VPRE-04 | Reuse hacia ruta compatible | Navegar con SPA a `/formExample` | El formulario `draft-fragment` conserva texto, checkbox y textarea; el shell `live-shell` conserva texto, rango y `details` | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VPRE-05 | Bloques control vuelven al HTML base | En `/formExample`, comparar con el bloque no preservado equivalente | El bloque de control muestra el HTML base del destino | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VPRE-06 | Reuse de vuelta al origen | Modificar otra vez el fragmento preservado en `/formExample` y volver a `/fragmentCache` | Los cambios mas recientes vuelven con el mismo nodo vivo | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VPRE-07 | Hook de preserve observable | Durante el flujo `/fragmentCache -> /formExample` y regreso, revisar los monitores embebidos | Se registran eventos `volt:fragment-preserve` con claves `draft-fragment` o `live-shell` | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VPRE-08 | Descarte por politica documental `reset` | Desde una pantalla con cambios vivos, navegar a `/fragmentCacheReset` | No reaparecen los cambios previos; se muestra el HTML base del destino | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VPRE-09 | Hook de discard observable | Revisar el monitor al entrar a `/fragmentCacheReset` | Se registra `volt:fragment-discard` con razon compatible con politica documental (`document-policy`) | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |
| VPRE-10 | No hay residuos tras reset | Volver a `/fragmentCache` desde `/fragmentCacheReset` | Los cambios descartados no reaparecen por accidente | `[ ] OK` `[ ] Parcial` `[ ] Falla` |  |

## Observaciones Operativas

- `volt:preserve` exige una ruta destino compatible que vuelva a exponer la misma clave.
- A diferencia de `volt:persist`, no debe sobrevivir a una pantalla intermedia sin target compatible.
- El caso de `reset` depende de la meta:
  - `<meta name="volt-fragment-control" content="reset">`
- Si el destino compatible muestra el HTML base en lugar del estado vivo, fallo el reuse.
- Si el destino `reset` muestra los cambios previos, fallo el descarte.

## Señales De Regresion

- no se emite `volt:fragment-preserve`
- no se emite `volt:fragment-discard` al entrar a `/fragmentCacheReset`
- el formulario o shell de control viajan cuando no deberian
- el fragmento preservado pierde estado en una ruta compatible
- el reset no limpia los cambios vivos previos

## Resultado De La Pasada

| Campo | Valor |
| --- | --- |
| Fecha | 2026-07-14 |
| Entorno | Local `php volt serve --port=8001` |
| Navegador | Browser integrado del agente |
| Build | `[ ] dev` `[x] build` |
| Total casos | 10 |
| OK | 10 |
| Parcial | 0 |
| Falla | 0 |
| Decision | `[x] cerrar item` `[ ] mantener en progreso` `[ ] corregir antes de cerrar` |

Notas de la pasada:

- flujo validado: `/fragmentCache -> /formExample -> /fragmentCacheReset -> /fragmentCache`
- en `/formExample`, el formulario `draft-fragment` y el shell `live-shell` conservaron estado vivo; los bloques de control volvieron a su HTML base
- en `/fragmentCacheReset`, el destino mostro HTML nuevo y el monitor registro `volt:fragment-discard` para `<form>` y `<section>`
- al volver a `/fragmentCache`, el estado previo descartado no reaparecio; el documento `reset` actuo como nueva base fresca para claves compatibles en la navegacion siguiente

## Cierre Documental

Si la pasada queda sana:

1. mover `cerrar validacion fina de volt:preserve` a `[x]` en `1-Versions.md`
2. actualizar `01-Contrato-Vigente.md`
3. reflejar el cierre en `10-Manual_Runtime_QA.md`
4. actualizar `11-Matriz-Implementacion-Runtime.md`

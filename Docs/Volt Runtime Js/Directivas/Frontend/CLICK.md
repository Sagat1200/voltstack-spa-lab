# Directiva Frontend: `volt:click`

## Introduccion

`volt:click` dispara una **action reactiva** (request POST al endpoint del componente, default `/_volt/action`) cuando el usuario hace click en el elemento.

Es una directiva **DOM -> backend**:

- Envia `component`, `action`, `params`, `updates` y `snapshot` al servidor.
- El servidor responde con `effects` y opcionalmente con `html`/`snapshot`.
- El runtime aplica efectos y, si corresponde, puede hacer fallback a reemplazo de HTML.

En runtime, el handler escucha:

- `[volt-click]` y `[volt:click]`

## Cuando usar

Usa `volt:click` cuando:

- Necesitas ejecutar una action del componente (mutación de estado server-driven, validación, side-effects controlados).
- Quieres disparar acciones idempotentes de UI (incrementar contador, guardar, recalcular, refrescar).
- Quieres aprovechar el pipeline de request (loading/success/error, timeout y abort por superseded).

Evita `volt:click` cuando:

- Solo necesitas lógica client-side (usa `volt:on` o `volt:dispatch`).
- La interacción es navegación (usa `volt:navigate`).
- La acción debe ejecutarse al enviar un form (usa `volt:submit`).

## Como usar

### 1) Sintaxis

El valor de la directiva es el nombre de la action (método público) en el componente:

```html
<button type="button" volt:click="fastAction">Ejecutar</button>
```

Forma alternativa:

```html
<button type="button" volt-click="fastAction">Ejecutar</button>
```

### 2) Params (payload adicional opcional)

Puedes enviar params adicionales serializados en JSON mediante `volt:params` / `volt-params`.

```html
<button
  type="button"
  volt:click="saveDraft"
  volt:params='{"source":"toolbar","mode":"quick"}'
>
  Guardar
</button>
```

Reglas:

- El valor debe ser JSON válido (usa comillas dobles en keys/strings).
- Si el JSON no se puede parsear, el runtime cancela el dispatch y registra el error en consola.

### 3) Updates (model sync acumulado)

Cuando existe `volt:model.local` o `volt:model.sync` dentro del root, el runtime agrega `updates` automáticamente vía `collectModelUpdates(root)` para que la action reciba el estado actual de inputs relevantes.

### 4) Timeout y estado de request

Puedes controlar el timeout por trigger usando:

- `data-volt-request-timeout="120ms"` (o `volt:request-timeout="120ms"`)

Durante la request:

- El trigger se deshabilita (`disabled=true`) mientras el request está activo.
- Si una action nueva llega antes de que termine la anterior, la anterior se aborta y queda clasificada como `aborted`/`stale` según el caso.

Los hooks de request se exponen vía eventos runtime (para telemetría):

- `volt:request-start`
- `volt:request-error`
- `volt:request-stale`

## Ejemplo de uso

Ejemplo inspirado en el lab de requests (`/runtimeRequestLab`):

```html
<button type="button" volt-click="fastAction" data-volt-target="fast-action-button">
  Fast action
</button>

<button
  type="button"
  volt:click="slowAction"
  data-volt-target="slow-action-button"
  data-volt-request-timeout="120ms"
>
  Timeout action (120ms)
</button>
```

## Escenario de uso

Escenario: action “rápida” + action “lenta” para validar abort/timeout y estados de UI.

- El usuario dispara `slowAction` (tarda ~1500ms).
- Antes de que termine, dispara `fastAction`.
- El runtime aborta la request anterior y conserva solo el resultado de la action más reciente.

Checklist de validación manual:

- Dispara `slowAction` con `data-volt-request-timeout` bajo y confirma `timeout`.
- Dispara `slowAction` y luego `fastAction` rápido y confirma que la primera queda `aborted`/`stale` y la segunda completa.
- Confirma que el botón trigger se deshabilita durante la request y se re-habilita al finalizar.

Rutas demo (spa-lab):

- `/runtimeRequestLab`

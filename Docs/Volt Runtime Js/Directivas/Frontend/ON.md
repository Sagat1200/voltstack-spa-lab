# Directiva Frontend: `volt:on`

## Introduccion

`volt:on` declara handlers de eventos del DOM directamente en el markup, ejecutados por el runtime en el navegador.

Su objetivo es permitir interacciones reactivas simples sin JavaScript inline:

- mutar `window.Volt.state` (set / toggle / delete)
- emitir `CustomEvent` (dispatch) para integrar UI con otros listeners frontend

`volt:on` es una directiva **frontend**: no dispara requests HTTP por si misma. Para backend usa `volt:click`, `volt:submit` o `volt:model.sync`.

## Cuando usar

Usa `volt:on` cuando:

- Necesitas una accion inmediata en frontend: toggles, flags, resets de estado, abrir/cerrar paneles.
- Quieres componer varias mutaciones del state en un solo evento (pipeline).
- Quieres emitir eventos de integracion (`dispatch:...`) para desacoplar UI y listeners.

Evita `volt:on` cuando:

- La accion depende de logica compleja o validaciones que deben vivir en backend.
- Requieres expresiones arbitrarias para el valor (el MVP soporta un subconjunto de literales y dos shortcuts de `$event`).
- Requieres listeners sobre eventos no soportados por el runtime (ver lista de eventos soportados).

## Como usar

### 1) Sintaxis

`volt:on` acepta una lista de reglas separadas por `|`:

```html
volt:on="evento(.modificador...)? -> accion | evento -> accion | ..."
```

El runtime procesa el evento desde un listener delegado: encuentra el elemento más cercano al `event.target` que tenga `volt:on` (o `volt-on`) y evalua sus reglas.

Nombres de atributo soportados:

```html
<button volt:on="..."></button>
<button volt-on="..."></button>
```

### 2) Eventos soportados

Eventos permitidos en el MVP:

- `click`
- `input`
- `change`
- `submit`
- `focus`
- `blur`
- `keydown`
- `keyup`

### 3) Modificadores

Se declaran con `.` después del nombre del evento:

- `.prevent`: llama `event.preventDefault()` si el evento es cancelable.
- `.stop`: llama `event.stopPropagation()`.
- `.once`: la regla se ejecuta solo la primera vez por elemento y por regla.
- `.self`: solo ejecuta si `event.target === trigger` (no si el evento viene de un hijo).

Ejemplo:

```html
<a href="/runtimeFocusAlt" volt:on="click.prevent -> dispatch:navigation:soft"></a>
```

### 4) Filtros de tecla (keydown/keyup)

En `keydown` y `keyup` puedes agregar un filtro de tecla como segmento extra:

```html
<input volt:on="keydown.escape -> state:set shared:ui.modalOpen = false">
```

Normalizacion MVP:

- `esc` se normaliza a `escape`
- `spacebar` o `" "` se normaliza a `space`

### 5) Acciones soportadas

#### A) `dispatch:<evento>`

Emite un `CustomEvent` con `bubbles: true` y `cancelable: true` desde el mismo elemento.

```html
<button volt:on="click -> dispatch:analytics:cta-click">CTA</button>
```

#### B) `state:set <scope>:<path> = <valor>`

Setea un valor en `window.Volt.state` (client o shared).

```html
<button volt:on="click -> state:set shared:ui.open = true">Abrir</button>
```

`<valor>` soporta:

- literales: `true`, `false`, `null`, números, strings `'...'` o `"..."` (con escapes simples)
- shortcuts:
  - `$event.target.value`
  - `$event.target.checked`

Ejemplos:

```html
<input
  type="text"
  volt:on="input -> state:set client:draft.note = $event.target.value"
>
<input
  type="checkbox"
  volt:on="change -> state:set shared:ui.enabled = $event.target.checked"
>
```

#### C) `state:toggle <scope>:<path>`

Invierte un boolean del state (usa `false` como fallback si no existe).

```html
<button volt:on="click -> state:toggle shared:ui.enabled">Toggle</button>
```

#### D) `state:delete <scope>:<path>`

Elimina una ruta del state.

```html
<button volt:on="click -> state:delete client:draft">Reset draft</button>
```

### 6) Composicion (pipelines)

Puedes encadenar varias acciones en una misma interacción:

```html
<button
  type="button"
  volt:on="click -> state:set client:focus.title = false | click -> state:set client:focus.title = true"
>
  Forzar reenfoque
</button>
```

Este patrón se usa para provocar una transición `false -> true` en directivas reactivas como `volt:focus`.

## Ejemplo de uso

```html
<section>
  <button
    type="button"
    volt:on="click -> state:toggle shared:ui.enabled"
  >
    Toggle enabled
  </button>

  <button
    type="button"
    volt:on="click -> state:set client:draft.note = 'set desde volt:on'"
  >
    Seed draft
  </button>

  <button
    type="button"
    volt:on="click -> dispatch:demo.events.audit"
  >
    Dispatch audit
  </button>

  <div>
    enabled: <strong volt:text="shared:ui.enabled ?? false">false</strong>
  </div>
  <div>
    note: <strong volt:text="client:draft.note ?? '(sin nota)'">(sin nota)</strong>
  </div>
</section>
```

## Escenario de uso

Escenario: panel de errores que se abre en frontend y, al abrirse, otra directiva (`volt:autofocus.when`) debe enfocar un campo de resumen.

Markup:

```html
<button
  type="button"
  volt:on="click -> state:set shared:focus.showErrors = false | click -> state:set shared:focus.showErrors = true"
>
  Abrir panel de errores
</button>

<section volt:show="shared:focus.showErrors === true">
  <textarea
    rows="4"
    volt:autofocus.when="shared:focus.showErrors"
    placeholder="Este campo debe recibir foco al abrir"
  ></textarea>

  <button
    type="button"
    volt:on="click -> state:set shared:focus.showErrors = false"
  >
    Cerrar
  </button>
</section>
```

Rutas demo (spa-lab):

- `/runtimeEvents` (varios ejemplos)
- `/runtimeFocus` (pipelines de focus/autofocus)

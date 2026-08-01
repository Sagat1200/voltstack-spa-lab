# API Publica: `runtime.on(...)` (`window.Volt.on`)

## Introduccion

El runtime emite hooks y eventos frontend como `CustomEvent` con `bubbles: true` (por ejemplo `volt:request-start`, `volt:request-finish`, `volt:dirty`, `volt:cache-hit`, `toast:show` via `volt:dispatch`, etc.).

`window.Volt.on(...)` es una API publica minima para observar esos eventos de forma consistente, sin depender de wiring manual con `document.addEventListener(...)` en cada parte de la aplicacion.

## Cuando usar

Usa `Volt.on(...)` cuando:

- Quieres observar hooks del runtime (`volt:*`) para telemetria, analitica o UI global.
- Quieres consumir eventos declarativos emitidos por `volt:dispatch` desde un solo lugar.
- Necesitas filtrar listeners por `component`, `root` o `selector` sin duplicar codigo.

Evita `Volt.on(...)` cuando:

- Necesitas acoplarte a un elemento especifico con un handler local (en ese caso usa listeners directos del DOM o `volt:on`).
- Necesitas control de orden estricto entre múltiples listeners; el orden final sigue siendo el del DOM.

## Como usar

### 1) Suscripcion basica

`Volt.on` registra un listener y retorna una funcion `unsubscribe()`:

```js
const off = window.Volt.on("volt:request-finish", function (event) {
  const detail = event.detail || {};
  console.log("Outcome:", detail.outcome);
});

off();
```

### 2) Opciones y filtros

`Volt.on(name, listener, options)` soporta estas opciones:

- `component` (string): filtra eventos cuyo `event.detail.component` coincide.
- `root` (Element): filtra eventos que ocurren en ese root (por target o containment).
- `selector` (string): filtra cuando `event.target.closest(selector)` matchea.
- `target` (EventTarget): define donde se agrega el listener (default `document`).
- `capture`, `passive`, `once` (bool): se pasan a `addEventListener`.

Ejemplo filtrando por componente:

```js
window.Volt.on(
  "volt:dirty",
  function (event) {
    console.log("Dirty detail:", event.detail);
  },
  {
    component: "spaReactive",
  },
);
```

Ejemplo filtrando por root:

```js
const root = document.querySelector('[data-volt-root="true"][data-volt-component="spaReactive"]');

window.Volt.on(
  "volt:error",
  function (event) {
    console.log("Runtime error:", event.detail);
  },
  {
    root: root,
  },
);
```

Ejemplo filtrando por selector:

```js
window.Volt.on(
  "toast:show",
  function (event) {
    console.log("Toast request:", event.detail);
  },
  {
    selector: "[volt-dispatch], [volt\\:dispatch]",
  },
);
```

### 3) Integracion con `volt:dispatch`

`volt:dispatch` emite `CustomEvent` sobre el elemento trigger (bubbling), por lo que puedes escucharlo globalmente:

```html
<button volt:dispatch="toast:show">Toast</button>
```

```js
window.Volt.on("toast:show", function (event) {
  const detail = event.detail || {};
  console.log("sourceElement:", detail.sourceElement);
});
```

## Notas del contrato

- El runtime no ejecuta JavaScript libre ni `eval` para eventos; `Volt.on` solo envuelve `addEventListener`.
- Los eventos del runtime usan `event.detail` como payload tipico.
- La compatibilidad depende de que los hooks sigan siendo `CustomEvent` con `bubbles: true`.


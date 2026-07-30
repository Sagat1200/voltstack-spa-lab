# Directiva Frontend: `volt:dispatch`

## Introduccion

`volt:dispatch` emite uno o varios `CustomEvent` desde un elemento del DOM usando una declaracion simple en markup. Es un canal **100% frontend**: no toca backend y no muta `window.Volt.state` por si mismo.

En el MVP actual, el runtime activa `volt:dispatch` desde un listener global delegado y el disparador implicito es `click`. Los eventos emitidos hacen `bubble` y pueden observarse desde listeners registrados en el propio elemento, contenedores, o directamente en `document`.

## Cuando usar

Usa `volt:dispatch` cuando quieras:

- Notificar a codigo frontend (o integraciones) sin acoplar el markup a JavaScript inline.
- Emitir señales como `menu:toggle`, `toast:show`, `analytics:cta-click`, `filters:changed`.
- Desacoplar UI (boton/link) de la reaccion (otro script o componente que escucha).

Evita `volt:dispatch` cuando:

- Necesitas modificar state directamente (usar `volt:on` con `state:set`, `state:toggle`, etc.).
- Necesitas modificadores (`prevent`, `stop`, `once`, `self`) o disparadores distintos de `click` (usar `volt:on`).
- Necesitas tocar backend (usar `volt:click` o `volt:submit`).

## Como usar

### 1) Sintaxis

Formato base:

```html
<button volt:dispatch="menu:toggle"></button>
```

Alias equivalente:

```html
<button volt-dispatch="menu:toggle"></button>
```

Multiples eventos (separados por `|`):

```html
<button volt:dispatch="dialog:close | analytics:cta-click"></button>
```

Reglas del nombre de evento:

- Debe iniciar con letra (`A-Za-z`) y puede contener `A-Za-z0-9:._-`
- Se ignoran entradas invalidas sin romper otras directivas del nodo

### 2) Activacion y bloqueo

- En el MVP, `volt:dispatch` se activa por `click`.
- Si el nodo esta deshabilitado, el runtime no emite eventos:
  - `disabled` (propiedad DOM)
  - `aria-disabled="true"`

### 3) Forma del evento (`detail`)

Por cada nombre declarado, el runtime emite:

- `new CustomEvent(nombre, { bubbles: true, cancelable: true, detail })`

El `detail` incluye metadata util:

- `sourceElement`: el elemento que disparo `volt:dispatch`
- `directive`: el valor crudo de la directiva
- `scopeId`: id operativo derivado del root (`data-volt-component` / `data-volt-scope-id` / url actual)
- `clientScope`: scope actual de cliente (derivado de la URL)
- `sharedScope`: `"shared"`
- `component`: componente del root si existe
- `originalEvent`: el `click` original

## Ejemplo de uso

Markup:

```html
<button type="button" volt:dispatch="demo.events.audit | analytics:cta-click">
  Auditar
</button>
```

Listener:

```js
document.addEventListener("demo.events.audit", function (event) {
  const detail = event && event.detail && typeof event.detail === "object" ? event.detail : {};
  console.log("audit", {
    scopeId: detail.scopeId,
    component: detail.component,
    originalType: detail.originalEvent ? detail.originalEvent.type : null,
  });
});
```

## Escenario de uso

Escenario: un CTA debe notificar dos cosas en un solo click:

- A la UI: “cerrar dialogo”
- A analitica: “registrar click”

Markup:

```html
<button
  type="button"
  volt:dispatch="dialog:close | analytics:cta-click"
>
  Confirmar
</button>
```

Puente de integracion (ejemplo):

```js
document.addEventListener("dialog:close", function () {
  const dialog = document.querySelector("[data-dialog='checkout']");
  if (dialog) {
    dialog.hidden = true;
  }
});

document.addEventListener("analytics:cta-click", function (event) {
  const detail = event && event.detail && typeof event.detail === "object" ? event.detail : {};
  window.__analyticsQueue = window.__analyticsQueue || [];
  window.__analyticsQueue.push({
    name: event.type,
    scopeId: detail.scopeId,
    component: detail.component,
    at: new Date().toISOString(),
  });
});
```

Rutas demo (spa-lab):

- `/runtimeEvents` (incluye ejemplos de `volt:dispatch` simple, multiple y convivencia con `volt:on`)

# Extensibilidad: Directivas Frontend (`window.Volt.directives`)

## Introduccion

El runtime expone un registro de directivas frontend para poder extender el pipeline sin modificar el core.

La API publica vive en `window.Volt.directives` y corre dentro del mismo loop de sincronizacion que ya usa el runtime para aplicar `volt:*` al DOM.

## Objetivo

- Registrar nuevas directivas frontend sin tocar el core del runtime.
- Mantener el orden actual del pipeline (directivas estructurales, luego estados runtime, luego render store).
- Permitir directivas que mutan el DOM con ciclos de estabilizacion controlados.

## API publica

### `Volt.directives.register(definition, options?)`

Registra una directiva. Retorna `true` si se registro, `false` si se rechazo (id invalido, contrato invalido, colision, etc.).

### `Volt.directives.unregister(id, options?)`

Desregistra una directiva. Retorna `true` si se elimino, `false` si no existe o si es builtin (a menos que `force=true`).

### `Volt.directives.list()`

Devuelve el listado actual con metadata de orden:

- `id`
- `group`
- `priority`
- `stabilize`
- `maxIterations`
- `builtin`
- `names` (si la directiva los expone)

### Helpers de expresiones (sin `eval`)

Estos helpers permiten reutilizar la misma gramatica y parser del runtime:

- `Volt.directives.resolveValue(expression)` resuelve `shared:*` / `client:*` y fallbacks con `??`.
- `Volt.directives.resolveActive(expression)` evalua expresiones booleanas de condiciones (AST del runtime).

## Contrato minimo de directiva

Una directiva puede registrarse de dos formas.

### 1) Modo `sync` (imperativo)

La directiva implementa `sync(root)` y retorna `true` si muta el DOM y requiere reintentar dentro de una iteracion de estabilizacion.

Campos:

- `id` (string, requerido)
- `group` (string, opcional)
- `priority` (number, opcional)
- `stabilize` (bool, opcional)
- `maxIterations` (number, opcional)
- `sync(root)` (function, requerido)

### 2) Modo `names + parse + apply` (declarativo)

El engine encuentra elementos que tengan atributos equivalentes a `names`, llama `parse(...)` y ejecuta `apply(...)` por instruccion.

Campos:

- `id` (string, requerido)
- `group` (string, opcional)
- `priority` (number, opcional)
- `stabilize` (bool, opcional)
- `maxIterations` (number, opcional)
- `names` (array o function, requerido)
- `parse(context)` (function, requerido)
- `apply(context)` (function, requerido)

## Grupos y orden de ejecucion

El runtime procesa cada root en este orden:

1) `before-state` (estructurales, ej. `for`, `if`)
2) estados runtime (`loading`, `error`, `dirty`, `success`)
3) `after-state` (mutacion post estado, ej. `portal`, `html`)
4) `render` (store render, ej. `text`, `class`, `attr`, `style`, `show`, `focus`)

Si un estabilizador de `after-state` muta el DOM (por ejemplo `html`), el runtime re-ejecuta `before-state` antes de continuar.

## Ejemplos

### A) Directiva simple (modo `names + parse + apply`)

```js
window.Volt.directives.register({
  id: "demo.uppercase",
  group: "render",
  priority: 900,
  names: ["volt:uppercase", "volt-uppercase"],
  parse: function ({ directive }) {
    return {
      enabled: String(directive || "").trim() !== "off",
    };
  },
  apply: function ({ element, instruction }) {
    if (!instruction || instruction.enabled !== true) {
      return false;
    }

    element.textContent = String(element.textContent || "").toUpperCase();
    return false;
  },
});
```

### B) Directiva condicional basada en el parser del runtime

```js
window.Volt.directives.register({
  id: "demo.visible",
  group: "render",
  priority: 910,
  names: ["volt:demo-visible", "volt-demo-visible"],
  parse: function ({ directive }) {
    return {
      active: window.Volt.directives.resolveActive(directive),
    };
  },
  apply: function ({ element, instruction }) {
    element.hidden = !instruction.active;
    return false;
  },
});
```

## Compatibilidad

El runtime registra directivas builtin que replican el orden y comportamiento actual del pipeline:

- `for`, `if` en `before-state`
- `portal`, `html` en `after-state`
- `text`, `model.local`, `model.sync`, `bind`, `class`, `attr`, `style`, `show`, `focus` en `render`

Esto permite extender el runtime sin cambiar el contrato de las directivas existentes.

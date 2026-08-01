# Custom Effects (`window.Volt.effects`)

## Introduccion

El runtime aplica `effects` enviados por el backend dentro del pipeline de patch (por ejemplo `attribute.set`, `html.replace`, `navigate`, etc.).

Los custom effects permiten registrar handlers para nuevos `effect.type` sin modificar el `switch` interno del runtime.

## API publica

### `Volt.effects.register(type, handler, options?)`

Registra un handler para un `effect.type`.

- `type` (string): nombre del effect (por ejemplo `toast.show`)
- `handler(context)` (function): puede ser async
- `options.replace` (bool): permite reemplazar un handler existente
- `options.priority` (number): metadata para orden/listado

### `Volt.effects.unregister(type)`

Elimina el handler para ese `type`.

### `Volt.effects.list()`

Devuelve una lista con `{ type, priority }`.

## Contrato del handler

El handler recibe:

- `root` (Element|null)
- `effect` (object)
- `target` (Element|null) resuelto por el runtime (por `target`, `selector`, `id`, `data-volt-target`)

Retornos soportados:

- `true`: handled y `preventsHtmlFallback=true`
- `false` / `null` / `undefined`: no handled (el runtime continua con sus handlers builtin)
- `{ handled, preventsHtmlFallback, target, detail }`
  - `handled` (bool)
  - `preventsHtmlFallback` (bool, default true)
  - `target` (Element|null) para re-fijar el target en el hook `volt:after-effect`
  - `detail` (object|null) mergeado al `volt:after-effect`

## Ejemplo

```js
Volt.effects.register("toast.show", function ({ effect }) {
  const message =
    effect && typeof effect.message === "string" ? effect.message : "OK";

  window.dispatchEvent(
    new CustomEvent("toast:show", {
      detail: {
        message: message,
      },
    }),
  );

  return {
    handled: true,
    preventsHtmlFallback: false,
    detail: {
      emitted: "toast:show",
    },
  };
});
```

## Notas

- Si un custom effect retorna `handled=true`, el runtime no ejecuta el handler builtin del mismo `type`.
- El runtime emite `volt:before-effect` y `volt:after-effect` para todos los effects (builtin o custom).


# Plugins Frontend (`window.Volt.plugins` / `window.Volt.use`)

## Introduccion

El runtime permite instalar plugins frontend para extender el comportamiento sin modificar el core.

Un plugin es una funcion o un objeto con `install(context)` que se ejecuta una sola vez y puede:

- Registrar directivas (`Volt.directives.register(...)`)
- Registrar custom effects (`Volt.effects.register(...)`)
- Suscribirse a eventos (`Volt.on(...)`)

## API publica

### `Volt.plugins.use(plugin, options?)`

Instala un plugin. Retorna `true` si se instaló, `false` si se rechazó.

Alias:

```js
Volt.use(plugin, options);
```

### `Volt.plugins.list()`

Lista plugins instalados con:

- `id`
- `priority`
- `installedAt`

### `Volt.plugins.uninstall(id)`

Elimina un registro de plugin por id. No ejecuta teardown automático (el plugin debe manejar sus `unsubscribe()`/cleanup).

## Contrato del plugin

El plugin puede ser:

- Una funcion: `function (context) { ... }`
- Un objeto: `{ id: "mi-plugin", install: function (context) { ... } }`

El `context` incluye referencias a las APIs públicas:

- `Volt`
- `state`, `on`, `directives`, `effects`, `components`, `telemetry`, `busy`
- `options`

## Ejemplo

```js
Volt.use({
  id: "demo.plugin",
  install: function ({ on, effects }) {
    if (effects) {
      effects.register("toast.show", function ({ effect }) {
        console.log("Toast effect:", effect);
        return {
          handled: true,
          preventsHtmlFallback: false,
        };
      });
    }

    if (on) {
      const off = on("volt:request-finish", function (event) {
        console.log("Outcome:", (event.detail || {}).outcome);
      });

      return {
        uninstall: off,
      };
    }

    return null;
  },
});
```

## Notas

- El runtime no ejecuta `eval`.
- Un plugin debe limpiar sus listeners (`unsubscribe`) por su cuenta.
- Si quieres garantizar orden relativo entre plugins, usa `options.priority`.


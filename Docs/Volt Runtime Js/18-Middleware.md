# Middleware del Runtime (`window.Volt.middleware`)

## Introduccion

El runtime expone un pipeline de middleware para extender operaciones criticas sin tocar el core.

El middleware se ejecuta como una cadena estilo Koa:

- cada handler recibe `(context, next)`
- puede leer/modificar `context`
- puede terminar la cadena sin llamar a `next()`

## API publica

### `Volt.middleware.register(kind, handler, options?)`

Registra un middleware y retorna `true` si se registro.

- `kind`: `"runtime" | "navigation" | "hydration" | "effect"`
- `handler(context, next)` puede ser async
- `options.id` (string) id estable (si no se da, se autogenera)
- `options.priority` (number) orden ascendente (menor = antes)
- `options.replace` (bool) permite reemplazar uno existente con el mismo id

### `Volt.middleware.unregister(id)`

Desregistra un middleware por id.

### `Volt.middleware.list(kind?)`

Lista middlewares por tipo (default `"runtime"`), con:

- `id`
- `kind`
- `priority`
- `registeredAt`

## Kinds soportados y contexto

### 1) `runtime`

Envuelve operaciones runtime de alto nivel:

- action dispatch (`dispatchAction(...)`)
- navegación (`visit(...)`)

Contexto típico:

- `kind`
- `component`/`action` (para acciones)
- `url` (para navegación)
- `trigger` (descriptor)

### 2) `navigation`

Envuelve `visit(...)` específicamente. Útil para:

- forcing policies por URL
- telemetría adicional
- feature flags

Contexto típico:

- `url`
- `options`

### 3) `hydration`

Envuelve `withPreservedUiState(...)` (patch/hidratación de UI). Útil para:

- instrumentation de patch
- políticas de preservación

Contexto típico:

- `root`
- `meta`

### 4) `effect`

Envuelve `applyEffect(...)`. Útil para:

- interceptar/modificar effects
- bloquear types
- medir costos

Contexto típico:

- `root`
- `effect`
- `target` (puede ser seteado por middleware)

## Ejemplos

### A) Medir navegación

```js
Volt.middleware.register(
  "navigation",
  async function (ctx, next) {
    const startedAt = performance.now();
    const result = await next();
    const durationMs = Math.round(performance.now() - startedAt);
    console.log("visit", ctx.url, durationMs, "ms");
    return result;
  },
  { id: "demo.nav-metrics", priority: 100 },
);
```

### B) Bloquear un effect por policy

```js
Volt.middleware.register(
  "effect",
  async function (ctx, next) {
    if (ctx.effect && ctx.effect.type === "navigate") {
      return {
        handled: true,
        preventsHtmlFallback: false,
      };
    }

    return next();
  },
  { id: "demo.block-navigate-effect", priority: 50 },
);
```


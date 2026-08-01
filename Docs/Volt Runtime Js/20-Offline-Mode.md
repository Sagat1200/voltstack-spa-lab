# Resilience: Modo Offline (Snapshots + Queue)

## Introduccion

El runtime soporta un modo offline pragmático para reducir fallos en UX cuando el navegador pierde conectividad:

- persistencia de snapshots por componente (para diagnostico y continuidad)
- cache offline de navegacion (HTML/layout) para poder renderizar rutas ya visitadas sin red
- cola de acciones reactivas cuando `navigator.onLine === false`
- flush automatico al volver online
- estado global de conectividad (atributos + hooks)

## Estado de conectividad

El runtime refleja el estado en `document.documentElement`:

- `data-volt-offline="true|false"`
- `data-volt-online="true|false"`

Y emite hooks:

- `volt:offline`
- `volt:online`

## API pública

### `Volt.network`

- `Volt.network.online()` → bool
- `Volt.network.offline()` → bool
- `Volt.network.current()` → `{ online: bool }`

### `Volt.snapshots` (offline snapshots)

Snapshots se persisten por componente usando `localStorage` con la llave:

- `volt:offline:snapshot:<component>`

API:

- `Volt.snapshots.get(component)` → `{ component, snapshot, url, storedAt } | null`
- `Volt.snapshots.clear(component)` → bool

Notas:

- `snapshot` se almacena como string JSON (tal cual el atributo `data-volt-snapshot`).
- El runtime persiste snapshots al boot y tras cada patch que actualiza `data-volt-snapshot`.

### Cache offline de navegación (render offline)

Cuando una navegación (`Volt.visit(...)`) ocurre offline, el runtime intenta renderizar desde cache persistente (si existe) antes de degradar.

Persistencia:

- `localStorage` key: `volt:offline:navigation:<url-normalized>`
- el valor almacena el entry de cache de navegación (incluye `html`, `layout`, `navigationMode`, `documentContract`, etc.)
- el runtime mantiene un índice LRU y limita el cache a un número pequeño de entradas

Resultado:

- si existe cache para esa URL, el runtime aplica `applyDocumentPayload(...)` y navega en modo SPA sin request.
- si no existe cache, cae al modo degradado (hook `volt:navigate-offline` + retorno `{ offline: true }`).

### `Volt.queue` (queued actions)

Cuando el navegador está offline, `dispatchAction(...)` encola la action en memoria (no hace POST).

API:

- `Volt.queue.list()` → array de entries `{ component, action, params, updates, trigger, queuedAt }`
- `Volt.queue.flush(options?)` → Promise<{ flushed, remaining, offline?, busy? }>
- `Volt.queue.clear()` → `{ cleared }`

Flush:

- se ejecuta automaticamente al volver online (evento `online`)
- dispara hooks:
  - `volt:queue-flush-start`
  - `volt:queue-flush-finish`

Hook por encolado:

- `volt:action-queued` con `{ component, action, trigger, queueSize }`

## Modo degradado de navegación

Si `navigator.onLine === false` y no existe cache offline para la URL, el runtime bloquea la navegación SPA y emite:

- `volt:navigate-offline` con `{ url, trigger }`

`Volt.visit(...)` retorna `{ offline: true, url }` sin recargar el documento.


# Directiva Frontend: `volt:navigate`

## Introduccion

`volt:navigate` habilita navegacion SPA “server-driven” para enlaces (`<a>`) dentro de VoltStack.

Cuando el runtime intercepta el click, ejecuta una visita SPA:

- Hace un `GET` con headers `X-Requested-With: VoltStack` y `X-Volt-Navigate: true`.
- Aplica el payload reemplazando el documento (head/body) segun el contrato del runtime.
- Emite hooks de navegacion y request.
- Puede usar cache/prefetch para reducir latencia.

## Cuando usar

Usa `volt:navigate` cuando:

- Quieres transiciones SPA (sin recarga completa) entre rutas del mismo origen.
- Necesitas conservar fragments (`volt:preserve` / `volt:persist`) y estado UI entre navegaciones.
- Quieres control fino de cache/prefetch por link (`volt:cache`, `volt:prefetch`).

Evita `volt:navigate` cuando:

- El enlace apunta a otro origen (no se intercepta).
- Necesitas forzar una recarga dura por politica de documento o por un layout distinto (usa `volt:navigate="reload"`).
- Navegas a descargas (`download`) o a `target=_blank` (no se intercepta).

## Como usar

### 1) Sintaxis basica

```html
<a href="/perfil" volt:navigate>Perfil</a>
```

Alias equivalente:

```html
<a href="/perfil" volt-navigate>Perfil</a>
```

El runtime solo intercepta si:

- Es click izquierdo sin modificadores (no `ctrl/meta/shift/alt`).
- `target` es `_self` (o no existe) y no hay `download`.
- `href` no es `#...`.
- El URL es del mismo `origin`.
- El documento actual no esta en modo `reload-only` (`data-volt-document="reload"` o meta equivalente).

### 2) Politica de navegacion (auto / spa / reload)

Puedes forzar el modo de navegacion por link:

```html
<a href="/dashboard" volt:navigate="spa">Ir SPA</a>
<a href="/legacy" volt:navigate="reload">Forzar recarga</a>
```

Valores soportados por el parser:

- `spa` (tambien acepta `soft`, `client`)
- `reload` (tambien acepta `full-reload`, `hard-reload`, `document`)
- cualquier otro valor => `auto`

Notas:

- Si el modo resuelve a `reload`, el runtime no intercepta (deja navegar normal).
- Si el documento destino declara contrato `reload-only` (meta/atributo), el runtime hace fallback a `window.location.assign(...)`.

### 3) Replace vs Push (historial) y preservacion de scroll

Para reemplazar el historial (en vez de push):

```html
<a href="/busqueda?q=php" volt:navigate volt:replace>Actualizar query</a>
```

Para preservar scroll al navegar:

```html
<a href="/feed" volt:navigate volt:preserve-scroll>Volver al feed</a>
```

Aliases:

- `volt:replace` / `volt-replace`
- `volt:preserve-scroll` / `volt-preserve-scroll`

### 4) Prefetch (opcional)

El runtime puede prefetch de paginas antes del click. Se activa sobre links que cumplan:

- `a[volt:navigate]` o `a[volt:prefetch]` (y sus aliases)
- mismo origen
- politica de navegacion distinta de `reload`

Control por link:

```html
<a href="/productos" volt:navigate volt:prefetch="hover">Productos</a>
<a href="/checkout" volt:navigate volt:prefetch="none">Checkout (sin prefetch)</a>
```

Tokens soportados por `volt:prefetch`:

- Desactivar: `none`, `off`, `false`
- Permitir todo: `auto`, `all`, `eager`, `true`
- Intent: `hover`, `focus`, `intent`
- Viewport: `viewport`, `visible`
- Idle/heuristico: `idle`, `heuristic`

Fuentes reales que dispara el runtime:

- Intent: `pointerenter` y `focusin`.
- Viewport: `IntersectionObserver` cuando el enlace entra a viewport (con margen).
- Idle/heuristico: selecciona un candidato cercano al viewport en un idle callback.

### 5) Cache de navegacion (opcional)

Puedes controlar cache por link con `volt:cache`:

```html
<a href="/catalogo" volt:navigate volt:cache="ttl=15s">Catalogo</a>
<a href="/catalogo" volt:navigate volt:cache="reload">Bypass cache</a>
<a href="/catalogo" volt:navigate volt:cache="no-store">No guardar</a>
<a href="/catalogo" volt:navigate volt:cache="invalidate">Invalida antes</a>
```

Modos:

- `default`: usa cache si existe.
- `reload`: invalida (por URL) y fuerza fetch de red.
- `invalidate`: invalida (por URL) antes de navegar; luego permite almacenar de nuevo.
- `no-store`: no lee ni escribe cache.

TTL:

- `ttl=...` o `max-age=...` (tambien `ttl:...`, `max-age:...`)
- Acepta `ms` o `s` (por ejemplo `1500ms`, `15s`)

Invalidacion por evento:

```js
document.dispatchEvent(
  new CustomEvent("volt:navigation-cache-invalidate", {
    detail: { url: "/catalogo", reason: "manual" },
  }),
);
```

Si el `detail.url` no existe, el runtime limpia todo el cache de navegacion.

### 6) Meta tags y atributos de documento (contratos)

Ademas del link, el runtime considera configuracion declarada en el documento:

- Modo de navegacion:
  - `<meta name="volt-navigation-mode" content="spa|reload|auto">`
  - `<meta name="volt:navigation-mode" content="spa|reload|auto">`
  - `data-volt-navigation-mode="spa|reload|auto"` en `body`
- Contrato de documento (si se permite SPA):
  - `<meta name="volt-document" content="spa|reload">`
  - `<meta name="volt:document" content="spa|reload">`
  - `data-volt-document="spa|reload"` en `body` o `html`
- Cache control del documento:
  - `<meta name="volt-cache-control" content="...">`
  - `<meta name="volt:navigation-cache" content="...">`
- Control de fragments en navegacion:
  - `<meta name="volt-fragment-control" content="preserve|reset">`
  - `<meta name="volt:fragment-cache" content="preserve|reset">`

## Ejemplo de uso

```html
<nav class="flex gap-3">
  <a
    href="/dashboard"
    volt:navigate="spa"
    volt:prefetch="hover"
    volt:cache="ttl=5s"
  >
    Dashboard
  </a>

  <a href="/legacy" volt:navigate="reload">
    Legacy (full reload)
  </a>

  <a href="/feed" volt:navigate volt:preserve-scroll>
    Feed (preserve scroll)
  </a>

  <a href="/busqueda?q=volt" volt:navigate volt:replace>
    Buscar (replace)
  </a>
</nav>

<script>
  document.addEventListener("volt:before-navigate", function (event) {
    console.log("before navigate", event.detail);
  });
  document.addEventListener("volt:navigated", function (event) {
    console.log("navigated", event.detail);
  });
</script>
```

## Escenario de uso

Escenario: “navegacion SPA con prefetch y cache”.

- El usuario pasa el mouse por el enlace (prefetch por `hover`).
- El runtime hace prefetch del HTML y lo guarda en cache (si `volt:cache` no es `no-store`).
- Al hacer click, `volt:navigate` intercepta y ejecuta `visit(...)`.
- Si el cache tiene entry valida, la navegacion puede ser cache-hit y el patch aplica mas rapido.
- Se emiten `volt:before-navigate` y `volt:navigated` con metadata (url, finalUrl, modo, fragments preservados, etc.).

Checklist de validacion manual:

- Confirma que `ctrl+click` o `target=_blank` NO se interceptan.
- Verifica que `volt:navigate="reload"` fuerza recarga completa.
- Activa prefetch (`volt:prefetch="hover"`) y revisa eventos de cache (`volt:cache-hit/miss/store`).
- Prueba `volt:cache="invalidate"` y confirma que invalida antes de navegar.

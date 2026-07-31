# Directiva Frontend: `volt:html`

## Introduccion

`volt:html` actualiza el contenido HTML interno de un elemento usando un valor derivado de `window.Volt.state`.

Es una directiva **DOM <- state**: el runtime evalua la expresion, formatea el valor a una representacion HTML y asigna `element.innerHTML` cuando detecta un cambio.

## Cuando usar

Usa `volt:html` cuando:

- Necesitas renderizar un fragmento HTML controlado por el sistema (ej. snippet server-driven, contenido editorial curado, un slot de marketing).
- Quieres inyectar markup pequeño como parte de un panel reactivo sin reenviar el HTML completo del componente.
- Necesitas renderizar una version “rich” (con links/strong/em) de un valor que vive en state.

Evita `volt:html` cuando:

- El contenido proviene de usuarios o fuentes no confiables (riesgo de XSS). En ese caso usa `volt:text` o sanitiza en backend antes de exponerlo al state.
- Solo necesitas texto plano (usa `volt:text`).
- Tu caso requiere reconciliacion/patch fino de nodos internos (usa el render server-driven / patch normal del runtime en lugar de mutar `innerHTML`).

## Como usar

### 1) Sintaxis

Formato base:

```html
<div volt:html="expresion"></div>
```

Alias equivalente:

```html
<div volt-html="expresion"></div>
```

### 2) Expresion (origen)

La expresion usa el resolvedor base del runtime (mismo que `volt:text`):

- Referencia a state: `client:mi.path` o `shared:mi.path`
- Literales: `"texto"`, `'texto'`, `true`, `false`, `null`, `123`, `3.14`
- Fallback por coalescencia: `??`

Ejemplos:

```html
<div volt:html="shared:ui.noticeHtml"></div>
<div volt:html="shared:ui.noticeHtml ?? '<strong>Sin aviso</strong>'"></div>
```

### 3) Semantica de aplicacion

- Si la expresion no existe o no resuelve, el runtime aplica `""` (limpia el contenido).
- El runtime aplica `innerHTML` solo cuando el valor formateado cambia (evita re-asignar el mismo HTML).
- Formato del valor:
  - `string`: se usa tal cual
  - `number` / `boolean`: se convierte a string
  - `object`: se serializa a JSON si es posible, o se convierte a string

## Ejemplo de uso

Markup:

```html
<section>
  <h2>Noticias</h2>
  <div
    style="border:1px solid rgba(148,163,184,0.24);border-radius:12px;padding:12px;"
    volt:html="shared:news.bannerHtml ?? '<em>Sin anuncios</em>'"
  ></div>
</section>
```

JS (demo):

```js
window.Volt.state.set(
  "news.bannerHtml",
  "<strong>Oferta</strong>: <a href='/pricing'>ver planes</a>",
  { scope: "shared", action: "demo:html" },
);
```

## Escenario de uso

Escenario: aviso global controlado por el backend (o un feature flag) que aparece en multiples rutas SPA sin re-render pesado.

Objetivo:

- Si `shared:ui.noticeHtml` existe, se muestra tal cual.
- Si se elimina, el area se limpia sin romper layout.
- El contenido se actualiza en caliente en la misma sesion.

Markup:

```html
<div
  role="status"
  aria-live="polite"
  volt:html="shared:ui.noticeHtml ?? ''"
></div>
```

Flujo:

```js
window.Volt.state.set("ui.noticeHtml", "<span>Modo mantenimiento en 10 min</span>", {
  scope: "shared",
  action: "scenario:notice",
});

window.setTimeout(function () {
  window.Volt.state.set("ui.noticeHtml", "<span>Mantenimiento activo</span>", {
    scope: "shared",
    action: "scenario:notice",
  });
}, 1000);

window.setTimeout(function () {
  window.Volt.state.delete("ui.noticeHtml", {
    scope: "shared",
    action: "scenario:notice",
  });
}, 2000);
```

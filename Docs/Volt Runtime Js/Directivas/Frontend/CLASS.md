# Directiva Frontend: `volt:class`

## Introduccion

`volt:class` aplica o revierte clases CSS en un elemento en función de una condición evaluada contra `window.Volt.state`.

Es una directiva **DOM <- state**: no muta el store, solo hace `classList.toggle(...)` de una o varias clases y restaura el estado inicial cuando la regla deja de aplicar.

Soporta **múltiples reglas** en el mismo atributo, separadas por `|`.

## Cuando usar

Usa `volt:class` cuando:

- Necesitas resaltar, atenuar o marcar visualmente elementos según estado (ej. `highlight`, `error`, `selected`).
- Quieres activar/desactivar estilos Tailwind sin re-render (solo toggles de clase).
- Necesitas reglas compuestas (client vs shared) con prioridad declarativa.

Evita `volt:class` cuando:

- Necesitas estilos inline dinámicos (usa `volt:style`).
- Necesitas atributos (usa `volt:attr`) o propiedades como `value` (usa `volt:bind` / `volt:model.*`).
- El markup debe cambiar (usa `volt:if` / `volt:show`).

## Como usar

### 1) Sintaxis

Una regla tiene forma:

```text
<condicion> -> <clases>
```

Ejemplo:

```html
<div volt:class="client:ui.highlight -> ring-2 ring-cyan-400 shadow-lg"></div>
```

Alias equivalente:

```html
<div volt-class="client:ui.highlight -> ring-2 ring-cyan-400 shadow-lg"></div>
```

### 2) Múltiples reglas

Puedes componer reglas con `|`:

```html
<div
  volt:class="client:ui.highlightClientCard && !shared:ui.lockSharedAction -> ring-4 ring-cyan-400 shadow-lg shadow-cyan-950/40 | shared:ui.highlightSharedCard -> -translate-y-1 shadow-xl shadow-fuchsia-950/30"
></div>
```

### 3) Condición (expresión)

La condición usa el parser del runtime, soportando:

- `client:path` y `shared:path`
- `!` negación
- `&&` y `||`
- comparaciones: `===`, `!==`, `==`, `!=`, `>`, `<`, `>=`, `<=`
- paréntesis `( ... )`
- literales (`true`, `false`, `null`, números y strings)

### 4) Lista de clases y restore

El lado derecho es una lista de clases separadas por espacios.

Reglas:

- Cada clase se “baselinea” al primer sync: el runtime guarda si estaba presente originalmente.
- Cuando la regla está activa: fuerza `toggle(class, true)`.
- Cuando la regla está inactiva: restaura al valor inicial (si existía, la agrega; si no, la remueve).

## Ejemplo de uso

```html
<article
  class="p-4 rounded-xl border border-slate-700 bg-slate-950"
  volt:class="client:ui.highlight -> ring-2 ring-cyan-400 shadow-lg"
>
  Card
</article>
```

## Escenario de uso

Escenario: highlight con prioridad “shared domina” para un card:

```html
<article
  class="p-4 rounded-xl border transition border-slate-700 bg-slate-950"
  volt:class="client:ui.highlightClientCard && !shared:ui.lockSharedAction -> ring-4 ring-cyan-400 shadow-lg shadow-cyan-950/40 | shared:ui.highlightSharedCard -> -translate-y-1 shadow-xl shadow-fuchsia-950/30"
>
  Card con reglas multiples
</article>
```

Validación manual:

- Activa `client:ui.highlightClientCard` y confirma que aparecen las clases de la primera regla.
- Activa `shared:ui.highlightSharedCard` y confirma que se aplican clases de la segunda regla.
- Activa `shared:ui.lockSharedAction` y confirma que la regla client deja de aplicar (por el `!shared:...`).
- Desactiva todo y confirma restauración del baseline (sin rings/shadows extra).

Rutas demo (spa-lab):

- `/runtimeAdvancedDirectives` (incluye ejemplos de `volt:class` con múltiples reglas)

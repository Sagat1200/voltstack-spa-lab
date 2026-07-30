# Directiva Frontend: `volt:bind`

## Introduccion

`volt:bind` refleja valores desde el state del runtime hacia el DOM (**DOM <- state**) asignando directamente **propiedades DOM** (y, cuando aplica, el **atributo reflejado**) en el elemento destino.

Es una directiva unidireccional: **no escribe al store**. Su rol es mantener el DOM sincronizado cuando cambia `window.Volt.state` o cuando el runtime rehidrata/resincroniza el arbol por patch o navegacion SPA.

## Cuando usar

Usa `volt:bind` cuando necesites un binding simple y uniforme para propiedades DOM como:

- `value` en inputs (mirror de state a campo)
- `checked` / `disabled` / `hidden` / `required` / `readonly` (UI gating / toggles)
- `href` / `src` / `title` / `placeholder` / `id` / `name` (atributos comunes que existen como propiedad DOM)

Evita `volt:bind` cuando:

- Necesitas escribir state desde el DOM (usar `volt:model.local`, `volt:model.sync` o `volt:on`)
- Necesitas manipular `class`, `style` o atributos condicionales complejos (usar `volt:class`, `volt:style`, `volt:attr`)
- Necesitas actualizar `textContent` o HTML declarativo (usar `volt:text` o `volt:html`)

## Como usar

### 1) Sintaxis

Formato base:

```html
volt:bind:propiedad="expresion"
```

Aliases equivalentes (mismo comportamiento):

```html
volt-bind-propiedad="expresion"
data-volt-bind-propiedad="expresion"
```

La `propiedad` se normaliza:

- Se acepta `kebab-case` y se convierte a `camelCase` (`aria-label` -> `ariaLabel`)
- `readonly` se normaliza a `readOnly`

### 2) Expresion (origen)

El origen reutiliza el resolvedor base de expresiones del runtime:

- Referencia a state: `client:mi.path` o `shared:mi.path`
- Literales: `"texto"`, `'texto'`, `true`, `false`, `null`, `123`, `3.14`
- Fallback por coalescencia: `??` (el runtime toma el primer segmento usable)

Ejemplos:

```html
<input volt:bind:value="client:draft.note">
<input volt:bind:placeholder="shared:ui.placeholder ?? 'Escribe aqui'">
<a volt:bind:title="shared:links.title ?? 'Abrir'"></a>
```

### 3) Semantica de aplicacion

- Si la propiedad existe en el elemento (`propiedad in element`), el runtime asigna `element[propiedad]`.
- Si no existe pero hay un nombre de atributo reflejable, el runtime cae a `setAttribute(...)`.
- Propiedades booleanas aplican coercion estandar:
  - booleanas tratadas como tales: `checked`, `disabled`, `hidden`, `required`, `readOnly`, `selected`
  - cuando son `true`, el runtime mantiene consistente el atributo reflejado; cuando son `false`, lo elimina.
- Si la ref no existe, o el valor resuelto es `null`/`undefined`, el runtime restaura un baseline seguro:
  - `value` vuelve a `""` o al valor inicial SSR del nodo (si existia)
  - booleanas vuelven a `false`
  - propiedades textuales restauran el atributo original si existia o lo eliminan si no habia baseline

## Ejemplo de uso

Ejemplo completo (mirror de state hacia DOM) usando la API publica `window.Volt.state`:

## Escenario de uso

Escenario: formulario con UX reactiva sin roundtrip, donde un boton debe bloquearse mientras hay una operacion en curso, y un enlace/preview deben reflejar el estado seleccionado.

Objetivo:

- El usuario escribe una nota (la nota vive en `client scope` por URL).
- Un toggle global habilita/deshabilita el modulo (vive en `shared scope`).
- Un flag `busy` global bloquea el boton (vive en `shared scope`).
- Un link y un preview reflejan el destino seleccionado (viven en `shared scope`).

Markup:

```html
<section>
  <header>
    <label>
      Nota
      <input
        type="text"
        value="SSR baseline"
        volt:bind:value="client:bind.note ?? ''"
      >
    </label>
  </header>

  <div>
    <label>
      <input type="checkbox" volt:bind:checked="shared:bind.enabled">
      Modulo habilitado
    </label>
  </div>

  <div>
    <button type="button" volt:bind:disabled="shared:bind.busy">
      Guardar
    </button>
  </div>

  <footer>
    <a
      volt:bind:href="shared:bind.linkUrl"
      volt:bind:title="shared:bind.linkTitle ?? 'Abrir'"
    >
      Ver detalle
    </a>
  </footer>
</section>
```

Flujo (sin backend, solo para mostrar el contrato DOM <- state):

```js
window.Volt.state.set("bind.note", "Borrador local", { scope: "client", action: "scenario:init" });
window.Volt.state.set("bind.enabled", true, { scope: "shared", action: "scenario:init" });
window.Volt.state.set("bind.linkUrl", "/runtimeEvents", { scope: "shared", action: "scenario:init" });
window.Volt.state.set("bind.linkTitle", "Ir a runtimeEvents", { scope: "shared", action: "scenario:init" });

window.Volt.state.set("bind.busy", true, { scope: "shared", action: "scenario:busy" });
window.setTimeout(function () {
  window.Volt.state.set("bind.busy", false, { scope: "shared", action: "scenario:busy" });
}, 800);
```

```html
<div>
  <label>
    Nota:
    <input
      type="text"
      value="SSR value baseline"
      volt:bind:value="client:bind.note ?? ''"
    >
  </label>

  <label>
    <input type="checkbox" volt:bind:checked="shared:bind.enabled">
    Habilitado
  </label>

  <button type="button" volt:bind:disabled="shared:bind.busy">
    Guardar
  </button>

  <a
    volt:bind:href="shared:bind.linkUrl"
    volt:bind:title="shared:bind.linkTitle ?? 'Ir al detalle'"
  >
    Ver detalle
  </a>
</div>
```

```js
window.Volt.state.set("bind.note", "Hola", { scope: "client", action: "demo" });
window.Volt.state.set("bind.enabled", true, { scope: "shared", action: "demo" });
window.Volt.state.set("bind.busy", false, { scope: "shared", action: "demo" });
window.Volt.state.set("bind.linkUrl", "/runtimeEvents", { scope: "shared", action: "demo" });
window.Volt.state.set("bind.linkTitle", "Abrir runtimeEvents", { scope: "shared", action: "demo" });
```

Rutas demo (spa-lab):

- `/runtimeBind` (origen)
- `/runtimeBindAlt` (destino para validar baseline y navegacion SPA)

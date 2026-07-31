# Directiva Frontend: `volt:model.local`

## Introduccion

`volt:model.local` crea un binding bidireccional entre un control del DOM (`input`, `textarea`, `select`) y `window.Volt.state`, sin roundtrip al backend.

Contrato:

- **DOM -> state**: en eventos `input` y `change`, el runtime lee el valor del control y lo escribe en el store (si el valor cambia).
- **state -> DOM**: durante sincronizaciones del runtime, el control se actualiza para reflejar el state (y si el state no existe, se restaura al baseline SSR del control).

Este binding es local al runtime: no dispara actions HTTP. Para enviar actualizaciones al backend usa `volt:model.sync`.

## Cuando usar

Usa `volt:model.local` cuando:

- Necesitas drafts locales (buscadores, filtros, formularios multistep) sin tocar backend.
- Quieres que el valor viva en `client scope` (por URL) o `shared scope` (persistente entre rutas SPA).
- Quieres que cambios desde otras directivas (`volt:on` con `state:set`) o desde código (API `window.Volt.state`) se reflejen automáticamente en el control.

Evita `volt:model.local` cuando:

- Requieres persistir inmediatamente en backend o disparar validaciones server-side (usa `volt:model.sync`, `volt:submit` o acciones).
- Necesitas un binding a elementos que no son controles (`div`, `span`, etc.). En ese caso usa `volt:bind`, `volt:text`, `volt:attr`, etc.

## Como usar

### 1) Sintaxis

```html
<input volt:model.local="client:ruta.al.state">
```

Aliases equivalentes:

```html
<input volt-model-local="client:ruta.al.state">
<input data-volt-model-local="client:ruta.al.state">
```

### 2) Expresion (destino)

La expresion debe ser una referencia simple a state:

- `client:foo.bar`
- `shared:foo.bar`

Reglas:

- Solo se acepta la forma `scope:path` donde `path` usa `A-Za-z0-9_.-`
- No se permiten expresiones complejas ni negación en `volt:model.local`

### 3) Tipos soportados y serializacion

Soportado:

- `textarea` / `select`: usa `element.value` (string)
- `input[type=checkbox]`: usa `element.checked` (boolean)
- `input[type=radio]`: solo escribe cuando está marcado; el valor escrito es `element.value` (string)
- otros `input`: usa `element.value` (string)

### 4) Baseline SSR y restauracion

En la primera sincronizacion, el runtime captura un baseline del control (valor SSR inicial):

- `value` (si existe)
- `checked` (si existe)
- `selected` (si existe)

Si el state no existe para la ruta configurada, el runtime restaura el control al baseline en lugar de “inventar” un valor.

## Ejemplo de uso

```html
<section>
  <label>
    Nota (client):
    <input type="text" value="SSR local note" volt:model.local="client:draft.note">
  </label>

  <label>
    Body (client):
    <textarea rows="4" volt:model.local="client:draft.body">SSR local body</textarea>
  </label>

  <label>
    <input type="checkbox" checked volt:model.local="shared:ui.enabled">
    Enabled (shared)
  </label>

  <label>
    Categoria (shared):
    <select volt:model.local="shared:filters.category">
      <option value="backlog">Backlog</option>
      <option value="review" selected>Review</option>
      <option value="done">Done</option>
    </select>
  </label>
</section>
```

## Escenario de uso

Escenario: filtros persistentes entre rutas SPA, donde:

- `shared:filters.category` debe conservarse al navegar a otra ruta.
- `client:draft.note` debe reiniciarse al cambiar de URL (scope cliente derivado de la URL).

Markup (ruta A):

```html
<select volt:model.local="shared:filters.category">
  <option value="backlog">Backlog</option>
  <option value="review">Review</option>
  <option value="done">Done</option>
</select>

<input type="text" volt:model.local="client:draft.note" value="SSR note">

<a href="/runtimeModelLocalAlt" volt:navigate>Ir a ruta alterna</a>
```

Validacion:

- Cambia el `select` y navega con SPA: el `select` debe volver con el mismo valor (shared).
- Escribe en el `input` y navega con SPA: el `input` debe volver al baseline SSR en la ruta alterna (client por URL).

Rutas demo (spa-lab):

- `/runtimeModelLocal`
- `/runtimeModelLocalAlt`

# Directiva Frontend: `volt:attr`

## Introduccion

`volt:attr` aplica o revierte atributos HTML en un elemento en función de una condición evaluada contra `window.Volt.state`.

Es una directiva **DOM <- state**: no cambia el store, solo muta atributos del nodo y los restaura a su estado inicial cuando la condición deja de cumplirse.

La directiva soporta **múltiples reglas** en el mismo atributo, separadas por `|`.

## Cuando usar

Usa `volt:attr` cuando:

- Necesitas habilitar/deshabilitar controles vía `disabled`, `aria-disabled`, `aria-hidden`, `data-*`, `title`, etc.
- Quieres exponer estado operacional en atributos (`data-lock=...`, `data-state=...`) para CSS, QA o telemetría.
- Requieres toggles que no justifican re-render ni envío de HTML (solo atributos).

Evita `volt:attr` cuando:

- Necesitas clases dinámicas (usa `volt:class`) o estilos dinámicos (usa `volt:style`).
- Necesitas setear una propiedad DOM (p.ej. `value`, `checked`) y no solo un atributo (usa `volt:bind` / `volt:model.*`).
- Necesitas texto/HTML (usa `volt:text` / `volt:html`).

## Como usar

### 1) Sintaxis

Una regla tiene forma:

```text
<condicion> -> <atributos>
```

Y se declara así:

```html
<button volt:attr="condicion -> disabled=disabled, aria-disabled=true"></button>
```

Alias equivalente:

```html
<button volt-attr="condicion -> disabled=disabled, aria-disabled=true"></button>
```

### 2) Múltiples reglas

Puedes componer varias reglas en un solo atributo con `|`:

```html
<button
  volt:attr="client:ui.lockClientAction -> disabled=disabled | shared:ui.lockSharedAction -> disabled=disabled, title=Bloqueado"
></button>
```

El runtime evalúa cada regla y aplica/revierte sus atributos según corresponda.

### 3) Condición (expresión)

La condición usa el parser de condiciones del runtime. Soporta:

- `client:path` y `shared:path`
- `!` negación
- `&&` y `||`
- comparaciones: `===`, `!==`, `==`, `!=`, `>`, `<`, `>=`, `<=`
- paréntesis `( ... )`
- literales (`true`, `false`, `null`, números y strings)

Ejemplo:

```html
<button volt:attr="client:counter >= 2 && !shared:ui.locked -> disabled=disabled"></button>
```

### 4) Lista de atributos

El lado derecho se define como lista separada por comas:

```text
disabled=disabled, aria-disabled=true, data-lock=shared, title=Bloqueado
```

Notas:

- Si un token no tiene `=`, el runtime lo interpreta como `name` con valor `""` (atributo vacío).
- Cuando una regla se desactiva, el runtime restaura el valor inicial del atributo (o lo elimina si no existía).

## Ejemplo de uso

```html
<button
  type="button"
  title="Disponible"
  volt:attr="client:ui.lockClientAction -> disabled=disabled, aria-disabled=true, data-lock=client-only"
>
  Guardar
</button>
```

## Escenario de uso

Escenario: control de locking a dos niveles (client vs shared), con prioridad de bloqueo compartido.

```html
<button
  type="button"
  title="Disponible"
  volt:attr="client:ui.lockClientAction && !shared:ui.lockSharedAction -> disabled=disabled, aria-disabled=true, data-lock=client-only | shared:ui.lockSharedAction -> disabled=disabled, aria-disabled=true, data-lock=shared, title=Bloqueado por shared"
>
  Boton con atributos dinamicos
</button>
```

Validación manual:

- Activa `client:ui.lockClientAction` y confirma `disabled` + `data-lock=client-only`.
- Activa `shared:ui.lockSharedAction` y confirma que domina la segunda regla, cambiando `data-lock=shared` y `title`.
- Desactiva ambas banderas y confirma restauración del `title` original y eliminación de `disabled` si no existía.

Rutas demo (spa-lab):

- `/runtimeAdvancedDirectives` (incluye ejemplos de `volt:attr` con múltiples reglas)

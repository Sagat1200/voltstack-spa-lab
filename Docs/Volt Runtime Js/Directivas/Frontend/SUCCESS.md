# Directiva Frontend: `volt:success`

## Introduccion

`volt:success` representa el estado “el ultimo request termino correctamente” dentro de un root reactivo (`data-volt-root="true"`).

Cuando una action (`volt:click`, `volt:submit`) termina OK, el runtime marca el root:

- `data-volt-success="true"`
- `data-volt-request-status="success"`

Opcionalmente, el runtime tambien guarda contexto del success:

- `data-volt-success-action` (action que termino OK)
- `data-volt-success-target` (target asociado al trigger)

`volt:success` es una familia de directivas de **estado runtime**: se usa para mostrar/ocultar UI y para togglear clases o atributos mientras el root esta en success.

## Cuando usar

Usa `volt:success` cuando:

- Necesitas un banner “Guardado con exito” despues de una action.
- Quieres aplicar estilos positivos (verde) a un bloque tras un request exitoso.
- Quieres scope por action/target para no mostrar success global por cualquier request.
- Quieres auto-limpiar el indicador con `volt:success.timeout` para evitar UI pegado.

Evita `volt:success` cuando:

- El “success” debe persistir como parte del dominio. En ese caso, modelalo en `shared`/`client`.
- Requieres mostrar un mensaje dinamico proveniente del backend: el runtime no expone `data-volt-success-message` como parte del contrato; conviene renderizar ese mensaje en `shared:*` y usar `volt:text`/`volt:if`.

## Como usar

### 1) Sintaxis basica (show / hide)

`volt:success` por defecto se comporta como “show cuando success”:

```html
<div volt:success>Listo.</div>
```

Para lo inverso:

```html
<div volt:success.hide>Sin cambios recientes</div>
```

Aliases equivalentes:

```html
<div volt-success>...</div>
<div volt-success-hide>...</div>
```

### 2) Clases y atributos condicionales

Puedes togglear clases:

```html
<section
  class="rounded-lg border p-4"
  volt:success.class="border-emerald-500 ring-2 ring-emerald-200"
>
  ...
</section>
```

Y atributos:

```html
<button
  type="button"
  volt:success.attr="aria-live=polite"
>
  ...
</button>
```

Notas:

- `volt:success.class` espera una lista de clases separadas por espacios.
- `volt:success.attr` acepta lista separada por comas (`name=value, name=value`).
- Cuando el root deja de estar en success, el runtime restaura el baseline inicial de clases/atributos.

### 3) Scope por action / target (opcional)

`volt:success` se puede filtrar por action o por target.

Reglas de scope:

- `volt:success.action="a1, a2"` filtra por `data-volt-success-action`.
- `volt:success.target="t1, t2"` filtra por `data-volt-success-target`.

Ejemplo (solo success de una action):

```html
<div volt:success.action="saveProfile">Guardado con exito.</div>
```

Ejemplo (solo success de un target):

```html
<div volt:success.target="profile.email">Email guardado.</div>
```

Shorthand:

```html
<div volt:success="saveProfile">...</div>
```

Comportamiento del shorthand:

- Si el runtime tiene `data-volt-success-action`, el shorthand se interpreta como lista de actions.
- Si no hay action en el contexto, el shorthand se interpreta como lista de targets.

### 4) Auto-limpieza por timeout (opcional)

Puedes pedir que el success se limpie automaticamente despues de un timeout:

```html
<span volt:success.timeout="1500ms" hidden></span>
```

Reglas:

- El runtime toma el menor timeout activo (si hay varios).
- Al cumplirse el timeout, el runtime limpia el estado (`data-volt-success="false"`).

Nota: el timeout tambien puede provenir de una policy registrada por effects del runtime (tipo `runtime.policy`) que aplique al mismo state/scope.

### 5) Min-duration (opcional)

Para evitar que el indicador de success parpadee (muy corto), puedes forzar una duracion minima:

```html
<span volt:success.min-duration="240ms" hidden></span>
```

Reglas:

- El runtime toma el menor min-duration activo (si hay varios).
- Si el success se intenta limpiar antes del minimo, difiere el clear hasta cumplirlo.

### 6) Cuando se limpia `success`

El runtime limpia `success` tipicamente en estos casos:

- Antes de disparar una nueva action (para evitar “success viejo” durante un nuevo request).
- Cuando el usuario edita un input (para no mostrar success mientras hay cambios).
- Por `volt:success.timeout` (auto-clear).

Ademas, emite hooks:

- `volt:success` al activarse (transition `false -> true`)
- `volt:success-cleared` al limpiarse

## Ejemplo de uso

```html
<form class="space-y-3" volt:submit="saveProfile">
  <label class="block">
    <span class="text-sm">Email</span>
    <input
      type="email"
      name="email"
      data-volt-target="profile.email"
      volt:model="profile.email"
      class="border rounded px-2 py-1 w-full"
    >
  </label>

  <button
    type="submit"
    class="px-4 py-2 rounded-lg border"
    volt:loading.attr="disabled=disabled, aria-disabled=true"
  >
    Guardar
  </button>

  <div
    class="text-sm"
    style="color:#059669;"
    volt:success.action="saveProfile"
  >
    Guardado con exito.
  </div>

  <span volt:success.timeout="2s" hidden></span>
  <span volt:success.min-duration="240ms" hidden></span>
</form>
```

## Escenario de uso

Escenario: “toast de success por action con auto-clear”.

- El usuario hace submit (action `saveProfile`).
- Si la respuesta es OK, el root pasa a `data-volt-success="true"` y `data-volt-success-action="saveProfile"`.
- El banner se muestra (scope por action).
- Con `volt:success.timeout="2s"`, el runtime limpia el success automaticamente y el banner desaparece.
- Si el usuario edita un input antes del timeout, el runtime limpia el success inmediatamente.

Checklist de validacion manual:

- Ejecuta una action exitosa y confirma que se activa `volt:success`.
- Si usas `volt:success.action="saveProfile"`, confirma que no se activa con otras actions.
- Con `volt:success.timeout`, confirma que el estado se limpia solo.
- Edita un input tras el success y confirma que el banner se limpia al instante.

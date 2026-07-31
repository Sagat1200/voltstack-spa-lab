# Directiva Frontend: `volt:loading`

## Introduccion

`volt:loading` representa el estado “hay un request en curso” dentro de un root reactivo (`data-volt-root="true"`).

Cuando el runtime entra en loading, marca el root con:

- `data-volt-loading="true"`
- `data-volt-request-status="loading"`

Y tambien puede anexar metadata del request:

- `data-volt-loading-action` (action activa)
- `data-volt-loading-target` (target del trigger)
- `data-volt-request-id` (id del request)

`volt:loading` es una familia de directivas de **estado runtime** para mostrar/ocultar UI, y para togglear clases o atributos mientras el root esta en loading.

## Cuando usar

Usa `volt:loading` cuando:

- Necesitas spinners, skeletons o indicadores de “procesando...” mientras corre una action.
- Quieres deshabilitar botones/inputs para evitar doble submit o clicks repetidos.
- Necesitas distinguir loading por action/target (scope) para cargar solo una parte del UI.
- Quieres evitar flicker con `delay` y mejorar UX manteniendo el indicador visible un minimo con `min-duration`.

Evita `volt:loading` cuando:

- Quieres manejar loading como parte del dominio (por ejemplo, “cargando catalogo” persistente). En ese caso modelalo en `shared`/`client`.

## Como usar

### 1) Sintaxis basica (show / hide)

`volt:loading` por defecto es “show cuando loading”:

```html
<div volt:loading>Procesando...</div>
```

Para lo inverso:

```html
<div volt:loading.hide>Listo</div>
```

Aliases equivalentes:

```html
<div volt-loading>...</div>
<div volt-loading-hide>...</div>
```

### 2) Clases y atributos condicionales

Togglear clases en loading:

```html
<button
  type="button"
  class="px-4 py-2 rounded-lg"
  volt:click="saveProfile"
  volt:loading.class="opacity-60 pointer-events-none"
>
  Guardar
</button>
```

Togglear atributos en loading:

```html
<button
  type="button"
  volt:click="saveProfile"
  volt:loading.attr="disabled=disabled, aria-disabled=true, aria-busy=true"
>
  Guardar
</button>
```

Notas:

- `volt:loading.class` espera una lista de clases separadas por espacios.
- `volt:loading.attr` acepta lista separada por comas (`name=value, name=value`).
- Al salir de loading, el runtime restaura el baseline inicial de clases/atributos.

### 3) Scope por action / target (opcional)

`volt:loading` se puede filtrar por action o por target.

Reglas de scope:

- `volt:loading.action="a1, a2"` filtra por `data-volt-loading-action`.
- `volt:loading.target="t1, t2"` filtra por `data-volt-loading-target`.

Ejemplo (solo loading de una action):

```html
<div volt:loading.action="saveProfile">Guardando...</div>
```

Ejemplo (solo loading de un target):

```html
<div volt:loading.target="profile.email">Validando email...</div>
```

Shorthand:

```html
<div volt:loading="saveProfile">...</div>
```

Comportamiento del shorthand:

- Si el runtime tiene `data-volt-loading-action` (lo normal durante una action), el shorthand se interpreta como lista de actions.
- Si no hay action en el contexto, el shorthand se interpreta como lista de targets.

### 4) Delay (opcional)

Para evitar flicker cuando el request es muy rapido, puedes retrasar el encendido del loading:

```html
<span volt:loading.delay="120ms" hidden></span>
```

Reglas:

- El runtime toma el menor delay activo (si hay varios).
- Si el request termina antes del delay, el loading nunca se enciende.

Nota: el delay tambien puede venir de una policy registrada por effects del runtime (tipo `runtime.policy`) que aplique al mismo state/scope.

### 5) Min-duration (opcional)

Para evitar que el indicador aparezca y desaparezca demasiado rapido, puedes forzar una duracion minima:

```html
<span volt:loading.min-duration="320ms" hidden></span>
```

Reglas:

- El runtime toma el menor min-duration activo (si hay varios).
- Si el request termina antes del minimo, la salida de loading se difiere hasta cumplirlo.

## Ejemplo de uso

```html
<form class="space-y-3">
  <label class="block">
    <span class="text-sm">Email</span>
    <input
      type="email"
      name="profile.email"
      data-volt-target="profile.email"
      volt:model.local="shared:profile.email"
      class="px-2 py-1 w-full rounded border"
    >
  </label>

  <div class="text-sm" volt:loading.action="saveProfile">
    Guardando cambios...
  </div>

  <button
    type="button"
    data-volt-target="profile.email"
    volt:click="saveProfile"
    class="px-4 py-2 rounded-lg border"
    volt:loading.attr="disabled=disabled, aria-disabled=true, aria-busy=true"
    volt:loading.class="opacity-60 pointer-events-none"
  >
    Guardar
  </button>

  <span volt:loading.delay="120ms" hidden></span>
  <span volt:loading.min-duration="320ms" hidden></span>
</form>
```

## Escenario de uso

Escenario: “spinner sin flicker + bloqueo de UI”.

- El usuario hace click en “Guardar”.
- Si el request tarda mas de `120ms`, se activa loading y aparece el mensaje “Guardando...”.
- Mientras loading esta activo, el boton se deshabilita y aplica estilos de bloqueo.
- Si el request termina muy rapido, el delay evita mostrar el spinner.
- Si el request termina justo despues de encender, `min-duration` evita parpadeo manteniendolo visible el minimo configurado.

Checklist de validacion manual:

- Prueba una action muy rapida y confirma que con `volt:loading.delay` el indicador no aparece.
- Prueba una action de 300-600ms y confirma que el indicador aparece y se mantiene (min-duration).
- Si usas `volt:loading.action="saveProfile"`, confirma que no se activa con otras actions.
- Si usas `volt:loading.target="profile.email"`, confirma que se activa solo cuando el target coincide.

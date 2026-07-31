# Directiva Frontend: `volt:error`

## Introduccion

`volt:error` representa el estado “el ultimo request termino en error” dentro de un root reactivo (`data-volt-root="true"`).

Cuando una action (`volt:click`, `volt:submit`) o un request del runtime falla, el runtime marca el root:

- `data-volt-error="true"`
- `data-volt-request-status="error"`

Ademas, el runtime puede anexar metadata del error al root:

- `data-volt-error-action` (action que fallo)
- `data-volt-error-target` (target asociado al trigger)
- `data-volt-error-message` (mensaje normalizado del error)

`volt:error` es una familia de directivas de **estado runtime**: se usa para mostrar/ocultar UI y para togglear clases o atributos mientras el root esta en error.

## Cuando usar

Usa `volt:error` cuando:

- Necesitas un banner/alerta global que se muestre si una action falla.
- Quieres estilos de error (por ejemplo `ring-red-500`) en un bloque o formulario al fallar un request.
- Quieres deshabilitar UI mientras existe un error activo hasta que el usuario reintente o se limpie.
- Quieres limitar el UI de error a una action o un target especifico (scope).

Evita `volt:error` cuando:

- Requieres “errores por campo” como parte del dominio; en ese caso conviene modelarlos en `shared`/`client` (o renderizarlos server-side) y usar `volt:text`/`volt:class`/`volt:attr` contra ese estado.

## Como usar

### 1) Sintaxis basica (show / hide)

`volt:error` por defecto se comporta como “show cuando error”:

```html
<div volt:error>Ocurrio un error, intenta de nuevo.</div>
```

Para lo inverso:

```html
<div volt:error.hide>Sin errores</div>
```

Aliases equivalentes:

```html
<div volt-error>...</div>
<div volt-error-hide>...</div>
```

### 2) Clases y atributos condicionales

Puedes togglear clases:

```html
<section
  class="p-4 rounded-lg border"
  volt:error.class="border-red-500 ring-2 ring-red-200"
>
  ...
</section>
```

Y atributos:

```html
<button
  type="button"
  volt:click="saveProfile"
  volt:error.attr="disabled=disabled, aria-disabled=true"
>
  Reintentar
</button>
```

Notas:

- `volt:error.class` espera una lista de clases separadas por espacios.
- `volt:error.attr` acepta lista separada por comas (`name=value, name=value`).
- Al limpiar el error, el runtime restaura el baseline inicial de clases/atributos.

### 3) Scope por action / target (opcional)

`volt:error` se puede filtrar por action o por target.

Reglas de scope:

- `volt:error.action="a1, a2"` filtra por `data-volt-error-action`.
- `volt:error.target="t1, t2"` filtra por `data-volt-error-target`.

Ejemplo (solo errores de una action):

```html
<div volt:error.action="saveProfile" class="text-sm" style="color:#ef4444;">
  Fallo el guardado del perfil.
</div>
```

Ejemplo (solo errores de un target):

```html
<div volt:error.target="profile.email" class="text-sm" style="color:#ef4444;">
  Error relacionado al email.
</div>
```

Shorthand:

```html
<div volt:error="saveProfile">...</div>
```

Comportamiento del shorthand:

- Si el runtime tiene `data-volt-error-action` (lo normal cuando falla una action), el shorthand se interpreta como lista de actions.
- Si no hay action en el contexto, el shorthand se interpreta como lista de targets.

### 4) Auto-limpieza por timeout (opcional)

Puedes pedir que el error se limpie automaticamente despues de un timeout:

```html
<span volt:error.timeout="2500ms" hidden></span>
```

Reglas:

- El runtime toma el menor timeout activo (si hay varios).
- Al cumplirse el timeout, el runtime limpia el error (`data-volt-error="false"`).

Nota: el timeout tambien puede provenir de una policy registrada por effects del runtime (tipo `runtime.policy`) que aplique al mismo state/scope.

## Ejemplo de uso

```html
<form>
  <label>
    Email
    <input
      type="email"
      name="profile.email"
      data-volt-target="profile.email"
      volt:model.local="shared:profile.email"
    >
  </label>

  <div
    volt:error.action="saveProfile"
    class="mt-2 text-sm"
    style="color:#ef4444;"
  >
    No se pudo guardar. Revisa tu conexion e intenta de nuevo.
  </div>

  <button
    type="button"
    data-volt-target="profile.email"
    volt:click="saveProfile"
    class="px-4 py-2 mt-3 rounded-lg"
    volt:error.action="saveProfile"
    volt:error.attr="disabled=disabled, aria-disabled=true"
  >
    Guardar
  </button>

  <span volt:error.timeout="3s" hidden></span>
</form>
```

## Escenario de uso

Escenario: “banner de error por action con auto-clear”.

- El usuario hace click en “Guardar” (`volt:click="saveProfile"`).
- Si el request falla, el root pasa a `data-volt-error="true"` y se guarda `data-volt-error-action="saveProfile"`.
- El UI muestra el banner (scope por action) y aplica estilos/atributos relacionados.
- Con `volt:error.timeout="3s"`, el runtime limpia el error automaticamente y el UI vuelve a estado normal.

Checklist de validacion manual:

- Fuerza un error (por ejemplo desconectando red o provocando un 500) y confirma que aparece el banner con `volt:error`.
- Si usas `volt:error.action="saveProfile"`, confirma que no se activa en errores de otras actions.
- Si usas `volt:error.target="profile.email"`, confirma que solo se activa cuando el target coincide.
- Con `volt:error.timeout`, confirma que el error se limpia solo tras el tiempo configurado.

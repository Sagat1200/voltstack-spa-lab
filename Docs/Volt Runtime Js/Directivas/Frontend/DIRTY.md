# Directiva Frontend: `volt:dirty`

## Introduccion

`volt:dirty` representa el estado “hay cambios locales sin confirmar” dentro de un root reactivo (`data-volt-root="true"`).

Cuando el runtime detecta un cambio en inputs (por ejemplo vía `volt:model.local` o `volt:model.sync`) marca el root como dirty:

- `data-volt-dirty="true"`
- `data-volt-request-status="dirty"`

Cuando se dispara una action (`volt:click`, `volt:submit`) y finaliza con éxito, el runtime limpia el estado:

- `data-volt-dirty="false"`

`volt:dirty` es una familia de directivas de **estado runtime**, no de store: se usa para mostrar/ocultar UI, togglear clases o atributos cuando el root está dirty.

## Cuando usar

Usa `volt:dirty` cuando:

- Necesitas mostrar un indicador “Cambios sin guardar”.
- Quieres habilitar/deshabilitar el botón “Guardar” solo si hay cambios.
- Quieres avisar al usuario antes de navegar (UI de warning) cuando haya borrador sin persistir.

Evita `volt:dirty` cuando:

- El estado “dirty” debe ser parte del dominio (persistir o sincronizar como dato), en cuyo caso conviene modelarlo en `shared`/`client`.
- La pantalla no tiene inputs ni cambios locales (el estado se quedará en `idle`).

## Como usar

### 1) Sintaxis basica (show / hide)

`volt:dirty` por defecto se comporta como “show cuando dirty”:

```html
<div volt:dirty>Cambios sin guardar</div>
```

Para lo inverso:

```html
<div volt:dirty.hide>Sin cambios pendientes</div>
```

Aliases equivalentes:

```html
<div volt-dirty>...</div>
<div volt-dirty-hide>...</div>
```

### 2) Clases y atributos condicionales

Puedes togglear clases:

```html
<button
  type="button"
  class="px-4 py-2 rounded-lg"
  volt:dirty.class="ring-2 ring-amber-400"
>
  Guardar
</button>
```

Y atributos:

```html
<button
  type="button"
  volt:dirty.attr="disabled=disabled, aria-disabled=true, title=Guarda antes de continuar"
>
  Guardar
</button>
```

Notas:

- `volt:dirty.class` espera una lista de clases separadas por espacios.
- `volt:dirty.attr` acepta lista separada por comas (`name=value, name=value`).
- Cuando el root vuelve a clean, el runtime restaura el baseline inicial de clases/atributos.

### 3) Scope por target (opcional)

El runtime asocia el estado dirty a un `target` cuando proviene de un input. El `target` se resuelve así:

1) `volt:model` (si existe)
2) `data-volt-target`
3) `name`
4) `id`

Puedes filtrar la directiva a uno o más targets usando el shorthand:

```html
<div volt:dirty="profile.email">Cambios en email</div>
<div volt:dirty="profile.email, profile.name">Cambios en perfil</div>
```

Si el dirty no tiene target (o no coincide), esa directiva no se activa.

### 4) Debounce para el estado dirty (opcional)

Para evitar flicker en escenarios de escritura rápida, puedes debounciar el “marcado dirty”:

```html
<span volt:dirty.debounce="220ms" hidden></span>
```

Reglas:

- El runtime toma el menor debounce activo (si hay varios).
- Afecta el momento en que el root pasa a `data-volt-dirty="true"`.
- Limpiar a clean (por éxito de request) ocurre inmediato.

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

  <div volt:dirty class="text-sm" style="color:#f59e0b;">
    Hay cambios sin guardar.
  </div>

  <button
    type="button"
    volt:click="saveProfile"
    volt:dirty.attr="disabled=disabled, aria-disabled=true"
  >
    Guardar
  </button>
</form>
```

## Escenario de uso

Escenario: “borrador con guardado explícito”.

- El usuario edita `profile.email` (dirty=true).
- El UI muestra “Cambios sin guardar” y habilita el botón Guardar.
- Tras `saveProfile` (action exitosa), el runtime limpia dirty (dirty=false) y el UI vuelve a estado idle.

Checklist de validación manual:

- Edita un input con `volt:model.local` y confirma que aparece un indicador con `volt:dirty`.
- Ejecuta una action “Guardar” y confirma que desaparece el indicador (vuelve a clean).
- Si usas `volt:dirty="profile.email"`, confirma que sólo se activa cuando el target coincide.

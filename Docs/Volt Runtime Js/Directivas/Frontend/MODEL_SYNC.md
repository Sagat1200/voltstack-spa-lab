# Directiva Frontend: `volt:model.sync`

## Introduccion

`volt:model.sync` crea un binding bidireccional entre un control del DOM (`input`, `textarea`, `select`) y `window.Volt.state`, y además agenda una sincronizacion con el backend mediante una accion interna del runtime.

Contrato (MVP):

- **DOM -> state (optimista)**: en `input` y `change`, el runtime escribe el valor en `window.Volt.state` si cambió.
- **Backend sync (debounced)**: el runtime agenda un POST a `/_volt/action` con `action="__volt_sync__"` usando debounce fijo (220ms).
- **state -> DOM**: el control se resincroniza desde el state (igual que en `volt:model.local`) durante las pasadas del runtime.

En el backend, `__volt_sync__` aplica `updates` al componente y retorna efectos/patch; no ejecuta una acción del componente.

## Cuando usar

Usa `volt:model.sync` cuando:

- Necesitas UX optimista (UI responde instantáneamente) pero con confirmacion backend (persistencia/validacion).
- Quieres un “autosave” con debounce para inputs o formularios de edición.
- Necesitas que `shared scope` sobreviva navegaciones SPA, pero a la vez sincronizar a propiedades del componente en servidor.

Evita `volt:model.sync` cuando:

- El flujo requiere un submit explícito o un request por botón (usa `volt:submit` o `volt:click`).
- La sincronizacion debe ser por lote, por seccion o con reglas complejas (usa `data-volt-state-sync` en el root + acción explícita, o un endpoint dedicado).
- No necesitas backend (usa `volt:model.local`).

## Como usar

### 1) Sintaxis

```html
<input volt:model.sync="client:ruta.al.state">
```

Aliases equivalentes:

```html
<input volt-model-sync="client:ruta.al.state">
<input data-volt-model-sync="client:ruta.al.state">
```

### 2) Expresion (destino en state)

La expresion debe ser una referencia simple a state:

- `client:foo.bar`
- `shared:foo.bar`

### 3) Payload hacia backend (`updates`)

El runtime necesita decidir qué campo mandar en `updates`. Hay dos caminos:

#### A) Fallback por `name`

Si el elemento tiene `name="campo"`, el runtime manda:

- `updates[campo] = <valor del control>`

Ejemplo:

```html
<input
  name="serverTitle"
  volt:model.sync="client:sync.title"
>
```

#### B) Mapeo explícito con `data-volt-state-sync`

Puedes mapear valores desde state hacia `params` o `updates` con reglas:

```html
data-volt-state-sync="client:sync.alias->updates.serverAliasMirror"
```

Formato de cada regla:

```text
<scope>:<path> -> (params|updates).<field>
```

- `scope`: `client` o `shared`
- `path`: `A-Za-z0-9_.-`
- `field`: `A-Za-z_` seguido de `A-Za-z0-9_`
- Se pueden declarar varias reglas separadas por coma.

Notas:

- Las reglas se pueden declarar en el root del componente o en el propio elemento.
- Si existen reglas de `data-volt-state-sync`, el runtime dispara la sync aunque el `name` no exista; el mapeo real se aplica justo antes de enviar el request.

### 4) Estados de request (loading/success/error)

Como el runtime usa la acción interna `__volt_sync__`, puedes observar estados con:

```html
<div volt:loading="__volt_sync__">Sincronizando...</div>
<div volt:success="__volt_sync__">Confirmado.</div>
<div volt:error="__volt_sync__">Falló.</div>
```

## Ejemplo de uso

```html
<section>
  <label>
    Titulo:
    <input
      type="text"
      name="serverTitle"
      value="SSR sync title"
      volt:model.sync="client:sync.title"
    >
  </label>

  <label>
    <input
      type="checkbox"
      name="serverEnabled"
      checked
      volt:model.sync="shared:sync.enabled"
    >
    Enabled
  </label>

  <div volt:loading="__volt_sync__" volt:loading.delay="80ms">
    Sincronizando...
  </div>
</section>
```

## Escenario de uso

Escenario: editor con autosave optimista, donde el usuario escribe en un input y:

- El preview (basado en `window.Volt.state`) se actualiza inmediato.
- El backend confirma y actualiza propiedades SSR visibles (o valida y devuelve errores).

Markup:

```html
<input
  type="text"
  name="serverTitle"
  volt:model.sync="client:sync.title"
  value="SSR sync title"
>

<strong>Preview runtime:</strong>
<span volt:text="client:sync.title ?? '(sin titulo)'">(sin titulo)</span>

<strong>Mirror backend:</strong>
<span data-volt-target="sync-server-title">SSR sync title</span>

<div volt:loading="__volt_sync__" volt:loading.delay="80ms" volt:loading.min-duration="240ms">
  Guardando...
</div>
<div volt:success="__volt_sync__">Guardado.</div>
<div volt:error="__volt_sync__" volt:error.timeout="3s">Error al guardar.</div>
```

Validacion:

- Teclea rápido: debe salir un solo request por ventana de debounce.
- La UI (preview) cambia al instante (optimista).
- El mirror backend solo cambia tras confirmación.

Rutas demo (spa-lab):

- `/runtimeModelSync`
- `/runtimeModelSyncAlt`

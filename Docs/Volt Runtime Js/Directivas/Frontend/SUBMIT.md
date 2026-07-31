# Directiva Frontend: `volt:submit`

## Introduccion

`volt:submit` intercepta el submit nativo de un formulario (`<form>`) y lo convierte en una action del runtime (request `POST` al endpoint de actions).

Cuando un `<form>` tiene `volt:submit`, el runtime:

- Intercepta el evento `submit` a nivel `document`.
- Hace `event.preventDefault()` (no hay submit tradicional del browser).
- Construye `params` desde `FormData(form)`.
- Construye `updates` desde inputs dentro del root que declaran `volt:model`.
- Ejecuta `dispatchAction(root, action, params, updates, trigger)`.

## Cuando usar

Usa `volt:submit` cuando:

- Quieres un flujo “formulario” pero con el pipeline del runtime (loading/error/success/dirty, hooks, patch/effects).
- Necesitas enviar campos por `name` (FormData) y/o por `volt:model` (updates) en una sola action.
- Quieres evitar JS manual para construir payloads de forms.

Evita `volt:submit` cuando:

- Necesitas un submit nativo (descarga de archivo por POST, navegación fuera del runtime, integración con terceros que dependen del submit real).
- Estas enviando archivos: el runtime ignora entradas `File/Blob` al construir `params`.

## Como usar

### 1) Sintaxis basica

```html
<form volt:submit="saveProfile">
  ...
</form>
```

Alias equivalente:

```html
<form volt-submit="saveProfile">...</form>
```

Reglas:

- El valor del atributo es el nombre de la action.
- El submit siempre se intercepta si el form tiene `volt:submit`/`volt-submit` y pertenece a un root (`data-volt-root="true"`).

### 2) `params` (FormData) vs `updates` (volt:model)

`volt:submit` envia dos “bolsas” de datos:

- `params`: se arma desde `FormData(form)` y toma los inputs por su `name` (solo strings).
- `updates`: se arma desde todos los inputs del root con `volt:model` (checkbox => boolean, resto => string).

Esto permite dos estilos:

- HTML form tradicional (solo `name`): usa `params`.
- Form “identificado” por keys estables: usa `volt:model` y consume `updates`.

Nota: puedes mezclar ambos.

### 3) Selective state sync (opcional)

Si declaras reglas `volt:state-sync` en el root o en el form, el runtime puede copiar valores desde `shared:*`/`client:*` hacia `params.*` o `updates.*` justo antes de enviar.

Formato:

```html
<form
  volt:submit="saveProfile"
  volt:state-sync="shared:profile.id -> params.id, client:csrf -> params.csrf"
>
  ...
</form>
```

### 4) Integracion con estados runtime y hooks

Al usar `volt:submit`, el request entra al pipeline comun de actions:

- `volt:request-start`
- `volt:request-error`
- `volt:request-finish`
- (y potencialmente `volt:request-abort`, `volt:request-stale`)

Esto a su vez alimenta los estados `loading/error/success/dirty` del root.

## Ejemplo de uso

```html
<form volt:submit="saveProfile" class="space-y-3">
  <label class="block">
    <span class="text-sm">Email</span>
    <input
      type="email"
      name="email"
      volt:model="profile.email"
      class="border rounded px-2 py-1 w-full"
    >
  </label>

  <input type="hidden" name="source" value="settings">

  <button
    type="submit"
    class="px-4 py-2 rounded-lg border"
    volt:loading.attr="disabled=disabled, aria-disabled=true, aria-busy=true"
  >
    Guardar
  </button>

  <div class="text-sm" style="color:#ef4444;" volt:error.action="saveProfile">
    Ocurrio un error al guardar.
  </div>
</form>

<script>
  document.addEventListener("volt:request-start", function (event) {
    if (event.detail && event.detail.type === "action") {
      console.log("action start", event.detail);
    }
  });
</script>
```

## Escenario de uso

Escenario: “formulario con params + updates”.

- El usuario edita el email.
- Al hacer submit, el runtime:
  - arma `params` desde `FormData` (por ejemplo `source=settings`)
  - arma `updates` desde `volt:model` (por ejemplo `profile.email=a@b.com`)
- La action `saveProfile` procesa ambos.
- El UI puede reaccionar con `volt:loading`, `volt:error`, `volt:success` y `volt:dirty`.

Checklist de validacion manual:

- Confirma que el submit nativo no ocurre (no recarga la pagina).
- Verifica que inputs con `name` llegan en `params` y los de `volt:model` llegan en `updates`.
- Prueba un checkbox con `volt:model` y confirma que el update es boolean.
- Agrega un `<input type="file">` y confirma que no se incluye en `params` (solo strings).

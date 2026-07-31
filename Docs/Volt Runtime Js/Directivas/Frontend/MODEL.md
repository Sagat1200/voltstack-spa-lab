# Directiva Frontend: `volt:model`

## Introduccion

`volt:model` es la directiva base para identificar campos de formulario dentro de un root reactivo.

Su rol principal es habilitar dos comportamientos del runtime:

- **Recoleccion de updates**: cuando disparas una action (`volt:click`, `volt:submit`), el runtime colecta todos los inputs dentro del root que tengan `volt:model` y envia sus valores como `updates`.
- **Identidad de campo (target)**: el runtime usa `volt:model` como el primer candidato para resolver el `target` de un input (lo que alimenta estados como `dirty`/`success` y mecanismos de preservacion de foco).

`volt:model` no enlaza el input con `shared:`/`client:`. Para binding reactivo al store del runtime usa:

- `volt:model.local`
- `volt:model.sync`

## Cuando usar

Usa `volt:model` cuando:

- Quieres enviar valores de inputs como `updates` al backend al ejecutar una action.
- Necesitas controlar `target` de un campo de forma explicita (para `volt:dirty="..."`, `volt:success.target="..."`, o para preservacion de foco).
- Quieres formularios “clásicos” (inputs controlados por el DOM) pero integrados al request pipeline del runtime.

Evita `volt:model` cuando:

- Necesitas reactividad inmediata en el frontend (usa `volt:model.local` o `volt:model.sync`).
- Quieres renderizar/derivar UI con expresiones de store (`volt:text`, `volt:if`, `volt:for`), ya que `volt:model` no escribe en el store runtime.

## Como usar

### 1) Sintaxis

```html
<input type="text" volt:model="profile.email">
```

Alias equivalente:

```html
<input type="text" volt-model="profile.email">
```

Reglas:

- El valor de `volt:model` es la “clave” del campo en el objeto `updates`.
- Si el input es `checkbox`, el runtime envia `true/false`.
- En otros tipos de input, envia `element.value` como string.

### 2) Integracion con actions (updates)

Al ejecutar una action, el runtime arma un objeto:

```json
{
  "profile.email": "a@b.com",
  "profile.newsletter": true
}
```

y lo incluye como `updates` en el request.

Esto permite que la action reciba datos sin tener que leer un `<form>` tradicional, y sin requerir `volt:model.local`.

### 3) Target de campo (dirty / success / focus)

Cuando el runtime necesita resolver el target de un input, usa esta prioridad:

1) `volt:model`
2) `data-volt-target`
3) `name`
4) `id`

Por eso, `volt:model` es una forma estable de definir el target aunque el `name` o `id` cambien.

## Ejemplo de uso

```html
<form class="space-y-3">
  <label class="block">
    <span class="text-sm">Email</span>
    <input
      type="email"
      volt:model="profile.email"
      class="px-2 py-1 w-full rounded border"
    >
  </label>

  <label class="flex gap-2 items-center">
    <input type="checkbox" volt:model="profile.newsletter">
    <span class="text-sm">Recibir noticias</span>
  </label>

  <div class="text-sm" style="color:#f59e0b;" volt:dirty="profile.email">
    Cambios sin guardar en email.
  </div>

  <button
    type="button"
    class="px-4 py-2 rounded-lg border"
    volt:click="saveProfile"
  >
    Guardar
  </button>
</form>
```

## Escenario de uso

Escenario: “formulario clasico con updates + dirty por target”.

- El usuario edita el email: el runtime marca el root como dirty y el target se resuelve a `profile.email` (desde `volt:model`).
- Al hacer click en “Guardar”, el runtime colecta `updates` desde todos los inputs con `volt:model` y ejecuta la action `saveProfile`.
- Si la action responde OK, el runtime limpia el estado dirty y el indicador desaparece.

Checklist de validacion manual:

- Confirma que al ejecutar la action se envian `updates` con las claves de `volt:model`.
- Verifica que un checkbox con `volt:model` envia boolean.
- Usa `volt:dirty="profile.email"` y confirma que se activa con cambios en el input que declara `volt:model="profile.email"`.

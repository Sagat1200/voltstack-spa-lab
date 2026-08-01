# Resilience: Retry de Requests (Navigation + Action)

## Introduccion

El runtime soporta retry para requests de:

- navegacion SPA (GET / navigation payload)
- acciones reactivas (POST `/_volt/action`)

El retry es opt-in via atributos en el trigger o el root, y emite eventos para telemetria y UI.

## Contrato (atributos)

Los mismos atributos aplican para navegacion y actions:

- `data-volt-request-retry` (o `volt:request-retry`): habilita retry (boolean) o define intentos (number)
- `data-volt-request-retry-delay` (o `volt:request-retry-delay`): delay entre intentos (`180ms`, `1s`, etc.)

Aliases soportados por el runtime:

- `data-volt-retry`, `volt-retry`, `volt:retry`
- `data-volt-retry-delay`, `volt-retry-delay`, `volt:retry-delay`

Ejemplos:

```html
<button
  type="button"
  volt:click="saveProfile"
  data-volt-request-retry="1"
  data-volt-request-retry-delay="180ms"
>
  Guardar
</button>
```

```html
<div data-volt-root="true" data-volt-request-retry="true">
  ...
</div>
```

## Cuando se reintenta

El runtime reintenta cuando:

- `timeout`
- `network-error`
- `http-error` con status permitido: `408, 425, 429, 500, 502, 503, 504`

## Eventos emitidos

Durante retry el runtime emite:

- `volt:request-retry` con `detail` que incluye:
  - `type` (`navigation` o `action`)
  - `retryAttempt`, `retryAttempts`, `retryDelayMs`
  - `errorKind`, `message`, `status` (cuando aplica)

El flujo normal conserva:

- `volt:request-start`
- `volt:request-finish`
- `volt:request-error` (solo cuando ya no se puede reintentar)

## Notas

- El retry no cambia el `requestId` interno de la accion; solo reintenta la misma operacion hasta agotar el policy.
- El retry es compatible con `AbortController`: si el request es superseded o abortado, no reintenta.


# Directiva Frontend: `volt:focus`

## Introduccion

`volt:focus` permite que el runtime asigne foco a un elemento del DOM de forma reactiva, a partir de una expresion basada en `window.Volt.state`.

Es una directiva **DOM <- state**: el runtime observa la condicion y, cuando detecta la transicion **false -> true**, intenta enfocar el elemento (sin hacer scroll si el navegador lo soporta).

## Cuando usar

Usa `volt:focus` cuando:

- Necesitas guiar al usuario hacia el siguiente campo (wizard, stepper, busqueda, filtros).
- Quieres reenfocar un control despues de un patch reactivo o una navegacion SPA.
- Quieres mover foco sin escribir JavaScript inline y sin acoplar el markup a listeners.

Evita `volt:focus` cuando:

- El elemento se crea/destruye por condiciones (paneles, modales, alerts). En ese caso suele ser mejor `volt:autofocus.when` para enfocar cuando el bloque aparece.
- Necesitas manejar un trigger distinto a una transicion de state (usar `volt:on` + `dispatch` o un bridge JS).
- El foco depende de reglas complejas de accesibilidad que requieren logica externa (resolverlo en un controlador/bridge).

## Como usar

### 1) Sintaxis

Formato base:

```html
volt:focus="condicion"
```

Alias equivalente:

```html
volt-focus="condicion"
```

### 2) Condicion (expresion)

La expresion es una condicion evaluada contra el state del runtime. Soporta:

- Referencias a state: `client:ruta.al.estado`, `shared:ruta.al.estado`
- Negacion: `!client:flag`
- Composicion: `&&`, `||`
- Comparaciones: `===`, `!==`, `==`, `!=`, `>`, `<`, `>=`, `<=`
- Parentesis: `( ... )`
- Literales: `true`, `false`, `null`, numeros y strings `"..."` / `'...'`

Ejemplos:

```html
<input volt:focus="client:focus.title">
<textarea volt:focus="shared:focus.open === true && client:focus.step === 2"></textarea>
<input volt:focus="!(shared:modal.open === true)"></input>
```

### 3) Semantica de enfoque

- El runtime intenta enfocar solo cuando la condicion pasa a `true` y antes estaba en `false`.
- Si el elemento ya esta enfocado (`document.activeElement === element`), no hace nada.
- Si el elemento no es enfocable o no es visible, el runtime ignora el enfoque:
  - `hidden === true`, `disabled`, `aria-disabled="true"`, `display:none`, `visibility:hidden`, sin rects visibles, etc.
- Si multiples elementos califican en la misma pasada, el runtime elige un solo candidato. En la implementacion actual, el ultimo elemento encontrado (orden DOM) gana.

Nota: para forzar un reenfoque sobre el mismo target, resetea la bandera a `false` y luego vuelvela a `true` (provoca otra transicion).

## Ejemplo de uso

Markup:

```html
<div>
  <button
    type="button"
    volt:on="click -> state:set client:focus.search = false | click -> state:set client:focus.search = true"
  >
    Enfocar buscador
  </button>

  <input
    type="search"
    placeholder="Buscar..."
    volt:focus="client:focus.search"
  >
</div>
```

## Escenario de uso

Escenario: wizard con validacion, donde al intentar avanzar y hay errores, el runtime debe enviar el foco al primer campo con error para reducir friccion.

Estrategia:

- El backend (o una accion frontend) setea banderas en `client:focus.*` segun el error.
- El UI solo declara targets con `volt:focus`.
- Para repetidos intentos, se usa el patron `false -> true`.

Markup:

```html
<form>
  <input
    name="email"
    type="email"
    placeholder="Email"
    volt:focus="client:focus.email"
  >

  <input
    name="password"
    type="password"
    placeholder="Password"
    volt:focus="client:focus.password"
  >

  <button
    type="button"
    volt:on="click -> state:set client:focus.email = false | click -> state:set client:focus.password = false | click -> state:set client:focus.email = true"
  >
    Continuar (simula error en email)
  </button>
</form>
```

## Rutas demo (spa-lab)

- `/runtimeFocus`
- `/runtimeFocusAlt`

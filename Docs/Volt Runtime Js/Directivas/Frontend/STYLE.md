# Directiva Frontend: `volt:style`

## Introduccion

`volt:style` aplica estilos inline (CSS) a un elemento en funcion de reglas condicionales evaluadas contra el estado `client`/`shared`.

Es una directiva de store: el runtime parsea una o mas reglas con formato:

```
<condicion> -> <estilos>
```

y en cada sincronizacion:

- Evalua la condicion (AST).
- Si es verdadera, aplica los estilos.
- Si es falsa, restaura el “baseline” original de cada propiedad que esa regla haya tocado.

## Cuando usar

Usa `volt:style` cuando:

- Necesitas togglear estilos que no son faciles de expresar como clases (por ejemplo `display`, `opacity`, `pointer-events`).
- Quieres aplicar valores dinamicos simples sin escribir JS (segun flags en `shared:*` o `client:*`).
- Quieres reglas multiples con prioridad explicita por orden (varias condiciones en un solo atributo).

Evita `volt:style` cuando:

- Puedes resolverlo con utilidades CSS/clases (por ejemplo Tailwind) usando `volt:class` (suele ser mas mantenible).
- Requieres estilos con logica compleja o calculos (no hay eval de JS, solo expresion booleana de store).
- Tu objetivo es ocultar/mostrar: usa `volt:show`/`volt:if` en vez de `display:none` manual, salvo que tengas un caso particular.

## Como usar

### 1) Sintaxis

Regla unica:

```html
<div volt:style="shared:disabled === true -> opacity: 0.5; pointer-events: none;">
  ...
</div>
```

Reglas multiples (separadas por `|`):

```html
<div
  volt:style="
    shared:variant === 'danger' -> background: #fee2e2; color: #991b1b;
    | shared:variant === 'success' -> background: #dcfce7; color: #166534;
  "
>
  ...
</div>
```

Alias equivalente:

```html
<div volt-style="shared:disabled === true -> opacity: 0.5;"></div>
```

### 2) Formato de estilos (lado derecho)

El lado derecho se parsea como lista de declaraciones CSS separadas por `;`:

- `prop: value;`
- `prop2: value2;`

Reglas:

- Cada declaracion debe tener `:` y tanto `prop` como `value` deben ser no vacios.
- El runtime usa `element.style.setProperty(prop, value)` (sin `!important` automatico).

### 3) Expresiones soportadas (lado izquierdo)

La condicion soporta la misma gramatica que `volt:class`/`volt:attr`/`volt:show`:

- Referencias: `shared:ruta` / `client:ruta` (por ejemplo `shared:user.role`)
- Operadores: `!`, `&&`, `||`
- Comparadores: `===`, `!==`, `==`, `!=`, `>`, `<`, `>=`, `<=`
- Parentesis: `( ... )`
- Literales: strings (`'...'` / `"..."`), numeros, `true/false`, `null`

Notas:

- Si la expresion es invalida, la regla se evalua como `false` (no aplica).
- `|` se interpreta a nivel top-level (no rompe `||` ni strings/paréntesis).

### 4) Baseline y restauracion

Cuando una regla aplica estilos por primera vez, el runtime guarda el baseline original de cada propiedad tocada (valor y prioridad).

Al dejar de aplicar:

- Si la propiedad originalmente no existia, el runtime hace `removeProperty(prop)`.
- Si existia, restaura `setProperty(prop, valorOriginal, prioridadOriginal)`.

Importante: el baseline se guarda por regla (signature). Si tienes varias reglas afectando la misma propiedad, cada una mantiene su baseline por separado.

## Ejemplo de uso

```html
<section class="p-4 rounded-lg border">
  <div
    class="px-3 py-2 rounded"
    volt:style="
      shared:status === 'loading' -> opacity: 0.6; pointer-events: none;
      | shared:status === 'error' -> background: #fee2e2; color: #991b1b;
    "
  >
    Estado: {{ shared.status }}
  </div>
</section>
```

## Escenario de uso

Escenario: “deshabilitar un bloque completo durante loading sin re-render”.

- El backend expone `shared:status` y lo cambia a `loading` durante un request.
- `volt:style` aplica `pointer-events: none` y baja opacidad del contenedor.
- Al volver a `idle`, se restauran los estilos originales sin necesidad de manejar clases manualmente.

Checklist de validacion manual:

- Alterna `shared:status` y confirma que las propiedades CSS se setean y luego se restauran al baseline.
- Prueba multiples reglas y confirma que solo aplica la primera condicion verdadera (si varias son verdaderas, todas se evaluan y pueden aplicar/restaurar en secuencia).
- Verifica que errores de sintaxis en la condicion no rompen el runtime y la regla simplemente no aplica.

# Directiva Frontend: `volt:show`

## Introduccion

`volt:show` controla la visibilidad de un elemento en funcion de una expresion booleana evaluada contra el estado `client`/`shared`.

A diferencia de `volt:if`, `volt:show` no remueve el nodo del DOM. En su lugar:

- Aplica `hidden = true`
- Setea `aria-hidden="true"`
- Fuerza `display: none !important`

Cuando la condicion deja de aplicar, restaura el “baseline” original (el `hidden` inicial, el `aria-hidden` inicial y el `display` inicial).

## Cuando usar

Usa `volt:show` cuando:

- Quieres ocultar/mostrar UI sin desmontar el DOM (preservas focus, listeners y estado DOM).
- Necesitas toggles frecuentes (tabs, secciones expandibles) sin el costo de recrear nodos.
- Quieres que el nodo siga existiendo para mediciones o para conservar layout interno (aunque oculto).

Evita `volt:show` cuando:

- Necesitas remover el DOM por completo (validaciones de form, elementos costosos, evitar listeners) y te sirve mas `volt:if`.
- El contenido oculto no debe existir por accesibilidad/semantica (usa `volt:if`).

## Como usar

### 1) Sintaxis basica

```html
<div volt:show="shared:sidebarOpen">
  Sidebar
</div>
```

Alias equivalente:

```html
<div volt-show="shared:sidebarOpen">Sidebar</div>
```

### 2) Modificador `.hide` (inverso)

```html
<div volt:show.hide="shared:sidebarOpen">
  Se muestra cuando sidebarOpen es false
</div>
```

Alias equivalente:

```html
<div volt-show-hide="shared:sidebarOpen">...</div>
```

Semantica:

- `volt:show`: muestra cuando la condicion es true.
- `volt:show.hide`: oculta cuando la condicion es true.

### 3) Expresiones soportadas

La condicion se parsea como una expresion de store (AST) y soporta:

- Referencias: `shared:ruta` / `client:ruta` (por ejemplo `shared:user.authenticated`)
- Operadores: `!`, `&&`, `||`
- Comparadores: `===`, `!==`, `==`, `!=`, `>`, `<`, `>=`, `<=`
- Parentesis: `( ... )`
- Literales: strings (`'...'` / `"..."`), numeros, `true/false`, `null`

Notas:

- Si la expresion es invalida, se considera `false`.
- Si una ruta no existe, el valor es `undefined` (falsy).

## Ejemplo de uso

```html
<section class="p-4 rounded-lg border">
  <button type="button" class="px-3 py-2 rounded-lg border" volt:click="toggleHelp">
    Toggle ayuda
  </button>

  <div class="mt-3 text-sm" volt:show="shared:helpOpen === true">
    Aqui va un bloque de ayuda que se oculta sin desmontarse.
  </div>

  <div class="mt-3 text-sm" volt:show.hide="shared:helpOpen === true">
    Este bloque se muestra cuando helpOpen es false.
  </div>
</section>
```

## Escenario de uso

Escenario: “tabs que preservan estado del DOM”.

- Cada tab es un contenedor con `volt:show="shared:activeTab === 'billing'"`, etc.
- Al cambiar `shared:activeTab`, el runtime oculta/muestra contenedores sin removerlos.
- Inputs dentro del tab conservan focus y estado (mientras no se re-renderice por otras directivas).

Checklist de validacion manual:

- Cambia el valor en `shared:*` y confirma que el elemento alterna `hidden`, `aria-hidden` y `display`.
- Confirma que al mostrar de nuevo se restaura el `display` original (si tenia).
- Compara contra `volt:if`: con `show` el nodo sigue existiendo en el DOM.

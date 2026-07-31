# Directiva Frontend: `volt:if`

## Introduccion

`volt:if` controla si un fragmento de DOM existe o no, en funcion de una expresion booleana evaluada contra el estado `client`/`shared`.

A diferencia de `volt:show` (que hace show/hide), `volt:if`:

- Inserta el nodo cuando la condicion es verdadera.
- Remueve el nodo del DOM cuando la condicion es falsa.

Internamente el runtime reemplaza el nodo por un placeholder `<template>` y “reconstruye” el elemento cuando vuelve a activarse.

## Cuando usar

Usa `volt:if` cuando:

- Necesitas evitar que un bloque exista en el DOM (por ejemplo, para no validar inputs ocultos o para no montar componentes costosos).
- Quieres controlar la presencia de UI en base a store (`shared:*` o `client:*`) sin escribir JS.
- Quieres condicionar bloques grandes donde `volt:show` dejaria nodos y listeners vivos.

Evita `volt:if` cuando:

- Necesitas preservar estado de DOM entre toggles (focus, valores no sincronizados, scroll interno); al remover/re-insertar se pierde.
- Solo necesitas ocultar/mostrar sin desmontar (usa `volt:show`).
- El bloque es muy dinamico y cambia con alta frecuencia; `volt:if` muta DOM y puede generar churn.

## Como usar

### 1) Sintaxis basica

```html
<div volt:if="shared:isOpen">
  ...
</div>
```

Alias equivalente:

```html
<div volt-if="shared:isOpen">...</div>
```

La expresion puede usar:

- Referencias a store: `shared:ruta` o `client:ruta`
- Operadores: `!`, `&&`, `||`
- Comparadores: `===`, `!==`, `==`, `!=`, `>`, `<`, `>=`, `<=`
- Parentesis: `( ... )`
- Literales: strings (`'...'` / `"..."`), numeros, `true/false`, `null`

### 2) Notas de evaluacion

- Si la expresion es invalida (token no reconocido, comillas sin cerrar, parentesis desbalanceados), se considera `false`.
- Si una ruta no existe, el valor se evalua como `undefined` (falsy).
- Por las reglas de parsing, comparaciones encadenadas como `a < b < c` se asocian por la izquierda.

### 3) Composicion con otras directivas

Como `volt:if` inserta/remueve DOM, es comun combinarlo con otras directivas dentro del bloque:

- `volt:for` para renderizar listas solo si hay items.
- `volt:click` para acciones dentro de un modal.
- `volt:class` / `volt:attr` para estilos condicionales internos.

Nota: el runtime hace multiples pasadas de sync (hasta 5) para estabilizar el DOM cuando hay `if` anidados o cuando un `if` desbloquea nuevos nodos con directivas.

## Ejemplo de uso

```html
<section class="p-4 rounded-lg border">
  <button
    type="button"
    class="px-3 py-2 rounded-lg border"
    volt:click="toggleAdvanced"
  >
    Toggle avanzado
  </button>

  <div class="mt-3" volt:if="shared:advanced === true">
    <h3 class="font-medium">Opciones avanzadas</h3>

    <label class="block mt-2">
      <span class="text-sm">Modo</span>
      <select volt:model.local="shared:advancedMode">
        <option value="safe">Safe</option>
        <option value="fast">Fast</option>
      </select>
    </label>
  </div>
</section>
```

## Escenario de uso

Escenario: “montaje condicional de formulario avanzado”.

- La pantalla inicia con `shared:advanced === false`, el bloque no existe en el DOM.
- El usuario ejecuta `toggleAdvanced` y el backend actualiza `shared:advanced` a `true`.
- El runtime inserta el bloque (clona el template) y luego sincroniza directivas internas (`volt:model.local`, etc.).
- Si el usuario vuelve a desactivar, el runtime remueve el bloque del DOM (se pierde estado local del DOM).

Checklist de validacion manual:

- Confirma que el bloque aparece/desaparece cuando cambia `shared:advanced`.
- Asegura que al ocultar con `volt:if` el DOM realmente se remueve (no solo se oculta).
- Verifica que al re-mostrar, el contenido se re-crea desde el template (inputs vuelven a estado inicial salvo que el store los vuelva a llenar).

# Directiva Frontend: `volt:for`

## Introduccion

`volt:for` renderiza una lista repitiendo un template de DOM por cada item de un arreglo ubicado en el estado `client` o `shared`.

Es una directiva de “render de store”: en cada sincronizacion del runtime, el engine:

- Resuelve el arreglo desde `(client|shared):path`.
- Elimina los nodos previamente renderizados.
- Clona e inserta nuevamente el template para todos los items (no hay diffing por key).

Dentro del template clonado, `volt:for` soporta interpolacion simple con `{{ ... }}` en:

- Text nodes
- Atributos

## Cuando usar

Usa `volt:for` cuando:

- Necesitas renderizar listas pequeñas/medianas (chips, items, filas) desde estado reactivo.
- Quieres iterar sobre `shared:*` (dato viene del backend) o `client:*` (dato local) sin escribir JS.
- Quieres combinarlo con otras directivas dentro de cada item (por ejemplo `volt:click`, `volt:class`, `volt:text`), ya que el runtime sincroniza primero `for` y luego el resto de directivas de store.

Evita `volt:for` cuando:

- La lista es grande (por estrategia de “borra y pinta todo” ante cambios).
- Necesitas preservar estado de DOM por item (focus, scroll interno, inputs no sincronizados): al re-render se pierde.
- Necesitas iterar sobre objetos (solo itera `Array`; si el valor no es array se comporta como lista vacia).

## Como usar

### 1) Sintaxis

Forma general:

```html
<li volt:for="item, index in shared:items">...</li>
```

Reglas:

- `item` es el alias del item (obligatorio).
- `index` es el alias del indice (opcional). Si no lo declaras, el alias por defecto es `index`.
- `in` define el origen: `client:*` o `shared:*`.
- El `path` acepta caracteres `A-Z a-z 0-9 _ . -` (por ejemplo `shared:users`, `client:cart.items`).

Aliases equivalentes:

```html
<li volt-for="item, index in shared:items">...</li>
```

### 2) Interpolacion `{{ ... }}`

En el template puedes usar:

- `{{ item }}`
- `{{ index }}`
- `{{ item.prop.subprop }}`

Notas:

- No se evaluan expresiones arbitrarias. Solo alias + path.
- Si el valor es un objeto, el runtime intenta `JSON.stringify(...)`; si falla, inserta texto vacio.

### 3) Uso con otras directivas dentro del item

Como `volt:for` clona DOM real, puedes usar directivas dentro del item. Ejemplos tipicos:

- `volt:click` para acciones por fila
- `volt:class` / `volt:attr` para estilos condicionales por item
- `volt:text` para imprimir valores en un nodo

## Ejemplo de uso

```html
<ul class="space-y-2">
  <li
    class="flex justify-between items-center px-3 py-2 rounded-lg border"
    volt:for="user, i in shared:users"
  >
    <span class="font-medium">
      {{ i }}. {{ user.name }}
    </span>

    <button
      type="button"
      class="text-sm underline"
      volt:click="selectUser"
      volt:params="{\"id\":\"{{ user.id }}\"}"
    >
      Seleccionar
    </button>
  </li>
</ul>
```

## Escenario de uso

Escenario: “lista de resultados con accion por item”.

- El backend actualiza `shared:users` (arreglo).
- El runtime sincroniza el root: `volt:for` elimina nodos previos y renderiza la lista completa.
- Cada item incluye un boton con `volt:click` que manda parametros (`volt:params`) usando interpolacion.

Checklist de validacion manual:

- Cambia `shared:users` (por una action o navegacion) y confirma que el listado se re-renderiza.
- Verifica que `{{ user.name }}` y `{{ user.id }}` se interpolan en texto/atributos.
- Confirma que el click en un item envia los params esperados.

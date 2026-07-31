# Directiva Frontend: `volt:text`

## Introduccion

`volt:text` imprime texto dentro de un elemento en funcion de un valor obtenido del store del runtime (`shared:*` o `client:*`).

El runtime resuelve el valor y lo asigna via `textContent`, por lo que:

- No interpreta HTML (mitiga inyeccion de markup/XSS accidental).
- Siempre sobrescribe el contenido del nodo en cada sync.

`volt:text` soporta una expresion con fallbacks usando `??` (primer valor definido).

## Cuando usar

Usa `volt:text` cuando:

- Necesitas mostrar un dato simple del store (nombre, estado, conteo).
- Quieres concatenar fallbacks sin escribir JS (por ejemplo `client:* ?? shared:* ?? 'default'`).
- Quieres imprimir valores que pueden ser `null/undefined` sin romper UI.

Evita `volt:text` cuando:

- Necesitas renderizar HTML (usa `volt:html` bajo su contrato).
- Necesitas preservar el contenido original del elemento si el dato no existe: `volt:text` escribe `""` cuando no encuentra valor (no restaura baseline).
- Necesitas formateo complejo (moneda/fecha) sin apoyo del backend; el runtime solo hace `String(...)` / `JSON.stringify(...)`.

## Como usar

### 1) Sintaxis basica

```html
<span volt:text="shared:user.name"></span>
```

Alias equivalente:

```html
<span volt-text="shared:user.name"></span>
```

El valor puede ser una referencia:

- `shared:ruta`
- `client:ruta`

La `ruta` acepta caracteres `A-Z a-z 0-9 _ . -` (por ejemplo `shared:user.name`, `client:draft.note`).

### 2) Fallbacks con `??`

Puedes encadenar segmentos. El runtime toma el primer segmento cuyo valor sea distinto de `null/undefined`:

```html
<span volt:text="client:draft.note ?? shared:draft.note ?? 'Sin nota disponible'">
  Sin nota disponible
</span>
```

Segmentos soportados:

- Referencias: `client:*` / `shared:*`
- Literales: strings `'...'` o `"..."`, numeros, `true/false`, `null`

### 3) Normalizacion a string

El runtime normaliza el valor asi:

- `null` / `undefined` => `""`
- `object` => `JSON.stringify(value)` (si falla, `""`)
- otros => `String(value)`

## Ejemplo de uso

```html
<section class="rounded-lg border p-4 space-y-2">
  <div class="text-sm">
    Usuario:
    <strong volt:text="shared:user.name ?? 'Anonimo'"></strong>
  </div>

  <div class="text-sm">
    Estado:
    <span
      class="px-2 py-1 rounded"
      volt:text="shared:status ?? 'idle'"
    ></span>
  </div>
</section>
```

## Escenario de uso

Escenario: “mostrar borrador local con fallback a servidor”.

- El backend publica `shared:draft.note`.
- El usuario edita localmente y guardas el borrador en `client:draft.note` (por ejemplo via una action o API de state).
- La UI muestra `client:draft.note` si existe; si no, muestra el valor de `shared:draft.note`.
- Si ninguno existe, muestra un literal por default.

Checklist de validacion manual:

- Define `shared:draft.note` y confirma que se imprime.
- Define `client:draft.note` y confirma que tiene prioridad sobre `shared`.
- Borra ambos y confirma que aparece el literal fallback.
- Inserta un string con `<b>html</b>` y confirma que se imprime como texto, no como markup.

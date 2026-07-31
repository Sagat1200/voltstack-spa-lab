# Directiva Frontend: `volt:persist`

## Introduccion

`volt:persist` marca un fragmento del DOM como **persistente** a través de navegaciones SPA, incluso cuando una o varias rutas intermedias **no** declaran un target compatible.

Diferencia principal vs `volt:preserve`:

- `volt:preserve` requiere que el destino inmediato tenga un target con la misma key; si no existe, el fragmento se descarta.
- `volt:persist` guarda el nodo vivo en un **registro persistente** del runtime y lo puede reinyectar más adelante cuando aparezca un target compatible.

En el runtime, los fragmentos persistentes viven en `runtime.persistentFragments` (Map key -> { element, tagName, ... }).

## Cuando usar

Usa `volt:persist` cuando:

- Necesitas que UI viva (sidebar, mini player, draft) sobreviva a un flujo con pantalla puente/intermedia que no tiene el target.
- Quieres “carry” de un bloque interactivo entre varias rutas SPA sin depender del HTML de cada una.
- Necesitas preservar estado local DOM (inputs, details, contenteditable) sin rehidratar desde state.

Evita `volt:persist` cuando:

- El bloque debe existir siempre en el DOM visible (si una pantalla no tiene target, el nodo quedará fuera del DOM visible hasta reinyectarse).
- No puedes garantizar keys estables o el mismo `tagName` entre origen y destino final.
- El bloque depende de estilos/contenerización local y reinyectarlo en otra estructura lo rompe.

## Como usar

### 1) Sintaxis

```html
<section volt:persist="mi-key"></section>
```

Aliases equivalentes:

```html
<section volt-persist="mi-key"></section>
<section data-volt-persist="mi-key"></section>
```

### 2) Clave (key) y matching

La key se resuelve igual que `volt:preserve`:

1) valor explícito del atributo (`volt:persist="persist-sidebar"`)
2) fallback a `id`
3) fallback a `data-volt-target`

Matching:

- Para reinyectar, el documento destino debe declarar un target con la misma key.
- El `tagName` debe coincidir entre fragmento persistido y target (`section` con `section`, `aside` con `aside`, etc.).
- Si hay mismatch, el fragmento se descarta del registro para evitar incoherencias.

### 3) Registro persistente (lifecycle)

En una navegacion SPA, el runtime ejecuta:

1) **capturePersistentFragments**: captura candidatos `volt:persist` del DOM actual y los guarda en el registro.
2) Reemplaza el `body` por el HTML del destino.
3) **restorePersistentFragments**: si el destino declara targets compatibles, reemplaza esos placeholders por los nodos vivos del registro.

Si una pantalla no tiene targets compatibles:

- `persistedCount` en esa navegación puede ser `0`, pero el `registrySize` sigue > 0.
- Los nodos no aparecen en el DOM visible hasta que exista un target compatible.

### 4) Política de documento

El registro persistente está sujeto a la misma política de fragment control:

- `volt-fragment-control: preserve` permite capturar/restaurar.
- `volt-fragment-control: reset` descarta y limpia el registro.

## Ejemplo de uso

Ruta origen:

```html
<meta name="volt-fragment-control" content="preserve">

<section data-volt-persist="persist-sidebar">
  <strong>Sidebar vivo</strong>
  <div contenteditable="true">Edita esto</div>
</section>

<a href="/runtimePersistBridge" volt:navigate>Ir al puente</a>
```

Ruta puente (sin targets persist):

```html
<meta name="volt-fragment-control" content="preserve">
<p>Aquí no hay data-volt-persist="persist-sidebar"</p>
```

Ruta destino final (reinyecta):

```html
<meta name="volt-fragment-control" content="preserve">

<section data-volt-persist="persist-sidebar">
  <strong>Placeholder destino</strong>
</section>
```

## Escenario de uso

Escenario: “sidebar + player” persistentes a través de un bridge:

- En `/runtimePersist` editas el sidebar y el mini player (valores, check, details, range).
- Navegas a `/runtimePersistBridge`, que no declara targets persistidos.
- Navegas a `/runtimePersistAlt`, que declara `persist-sidebar` y `persist-player` y debe reinyectar los mismos nodos vivos.

Checklist de validación manual:

- Edita contenido en `volt:persist="persist-sidebar"` y `volt:persist="persist-player"`.
- Navega al bridge: los nodos desaparecen del DOM visible pero el registro se mantiene.
- Navega al destino: los nodos reaparecen con el estado editado.

Rutas demo (spa-lab):

- `/runtimePersist`
- `/runtimePersistBridge`
- `/runtimePersistAlt`

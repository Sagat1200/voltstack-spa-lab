# Directiva Frontend: `volt:preserve`

## Introduccion

`volt:preserve` marca un fragmento del DOM como **preservable** durante una navegacion SPA: cuando el runtime reemplaza el `body` por el HTML de la ruta destino, intenta **reinsertar el nodo real existente** (con su estado local, valores, scroll interno y listeners) en el nuevo documento.

El mecanismo es opt-in y funciona por emparejamiento de **clave (key)** entre:

- el elemento preservado en la pagina origen (captura)
- el “placeholder” equivalente declarado en la pagina destino (target)

Si no hay match, hay mismatch de tag, o la politica de documento lo prohíbe, el fragmento se descarta y se usa el HTML nuevo.

## Cuando usar

Usa `volt:preserve` cuando:

- Necesitas mantener un borrador real (inputs/textarea/checkbox) al navegar entre pantallas SPA (ej. “/edit” -> “/preview” -> “/edit”).
- Tienes un “shell vivo” con UI local (contenteditable, details open/closed, sliders) que no quieres rehidratar desde state.
- Quieres reducir friccion de UX conservando estado del DOM sin enviar payload adicional.

Evita `volt:preserve` cuando:

- El contenido debe recomponerse siempre desde el servidor (ej. datos críticos que no deben quedar “stale”).
- El fragmento depende del contexto de su contenedor original (CSS/layout) y moverlo a otro documento puede romperlo.
- No puedes garantizar una clave estable y un target equivalente en la ruta destino.

## Como usar

### 1) Sintaxis

`volt:preserve` es un atributo booleano u opcionalmente con valor (key):

```html
<form volt:preserve></form>
<form volt:preserve="draft-fragment"></form>
```

Aliases equivalentes:

```html
<form volt-preserve="draft-fragment"></form>
<form data-volt-preserve="draft-fragment"></form>
```

### 2) Clave (key) y reglas de emparejamiento

La clave se resuelve así:

1) Si el atributo tiene valor no vacío (`volt:preserve="mi-key"`), esa es la key.
2) Si no, usa `id` si existe.
3) Si no, usa `data-volt-target` si existe.

Reglas:

- La key debe ser **única** por documento para los fragmentos preservables.
- El elemento destino debe declarar la **misma key** y el **mismo tagName** (`form` con `form`, `section` con `section`, etc.).
- Si hay duplicados, el runtime descarta fragmentos con motivo `duplicate-source` o `duplicate-target`.

### 3) Politica de documento (habilitar / reset)

La preservacion puede ser deshabilitada por politica de documento:

- Meta: `<meta name="volt-fragment-control" content="preserve|reset">`
- Alias meta: `<meta name="volt:fragment-cache" content="...">`
- Atributo en body: `data-volt-fragment-control="..."`

Tokens relevantes:

- `preserve` / `keep` / `on` / `true`: habilita restauracion de fragmentos preservados.
- `reset` / `discard` / `drop` / `no-store` / `none` / `off` / `false`: descarta y no restaura.

Nota: aunque el fragmento exista, si el cache mode de la navegacion es `no-store` (politica de cache de navegacion), el runtime evita restaurar y descarta con motivo de politica.

### 4) Restricciones practicas

- Solo se consideran candidatos “top-level”: un fragmento dentro de otro fragmento retenido (preserve/persist) no se procesa como candidato independiente.
- La restauracion se hace reemplazando el target del documento destino (`target.replaceWith(fragment.element)`), por lo que el target es un placeholder declarativo.

## Ejemplo de uso

Dos rutas SPA que comparten el mismo fragmento preservable por key.

Ruta A:

```html
<meta name="volt-fragment-control" content="preserve">

<form data-volt-preserve="draft-fragment">
  <input type="text" name="draft_name" value="SSR base">
  <textarea name="draft_notes">SSR notes</textarea>
</form>

<a href="/formExample" volt:navigate>Ir a formExample</a>
```

Ruta B (misma key y mismo tag):

```html
<meta name="volt-fragment-control" content="preserve">

<form data-volt-preserve="draft-fragment">
  <input type="text" name="draft_name" value="SSR base destino">
  <textarea name="draft_notes">SSR notes destino</textarea>
</form>
```

## Escenario de uso

Escenario: “draft cross-route” para evitar perder texto al navegar:

- El usuario escribe en un textarea largo (estado local DOM).
- Navega a otra pantalla SPA para consultar referencias.
- Vuelve: el textarea conserva el texto, selección y scroll interno porque el nodo fue preservado.

Checklist de validacion manual:

- Edita el fragmento preservado (inputs, textarea, checkbox).
- Navega SPA a una ruta destino que declare el mismo `data-volt-preserve="<key>"`.
- Confirma que el fragmento preservado mantiene valores locales.
- Confirma que un fragmento sin `data-volt-preserve` se reinicia al HTML base.

Eventos utiles de telemetria (opcionales):

- `volt:fragment-preserve` (cuando el fragmento fue reinsertado)
- `volt:fragment-discard` (cuando se descartó; ej. missing-key, tag-mismatch, duplicate-source, missing-target, document-policy)

Rutas demo (spa-lab):

- `/fragmentCache` (origen)
- `/formExample` (destino compatible)
- `/fragmentCacheReset` (destino con politica de descarte)

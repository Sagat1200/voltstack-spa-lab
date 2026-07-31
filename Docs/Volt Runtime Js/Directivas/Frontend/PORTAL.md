# Directiva Frontend: `volt:portal`

## Introduccion

`volt:portal` mueve un nodo real del DOM hacia otro contenedor (target) indicado por un selector CSS.

No clona el contenido: el mismo elemento (con sus listeners y estado interno) se reubica en el árbol.

El runtime conserva un placeholder oculto en la ubicación original para poder restaurar el nodo cuando:

- el selector queda vacío
- el target no existe
- la directiva se elimina

## Cuando usar

Usa `volt:portal` cuando:

- Necesitas proyectar UI “global” (banners, modales, drawers, tooltips) hacia roots del layout.
- Quieres declarar el contenido cerca de su lógica (dentro del componente) pero renderizarlo fuera del contenedor visual.
- Quieres que el portal sobreviva patching SPA sin duplicar nodos.

Evita `volt:portal` cuando:

- El target no está garantizado (o su selector cambia) y no quieres el comportamiento de “restore”.
- Solo necesitas posicionamiento visual (a veces CSS es suficiente sin mover nodos).
- Buscas múltiples instancias en el mismo target sin control de stacking (resolverlo con un “manager” o con `z-index`/orden).

## Como usar

### 1) Sintaxis

```html
<div volt:portal="#selector"></div>
```

Alias equivalente:

```html
<div volt-portal="#selector"></div>
```

### 2) Valor: selector CSS

- Debe ser un selector válido para `document.querySelector(...)`.
- Si está vacío o solo whitespace, el runtime restaura el nodo a su lugar original.

Ejemplos:

```html
<div volt:portal="#volt-modals-root"></div>
<div volt:portal="#volt-banners-root"></div>
<div volt:portal="#volt-drawers-root"></div>
```

### 3) Semantica (placeholder y restore)

- Al primer move, el runtime inserta un placeholder oculto `span[data-volt-portal-placeholder="true"]` antes del nodo.
- Si el nodo ya está en el target, no repite el append.
- Si el target desaparece o el selector es inválido, el nodo vuelve inmediatamente después del placeholder.

## Ejemplo de uso

```html
<section>
  <button
    type="button"
    volt:on="click -> state:set shared:portal.modalOpen = true"
  >
    Abrir modal
  </button>

  <section
    volt:portal="#volt-modals-root"
    volt:show="shared:portal.modalOpen === true"
    style="position:fixed;inset:0;display:grid;place-items:center;background:rgba(2,6,23,0.74);"
  >
    <div style="background:#0f172a;color:#e2e8f0;border-radius:16px;padding:20px;">
      <strong>Modal</strong>
      <button type="button" volt:on="click -> state:set shared:portal.modalOpen = false">
        Cerrar
      </button>
    </div>
  </section>
</section>
```

## Escenario de uso

Escenario: layout con roots globales:

- `#volt-banners-root` para banners (top)
- `#volt-modals-root` para modales (center)
- `#volt-drawers-root` para drawers (right)

Objetivo:

- El componente declara los tres bloques dentro de su markup local.
- Cada bloque aparece en su root global cuando una bandera `shared` está activa.
- Al navegar SPA a otra ruta, si las banderas siguen activas, el portal se re-monta y vuelve a proyectarse.

Markup (fragmento):

```html
<div volt:portal="#volt-banners-root" volt:show="shared:portal.bannerOpen === true">
  <strong>Banner portalizado</strong>
</div>

<section volt:portal="#volt-modals-root" volt:show="shared:portal.modalOpen === true">
  <strong>Modal portalizado</strong>
</section>

<aside volt:portal="#volt-drawers-root" volt:show="shared:portal.drawerOpen === true">
  <strong>Drawer portalizado</strong>
</aside>
```

Validacion:

- Abre banner/modal/drawer y confirma que no aparecen donde fueron declarados, sino en sus roots globales.
- Navega SPA a una ruta alterna: si el state `shared` sigue activo, el contenido debe reaparecer portalizado sin duplicarse.

Rutas demo (spa-lab):

- `/runtimePortal`
- `/runtimePortalAlt`

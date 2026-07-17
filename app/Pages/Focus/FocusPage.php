<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Focus;

use VoltStack\Runtime\Component\Component;
use VoltStack\Runtime\Protocol\ActionEffectOptions;

final class FocusPage extends Component
{
    public string $title = 'Focus Demo';

    public string $requestMarker;

    public int $patchSequence = 0;

    public string $patchSummary = 'Aun sin patch reactivo.';

    public string $selectionProbeValue = 'Selecciona una parte de este texto y luego dispara el patch reactivo para confirmar que el runtime conserva focus, cursor y selection range.';

    public string $selectionProbeNotes = "Linea 1. El runtime debe conservar el cursor y la seleccion.\nLinea 2. Esta textarea tambien debe mantener su scroll interno.\nLinea 3. Dispara el patch despues de mover la barra vertical.\nLinea 4. El contenido se mantiene estable entre renders.\nLinea 5. La restauracion usa el mismo target activo.\nLinea 6. El contrato espera selectionStart y selectionEnd coherentes.\nLinea 7. El scroll del control no debe volver a cero.\nLinea 8. La UI visible del lab debe dejarlo claro.";

    public function mount(): void
    {
        $this->requestMarker = sprintf('focus-%s', substr((string) microtime(true), -6));
    }

    public function refreshPatchProbe(): ActionEffectOptions
    {
        $this->patchSequence++;
        $this->requestMarker = sprintf('focus-patch-%02d', $this->patchSequence);
        $this->patchSummary = sprintf(
            'Patch #%d aplicado a las %s para validar restore de focus/selection/scroll.',
            $this->patchSequence,
            date('H:i:s')
        );

        return ActionEffectOptions::make();
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="focus-mode">
<style data-volt-head-key="focus-demo-styles">
    [data-focus-id]:focus,
    [data-focus-id]:focus-visible {
        outline: 3px solid rgba(250, 204, 21, 0.95);
        outline-offset: 3px;
        box-shadow: 0 0 0 4px rgba(250, 204, 21, 0.18);
    }
</style>
<script data-volt-head-key="focus-demo-bridge">
    (() => {
        if (window.__voltRuntimeFocusDemoInstalled) {
            return;
        }

        window.__voltRuntimeFocusDemoInstalled = true;

        function state() {
            return window.Volt && window.Volt.state ? window.Volt.state : null;
        }

        function setClient(path, value) {
            const api = state();

            if (!api) {
                return;
            }

            api.set(path, value, {
                scope: 'client'
            });
        }

        function isSelectable(target) {
            if (!target || !target.tagName) {
                return false;
            }

            if (target.tagName === 'TEXTAREA') {
                return true;
            }

            if (target.tagName !== 'INPUT') {
                return false;
            }

            return ['text', 'search', 'url', 'tel', 'password', 'email', 'number'].includes(
                String(target.type || 'text').toLowerCase()
            );
        }

        function resolveFocusId(target) {
            return target && typeof target.getAttribute === 'function' && target.getAttribute('data-focus-id') ?
                target.getAttribute('data-focus-id') :
                target && target.id ?
                target.id :
                target && target.name ?
                target.name :
                target && target.tagName ?
                target.tagName.toLowerCase() :
                '(sin target)';
        }

        function updateText(selector, value) {
            document.querySelectorAll(selector).forEach((node) => {
                node.textContent = value;
            });
        }

        function syncInspector(reason) {
            const active = document.activeElement && typeof document.activeElement === 'object' ? document.activeElement : null;
            const scrollBox = document.querySelector('[data-volt-target="focus-scroll-box"]');
            const selectionRange = isSelectable(active) && typeof active.selectionStart === 'number' &&
                typeof active.selectionEnd === 'number' ?
                `${active.selectionStart}-${active.selectionEnd}` :
                'n/d';
            const selectionDirection = isSelectable(active) && typeof active.selectionDirection === 'string' ?
                active.selectionDirection :
                'none';
            const innerScrollTop = isSelectable(active) && typeof active.scrollTop === 'number' ? Math.round(active.scrollTop) :
                0;

            updateText('[data-runtime-check="focus-active-element"]', resolveFocusId(active));
            updateText('[data-runtime-check="focus-selection-range"]', selectionRange);
            updateText('[data-runtime-check="focus-selection-direction"]', selectionDirection);
            updateText('[data-runtime-check="focus-selection-scroll-top"]', String(innerScrollTop));
            updateText(
                '[data-runtime-check="focus-scroll-box-top"]',
                scrollBox && typeof scrollBox.scrollTop === 'number' ? String(Math.round(scrollBox.scrollTop)) : '0'
            );
            updateText(
                '[data-runtime-check="focus-scroll-box-left"]',
                scrollBox && typeof scrollBox.scrollLeft === 'number' ? String(Math.round(scrollBox.scrollLeft)) : '0'
            );
            updateText('[data-runtime-check="focus-inspector-reason"]', reason);
        }

        document.addEventListener('focusin', (event) => {
            const target = event && event.target && typeof event.target === 'object' ? event.target : null;
            const focusId = resolveFocusId(target);

            setClient('focus.lastActive', focusId);
            setClient('focus.lastActiveAt', new Date().toLocaleTimeString());
            syncInspector('focusin');
        });

        document.addEventListener('selectionchange', () => {
            syncInspector('selectionchange');
        });

        document.addEventListener(
            'scroll',
            () => {
                syncInspector('scroll');
            },
            true
        );

        document.addEventListener('volt:after-patch', () => {
            window.requestAnimationFrame(() => {
                syncInspector('after-patch');
            });
        });

        document.addEventListener('volt:navigated', () => {
            window.requestAnimationFrame(() => {
                syncInspector('navigated');
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            window.requestAnimationFrame(() => {
                syncInspector('boot');
            });
        });

        syncInspector('boot');
    })();
</script>
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(59,130,246,0.24);background:linear-gradient(135deg,rgba(30,64,175,0.90),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#eff6ff;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(96,165,250,0.32);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#93c5fd;">Runtime
            Focus MVP</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#dbeafe;line-height:1.75;max-inline-size:76ch;">
                Esta pantalla valida el nuevo contrato de <code>volt:focus</code> y
                <code>volt:autofocus.when</code>. Los botones mutan <code>window.Volt.state</code> y el runtime decide
                qué elemento debe recibir foco en cada pasada.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(96,165,250,0.18);background:rgba(30,64,175,0.22);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#93c5fd;">Request
                marker</span>
            <strong style="font-size:14px;color:#eff6ff;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#dbeafe;">Ruta sugerida:
                <code>/runtimeFocus -> /runtimeFocusAlt</code>.</span>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Acciones para mover el foco</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Cada boton resetea la bandera relevante y la vuelve a poner en <code>true</code> para forzar una nueva
                transicion <code>false -&gt; true</code>.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button"
                volt:on="click -> state:set client:focus.title = false | click -> state:set client:focus.notes = false | click -> state:set shared:focus.returnAction = false | click -> state:set client:focus.title = true | click -> state:set client:focus.lastAction = 'focus-title'"
                style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.18);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Focar input de titulo
            </button>
            <button type="button"
                volt:on="click -> state:set client:focus.title = false | click -> state:set client:focus.notes = false | click -> state:set shared:focus.returnAction = false | click -> state:set client:focus.notes = true | click -> state:set client:focus.lastAction = 'focus-notes'"
                style="border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Focar textarea de notas
            </button>
            <button type="button"
                volt:on="click -> state:set client:focus.title = false | click -> state:set client:focus.notes = false | click -> state:set shared:focus.returnAction = false | click -> state:set shared:focus.returnAction = true | click -> state:set client:focus.lastAction = 'focus-return-action'"
                style="border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.18);color:#fde68a;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Focar boton de retorno
            </button>
            <button type="button"
                volt:on="click -> state:set shared:focus.showErrors = false | click -> state:set shared:focus.showErrors = true | click -> state:set client:focus.lastAction = 'autofocus-error-panel'"
                style="border:1px solid rgba(168,85,247,0.28);background:rgba(88,28,135,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Abrir panel con autofocus
            </button>
            <button type="button"
                volt:on="click -> state:delete client:focus | click -> state:delete shared:focus | click -> state:set client:focus.lastAction = 'reset-focus-state'"
                style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Reset focus state
            </button>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(148,163,184,0.24);background:#020617;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Contrato de patch, seleccion y scroll</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Selecciona texto en el input o la textarea, mueve el scroll del panel inferior y luego dispara el patch
                reactivo. El runtime debe restaurar el foco activo, el rango de seleccion y el scroll interno del
                contenedor marcado.
            </p>
        </div>

        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
            <article
                style="display:grid;gap:14px;border:1px solid rgba(59,130,246,0.24);background:rgba(30,64,175,0.14);border-radius:18px;padding:18px;color:#dbeafe;">
                <strong style="color:#93c5fd;">Patch server-driven del root</strong>
                <p style="margin:0;color:#bfdbfe;line-height:1.7;">
                    Este boton fuerza una nueva respuesta del componente sin tocar los campos de prueba. El objetivo es
                    validar el restore de UI sobre HTML regenerado.
                </p>
                <button type="button" data-volt-target="focus-patch-button" volt-click="refreshPatchProbe"
                    onmousedown="event.preventDefault()"
                    style="inline-size:max-content;border:1px solid rgba(59,130,246,0.30);background:rgba(30,64,175,0.22);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                    Disparar patch reactivo
                </button>
                <div
                    style="display:grid;gap:10px;border:1px solid rgba(96,165,250,0.20);background:rgba(30,64,175,0.12);border-radius:14px;padding:14px;">
                    <span data-runtime-check="focus-patch-sequence" style="color:#bfdbfe;">patch.sequence = {{ $patchSequence }}</span>
                    <span data-runtime-check="focus-patch-summary" style="color:#dbeafe;">{{ $patchSummary }}</span>
                    <span data-runtime-check="focus-patch-request-marker" style="color:#93c5fd;">request-marker = {{ $requestMarker }}</span>
                </div>
            </article>

            <article
                style="display:grid;gap:14px;border:1px solid rgba(16,185,129,0.24);background:rgba(6,95,70,0.14);border-radius:18px;padding:18px;color:#d1fae5;">
                <strong style="color:#86efac;">Seleccion y cursor preservados</strong>
                <input id="focus-selection-input" type="text" name="focusSelectionInput"
                    data-volt-target="focus-selection-input" data-focus-id="focus-selection-input"
                    value="{{ $selectionProbeValue }}"
                    style="inline-size:100%;border:1px solid rgba(16,185,129,0.28);background:#022c22;color:#ecfdf5;border-radius:10px;padding:10px 12px;">
                <textarea id="focus-selection-textarea" name="focusSelectionTextarea"
                    data-volt-target="focus-selection-textarea" data-focus-id="focus-selection-textarea" rows="4"
                    style="inline-size:100%;border:1px solid rgba(16,185,129,0.28);background:#022c22;color:#ecfdf5;border-radius:10px;padding:10px 12px;overflow-anchor:none;">{{ $selectionProbeNotes }}</textarea>
                <p style="margin:0;color:#a7f3d0;line-height:1.7;">
                    Prueba sugerida: selecciona un fragmento, pulsa <code>Disparar patch reactivo</code> y confirma que
                    el inspector sigue mostrando el mismo target y un rango distinto de <code>n/d</code>.
                </p>
            </article>
        </div>

        <article
            style="display:grid;gap:14px;border:1px solid rgba(245,158,11,0.24);background:rgba(120,53,15,0.14);border-radius:18px;padding:18px;color:#fde68a;">
            <div style="display:grid;gap:8px;">
                <strong style="color:#fcd34d;">Scroll interno restaurable</strong>
                <p style="margin:0;color:#fde68a;line-height:1.7;">
                    Este contenedor usa <code>data-volt-preserve-scroll</code> y un <code>data-volt-target</code>
                    estable para que el runtime pueda reponer su posicion despues del patch.
                </p>
            </div>

            <div data-volt-target="focus-scroll-box" data-focus-id="focus-scroll-box" data-volt-preserve-scroll
                style="max-block-size:240px;overflow:auto;border:1px solid rgba(245,158,11,0.24);background:rgba(120,53,15,0.18);border-radius:16px;padding:14px 16px;display:grid;gap:12px;overflow-anchor:none;">
                <article
                    style="border:1px solid rgba(251,191,36,0.28);background:rgba(120,53,15,0.20);border-radius:12px;padding:12px 14px;">
                    <strong style="display:block;color:#fef3c7;">Contrato estable del panel</strong>
                    <span style="display:block;margin-top:8px;color:#fde68a;line-height:1.7;">
                        Este bloque no cambia de altura entre patches para que el scroll observable dependa del runtime
                        y no del propio contenido del laboratorio.
                    </span>
                </article>
                @for ($index = 1; $index <= 14; $index++)
                    <article
                    style="display:grid;gap:6px;border:1px solid rgba(245,158,11,0.16);background:rgba(15,23,42,0.35);border-radius:12px;padding:12px 14px;">
                    <strong style="color:#fef3c7;">Fila {{ $index }}</strong>
                    <span style="color:#fde68a;line-height:1.7;">
                        El patch reactivo no debe devolver este panel al inicio si el contenedor conserva
                        <code>data-volt-preserve-scroll</code> y el mismo <code>data-volt-target</code>.
                    </span>
        </article>
        @endfor
</div>
</article>
</section>

<section style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
    <article
        style="display:grid;gap:14px;border:1px solid rgba(59,130,246,0.24);background:rgba(30,64,175,0.14);border-radius:18px;padding:18px;color:#dbeafe;">
        <strong style="color:#93c5fd;"><code>volt:focus="client:focus.title"</code></strong>
        <input type="text" data-focus-id="focus-title-input" volt:focus="client:focus.title"
            placeholder="Este input deberia recibir foco"
            style="inline-size:100%;border:1px solid rgba(96,165,250,0.28);background:#020617;color:#eff6ff;border-radius:10px;padding:10px 12px;">
        <span style="color:#bfdbfe;">Úsalo para validar foco reactivo ligado al scope cliente.</span>
    </article>

    <article
        style="display:grid;gap:14px;border:1px solid rgba(16,185,129,0.24);background:rgba(6,95,70,0.14);border-radius:18px;padding:18px;color:#d1fae5;">
        <strong style="color:#86efac;"><code>volt:focus="client:focus.notes"</code></strong>
        <textarea data-focus-id="focus-notes-textarea" rows="4" volt:focus="client:focus.notes"
            placeholder="La transicion false -> true debe enfocar esta textarea"
            style="inline-size:100%;border:1px solid rgba(16,185,129,0.28);background:#022c22;color:#ecfdf5;border-radius:10px;padding:10px 12px;"></textarea>
        <span style="color:#a7f3d0;">Tambien sirve para ver que la ultima coincidencia activa puede ganar en una
            pasada.</span>
    </article>

    <article
        style="display:grid;gap:14px;border:1px solid rgba(245,158,11,0.24);background:rgba(120,53,15,0.14);border-radius:18px;padding:18px;color:#fde68a;">
        <strong style="color:#fcd34d;"><code>volt:focus="shared:focus.returnAction"</code></strong>
        <button type="button" data-focus-id="focus-return-action" volt:focus="shared:focus.returnAction"
            style="inline-size:max-content;border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.22);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
            Boton de retorno enfocable
        </button>
        <span style="color:#fde68a;">Este target usa estado compartido para poder probar la navegacion SPA hacia la
            ruta alterna.</span>
    </article>
</section>

<section volt:show="shared:focus.showErrors === true"
    style="display:grid;gap:16px;border:1px solid rgba(168,85,247,0.24);background:rgba(88,28,135,0.18);border-radius:20px;padding:24px;color:#f5d0fe;">
    <div style="display:grid;gap:8px;">
        <h2 style="margin:0;font-size:24px;">Panel con <code>volt:autofocus.when</code></h2>
        <p style="margin:0;color:#e9d5ff;line-height:1.7;">
            Cuando <code>shared:focus.showErrors</code> pasa a <code>true</code>, el runtime debe enfocar la
            textarea de resumen sin que tengas que hacer click manual.
        </p>
    </div>

    <textarea data-focus-id="focus-error-summary" rows="4" volt:autofocus.when="shared:focus.showErrors"
        placeholder="Este campo debe recibir foco automatico al abrir el panel"
        style="inline-size:100%;border:1px solid rgba(192,132,252,0.32);background:#2e1065;color:#faf5ff;border-radius:10px;padding:10px 12px;"></textarea>

    <div style="display:flex;flex-wrap:wrap;gap:12px;">
        <button type="button"
            volt:on="click -> state:set shared:focus.showErrors = false | click -> state:set client:focus.lastAction = 'close-error-panel'"
            style="border:1px solid rgba(192,132,252,0.30);background:rgba(76,29,149,0.24);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
            Cerrar panel
        </button>
        <a href="/runtimeFocusAlt" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(96,165,250,0.30);background:rgba(30,64,175,0.22);color:#dbeafe;border-radius:10px;padding:10px 14px;text-decoration:none;">
            Ir a runtimeFocusAlt
        </a>
    </div>
</section>

<section
    style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
    <div style="display:grid;gap:8px;">
        <h2 style="margin:0;font-size:24px;">Inspector visual</h2>
        <p style="margin:0;color:#94a3b8;line-height:1.7;">
            El bridge del demo escucha <code>focusin</code>, <code>selectionchange</code>,
            <code>scroll</code> y <code>volt:after-patch</code> para dejar visible el contrato de restauracion.
        </p>
    </div>

    <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
        <article
            style="display:grid;gap:8px;border:1px solid rgba(59,130,246,0.20);background:rgba(30,64,175,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#93c5fd;">Ultimo elemento enfocado</strong>
            <span volt:text="client:focus.lastActive ?? '(sin foco aun)'" style="color:#dbeafe;">(sin foco
                aun)</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(16,185,129,0.20);background:rgba(6,95,70,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#86efac;">Ultima accion</strong>
            <span volt:text="client:focus.lastAction ?? '(sin accion)'" style="color:#d1fae5;">(sin accion)</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.20);background:rgba(120,53,15,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#fcd34d;">Hora del ultimo foco</strong>
            <span volt:text="client:focus.lastActiveAt ?? '(sin hora)'" style="color:#fde68a;">(sin hora)</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#e9d5ff;">Error panel abierto</strong>
            <span volt:text="shared:focus.showErrors ?? false" style="color:#f5d0fe;">false</span>
        </article>
    </div>

    <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
        <article
            style="display:grid;gap:8px;border:1px solid rgba(59,130,246,0.20);background:rgba(30,64,175,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#93c5fd;">Target activo del restore</strong>
            <span data-runtime-check="focus-active-element" style="color:#dbeafe;">(sin target)</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(16,185,129,0.20);background:rgba(6,95,70,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#86efac;">Selection range</strong>
            <span data-runtime-check="focus-selection-range" style="color:#d1fae5;">n/d</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.20);background:rgba(120,53,15,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#fcd34d;">Selection direction</strong>
            <span data-runtime-check="focus-selection-direction" style="color:#fde68a;">none</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#e9d5ff;">Scroll interno del control</strong>
            <span data-runtime-check="focus-selection-scroll-top" style="color:#f5d0fe;">0</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(14,165,233,0.20);background:rgba(8,47,73,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#67e8f9;">Scroll box top</strong>
            <span data-runtime-check="focus-scroll-box-top" style="color:#a5f3fc;">0</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(34,197,94,0.20);background:rgba(20,83,45,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#86efac;">Scroll box left</strong>
            <span data-runtime-check="focus-scroll-box-left" style="color:#dcfce7;">0</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(148,163,184,0.20);background:rgba(15,23,42,0.45);border-radius:16px;padding:16px;">
            <strong style="color:#cbd5e1;">Ultimo motivo del inspector</strong>
            <span data-runtime-check="focus-inspector-reason" style="color:#e2e8f0;">boot</span>
        </article>
    </div>
</section>

<section
    style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
    <a href="/runtimeFocusAlt" volt:navigate
        style="display:inline-flex;align-items:center;border:1px solid rgba(59,130,246,0.30);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
        Probar navegacion a runtimeFocusAlt
    </a>
    <a href="{{ route('spaReactive') }}" volt:navigate
        style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
        Inicio Sistema SPA Full Reactive
    </a>
</section>
</div>
@endsection
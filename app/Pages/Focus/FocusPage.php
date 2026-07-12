<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Focus;

use VoltStack\Runtime\Component\Component;

final class FocusPage extends Component
{
    public string $title = 'Focus Demo';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = sprintf('focus-%s', substr((string) microtime(true), -6));
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
        if (window.__voltFocusDemoInstalled) {
            return;
        }

        window.__voltFocusDemoInstalled = true;

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

        document.addEventListener('focusin', (event) => {
            const target = event && event.target && typeof event.target === 'object' ? event.target : null;
            const focusId = target && typeof target.getAttribute === 'function' && target.getAttribute(
                    'data-focus-id') ?
                target.getAttribute('data-focus-id') :
                target && target.id ?
                target.id :
                target && target.name ?
                target.name :
                target && target.tagName ?
                target.tagName.toLowerCase() :
                '(sin target)';

            setClient('focus.lastActive', focusId);
            setClient('focus.lastActiveAt', new Date().toLocaleTimeString());
        });
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
                El bridge del demo escucha <code>focusin</code> y escribe el último target activo en
                <code>window.Volt.state</code>.
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
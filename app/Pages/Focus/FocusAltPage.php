<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Focus;

use VoltStack\Runtime\Component\Component;

final class FocusAltPage extends Component
{
    public string $title = 'Focus Alt';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="focus-alt-mode">
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
<div style="display:grid;gap:20px;max-inline-size:1040px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(14,165,233,0.24);background:linear-gradient(135deg,rgba(8,47,73,0.90),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#ecfeff;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(34,211,238,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#67e8f9;">Runtime
            Focus Alt</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:34px;line-height:1.1;">{{ $title }}</h1>
            <p style="margin:0;color:#a5f3fc;line-height:1.75;max-inline-size:74ch;">
                Esta ruta sirve para validar navegación SPA. Las banderas del scope
                <code>shared:focus.*</code> pueden volver a activar foco aquí, mientras que las del scope cliente de la
                pantalla anterior no deberían arrastrarse a esta URL.
            </p>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Targets de foco en la ruta alterna</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Si llegas con <code>shared:focus.returnAction === true</code> o
                <code>shared:focus.showErrors === true</code>,
                el primer montaje de esta vista debe poder enfocar el target correspondiente.
            </p>
        </div>

        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
            <article
                style="display:grid;gap:14px;border:1px solid rgba(245,158,11,0.24);background:rgba(120,53,15,0.14);border-radius:18px;padding:18px;color:#fde68a;">
                <strong style="color:#fcd34d;"><code>volt:focus="shared:focus.returnAction"</code></strong>
                <button type="button" data-focus-id="alt-return-action" volt:focus="shared:focus.returnAction"
                    style="inline-size:max-content;border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.22);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                    Accion compartida de retorno
                </button>
                <span style="color:#fde68a;">Este botón permite comprobar foco compartido tras navegar por SPA.</span>
            </article>

            <article
                style="display:grid;gap:14px;border:1px solid rgba(34,197,94,0.24);background:rgba(6,78,59,0.14);border-radius:18px;padding:18px;color:#d1fae5;">
                <strong style="color:#86efac;">Reactivar desde esta ruta</strong>
                <div style="display:flex;flex-wrap:wrap;gap:12px;">
                    <button type="button"
                        volt:on="click -> state:set shared:focus.returnAction = false | click -> state:set shared:focus.returnAction = true | click -> state:set client:focus.lastAction = 'alt-focus-return-action'"
                        style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.18);color:#dcfce7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Focar este boton otra vez
                    </button>
                    <button type="button"
                        volt:on="click -> state:set shared:focus.showErrors = false | click -> state:set shared:focus.showErrors = true | click -> state:set client:focus.lastAction = 'alt-open-error-panel'"
                        style="border:1px solid rgba(168,85,247,0.28);background:rgba(88,28,135,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Abrir panel con autofocus
                    </button>
                </div>
            </article>
        </div>
    </section>

    <section volt:show="shared:focus.showErrors === true"
        style="display:grid;gap:16px;border:1px solid rgba(168,85,247,0.24);background:rgba(88,28,135,0.18);border-radius:20px;padding:24px;color:#f5d0fe;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Autofocus compartido</h2>
            <p style="margin:0;color:#e9d5ff;line-height:1.7;">
                Si esta bandera llega en <code>true</code> desde la ruta anterior, esta textarea debe poder recibir foco
                en el primer sync útil de la vista.
            </p>
        </div>

        <textarea data-focus-id="alt-error-summary" rows="4" volt:autofocus.when="shared:focus.showErrors"
            placeholder="Resumen de error en la ruta alterna"
            style="inline-size:100%;border:1px solid rgba(192,132,252,0.32);background:#2e1065;color:#faf5ff;border-radius:10px;padding:10px 12px;"></textarea>

        <button type="button"
            volt:on="click -> state:set shared:focus.showErrors = false | click -> state:set client:focus.lastAction = 'alt-close-error-panel'"
            style="inline-size:max-content;border:1px solid rgba(192,132,252,0.30);background:rgba(76,29,149,0.24);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
            Cerrar panel
        </button>
    </section>

    <section
        style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <article
            style="display:grid;gap:8px;border:1px solid rgba(14,165,233,0.20);background:rgba(8,47,73,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#67e8f9;">Ultimo elemento enfocado</strong>
            <span volt:text="client:focus.lastActive ?? '(sin foco aun)'" style="color:#a5f3fc;">(sin foco aun)</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.20);background:rgba(120,53,15,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#fcd34d;">Ultima accion</strong>
            <span volt:text="client:focus.lastAction ?? '(sin accion)'" style="color:#fde68a;">(sin accion)</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#e9d5ff;">Error panel abierto</strong>
            <span volt:text="shared:focus.showErrors ?? false" style="color:#f5d0fe;">false</span>
        </article>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeFocus" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(59,130,246,0.30);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver a runtimeFocus
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection
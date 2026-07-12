<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Html;

use VoltStack\Runtime\Component\Component;

final class HtmlPage extends Component
{
    public string $title = 'Html Demo';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = sprintf('html-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="html-mode">
<script data-volt-head-key="html-demo-bridge">
    (() => {
        if (window.__voltHtmlDemoInstalled) {
            return;
        }

        window.__voltHtmlDemoInstalled = true;

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

        function setShared(path, value) {
            const api = state();

            if (!api) {
                return;
            }

            api.set(path, value, {
                scope: 'shared'
            });
        }

        const samples = {
            clientCard: `
<article style="display:grid;gap:12px;border:1px solid rgba(14,165,233,0.28);background:rgba(8,47,73,0.75);border-radius:16px;padding:16px;color:#cffafe;">
  <strong style="color:#67e8f9;">Client preview from volt:html</strong>
  <p style="margin:0;line-height:1.7;" volt:text="shared:html.message ?? 'Shared message pending'">Shared message pending</p>
  <button type="button" volt:on="click -> state:set shared:html.showNote = false | click -> state:set shared:html.showNote = true | click -> state:set shared:html.lastAction = 'inner-client-button'" style="inline-size:max-content;border:1px solid rgba(34,211,238,0.28);background:rgba(8,47,73,0.92);color:#ecfeff;border-radius:10px;padding:8px 12px;cursor:pointer;">
    Trigger nested volt:on
  </button>
  <p volt:show="shared:html.showNote === true" style="margin:0;color:#a5f3fc;">This note is controlled by a directive inside injected HTML.</p>
</article>`,
            sharedCard: `
<section style="display:grid;gap:12px;border:1px solid rgba(168,85,247,0.28);background:rgba(88,28,135,0.70);border-radius:16px;padding:16px;color:#f5d0fe;">
  <strong style="color:#e9d5ff;">Shared fragment from volt:html</strong>
  <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
    <span volt:text="shared:html.message ?? 'No shared message yet'" style="color:#f5d0fe;">No shared message yet</span>
    <button type="button" volt:on="click -> state:set shared:html.message = 'Shared message changed from nested HTML' | click -> state:set shared:html.lastAction = 'inner-shared-button'" style="border:1px solid rgba(192,132,252,0.28);background:rgba(76,29,149,0.85);color:#faf5ff;border-radius:10px;padding:8px 12px;cursor:pointer;">
      Update shared message
    </button>
  </div>
</section>`
        };

        document.addEventListener('click', (event) => {
            const trigger = event.target && typeof event.target.closest === 'function' ?
                event.target.closest('[data-runtime-html-sample]') :
                null;

            if (!trigger) {
                return;
            }

            event.preventDefault();

            const sample = trigger.getAttribute('data-runtime-html-sample') || '';

            if (sample === 'client-card') {
                setClient('html.preview', samples.clientCard);
                setShared('html.message', 'Shared message from client sample');
                setShared('html.showNote', false);
                setShared('html.lastAction', 'load-client-sample');
                return;
            }

            if (sample === 'shared-card') {
                setShared('html.fragment', samples.sharedCard);
                setShared('html.message', 'Shared message from shared sample');
                setShared('html.lastAction', 'load-shared-sample');
                return;
            }
        });
    })();
</script>
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(14,165,233,0.24);background:linear-gradient(135deg,rgba(8,47,73,0.90),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#ecfeff;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(34,211,238,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#67e8f9;">Runtime
            Html MVP</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#a5f3fc;line-height:1.75;max-inline-size:76ch;">
                Esta pantalla valida <code>volt:html</code> como lectura DOM desde state. El runtime reemplaza el
                contenido interno del contenedor y luego vuelve a activar directivas dentro del HTML inyectado.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(34,211,238,0.18);background:rgba(8,47,73,0.22);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#67e8f9;">Request
                marker</span>
            <strong style="font-size:14px;color:#ecfeff;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#a5f3fc;">Ruta sugerida:
                <code>/runtimeHtml -> /runtimeHtmlAlt</code>.</span>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Acciones del laboratorio</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Puedes escribir HTML manualmente en los textareas o cargar muestras prediseñadas con directivas
                runtime internas.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button" data-runtime-html-sample="client-card"
                style="border:1px solid rgba(34,211,238,0.28);background:rgba(8,47,73,0.18);color:#cffafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Cargar muestra client
            </button>
            <button type="button" data-runtime-html-sample="shared-card"
                style="border:1px solid rgba(168,85,247,0.28);background:rgba(88,28,135,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Cargar muestra shared
            </button>
            <button type="button"
                volt:on="click -> state:set shared:html.showNote = false | click -> state:set shared:html.showNote = true | click -> state:set shared:html.lastAction = 'toggle-shared-note'"
                style="border:1px solid rgba(16,185,129,0.28);background:rgba(6,78,59,0.18);color:#d1fae5;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Mostrar nota shared
            </button>
            <button type="button" volt:on="click -> state:delete client:html | click -> state:delete shared:html"
                style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Reset html state
            </button>
        </div>
    </section>

    <section style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
        <article
            style="display:grid;gap:14px;border:1px solid rgba(34,211,238,0.20);background:rgba(8,47,73,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#67e8f9;">Fuente client HTML</strong>
            <textarea rows="10"
                volt:on="input -> state:set client:html.preview = $event.target.value | input -> state:set shared:html.lastAction = 'client-textarea-input'"
                placeholder="<article><strong>HTML client preview</strong></article>"
                style="inline-size:100%;border:1px solid rgba(34,211,238,0.28);background:#082f49;color:#ecfeff;border-radius:12px;padding:12px;font-family:Consolas,monospace;font-size:13px;line-height:1.6;"></textarea>
            <p style="margin:0;color:#a5f3fc;line-height:1.7;">
                Este contenedor usa <code>volt:html="client:html.preview"</code>. El contenido client deberia resetearse
                al navegar a otra ruta.
            </p>
            <div volt:html="client:html.preview"
                style="display:grid;gap:12px;min-block-size:140px;border:1px dashed rgba(34,211,238,0.30);background:rgba(2,6,23,0.42);border-radius:16px;padding:16px;color:#cffafe;">
                <span style="color:#64748b;">Client preview placeholder</span>
            </div>
        </article>

        <article
            style="display:grid;gap:14px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#e9d5ff;">Fuente shared HTML</strong>
            <textarea rows="10"
                volt:on="input -> state:set shared:html.fragment = $event.target.value | input -> state:set shared:html.lastAction = 'shared-textarea-input'"
                placeholder="<section><em>Shared HTML fragment</em></section>"
                style="inline-size:100%;border:1px solid rgba(192,132,252,0.28);background:#3b0764;color:#faf5ff;border-radius:12px;padding:12px;font-family:Consolas,monospace;font-size:13px;line-height:1.6;"></textarea>
            <p style="margin:0;color:#f5d0fe;line-height:1.7;">
                Este contenedor usa <code>volt:html="shared:html.fragment"</code>. El contenido shared deberia
                mantenerse disponible en <code>/runtimeHtmlAlt</code>.
            </p>
            <div volt:html="shared:html.fragment"
                style="display:grid;gap:12px;min-block-size:140px;border:1px dashed rgba(192,132,252,0.30);background:rgba(15,23,42,0.42);border-radius:16px;padding:16px;color:#f5d0fe;">
                <span style="color:#a78bfa;">Shared preview placeholder</span>
            </div>
        </article>
    </section>

    <section
        style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <article
            style="display:grid;gap:8px;border:1px solid rgba(34,211,238,0.20);background:rgba(8,47,73,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#67e8f9;">Shared message</strong>
            <span volt:text="shared:html.message ?? '(sin mensaje)'" style="color:#cffafe;">(sin mensaje)</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#e9d5ff;">Shared last action</strong>
            <span volt:text="shared:html.lastAction ?? '(sin accion)'" style="color:#f5d0fe;">(sin accion)</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(16,185,129,0.20);background:rgba(6,78,59,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#86efac;">Show note</strong>
            <span volt:text="shared:html.showNote ?? false" style="color:#d1fae5;">false</span>
        </article>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeHtmlAlt" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(34,211,238,0.28);background:rgba(8,47,73,0.18);color:#cffafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Ir a runtimeHtmlAlt
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection
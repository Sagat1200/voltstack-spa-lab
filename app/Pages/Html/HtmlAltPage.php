<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Html;

use VoltStack\Runtime\Component\Component;

final class HtmlAltPage extends Component
{
    public string $title = 'Html Alt';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="html-alt-mode">
<script data-volt-head-key="html-demo-bridge">
    (() => {
        if (window.__voltHtmlAltDemoInstalled) {
            return;
        }

        window.__voltHtmlAltDemoInstalled = true;

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

        document.addEventListener('click', (event) => {
            const trigger = event.target && typeof event.target.closest === 'function' ?
                event.target.closest('[data-runtime-html-alt-action]') :
                null;

            if (!trigger) {
                return;
            }

            event.preventDefault();

            const action = trigger.getAttribute('data-runtime-html-alt-action') || '';

            if (action === 'shared-fragment') {
                setShared('html.fragment', '<section style="display:grid;gap:8px;border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.72);border-radius:16px;padding:16px;color:#fef3c7;"><strong>Shared fragment from alt</strong><span volt:text="shared:html.message ?? \'No message\'">No message</span></section>');
                setShared('html.message', 'Shared fragment set from runtimeHtmlAlt');
                setShared('html.lastAction', 'alt-load-shared-fragment');
                return;
            }

            if (action === 'client-fragment') {
                setClient('html.preview', '<article style="display:grid;gap:8px;border:1px solid rgba(14,165,233,0.28);background:rgba(8,47,73,0.74);border-radius:16px;padding:16px;color:#cffafe;"><strong>Client fragment from alt</strong><span>Client state was recreated on this route.</span></article>');
            }
        });
    })();
</script>
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1080px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(34,211,238,0.24);background:linear-gradient(135deg,rgba(8,47,73,0.90),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#ecfeff;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(34,211,238,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#67e8f9;">Runtime
            Html Alt</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:34px;line-height:1.1;">{{ $title }}</h1>
            <p style="margin:0;color:#a5f3fc;line-height:1.75;max-inline-size:76ch;">
                Esta ruta valida navegación SPA para <code>volt:html</code>: el contenido shared puede sobrevivir al
                cambio de ruta, mientras el contenido client se reconstruye por ruta.
            </p>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Acciones en la ruta alterna</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Usa estos botones para comprobar que la misma directiva puede renderizar HTML desde shared o client en
                esta ruta SPA.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button" data-runtime-html-alt-action="shared-fragment"
                style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.18);color:#fde68a;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Cargar shared fragment
            </button>
            <button type="button" data-runtime-html-alt-action="client-fragment"
                style="border:1px solid rgba(34,211,238,0.28);background:rgba(8,47,73,0.18);color:#cffafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Cargar client fragment
            </button>
            <button type="button"
                volt:on="click -> state:delete client:html | click -> state:delete shared:html"
                style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Reset html state
            </button>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
        <article
            style="display:grid;gap:14px;border:1px solid rgba(34,211,238,0.20);background:rgba(8,47,73,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#67e8f9;">Client HTML on alt route</strong>
            <p style="margin:0;color:#a5f3fc;line-height:1.7;">
                Si vienes desde <code>/runtimeHtml</code>, este bloque deberia quedar vacio salvo que vuelvas a cargar
                contenido client en esta ruta.
            </p>
            <div volt:html="client:html.preview"
                style="display:grid;gap:12px;min-block-size:140px;border:1px dashed rgba(34,211,238,0.30);background:rgba(2,6,23,0.42);border-radius:16px;padding:16px;color:#cffafe;">
                <span style="color:#64748b;">Client html placeholder on alt route</span>
            </div>
        </article>

        <article
            style="display:grid;gap:14px;border:1px solid rgba(250,204,21,0.20);background:rgba(120,53,15,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#fcd34d;">Shared HTML on alt route</strong>
            <p style="margin:0;color:#fde68a;line-height:1.7;">
                Si vienes con fragmento shared cargado, este bloque deberia seguir mostrando contenido y sus directivas
                internas.
            </p>
            <div volt:html="shared:html.fragment"
                style="display:grid;gap:12px;min-block-size:140px;border:1px dashed rgba(250,204,21,0.30);background:rgba(2,6,23,0.42);border-radius:16px;padding:16px;color:#fde68a;">
                <span style="color:#f59e0b;">Shared html placeholder on alt route</span>
            </div>
        </article>
    </section>

    <section
        style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <article style="display:grid;gap:8px;border:1px solid rgba(34,211,238,0.20);background:rgba(8,47,73,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#67e8f9;">Shared message</strong>
            <span volt:text="shared:html.message ?? '(sin mensaje)'" style="color:#cffafe;">(sin mensaje)</span>
        </article>
        <article style="display:grid;gap:8px;border:1px solid rgba(250,204,21,0.20);background:rgba(120,53,15,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#fcd34d;">Shared last action</strong>
            <span volt:text="shared:html.lastAction ?? '(sin accion)'" style="color:#fde68a;">(sin accion)</span>
        </article>
        <article style="display:grid;gap:8px;border:1px solid rgba(16,185,129,0.20);background:rgba(6,78,59,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#86efac;">Show note</strong>
            <span volt:text="shared:html.showNote ?? false" style="color:#d1fae5;">false</span>
        </article>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeHtml" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(34,211,238,0.28);background:rgba(8,47,73,0.18);color:#cffafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver a runtimeHtml
        </a>
        <a href="/" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al inicio
        </a>
    </section>
</div>
@endsection
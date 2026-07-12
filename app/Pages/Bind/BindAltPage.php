<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Bind;

use VoltStack\Runtime\Component\Component;

final class BindAltPage extends Component
{
    public string $title = 'Bind Alt';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="runtime-bind-alt-mode">
<script data-volt-head-key="runtime-bind-demo-bridge">
    (() => {
        if (window.__voltRuntimeBindDemoInstalled) {
            return;
        }

        window.__voltRuntimeBindDemoInstalled = true;

        function state() {
            return window.Volt && window.Volt.state ? window.Volt.state : null;
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
                event.target.closest('[data-runtime-bind-alt-action]') :
                null;

            if (!trigger) {
                return;
            }

            event.preventDefault();

            const action = trigger.getAttribute('data-runtime-bind-alt-action') || '';

            if (action === 'shared-link-alt') {
                setShared('bind.linkUrl', 'https://example.com/alt/runtime-bind');
                setShared('bind.linkTitle', 'Abrir enlace compartido desde alt');
                setShared('bind.lastAction', 'alt-shared-link');
            }
        });
    })();
</script>
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1080px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(56,189,248,0.24);background:linear-gradient(135deg,rgba(8,47,73,0.90),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#f0f9ff;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(125,211,252,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#7dd3fc;">Runtime
            Bind Alt</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:34px;line-height:1.1;">{{ $title }}</h1>
            <p style="margin:0;color:#bae6fd;line-height:1.75;max-inline-size:76ch;">
                Esta ruta valida navegación SPA para <code>volt:bind</code>: el estado
                <code>shared:bind.*</code> puede seguir vivo, mientras el alcance <code>client:bind.*</code> se
                reconstruye por ruta.
            </p>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Acciones en la ruta alterna</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Aquí puedes comprobar que los bindings siguen funcionando en otra vista SPA usando el mismo estado
                compartido.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button"
                volt:on="click -> state:toggle shared:bind.busy | click -> state:set shared:bind.lastAction = 'alt-toggle-busy'"
                style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.18);color:#fde68a;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar disabled
            </button>
            <button type="button" data-runtime-bind-alt-action="shared-link-alt"
                style="border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.18);color:#bae6fd;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Cargar enlace alt
            </button>
            <button type="button" volt:on="click -> state:delete client:bind | click -> state:delete shared:bind"
                style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Reset bind state
            </button>
        </div>
    </section>

    <section style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
        <article
            style="display:grid;gap:14px;border:1px solid rgba(56,189,248,0.20);background:rgba(8,47,73,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#7dd3fc;">Client scope en la ruta alterna</strong>
            <p style="margin:0;color:#bae6fd;line-height:1.7;">
                Si vienes desde <code>/runtimeBind</code>, este input debería volver a baseline salvo que escribas un
                nuevo valor client en esta ruta.
            </p>
            <input type="text" volt:bind:value="client:bind.note" value="SSR alt baseline"
                style="inline-size:100%;border:1px solid rgba(56,189,248,0.28);background:#020617;color:#f0f9ff;border-radius:12px;padding:12px;">
        </article>

        <article
            style="display:grid;gap:14px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#e9d5ff;">Shared scope en la ruta alterna</strong>
            <label style="display:flex;gap:12px;align-items:center;color:#f5d0fe;">
                <input type="checkbox" volt:bind:checked="shared:bind.enabled">
                <span>Checkbox enlazado desde shared</span>
            </label>
            <button type="button" volt:bind:disabled="shared:bind.busy"
                style="inline-size:max-content;border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.18);color:#fde68a;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Boton enlazado a disabled
            </button>
            <a volt:bind:href="shared:bind.linkUrl" volt:bind:title="shared:bind.linkTitle"
                href="https://example.com/ssr-alt-bind" title="SSR alt link title" target="_blank" rel="noreferrer"
                style="display:inline-flex;align-items:center;inline-size:max-content;border:1px solid rgba(192,132,252,0.28);background:rgba(76,29,149,0.24);color:#f5d0fe;border-radius:10px;padding:10px 14px;text-decoration:none;">
                Link enlazado en alt
            </a>
        </article>
    </section>

    <section
        style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <article
            style="display:grid;gap:8px;border:1px solid rgba(56,189,248,0.20);background:rgba(8,47,73,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#7dd3fc;">Client note</strong>
            <span volt:text="client:bind.note ?? '(sin valor)'" style="color:#bae6fd;">(sin valor)</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(16,185,129,0.20);background:rgba(6,78,59,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#86efac;">Enabled</strong>
            <span volt:text="shared:bind.enabled ?? false" style="color:#d1fae5;">false</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(250,204,21,0.20);background:rgba(120,53,15,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#fcd34d;">Busy</strong>
            <span volt:text="shared:bind.busy ?? false" style="color:#fde68a;">false</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#e9d5ff;">Last action</strong>
            <span volt:text="shared:bind.lastAction ?? '(sin accion)'" style="color:#f5d0fe;">(sin accion)</span>
        </article>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeBind" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.18);color:#bae6fd;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver a runtimeBind
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection
<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Bind;

use VoltStack\Runtime\Component\Component;

final class BindPage extends Component
{
    public string $title = 'Bind Demo';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = sprintf('bind-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="bind-mode">
<script data-volt-head-key="bind-demo-bridge">
(() => {
    if (window.__voltBindDemoInstalled) {
        return;
    }

    window.__voltBindDemoInstalled = true;

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

    function svgData(label, background, foreground) {
        return 'data:image/svg+xml;utf8,' + encodeURIComponent(`
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 220">
  <rect width="480" height="220" rx="24" fill="${background}"></rect>
  <text x="40" y="96" fill="${foreground}" font-family="Arial, sans-serif" font-size="26" font-weight="700">${label}</text>
  <text x="40" y="142" fill="${foreground}" font-family="Arial, sans-serif" font-size="16">volt:bind image preview</text>
</svg>`);
    }

    document.addEventListener('click', (event) => {
        const trigger = event.target && typeof event.target.closest === 'function' ?
            event.target.closest('[data-runtime-bind-action]') :
            null;

        if (!trigger) {
            return;
        }

        event.preventDefault();

        const action = trigger.getAttribute('data-runtime-bind-action') || '';

        if (action === 'preset-link-docs') {
            setShared('bind.linkUrl', 'https://example.com/docs/runtime-bind');
            setShared('bind.linkTitle', 'Abrir documentacion runtime bind');
            setShared('bind.lastAction', 'preset-link-docs');
            return;
        }

        if (action === 'preset-link-status') {
            setShared('bind.linkUrl', 'https://example.com/status/runtime-bind');
            setShared('bind.linkTitle', 'Abrir status runtime bind');
            setShared('bind.lastAction', 'preset-link-status');
            return;
        }

        if (action === 'preset-image-ocean') {
            setShared('bind.previewUrl', svgData('Ocean Preview', '#082f49', '#ecfeff'));
            setShared('bind.imageTitle', 'Ocean preview title');
            setShared('bind.lastAction', 'preset-image-ocean');
            return;
        }

        if (action === 'preset-image-amber') {
            setShared('bind.previewUrl', svgData('Amber Preview', '#78350f', '#fef3c7'));
            setShared('bind.imageTitle', 'Amber preview title');
            setShared('bind.lastAction', 'preset-image-amber');
        }
    });
})();
</script>
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(56,189,248,0.24);background:linear-gradient(135deg,rgba(12,74,110,0.90),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#f0f9ff;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(125,211,252,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#7dd3fc;">Runtime
            Bind MVP</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#bae6fd;line-height:1.75;max-inline-size:76ch;">
                Esta pantalla valida <code>volt:bind</code> como reflejo unidireccional DOM &lt;- state para
                propiedades como <code>value</code>, <code>checked</code>, <code>disabled</code>,
                <code>href</code>, <code>src</code>, <code>title</code> y <code>placeholder</code>.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(125,211,252,0.18);background:rgba(12,74,110,0.22);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#7dd3fc;">Request
                marker</span>
            <strong style="font-size:14px;color:#f0f9ff;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#bae6fd;">Ruta sugerida:
                <code>/runtimeBind -> /runtimeBindAlt</code>.</span>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Controles que mutan el state</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Estos controles escriben en <code>window.Volt.state</code>; los targets con <code>volt:bind</code>
                solo reflejan el valor resultante.
            </p>
        </div>

        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
            <label style="display:grid;gap:8px;color:#cbd5e1;">
                <span>Control client para <code>value</code></span>
                <input type="text"
                    volt:on="input -> state:set client:bind.note = $event.target.value | input -> state:set shared:bind.lastAction = 'client-note-input'"
                    placeholder="Escribe aqui para actualizar el input enlazado"
                    style="inline-size:100%;border:1px solid rgba(56,189,248,0.28);background:#082f49;color:#f0f9ff;border-radius:12px;padding:12px;">
            </label>

            <label style="display:grid;gap:8px;color:#cbd5e1;">
                <span>Control shared para <code>checked</code> y <code>placeholder</code></span>
                <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
                    <input type="checkbox"
                        volt:on="change -> state:set shared:bind.enabled = $event.target.checked | change -> state:set shared:bind.lastAction = 'toggle-enabled'">
                    <input type="text"
                        volt:on="input -> state:set shared:bind.placeholder = $event.target.value | input -> state:set shared:bind.lastAction = 'placeholder-input'"
                        placeholder="Placeholder enlazado"
                        style="flex:1;border:1px solid rgba(16,185,129,0.28);background:#022c22;color:#ecfdf5;border-radius:12px;padding:12px;">
                </div>
            </label>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button"
                volt:on="click -> state:toggle shared:bind.busy | click -> state:set shared:bind.lastAction = 'toggle-busy'"
                style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.18);color:#fde68a;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar disabled
            </button>
            <button type="button" data-runtime-bind-action="preset-link-docs"
                style="border:1px solid rgba(125,211,252,0.28);background:rgba(12,74,110,0.18);color:#bae6fd;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Cargar link docs
            </button>
            <button type="button" data-runtime-bind-action="preset-link-status"
                style="border:1px solid rgba(125,211,252,0.28);background:rgba(8,47,73,0.18);color:#e0f2fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Cargar link status
            </button>
            <button type="button" data-runtime-bind-action="preset-image-ocean"
                style="border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.18);color:#bae6fd;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Cargar imagen ocean
            </button>
            <button type="button" data-runtime-bind-action="preset-image-amber"
                style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.18);color:#fde68a;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Cargar imagen amber
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
            <strong style="color:#7dd3fc;">Targets con <code>volt:bind</code></strong>

            <label style="display:grid;gap:8px;color:#e0f2fe;">
                <span><code>volt:bind:value="client:bind.note"</code></span>
                <input type="text" volt:bind:value="client:bind.note" value="SSR value baseline"
                    style="inline-size:100%;border:1px solid rgba(56,189,248,0.28);background:#020617;color:#f0f9ff;border-radius:12px;padding:12px;">
            </label>

            <label style="display:grid;gap:8px;color:#e0f2fe;">
                <span><code>volt:bind:placeholder="shared:bind.placeholder"</code></span>
                <input type="text" volt:bind:placeholder="shared:bind.placeholder"
                    placeholder="SSR placeholder baseline"
                    style="inline-size:100%;border:1px solid rgba(16,185,129,0.28);background:#020617;color:#f0fdf4;border-radius:12px;padding:12px;">
            </label>

            <label style="display:flex;gap:12px;align-items:center;color:#e0f2fe;">
                <input type="checkbox" volt:bind:checked="shared:bind.enabled">
                <span><code>volt:bind:checked="shared:bind.enabled"</code></span>
            </label>

            <button type="button" volt:bind:disabled="shared:bind.busy"
                style="inline-size:max-content;border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.18);color:#fde68a;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Boton enlazado a disabled
            </button>
        </article>

        <article
            style="display:grid;gap:14px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#e9d5ff;">Bindings textuales y media</strong>

            <a volt:bind:href="shared:bind.linkUrl" volt:bind:title="shared:bind.linkTitle"
                href="https://example.com/ssr-bind" title="SSR link title" target="_blank" rel="noreferrer"
                style="display:inline-flex;align-items:center;inline-size:max-content;border:1px solid rgba(192,132,252,0.28);background:rgba(76,29,149,0.24);color:#f5d0fe;border-radius:10px;padding:10px 14px;text-decoration:none;">
                Link enlazado
            </a>

            <img volt:bind:src="shared:bind.previewUrl" volt:bind:title="shared:bind.imageTitle"
                src="data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 480 220'%3E%3Crect width='480' height='220' rx='24' fill='%230f172a'%3E%3C/rect%3E%3Ctext x='40' y='96' fill='%23e2e8f0' font-family='Arial' font-size='26' font-weight='700'%3ESSR Preview%3C/text%3E%3C/svg%3E"
                title="SSR image title" alt="Runtime bind preview"
                style="inline-size:100%;border:1px solid rgba(192,132,252,0.22);border-radius:16px;background:#020617;">

            <p style="margin:0;color:#ddd6fe;line-height:1.7;">
                El <code>src</code> y el <code>title</code> de esta imagen se actualizan desde estado compartido.
            </p>
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
        <a href="/runtimeBindAlt" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.18);color:#bae6fd;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Ir a runtimeBindAlt
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection
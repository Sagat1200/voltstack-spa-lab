<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Model;

use VoltStack\Runtime\Component\Component;

final class ModelLocalAltPage extends Component
{
    public string $title = 'Model Local Alt';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="model-local-alt-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1080px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(16,185,129,0.24);background:linear-gradient(135deg,rgba(6,78,59,0.90),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#ecfdf5;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(110,231,183,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#86efac;">Runtime
            Model Local Alt</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:34px;line-height:1.1;">{{ $title }}</h1>
            <p style="margin:0;color:#d1fae5;line-height:1.75;max-inline-size:76ch;">
                Esta ruta valida navegación SPA para <code>volt:model.local</code>: el estado
                <code>shared</code> puede mantenerse entre rutas, mientras el alcance <code>client</code> se resetea por
                URL.
            </p>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Controles en la ruta alterna</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Si llegas desde <code>/runtimeModelLocal</code>, los campos ligados a <code>shared</code> deben
                resincronizarse con el valor actual del store. Los ligados a <code>client</code> no deberían arrastrar
                el
                mismo valor.
            </p>
        </div>

        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
            <label style="display:grid;gap:8px;color:#d1fae5;">
                <span><code>volt:model.local="client:draft.note"</code></span>
                <input type="text" volt:model.local="client:draft.note" value="SSR alt note"
                    placeholder="Nota client en ruta alterna"
                    style="inline-size:100%;border:1px solid rgba(16,185,129,0.28);background:#022c22;color:#f0fdf4;border-radius:12px;padding:12px;">
            </label>

            <label style="display:flex;gap:12px;align-items:center;color:#d1fae5;">
                <input type="checkbox" volt:model.local="shared:ui.enabled">
                <span><code>volt:model.local="shared:ui.enabled"</code></span>
            </label>

            <label style="display:grid;gap:8px;color:#d1fae5;">
                <span><code>volt:model.local="shared:filters.category"</code></span>
                <select volt:model.local="shared:filters.category"
                    style="inline-size:100%;border:1px solid rgba(250,204,21,0.28);background:#1c1917;color:#fef3c7;border-radius:12px;padding:12px;">
                    <option value="backlog">Backlog</option>
                    <option value="review">Review</option>
                    <option value="done">Done</option>
                </select>
            </label>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button"
                volt:on="click -> state:set shared:ui.enabled = true | click -> state:set shared:filters.category = 'done'"
                style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.18);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Mutar shared desde alt
            </button>
            <button type="button"
                volt:on="click -> state:delete client:draft | click -> state:delete shared:ui | click -> state:delete shared:filters"
                style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Reset state
            </button>
        </div>
    </section>

    <section style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
        <article
            style="display:grid;gap:14px;border:1px solid rgba(16,185,129,0.20);background:rgba(6,78,59,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#86efac;">Lecturas del store</strong>
            <div style="display:grid;gap:10px;">
                <span>Client note:</span>
                <strong volt:text="client:draft.note ?? '(sin nota)'" style="color:#f0fdf4;">(sin nota)</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>Shared enabled:</span>
                <strong volt:text="shared:ui.enabled ?? false" style="color:#d1fae5;">false</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>Shared category:</span>
                <strong volt:text="shared:filters.category ?? '(sin categoria)'" style="color:#fef3c7;">(sin
                    categoria)</strong>
            </div>
        </article>

        <article
            style="display:grid;gap:14px;border:1px solid rgba(59,130,246,0.20);background:rgba(30,64,175,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#bfdbfe;">Mirrors con <code>volt:bind</code></strong>
            <input type="text" volt:bind:value="client:draft.note" value="SSR alt bind note"
                style="inline-size:100%;border:1px solid rgba(96,165,250,0.28);background:#020617;color:#eff6ff;border-radius:12px;padding:12px;">
            <label style="display:flex;gap:12px;align-items:center;color:#dbeafe;">
                <input type="checkbox" volt:bind:checked="shared:ui.enabled">
                <span>Checkbox reflejado</span>
            </label>
        </article>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeModelLocal" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,78,59,0.18);color:#d1fae5;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver a runtimeModelLocal
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection
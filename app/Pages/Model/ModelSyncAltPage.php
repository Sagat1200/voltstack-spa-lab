<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Model;

use VoltStack\Runtime\Component\Component;

final class ModelSyncAltPage extends Component
{
    public string $title = 'Model Sync Alt';

    public string $serverTitle = 'SSR alt sync title';

    public bool $serverEnabled = false;

    public string $serverCategory = 'backlog';

    public string $serverAliasMirror = 'SSR alt alias mirror';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="model-sync-alt-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1080px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(56,189,248,0.24);background:linear-gradient(135deg,rgba(8,47,73,0.92),rgba(15,23,42,0.96));border-radius:24px;padding:32px;color:#e0f2fe;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(125,211,252,0.28);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#7dd3fc;">Runtime
            Model Sync Alt</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:34px;line-height:1.1;">{{ $title }}</h1>
            <p style="margin:0;color:#bae6fd;line-height:1.75;max-inline-size:76ch;">
                Esta ruta alterna comprueba que el valor <code>client</code> de <code>volt:model.sync</code> se reinicia
                al navegar, mientras el alcance <code>shared</code> puede seguir vivo entre pantallas SPA.
            </p>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Controles en la ruta alterna</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Si llegas desde <code>/runtimeModelSync</code>, el campo de <code>client</code> no debe arrastrar el
                mismo valor, pero los de <code>shared</code> sí pueden resincronizarse con el store actual.
            </p>
        </div>

        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
            <label data-runtime-check="runtime-model-sync-alt-client-title-field"
                style="display:grid;gap:8px;color:#dbeafe;">
                <span><code>volt:model.sync="client:sync.title"</code></span>
                <input type="text" name="serverTitle" volt:model.sync="client:sync.title" value="SSR alt sync title"
                    placeholder="Título client en ruta alterna"
                    style="inline-size:100%;border:1px solid rgba(56,189,248,0.28);background:#082f49;color:#f0f9ff;border-radius:12px;padding:12px;">
            </label>

            <label data-runtime-check="runtime-model-sync-alt-shared-enabled-field"
                style="display:flex;gap:12px;align-items:center;color:#dbeafe;">
                <input type="checkbox" name="serverEnabled" volt:model.sync="shared:sync.enabled">
                <span><code>volt:model.sync="shared:sync.enabled"</code></span>
            </label>

            <label data-runtime-check="runtime-model-sync-alt-shared-category-field"
                style="display:grid;gap:8px;color:#dbeafe;">
                <span><code>volt:model.sync="shared:sync.category"</code></span>
                <select name="serverCategory" volt:model.sync="shared:sync.category"
                    style="inline-size:100%;border:1px solid rgba(250,204,21,0.28);background:#1c1917;color:#fef3c7;border-radius:12px;padding:12px;">
                    <option value="backlog">Backlog</option>
                    <option value="review">Review</option>
                    <option value="done">Done</option>
                </select>
            </label>

            <label data-runtime-check="runtime-model-sync-alt-alias-field" style="display:grid;gap:8px;color:#dbeafe;">
                <span><code>client:sync.alias -&gt; updates.serverAliasMirror</code></span>
                <input type="text" volt:model.sync="client:sync.alias"
                    data-volt-state-sync="client:sync.alias->updates.serverAliasMirror" value="SSR alt alias mirror"
                    placeholder="Alias con mapeo explícito"
                    style="inline-size:100%;border:1px solid rgba(167,139,250,0.28);background:#1e1b4b;color:#ede9fe;border-radius:12px;padding:12px;">
            </label>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <div volt:loading="__volt_sync__" volt:loading.delay="80ms" volt:loading.min-duration="240ms"
                style="border:1px solid rgba(125,211,252,0.22);background:rgba(8,47,73,0.34);color:#bae6fd;border-radius:999px;padding:8px 12px;font-size:13px;">
                Sincronizando...
            </div>
            <div volt:success="__volt_sync__"
                style="border:1px solid rgba(74,222,128,0.24);background:rgba(20,83,45,0.24);color:#dcfce7;border-radius:999px;padding:8px 12px;font-size:13px;">
                Sync confirmada.
            </div>
        </div>
    </section>

    <section style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
        <article
            style="display:grid;gap:14px;border:1px solid rgba(56,189,248,0.20);background:rgba(8,47,73,0.22);border-radius:18px;padding:18px;color:#e0f2fe;">
            <strong style="color:#7dd3fc;">Store runtime actual</strong>
            <div style="display:grid;gap:10px;">
                <span>Client title:</span>
                <strong data-runtime-check="runtime-model-sync-alt-client-title-store"
                    volt:text="client:sync.title ?? '(sin titulo)'" style="color:#f0f9ff;">(sin titulo)</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>Shared enabled:</span>
                <strong data-runtime-check="runtime-model-sync-alt-shared-enabled-store"
                    volt:text="shared:sync.enabled ?? false" style="color:#dbeafe;">false</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>Shared category:</span>
                <strong data-runtime-check="runtime-model-sync-alt-shared-category-store"
                    volt:text="shared:sync.category ?? '(sin categoria)'" style="color:#fef3c7;">(sin
                    categoria)</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>Alias client:</span>
                <strong data-runtime-check="runtime-model-sync-alt-client-alias-store"
                    volt:text="client:sync.alias ?? '(sin alias)'" style="color:#ede9fe;">(sin alias)</strong>
            </div>
        </article>

        <article
            style="display:grid;gap:14px;border:1px solid rgba(14,165,233,0.20);background:rgba(2,132,199,0.18);border-radius:18px;padding:18px;color:#e0f2fe;">
            <strong style="color:#bae6fd;">Mirror backend de esta ruta</strong>
            <div style="display:grid;gap:10px;">
                <span>serverTitle:</span>
                <strong data-runtime-check="runtime-model-sync-alt-server-title"
                    data-volt-target="sync-server-title">{{ $serverTitle }}</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>serverEnabled:</span>
                <strong data-runtime-check="runtime-model-sync-alt-server-enabled"
                    data-volt-target="sync-server-enabled">{{ $serverEnabled ? 'true' : 'false' }}</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>serverCategory:</span>
                <strong data-runtime-check="runtime-model-sync-alt-server-category"
                    data-volt-target="sync-server-category" style="color:#fde68a;">{{ $serverCategory }}</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>serverAliasMirror:</span>
                <strong data-runtime-check="runtime-model-sync-alt-server-alias" data-volt-target="sync-server-alias"
                    style="color:#ddd6fe;">{{ $serverAliasMirror }}</strong>
            </div>
        </article>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeModelSync" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.18);color:#bae6fd;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver a runtimeModelSync
        </a>
        <a href="/" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al inicio
        </a>
    </section>
</div>
@endsection
<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Model;

use VoltStack\Runtime\Component\Component;

final class ModelSyncPage extends Component
{
    public string $title = 'Model Sync Demo';

    public string $requestMarker;

    public string $serverTitle = 'SSR sync title';

    public string $serverBody = 'SSR sync body';

    public bool $serverEnabled = true;

    public string $serverCategory = 'review';

    public string $serverAliasMirror = 'SSR alias mirror';

    public function mount(): void
    {
        $this->requestMarker = sprintf('model-sync-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="runtime-model-sync-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(56,189,248,0.24);background:linear-gradient(135deg,rgba(8,47,73,0.92),rgba(15,23,42,0.96));border-radius:24px;padding:32px;color:#e0f2fe;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(125,211,252,0.28);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#7dd3fc;">Runtime
            Model Sync MVP</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#bae6fd;line-height:1.75;max-inline-size:78ch;">
                Esta pantalla valida <code>volt:model.sync</code> como binding optimista: primero escribe en
                <code>window.Volt.state</code> y luego agenda una sincronización reactiva al backend con debounce fijo.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(125,211,252,0.18);background:rgba(8,47,73,0.34);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#7dd3fc;">Request
                marker</span>
            <strong style="font-size:14px;color:#f0f9ff;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#bae6fd;">
                El MVP usa <code>__volt_sync__</code> como acción interna y debounce fijo de <code>220ms</code>.
            </span>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Formulario optimista con respaldo backend</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Los primeros cuatro campos usan el atributo <code>name</code> como fallback hacia
                <code>updates.&lt;name&gt;</code>. El quinto demuestra mapeo explícito con
                <code>data-volt-state-sync</code>.
            </p>
        </div>

        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
            <label style="display:grid;gap:8px;color:#dbeafe;">
                <span><code>volt:model.sync="client:sync.title"</code> + <code>name="serverTitle"</code></span>
                <input type="text" name="serverTitle" volt:model.sync="client:sync.title" value="SSR sync title"
                    placeholder="Escribe un título optimista"
                    style="inline-size:100%;border:1px solid rgba(56,189,248,0.28);background:#082f49;color:#f0f9ff;border-radius:12px;padding:12px;">
            </label>

            <label style="display:grid;gap:8px;color:#dbeafe;">
                <span><code>volt:model.sync="client:sync.body"</code> + <code>name="serverBody"</code></span>
                <textarea rows="5" name="serverBody" volt:model.sync="client:sync.body"
                    style="inline-size:100%;border:1px solid rgba(34,211,238,0.28);background:#082f49;color:#ecfeff;border-radius:12px;padding:12px;">SSR sync body</textarea>
            </label>

            <label style="display:flex;gap:12px;align-items:center;color:#dbeafe;">
                <input type="checkbox" name="serverEnabled" volt:model.sync="shared:sync.enabled" checked>
                <span><code>volt:model.sync="shared:sync.enabled"</code></span>
            </label>

            <label style="display:grid;gap:8px;color:#dbeafe;">
                <span><code>volt:model.sync="shared:sync.category"</code> + <code>name="serverCategory"</code></span>
                <select name="serverCategory" volt:model.sync="shared:sync.category"
                    style="inline-size:100%;border:1px solid rgba(250,204,21,0.28);background:#1c1917;color:#fef3c7;border-radius:12px;padding:12px;">
                    <option value="backlog">Backlog</option>
                    <option value="review" selected>Review</option>
                    <option value="done">Done</option>
                </select>
            </label>

            <label style="display:grid;gap:8px;color:#dbeafe;">
                <span><code>data-volt-state-sync="client:sync.alias-&gt;updates.serverAliasMirror"</code></span>
                <input type="text" volt:model.sync="client:sync.alias"
                    data-volt-state-sync="client:sync.alias->updates.serverAliasMirror" value="SSR alias mirror"
                    placeholder="Mapeo explícito sin depender de name"
                    style="inline-size:100%;border:1px solid rgba(167,139,250,0.28);background:#1e1b4b;color:#ede9fe;border-radius:12px;padding:12px;">
            </label>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <div volt:loading="__volt_sync__" volt:loading.delay="80ms" volt:loading.min-duration="240ms"
                style="border:1px solid rgba(125,211,252,0.22);background:rgba(8,47,73,0.34);color:#bae6fd;border-radius:999px;padding:8px 12px;font-size:13px;">
                Sincronizando con backend...
            </div>
            <div volt:success="__volt_sync__"
                style="border:1px solid rgba(74,222,128,0.24);background:rgba(20,83,45,0.24);color:#dcfce7;border-radius:999px;padding:8px 12px;font-size:13px;">
                Backend confirmado para la última edición.
            </div>
            <div volt:error="__volt_sync__" volt:error.timeout="3s"
                style="border:1px solid rgba(248,113,113,0.24);background:rgba(127,29,29,0.22);color:#fee2e2;border-radius:999px;padding:8px 12px;font-size:13px;">
                La sincronización falló.
            </div>
        </div>
    </section>

    <section style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
        <article
            style="display:grid;gap:14px;border:1px solid rgba(56,189,248,0.20);background:rgba(8,47,73,0.22);border-radius:18px;padding:18px;color:#e0f2fe;">
            <strong style="color:#7dd3fc;">Store optimista inmediato</strong>
            <div style="display:grid;gap:10px;">
                <span>Título client:</span>
                <strong volt:text="client:sync.title ?? '(sin titulo)'" style="color:#f0f9ff;">(sin titulo)</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>Body client:</span>
                <p volt:text="client:sync.body ?? '(sin body)'" style="margin:0;color:#bae6fd;">(sin body)</p>
            </div>
            <div style="display:grid;gap:10px;">
                <span>Enabled shared:</span>
                <strong volt:text="shared:sync.enabled ?? false" style="color:#dbeafe;">false</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>Category shared:</span>
                <strong volt:text="shared:sync.category ?? '(sin categoria)'" style="color:#fef3c7;">(sin
                    categoria)</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>Alias client:</span>
                <strong volt:text="client:sync.alias ?? '(sin alias)'" style="color:#ede9fe;">(sin alias)</strong>
            </div>
        </article>

        <article
            style="display:grid;gap:14px;border:1px solid rgba(14,165,233,0.20);background:rgba(2,132,199,0.18);border-radius:18px;padding:18px;color:#e0f2fe;">
            <strong style="color:#bae6fd;">Mirror renderizado por el servidor</strong>
            <p style="margin:0;color:#dbeafe;line-height:1.7;">
                Estos valores viven en propiedades públicas del componente y solo cambian cuando el backend confirma la
                sincronización.
            </p>
            <div style="display:grid;gap:10px;">
                <span>serverTitle:</span>
                <strong data-volt-target="sync-server-title" style="color:#f0f9ff;">{{ $serverTitle }}</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>serverBody:</span>
                <p data-volt-target="sync-server-body" style="margin:0;color:#e0f2fe;white-space:pre-wrap;">
                    {{ $serverBody }}
                </p>
            </div>
            <div style="display:grid;gap:10px;">
                <span>serverEnabled:</span>
                <strong data-volt-target="sync-server-enabled">{{ $serverEnabled ? 'true' : 'false' }}</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>serverCategory:</span>
                <strong data-volt-target="sync-server-category" style="color:#fde68a;">{{ $serverCategory }}</strong>
            </div>
            <div style="display:grid;gap:10px;">
                <span>serverAliasMirror:</span>
                <strong data-volt-target="sync-server-alias" style="color:#ddd6fe;">{{ $serverAliasMirror }}</strong>
            </div>
        </article>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeModelSyncAlt" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.18);color:#bae6fd;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Ir a runtimeModelSyncAlt
        </a>
        <a href="/" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al inicio
        </a>
    </section>
</div>
@endsection
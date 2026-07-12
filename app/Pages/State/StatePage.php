<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\State;

use VoltStack\Runtime\Component\Component;
use VoltStack\Runtime\Protocol\ActionEffectOptions;
use VoltStack\Runtime\Protocol\ActionManualEffectBuilder;

final class StatePage extends Component
{
    public string $title = 'State Demo';

    public string $requestMarker;

    public string $lastClientNote = 'Aun no sincronizado.';

    public string $lastSharedNote = 'Aun no sincronizado.';

    public int $sharedCounterMirror = 0;

    public string $lastSyncReport = '{"waiting":"Sin request reactiva aun."}';

    public function mount(): void
    {
        $this->requestMarker = sprintf('state-%s', substr((string) microtime(true), -6));
    }

    public function captureSelectiveSync(string $clientNote = '', string $sharedNote = ''): ActionEffectOptions
    {
        $this->lastClientNote = $clientNote !== '' ? $clientNote : '(vacio)';
        $this->lastSharedNote = $sharedNote !== '' ? $sharedNote : '(vacio)';
        $syncedAt = date('H:i:s');

        $report = [
            'clientNote' => $this->lastClientNote,
            'sharedNote' => $this->lastSharedNote,
            'sharedCounterMirror' => $this->sharedCounterMirror,
            'syncedAt' => $syncedAt,
        ];

        $this->lastSyncReport = (string) json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return ActionEffectOptions::make()
            ->effects(fn(ActionManualEffectBuilder $effects) => $effects
                ->stateMerge('shared', 'serverSync', [
                    'clientNote' => $this->lastClientNote,
                    'sharedNote' => $this->lastSharedNote,
                    'sharedCounterMirror' => $this->sharedCounterMirror,
                    'syncedAt' => $syncedAt,
                    'source' => 'captureSelectiveSync',
                ])
                ->event('demo.state.synced', $report)
                ->end());
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="runtime-state-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(34,197,94,0.24);background:linear-gradient(135deg,rgba(6,78,59,0.88),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#ecfdf5;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(134,239,172,0.32);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#bbf7d0;">Client
            State MVP</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#d1fae5;line-height:1.75;max-inline-size:72ch;">
                Esta pantalla valida el nuevo contrato de <code>window.Volt.state</code>. El
                <code>client state</code> vive en la URL actual y se limpia al navegar a otra vista SPA, mientras que
                el <code>shared state</code> sigue disponible entre pantallas.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(134,239,172,0.18);background:rgba(6,78,59,0.28);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#86efac;">Request
                marker</span>
            <strong style="font-size:14px;color:#f0fdf4;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#d1fae5;">Si cambia al volver, el documento se re-renderizo y el estado
                cliente se evaluo de nuevo para la nueva URL.</span>
        </div>
    </section>

    <section data-volt-state-example
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Controles del store runtime</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Usa estos botones para mutar el store actual y luego navega a la otra ruta para comprobar que el
                estado cliente se reinicia, pero el compartido se conserva.
            </p>
        </div>

        <div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article
                style="display:grid;gap:12px;border:1px solid rgba(34,197,94,0.20);background:rgba(6,78,59,0.18);border-radius:16px;padding:18px;">
                <strong style="color:#bbf7d0;">Client state</strong>
                <input type="text" data-volt-state-input="client-note" placeholder="Nota local de esta pantalla"
                    style="inline-size:100%;border:1px solid rgba(148,163,184,0.18);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button" data-volt-state-action="increment-client-counter"
                        style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.18);color:#dcfce7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Incrementar client.counter
                    </button>
                    <button type="button" data-volt-state-action="save-client-note"
                        style="border:1px solid rgba(96,165,250,0.28);background:rgba(14,116,144,0.16);color:#e0f2fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Guardar client.draft
                    </button>
                    <button type="button" data-volt-state-action="clear-client"
                        style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.16);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Limpiar client
                    </button>
                </div>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.18);border-radius:16px;padding:18px;">
                <strong style="color:#e9d5ff;">Shared state</strong>
                <input type="text" data-volt-state-input="shared-note" placeholder="Nota compartida entre pantallas"
                    style="inline-size:100%;border:1px solid rgba(148,163,184,0.18);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button" data-volt-state-action="increment-shared-counter"
                        style="border:1px solid rgba(168,85,247,0.28);background:rgba(88,28,135,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Incrementar shared.counter
                    </button>
                    <button type="button" data-volt-state-action="save-shared-note"
                        style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Guardar shared.draft
                    </button>
                    <button type="button" data-volt-state-action="clear-shared"
                        style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.16);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Limpiar shared
                    </button>
                </div>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(245,158,11,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">MVP de <code>volt:text</code></h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Esta prueba escribe texto directo desde el store runtime. Guarda notas en <code>client.draft</code> y
                <code>shared.draft</code> para ver como cambia el contenido sin re-render del bloque.
            </p>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article
                style="display:grid;gap:8px;border:1px solid rgba(34,197,94,0.20);background:rgba(6,78,59,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#bbf7d0;">Texto desde client:draft.note</strong>
                <span volt:text="client:draft.note" style="min-block-size:28px;color:#ecfdf5;line-height:1.7;">esperando
                    nota client...</span>
            </article>

            <article
                style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#e9d5ff;">Texto desde shared:draft.note</strong>
                <span volt:text="shared:draft.note" style="min-block-size:28px;color:#f5d0fe;line-height:1.7;">esperando
                    nota shared...</span>
            </article>

            <article
                style="display:grid;gap:8px;border:1px solid rgba(59,130,246,0.20);background:rgba(30,64,175,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#bfdbfe;">Texto desde shared:serverSync.syncedAt</strong>
                <span volt:text="shared:serverSync.syncedAt"
                    style="min-block-size:28px;color:#dbeafe;line-height:1.7;">sin sync selectivo aun</span>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(34,211,238,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">MVP de <code>volt:class</code></h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Esta prueba agrega o quita clases CSS desde el store runtime con el contrato
                <code>scope:path -&gt; clases</code>. La tarjeta cliente debe perder el resaltado al navegar a otra URL
                SPA; la compartida puede conservarlo.
            </p>
        </div>

        <div
            style="border:1px solid rgba(34,211,238,0.20);background:rgba(8,47,73,0.28);border-radius:16px;padding:16px;">
            <strong style="display:block;color:#a5f3fc;">Contrato activo</strong>
            <pre
                style="margin:10px 0 0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#cffafe;font-size:12px;line-height:1.7;">client:ui.highlightClientCard -&gt; ring-4 ring-cyan-400 shadow-lg shadow-cyan-950/40 -translate-y-1
shared:ui.highlightSharedCard -&gt; ring-4 ring-fuchsia-400 shadow-lg shadow-fuchsia-950/40 -translate-y-1</pre>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button" data-volt-state-action="toggle-client-class"
                style="border:1px solid rgba(34,211,238,0.28);background:rgba(8,47,73,0.22);color:#cffafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar client:ui.highlightClientCard
            </button>
            <button type="button" data-volt-state-action="toggle-shared-class"
                style="border:1px solid rgba(217,70,239,0.28);background:rgba(112,26,117,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar shared:ui.highlightSharedCard
            </button>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article class="transition-all duration-200"
                volt:class="client:ui.highlightClientCard -> ring-4 ring-cyan-400 shadow-lg shadow-cyan-950/40 -translate-y-1"
                style="display:grid;gap:8px;border:1px solid rgba(34,211,238,0.24);background:rgba(8,47,73,0.18);border-radius:16px;padding:16px;">
                <strong style="color:#a5f3fc;">Tarjeta ligada a client:ui.highlightClientCard</strong>
                <p style="margin:0;color:#cffafe;line-height:1.7;">
                    Si activas este estado aqui y luego navegas a <code>/runtimeStateAlt</code>, el resaltado debe
                    desaparecer porque el scope cliente cambia con la URL.
                </p>
            </article>

            <article class="transition-all duration-200"
                volt:class="shared:ui.highlightSharedCard -> ring-4 ring-fuchsia-400 shadow-lg shadow-fuchsia-950/40 -translate-y-1"
                style="display:grid;gap:8px;border:1px solid rgba(217,70,239,0.24);background:rgba(112,26,117,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#f5d0fe;">Tarjeta ligada a shared:ui.highlightSharedCard</strong>
                <p style="margin:0;color:#f5d0fe;line-height:1.7;">
                    Esta tarjeta puede seguir resaltada despues de navegar por SPA porque depende del store
                    compartido.
                </p>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(96,165,250,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">MVP de <code>volt:attr</code></h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Esta prueba agrega o restaura atributos HTML desde el store runtime con el contrato
                <code>scope:path -&gt; atributo=valor</code>. El bloqueo cliente debe perderse al navegar a otra URL
                SPA;
                el compartido puede persistir.
            </p>
        </div>

        <div
            style="border:1px solid rgba(96,165,250,0.20);background:rgba(30,64,175,0.18);border-radius:16px;padding:16px;">
            <strong style="display:block;color:#bfdbfe;">Contrato activo</strong>
            <pre
                style="margin:10px 0 0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#dbeafe;font-size:12px;line-height:1.7;">client:ui.lockClientAction -&gt; disabled=disabled, aria-disabled=true, data-lock=client, title=Bloqueado por client state
shared:ui.lockSharedAction -&gt; disabled=disabled, aria-disabled=true, data-lock=shared, title=Bloqueado por shared state</pre>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button" data-volt-state-action="toggle-client-attr"
                style="border:1px solid rgba(96,165,250,0.28);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar client:ui.lockClientAction
            </button>
            <button type="button" data-volt-state-action="toggle-shared-attr"
                style="border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar shared:ui.lockSharedAction
            </button>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article
                style="display:grid;gap:12px;border:1px solid rgba(96,165,250,0.24);background:rgba(30,64,175,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#bfdbfe;">Accion ligada a client:ui.lockClientAction</strong>
                <p style="margin:0;color:#dbeafe;line-height:1.7;">
                    Este boton debe quedar deshabilitado y con nuevos atributos mientras el estado cliente sea truthy.
                </p>
                <button type="button"
                    volt:attr="client:ui.lockClientAction -> disabled=disabled, aria-disabled=true, data-lock=client, title=Bloqueado por client state"
                    title="Disponible en esta URL"
                    style="border:1px solid rgba(96,165,250,0.28);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 14px;">
                    Accion client
                </button>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(16,185,129,0.24);background:rgba(6,95,70,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#d1fae5;">Accion ligada a shared:ui.lockSharedAction</strong>
                <p style="margin:0;color:#d1fae5;line-height:1.7;">
                    Este boton puede seguir deshabilitado despues de navegar porque depende del store compartido.
                </p>
                <button type="button"
                    volt:attr="shared:ui.lockSharedAction -> disabled=disabled, aria-disabled=true, data-lock=shared, title=Bloqueado por shared state"
                    title="Disponible en toda la pestaña"
                    style="border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:10px;padding:10px 14px;">
                    Accion shared
                </button>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(244,114,182,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">MVP de <code>volt:style</code></h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Esta prueba agrega o restaura estilos inline desde el store runtime con el contrato
                <code>scope:path -&gt; propiedad:valor; propiedad:valor</code>. El estilo cliente debe resetearse al
                navegar a otra URL SPA; el compartido puede conservarse.
            </p>
        </div>

        <div
            style="border:1px solid rgba(244,114,182,0.20);background:rgba(131,24,67,0.18);border-radius:16px;padding:16px;">
            <strong style="display:block;color:#fbcfe8;">Contrato activo</strong>
            <pre
                style="margin:10px 0 0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#fce7f3;font-size:12px;line-height:1.7;">client:ui.softenClientCard -&gt; opacity:0.55; transform:scale(0.98) translateY(6px); filter:saturate(0.7)
shared:ui.softenSharedCard -&gt; opacity:0.7; transform:scale(1.01) translateY(-4px); box-shadow:0 18px 40px rgba(217,70,239,0.22)</pre>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button" data-volt-state-action="toggle-client-style"
                style="border:1px solid rgba(244,114,182,0.28);background:rgba(131,24,67,0.18);color:#fce7f3;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar client:ui.softenClientCard
            </button>
            <button type="button" data-volt-state-action="toggle-shared-style"
                style="border:1px solid rgba(217,70,239,0.28);background:rgba(112,26,117,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar shared:ui.softenSharedCard
            </button>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article class="transition-all duration-200"
                volt:style="client:ui.softenClientCard -> opacity:0.55; transform:scale(0.98) translateY(6px); filter:saturate(0.7)"
                style="display:grid;gap:8px;border:1px solid rgba(244,114,182,0.24);background:rgba(131,24,67,0.14);border-radius:16px;padding:16px;">
                <strong style="color:#fbcfe8;">Tarjeta ligada a client:ui.softenClientCard</strong>
                <p style="margin:0;color:#fce7f3;line-height:1.7;">
                    Si activas este estado aqui y luego navegas a <code>/runtimeStateAlt</code>, el estilo inline debe
                    desaparecer porque el scope cliente cambia con la URL.
                </p>
            </article>

            <article class="transition-all duration-200"
                volt:style="shared:ui.softenSharedCard -> opacity:0.7; transform:scale(1.01) translateY(-4px); box-shadow:0 18px 40px rgba(217,70,239,0.22)"
                style="display:grid;gap:8px;border:1px solid rgba(217,70,239,0.24);background:rgba(112,26,117,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#f5d0fe;">Tarjeta ligada a shared:ui.softenSharedCard</strong>
                <p style="margin:0;color:#f5d0fe;line-height:1.7;">
                    Esta tarjeta puede conservar el estilo despues de navegar por SPA porque depende del store
                    compartido.
                </p>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(245,158,11,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">MVP de <code>volt:show</code></h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Esta prueba usa expresiones minimas con <code>client:</code> y <code>shared:</code>. El panel cliente
                se reinicia al navegar a otra URL SPA; el compartido puede seguir visible en la segunda pantalla.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button" data-volt-state-action="toggle-client-visibility"
                style="border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.18);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar client:ui.showClientPanel
            </button>
            <button type="button" data-volt-state-action="toggle-shared-visibility"
                style="border:1px solid rgba(217,70,239,0.28);background:rgba(112,26,117,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar shared:ui.showSharedPanel
            </button>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article volt:show="client:ui.showClientPanel"
                style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.24);background:rgba(120,53,15,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#fde68a;">Panel visible solo si client:ui.showClientPanel es truthy</strong>
                <p style="margin:0;color:#fef3c7;line-height:1.7;">
                    Este bloque depende del store cliente actual. Si navegas a <code>/runtimeStateAlt</code>, se debe
                    ocultar porque el scope cliente cambia con la URL.
                </p>
            </article>

            <article volt:show="shared:ui.showSharedPanel"
                style="display:grid;gap:8px;border:1px solid rgba(217,70,239,0.24);background:rgba(112,26,117,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#f5d0fe;">Panel visible si shared:ui.showSharedPanel es truthy</strong>
                <p style="margin:0;color:#f5d0fe;line-height:1.7;">
                    Este bloque depende del store compartido y puede permanecer visible incluso al navegar a la otra
                    pantalla SPA.
                </p>
            </article>

            <article volt:show.hide="shared:ui.showSharedPanel"
                style="display:grid;gap:8px;border:1px dashed rgba(148,163,184,0.28);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <strong style="color:#cbd5e1;">Fallback inverso con <code>volt:show.hide</code></strong>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">
                    Este bloque solo se muestra mientras <code>shared:ui.showSharedPanel</code> sea falsy.
                </p>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(59,130,246,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">MVP de <code>volt:if</code></h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Esta prueba desmonta y vuelve a montar nodos reales. El bloque cliente debe desaparecer al cambiar de
                URL SPA; el compartido puede volver a montarse en la segunda pantalla.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button" data-volt-state-action="toggle-client-if"
                style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar client:ui.mountClientPanel
            </button>
            <button type="button" data-volt-state-action="toggle-shared-if"
                style="border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Alternar shared:ui.mountSharedPanel
            </button>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article volt:if="client:ui.mountClientPanel"
                style="display:grid;gap:8px;border:1px solid rgba(59,130,246,0.24);background:rgba(30,64,175,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#bfdbfe;">Nodo montado solo si client:ui.mountClientPanel es truthy</strong>
                <p style="margin:0;color:#dbeafe;line-height:1.7;">
                    Si este bloque se monta, existe de verdad en el DOM. Al navegar a la segunda pantalla debe
                    desmontarse porque el store cliente cambia con la URL.
                </p>
            </article>

            <article volt:if="shared:ui.mountSharedPanel"
                style="display:grid;gap:8px;border:1px solid rgba(16,185,129,0.24);background:rgba(6,95,70,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#d1fae5;">Nodo montado si shared:ui.mountSharedPanel es truthy</strong>
                <p style="margin:0;color:#d1fae5;line-height:1.7;">
                    Este bloque puede permanecer montado entre pantallas SPA porque depende del store compartido.
                </p>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(14,165,233,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">MVP de <code>volt:for</code></h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Esta prueba repite nodos desde arreglos del store runtime y resuelve placeholders simples como
                <code>&#123;&#123; card.title &#125;&#125;</code> y <code>&#123;&#123; index &#125;&#125;</code>. La
                lista cliente debe cambiar con la URL; la
                compartida puede persistir al navegar.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button" data-volt-state-action="add-client-for-item"
                style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Agregar item client:list.items
            </button>
            <button type="button" data-volt-state-action="remove-client-for-item"
                style="border:1px solid rgba(59,130,246,0.28);background:rgba(15,23,42,0.9);color:#bfdbfe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Quitar item client:list.items
            </button>
            <button type="button" data-volt-state-action="add-shared-for-item"
                style="border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Agregar item shared:list.items
            </button>
            <button type="button" data-volt-state-action="remove-shared-for-item"
                style="border:1px solid rgba(16,185,129,0.28);background:rgba(15,23,42,0.9);color:#d1fae5;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Quitar item shared:list.items
            </button>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));align-items:start;">
            <article
                style="display:grid;gap:12px;border:1px solid rgba(59,130,246,0.24);background:rgba(30,64,175,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#bfdbfe;">Lista renderizada desde client:list.items</strong>
                <div style="display:grid;gap:10px;">
                    <article volt:for="card, index in client:list.items"
                        style="display:grid;gap:6px;border:1px solid rgba(59,130,246,0.18);background:rgba(30,64,175,0.16);border-radius:14px;padding:14px;">
                        <strong style="color:#dbeafe;">&#123;&#123; index &#125;&#125;. &#123;&#123; card.title
                            &#125;&#125;</strong>
                        <span style="color:#93c5fd;font-size:13px;">&#123;&#123; card.badge &#125;&#125;</span>
                        <p style="margin:0;color:#dbeafe;line-height:1.7;">&#123;&#123; card.detail &#125;&#125;</p>
                    </article>
                </div>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(16,185,129,0.24);background:rgba(6,95,70,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#d1fae5;">Lista renderizada desde shared:list.items</strong>
                <div style="display:grid;gap:10px;">
                    <article volt:for="card, index in shared:list.items"
                        style="display:grid;gap:6px;border:1px solid rgba(16,185,129,0.18);background:rgba(6,95,70,0.16);border-radius:14px;padding:14px;">
                        <strong style="color:#d1fae5;">&#123;&#123; index &#125;&#125;. &#123;&#123; card.title
                            &#125;&#125;</strong>
                        <span style="color:#6ee7b7;font-size:13px;">&#123;&#123; card.badge &#125;&#125;</span>
                        <p style="margin:0;color:#d1fae5;line-height:1.7;">&#123;&#123; card.detail &#125;&#125;</p>
                    </article>
                </div>
            </article>
        </div>
    </section>

    <section data-volt-state-example
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Snapshot actual</h2>
        <div style="display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
            <article
                style="border:1px solid rgba(34,197,94,0.20);background:rgba(6,78,59,0.12);border-radius:16px;padding:16px;">
                <strong style="display:block;color:#bbf7d0;">Client scope</strong>
                <code data-volt-state-client-scope
                    style="display:block;margin-block-start:8px;color:#ecfdf5;overflow-wrap:anywhere;">esperando...</code>
            </article>
            <article
                style="border:1px solid rgba(34,197,94,0.20);background:rgba(6,78,59,0.12);border-radius:16px;padding:16px;">
                <strong style="display:block;color:#bbf7d0;">Client snapshot</strong>
                <pre data-volt-state-client-snapshot
                    style="margin:8px 0 0;min-block-size:120px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#d1fae5;font-size:12px;line-height:1.7;">{"waiting":"client snapshot"}</pre>
            </article>
            <article
                style="border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.12);border-radius:16px;padding:16px;">
                <strong style="display:block;color:#e9d5ff;">Shared snapshot</strong>
                <pre data-volt-state-shared-snapshot
                    style="margin:8px 0 0;min-block-size:120px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#e9d5ff;font-size:12px;line-height:1.7;">{"waiting":"shared snapshot"}</pre>
            </article>
        </div>

        <article
            style="border:1px solid rgba(148,163,184,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
            <strong style="display:block;color:#cbd5e1;">Ultimo evento del state runtime</strong>
            <pre data-volt-state-last-event
                style="margin:8px 0 0;min-block-size:120px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#cbd5e1;font-size:12px;line-height:1.7;">{"waiting":"state event"}</pre>
        </article>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(59,130,246,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Sincronizacion selectiva frontend/backend</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Este formulario no lee todo el store runtime. Solo sincroniza las claves declaradas en
                <code>data-volt-state-sync</code> al momento de ejecutar la accion reactiva.
            </p>
        </div>

        <div
            style="border:1px solid rgba(59,130,246,0.20);background:rgba(14,116,144,0.14);border-radius:16px;padding:16px;">
            <strong style="display:block;color:#bfdbfe;">Contrato activo de esta demo</strong>
            <pre
                style="margin:10px 0 0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#dbeafe;font-size:12px;line-height:1.7;">client:draft.note -> params.clientNote
shared:draft.note -> params.sharedNote
shared:counter -> updates.sharedCounterMirror</pre>
        </div>

        <div volt:success="captureSelectiveSync"
            style="border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);border-radius:16px;padding:14px 16px;color:#d1fae5;">
            La accion recibio las claves seleccionadas y el backend devolvio un sync hacia
            <code>shared.serverSync</code>.
        </div>

        <form data-volt-target="state-sync-form" volt-submit="captureSelectiveSync"
            data-volt-state-sync="client:draft.note->params.clientNote, shared:draft.note->params.sharedNote, shared:counter->updates.sharedCounterMirror"
            style="display:grid;gap:16px;">
            <div
                style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));border:1px solid rgba(51,65,85,1);background:#020617;border-radius:18px;padding:18px;">
                <article
                    style="display:grid;gap:8px;border:1px solid rgba(34,197,94,0.18);background:rgba(6,78,59,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:#bbf7d0;">Params desde client.draft.note</strong>
                    <span style="font-size:13px;color:#86efac;">{{ $lastClientNote }}</span>
                </article>
                <article
                    style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.18);background:rgba(88,28,135,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:#e9d5ff;">Params desde shared.draft.note</strong>
                    <span style="font-size:13px;color:#f5d0fe;">{{ $lastSharedNote }}</span>
                </article>
                <article
                    style="display:grid;gap:8px;border:1px solid rgba(59,130,246,0.18);background:rgba(30,64,175,0.12);border-radius:14px;padding:14px;">
                    <strong style="color:#bfdbfe;">Updates desde shared.counter</strong>
                    <span style="font-size:13px;color:#dbeafe;">{{ $sharedCounterMirror }}</span>
                </article>
            </div>

            <div style="display:flex;flex-wrap:wrap;gap:12px;">
                <button type="submit" volt:loading.class="opacity-70" volt:loading.action="captureSelectiveSync"
                    volt:loading.delay="80ms" volt:loading.min-duration="400ms"
                    style="border:1px solid rgba(59,130,246,0.28);background:rgba(14,116,144,0.16);color:#dbeafe;border-radius:10px;padding:10px 16px;">
                    Enviar sync selectivo al backend
                </button>
                <span volt:loading="captureSelectiveSync"
                    style="display:inline-flex;align-items:center;border:1px solid rgba(250,204,21,0.28);background:rgba(250,204,21,0.08);color:#fde68a;border-radius:10px;padding:10px 14px;">
                    Sincronizando claves declaradas...
                </span>
            </div>
        </form>

        <article
            style="border:1px solid rgba(148,163,184,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
            <strong style="display:block;color:#cbd5e1;">Ultimo payload visto por el backend</strong>
            <pre
                style="margin:8px 0 0;min-block-size:120px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#cbd5e1;font-size:12px;line-height:1.7;">{{ $lastSyncReport }}</pre>
        </article>

        <p style="margin:0;color:#94a3b8;line-height:1.7;">
            Despues de guardar, revisa arriba el <code>Shared snapshot</code>: el backend empuja
            <code>shared.serverSync</code> mediante un effect del runtime.
        </p>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeStateAlt" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(168,85,247,0.28);background:rgba(88,28,135,0.16);color:#f5d0fe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Ir a la segunda pantalla SPA
        </a>
        <a href="/" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al inicio
        </a>
    </section>
</div>
@endsection
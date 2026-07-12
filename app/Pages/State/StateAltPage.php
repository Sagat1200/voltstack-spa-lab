<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\State;

use VoltStack\Runtime\Component\Component;

final class StateAltPage extends Component
{
    public string $title = 'State Destination';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = sprintf('state-alt-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="state-alt-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(168,85,247,0.24);background:linear-gradient(135deg,rgba(88,28,135,0.88),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#faf5ff;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(216,180,254,0.34);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#e9d5ff;">Shared
            State Check</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#f5d0fe;line-height:1.75;max-inline-size:72ch;">
                Esta segunda pantalla sirve para comprobar que el <code>client state</code> se vacia al cambiar de URL,
                mientras el <code>shared state</code> sigue disponible y puede seguir mutando sin roundtrip al backend.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(216,180,254,0.18);background:rgba(59,7,100,0.28);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#f0abfc;">Request
                marker</span>
            <strong style="font-size:14px;color:#faf5ff;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#f5d0fe;">Al llegar aqui por SPA, el scope cliente debe cambiar a esta
                URL.</span>
        </div>
    </section>

    <section data-volt-state-example
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Resultado esperado</h2>
        <ul style="margin:0;padding-inline-start:18px;display:grid;gap:10px;color:#94a3b8;line-height:1.7;">
            <li><code>client snapshot</code> debe aparecer vacio o con datos nuevos propios de esta ruta.</li>
            <li><code>shared snapshot</code> debe conservar lo que se haya guardado en la pantalla anterior.</li>
            <li><code>Client scope</code> debe apuntar a <code>/runtimeStateAlt</code>.</li>
            <li>Si activaste <code>shared:ui.showSharedPanel</code> en la primera pantalla, aqui debe seguir visible.
            </li>
        </ul>
    </section>

    <section
        style="display:grid;gap:14px;border:1px solid rgba(245,158,11,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Chequeo de <code>volt:text</code> en destino</h2>
        <p style="margin:0;color:#94a3b8;line-height:1.7;">
            El texto cliente debe quedar vacio al cambiar de URL si solo existia en la vista anterior. El texto
            compartido puede seguir visible al navegar por SPA.
        </p>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article
                style="display:grid;gap:8px;border:1px solid rgba(34,197,94,0.20);background:rgba(6,78,59,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#bbf7d0;">Texto desde client:draft.note</strong>
                <span volt:text="client:draft.note" style="min-block-size:28px;color:#ecfdf5;line-height:1.7;"></span>
            </article>

            <article
                style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#e9d5ff;">Texto desde shared:draft.note</strong>
                <span volt:text="shared:draft.note" style="min-block-size:28px;color:#f5d0fe;line-height:1.7;"></span>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:14px;border:1px solid rgba(34,211,238,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Chequeo de <code>volt:class</code> en destino</h2>
        <p style="margin:0;color:#94a3b8;line-height:1.7;">
            La tarjeta cliente no debe llegar resaltada si solo la activaste en la vista anterior. La compartida si
            puede mantener las clases extra al navegar por SPA.
        </p>

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
                <strong style="color:#a5f3fc;">Esta tarjeta no deberia seguir resaltada desde /runtimeState</strong>
                <p style="margin:0;color:#cffafe;line-height:1.7;">
                    Si se resalta aqui despues de llegar por SPA, debe ser porque la activaste en esta misma URL.
                </p>
            </article>

            <article class="transition-all duration-200"
                volt:class="shared:ui.highlightSharedCard -> ring-4 ring-fuchsia-400 shadow-lg shadow-fuchsia-950/40 -translate-y-1"
                style="display:grid;gap:8px;border:1px solid rgba(217,70,239,0.24);background:rgba(112,26,117,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#f5d0fe;">Esta tarjeta si puede mantener el resaltado compartido</strong>
                <p style="margin:0;color:#f5d0fe;line-height:1.7;">
                    Si la activaste antes de navegar, las clases deben seguir presentes porque dependen del store
                    compartido.
                </p>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:14px;border:1px solid rgba(96,165,250,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Chequeo de <code>volt:attr</code> en destino</h2>
        <p style="margin:0;color:#94a3b8;line-height:1.7;">
            El boton cliente no debe seguir bloqueado si solo lo activaste en la vista anterior. El compartido si puede
            conservar atributos como <code>disabled</code>, <code>title</code> y <code>data-lock</code>.
        </p>

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
                <strong style="color:#bfdbfe;">Este boton no deberia seguir bloqueado desde /runtimeState</strong>
                <button type="button"
                    volt:attr="client:ui.lockClientAction -> disabled=disabled, aria-disabled=true, data-lock=client, title=Bloqueado por client state"
                    title="Disponible en esta URL"
                    style="border:1px solid rgba(96,165,250,0.28);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 14px;">
                    Accion client en destino
                </button>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(16,185,129,0.24);background:rgba(6,95,70,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#d1fae5;">Este boton si puede seguir bloqueado al navegar</strong>
                <button type="button"
                    volt:attr="shared:ui.lockSharedAction -> disabled=disabled, aria-disabled=true, data-lock=shared, title=Bloqueado por shared state"
                    title="Disponible en toda la pestaña"
                    style="border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:10px;padding:10px 14px;">
                    Accion shared en destino
                </button>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:14px;border:1px solid rgba(244,114,182,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Chequeo de <code>volt:style</code> en destino</h2>
        <p style="margin:0;color:#94a3b8;line-height:1.7;">
            La tarjeta cliente no debe seguir estilizada si solo la activaste en la vista anterior. La compartida si
            puede conservar cambios inline como <code>opacity</code>, <code>transform</code> o <code>box-shadow</code>.
        </p>

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
                <strong style="color:#fbcfe8;">Esta tarjeta no deberia seguir estilizada desde /runtimeState</strong>
                <p style="margin:0;color:#fce7f3;line-height:1.7;">
                    Si cambia de estilo aqui despues de llegar por SPA, debe ser porque la activaste en esta misma URL.
                </p>
            </article>

            <article class="transition-all duration-200"
                volt:style="shared:ui.softenSharedCard -> opacity:0.7; transform:scale(1.01) translateY(-4px); box-shadow:0 18px 40px rgba(217,70,239,0.22)"
                style="display:grid;gap:8px;border:1px solid rgba(217,70,239,0.24);background:rgba(112,26,117,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#f5d0fe;">Esta tarjeta si puede conservar el estilo compartido</strong>
                <p style="margin:0;color:#f5d0fe;line-height:1.7;">
                    Si la activaste antes de navegar, el estilo inline debe seguir presente porque depende del store
                    compartido.
                </p>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:14px;border:1px solid rgba(245,158,11,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Chequeo de <code>volt:show</code> en destino</h2>
        <article volt:show="client:ui.showClientPanel"
            style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.24);background:rgba(120,53,15,0.16);border-radius:16px;padding:16px;">
            <strong style="color:#fde68a;">Este panel no deberia sobrevivir desde la vista anterior</strong>
            <p style="margin:0;color:#fef3c7;line-height:1.7;">
                Si lo activaste en <code>/runtimeState</code>, al cambiar de URL SPA debe resetearse porque depende del
                store cliente.
            </p>
        </article>

        <article volt:show="shared:ui.showSharedPanel"
            style="display:grid;gap:8px;border:1px solid rgba(217,70,239,0.24);background:rgba(112,26,117,0.16);border-radius:16px;padding:16px;">
            <strong style="color:#f5d0fe;">Este panel si puede seguir visible al navegar</strong>
            <p style="margin:0;color:#f5d0fe;line-height:1.7;">
                Si lo activaste antes de entrar aqui, la directiva debe mantenerlo visible porque depende del store
                compartido.
            </p>
        </article>
    </section>

    <section
        style="display:grid;gap:14px;border:1px solid rgba(59,130,246,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Chequeo de <code>volt:if</code> en destino</h2>
        <article volt:if="client:ui.mountClientPanel"
            style="display:grid;gap:8px;border:1px solid rgba(59,130,246,0.24);background:rgba(30,64,175,0.16);border-radius:16px;padding:16px;">
            <strong style="color:#bfdbfe;">Este nodo no deberia seguir montado desde la vista anterior</strong>
            <p style="margin:0;color:#dbeafe;line-height:1.7;">
                Si lo activaste en <code>/runtimeState</code>, aqui debe desmontarse porque dependia del store cliente.
            </p>
        </article>

        <article volt:if="shared:ui.mountSharedPanel"
            style="display:grid;gap:8px;border:1px solid rgba(16,185,129,0.24);background:rgba(6,95,70,0.16);border-radius:16px;padding:16px;">
            <strong style="color:#d1fae5;">Este nodo si puede seguir montado al navegar</strong>
            <p style="margin:0;color:#d1fae5;line-height:1.7;">
                Si lo activaste antes de entrar aqui, la directiva debe volver a montarlo porque depende del store
                compartido.
            </p>
        </article>
    </section>

    <section
        style="display:grid;gap:14px;border:1px solid rgba(14,165,233,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Chequeo de <code>volt:for</code> en destino</h2>
        <p style="margin:0;color:#94a3b8;line-height:1.7;">
            La lista cliente debe volver vacia si solo la llenaste en la pantalla anterior. La lista compartida puede
            seguir renderizada porque depende del store global de la pestaña.
        </p>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));align-items:start;">
            <article
                style="display:grid;gap:12px;border:1px solid rgba(59,130,246,0.24);background:rgba(30,64,175,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#bfdbfe;">Client list en la segunda pantalla</strong>
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
                <strong style="color:#d1fae5;">Shared list en la segunda pantalla</strong>
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
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article
                style="display:grid;gap:12px;border:1px solid rgba(34,197,94,0.20);background:rgba(6,78,59,0.18);border-radius:16px;padding:18px;">
                <strong style="color:#bbf7d0;">Mutar client state local</strong>
                <input type="text" data-volt-state-input="client-note" placeholder="Nota local de esta segunda pantalla"
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
                </div>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.18);border-radius:16px;padding:18px;">
                <strong style="color:#e9d5ff;">Seguir usando shared state</strong>
                <input type="text" data-volt-state-input="shared-note" placeholder="Nota compartida persistente"
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
        style="display:flex;flex-wrap:wrap;gap:12px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeState" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(96,165,250,0.28);background:rgba(14,116,144,0.14);color:#e0f2fe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver a la primera pantalla SPA
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection
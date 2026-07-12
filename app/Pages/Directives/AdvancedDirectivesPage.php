<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Directives;

use VoltStack\Runtime\Component\Component;
use VoltStack\Runtime\Protocol\ActionEffectOptions;

final class AdvancedDirectivesPage extends Component
{
    public string $title = 'Advanced Directives Demo';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = sprintf('adv-%s', substr((string) microtime(true), -6));
    }

    public function prepareNullUndefinedEdges(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->stateSet('client', 'edge', [
                'nullValue' => null,
                'emptyString' => '',
                'zeroValue' => 0,
                'falseValue' => false,
            ])
            ->stateSet('shared', 'edge', [
                'nullValue' => null,
                'emptyString' => '',
                'zeroValue' => 0,
                'falseValue' => false,
            ]);
    }

    public function clearNullUndefinedEdges(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->stateDelete('client', 'edge')
            ->stateDelete('shared', 'edge');
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="runtime-advanced-directives-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(14,165,233,0.24);background:linear-gradient(135deg,rgba(8,47,73,0.88),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#e0f2fe;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(125,211,252,0.32);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#bae6fd;">Runtime
            JS</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#bae6fd;line-height:1.75;max-inline-size:72ch;">
                Esta pantalla prueba la siguiente capa del runtime SPA:
                <code>volt:text</code> con <code>??</code>, condiciones compuestas en <code>volt:show</code> y
                <code>volt:if</code>, y reglas multiples en <code>volt:class</code>, <code>volt:attr</code> y
                <code>volt:style</code>.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(125,211,252,0.18);background:rgba(8,47,73,0.28);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#7dd3fc;">Request
                marker</span>
            <strong style="font-size:14px;color:#f0f9ff;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#bae6fd;">Muta el store runtime y observa el resultado sin re-render del
                documento.</span>
        </div>
    </section>

    <section data-volt-state-example
        style="display:grid;gap:20px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Controles de la demo</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Estos botones reutilizan el store runtime del skeleton. Las claves <code>client</code> cambian con la
                URL actual; las <code>shared</code> se comparten entre pantallas SPA.
            </p>
        </div>

        <div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article
                style="display:grid;gap:12px;border:1px solid rgba(14,165,233,0.20);background:rgba(8,47,73,0.18);border-radius:16px;padding:18px;">
                <strong style="color:#bae6fd;">Notas para <code>volt:text</code></strong>
                <input type="text" data-volt-state-input="client-note" placeholder="Nota client"
                    style="inline-size:100%;border:1px solid rgba(148,163,184,0.18);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
                <input type="text" data-volt-state-input="shared-note" placeholder="Nota shared"
                    style="inline-size:100%;border:1px solid rgba(148,163,184,0.18);background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 12px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button" data-volt-state-action="save-client-note"
                        style="border:1px solid rgba(14,165,233,0.28);background:rgba(8,47,73,0.22);color:#e0f2fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Guardar client.draft
                    </button>
                    <button type="button" data-volt-state-action="save-shared-note"
                        style="border:1px solid rgba(168,85,247,0.28);background:rgba(88,28,135,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Guardar shared.draft
                    </button>
                    <button type="button" data-volt-state-action="clear-client"
                        style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.16);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Limpiar client
                    </button>
                    <button type="button" data-volt-state-action="clear-shared"
                        style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.16);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Limpiar shared
                    </button>
                </div>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(250,204,21,0.20);background:rgba(113,63,18,0.16);border-radius:16px;padding:18px;">
                <strong style="color:#fde68a;">Presets de validacion manual</strong>
                <p style="margin:0;color:#fde68a;line-height:1.7;">
                    Cada preset deja la pantalla en un estado reproducible para revisar rapido que ramas, clases,
                    atributos y estilos quedan activos.
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button" data-volt-state-action="preset-text-shared-fallback"
                        style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.20);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Fallback shared
                    </button>
                    <button type="button" data-volt-state-action="preset-text-client-priority"
                        style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.20);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Prioridad client
                    </button>
                    <button type="button" data-volt-state-action="preset-compound-true"
                        style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.20);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Condicion true
                    </button>
                    <button type="button" data-volt-state-action="preset-compound-false"
                        style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.20);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Condicion false
                    </button>
                    <button type="button" data-volt-state-action="preset-relational-threshold-hit"
                        style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.20);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Umbral relacional
                    </button>
                    <button type="button" data-volt-state-action="preset-null-vs-undefined"
                        style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.20);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Null vs undefined
                    </button>
                    <button type="button" data-volt-state-action="preset-multi-rule-client"
                        style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.20);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Reglas client
                    </button>
                    <button type="button" data-volt-state-action="preset-multi-rule-shared"
                        style="border:1px solid rgba(250,204,21,0.28);background:rgba(120,53,15,0.20);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Reglas shared
                    </button>
                    <button type="button" data-volt-state-action="reset-runtime-advanced-demo"
                        style="border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.82);color:#cbd5e1;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Reset demo
                    </button>
                </div>
                <code data-runtime-preset-status
                    style="display:block;overflow-wrap:anywhere;border:1px solid rgba(250,204,21,0.20);background:rgba(15,23,42,0.82);border-radius:12px;padding:12px;color:#fef3c7;">Preset activo: ninguno</code>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(34,197,94,0.20);background:rgba(6,78,59,0.18);border-radius:16px;padding:18px;">
                <strong style="color:#bbf7d0;">Toggles para condiciones compuestas</strong>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button" data-volt-state-action="toggle-client-visibility"
                        style="border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.18);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Alternar client:ui.showClientPanel
                    </button>
                    <button type="button" data-volt-state-action="toggle-shared-visibility"
                        style="border:1px solid rgba(217,70,239,0.28);background:rgba(112,26,117,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Alternar shared:ui.showSharedPanel
                    </button>
                    <button type="button" data-volt-state-action="toggle-client-if"
                        style="border:1px solid rgba(59,130,246,0.28);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Alternar client:ui.mountClientPanel
                    </button>
                    <button type="button" data-volt-state-action="toggle-shared-if"
                        style="border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Alternar shared:ui.mountSharedPanel
                    </button>
                </div>
                <p style="margin:0;color:#86efac;line-height:1.7;">
                    Usa combinaciones como <code>client && !shared</code> o <code>shared || client</code> en las
                    tarjetas de abajo.
                </p>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(96,165,250,0.20);background:rgba(30,64,175,0.16);border-radius:16px;padding:18px;">
                <strong style="color:#bfdbfe;">Contadores para comparaciones</strong>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button" data-volt-state-action="increment-client-counter"
                        style="border:1px solid rgba(14,165,233,0.28);background:rgba(8,47,73,0.22);color:#cffafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Incrementar client.counter
                    </button>
                    <button type="button" data-volt-state-action="increment-shared-counter"
                        style="border:1px solid rgba(168,85,247,0.28);background:rgba(88,28,135,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Incrementar shared.counter
                    </button>
                </div>
                <p style="margin:0;color:#bfdbfe;line-height:1.7;">
                    Usa los snapshots para confirmar valores y probar reglas como <code>client:counter &gt;= 2</code>
                    o <code>shared:counter &lt; 3</code>.
                </p>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(124,58,237,0.20);background:rgba(76,29,149,0.16);border-radius:16px;padding:18px;">
                <strong style="color:#ddd6fe;">Casos borde null vs undefined</strong>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button" volt-click="prepareNullUndefinedEdges"
                        style="border:1px solid rgba(124,58,237,0.28);background:rgba(76,29,149,0.22);color:#ede9fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Preparar null vs undefined
                    </button>
                    <button type="button" volt-click="clearNullUndefinedEdges"
                        style="border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.82);color:#cbd5e1;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Limpiar edge cases
                    </button>
                </div>
                <p style="margin:0;color:#c4b5fd;line-height:1.7;">
                    Crea <code>null</code>, <code>''</code>, <code>0</code> y <code>false</code> en ambos scopes; las
                    rutas <code>*.undefinedValue</code> quedan ausentes y se leen como <code>undefined</code>.
                </p>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(96,165,250,0.20);background:rgba(30,64,175,0.16);border-radius:16px;padding:18px;">
                <strong style="color:#bfdbfe;">Toggles para reglas multiples</strong>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button" data-volt-state-action="toggle-client-class"
                        style="border:1px solid rgba(34,211,238,0.28);background:rgba(8,47,73,0.22);color:#cffafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Alternar class client
                    </button>
                    <button type="button" data-volt-state-action="toggle-shared-class"
                        style="border:1px solid rgba(217,70,239,0.28);background:rgba(112,26,117,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Alternar class shared
                    </button>
                    <button type="button" data-volt-state-action="toggle-client-attr"
                        style="border:1px solid rgba(96,165,250,0.28);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Alternar attr client
                    </button>
                    <button type="button" data-volt-state-action="toggle-shared-attr"
                        style="border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Alternar attr shared
                    </button>
                    <button type="button" data-volt-state-action="toggle-client-style"
                        style="border:1px solid rgba(244,114,182,0.28);background:rgba(131,24,67,0.18);color:#fce7f3;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Alternar style client
                    </button>
                    <button type="button" data-volt-state-action="toggle-shared-style"
                        style="border:1px solid rgba(217,70,239,0.28);background:rgba(112,26,117,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Alternar style shared
                    </button>
                </div>
            </article>
        </div>

        <div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
            <article
                style="border:1px solid rgba(14,165,233,0.20);background:rgba(8,47,73,0.18);border-radius:16px;padding:16px;">
                <strong style="display:block;color:#7dd3fc;">Client scope</strong>
                <code data-volt-state-client-scope
                    style="display:block;margin-block-start:8px;color:#e0f2fe;overflow-wrap:anywhere;">esperando...</code>
            </article>
            <article
                style="border:1px solid rgba(14,165,233,0.20);background:rgba(8,47,73,0.18);border-radius:16px;padding:16px;">
                <strong style="display:block;color:#7dd3fc;">Client snapshot</strong>
                <pre data-volt-state-client-snapshot
                    style="margin:8px 0 0;min-block-size:120px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#e0f2fe;font-size:12px;line-height:1.7;">{"waiting":"client snapshot"}</pre>
            </article>
            <article
                style="border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.16);border-radius:16px;padding:16px;">
                <strong style="display:block;color:#e9d5ff;">Shared snapshot</strong>
                <pre data-volt-state-shared-snapshot
                    style="margin:8px 0 0;min-block-size:120px;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#f5d0fe;font-size:12px;line-height:1.7;">{"waiting":"shared snapshot"}</pre>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(59,130,246,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Comparaciones relacionales</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Esta capa nueva permite comparar refs contra literales o contra otros valores del store usando
                <code>==</code>, <code>!=</code>, <code>===</code>, <code>!==</code>, <code>&gt;</code>,
                <code>&lt;</code>, <code>&gt;=</code> y <code>&lt;=</code>.
            </p>
        </div>
        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));align-items:start;">
            <article
                style="display:grid;gap:10px;border:1px solid rgba(59,130,246,0.24);background:rgba(30,64,175,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#bfdbfe;"><code>volt:show</code> con umbrales numericos</strong>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#dbeafe;font-size:12px;line-height:1.7;">client:counter >= 2 && shared:counter < 3</pre>
                <div data-runtime-check="relational-threshold-panel"
                    volt:show="client:counter >= 2 && shared:counter < 3"
                    style="display:grid;gap:6px;border:1px solid rgba(59,130,246,0.24);background:rgba(30,64,175,0.24);border-radius:12px;padding:14px;">
                    <strong style="color:#bfdbfe;">Visible con client.counter >= 2 y shared.counter < 3</strong>
                            <span style="color:#dbeafe;">Incrementa ambos contadores y observa cuando este panel aparece
                                o se
                                oculta.</span>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(16,185,129,0.24);background:rgba(6,95,70,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#d1fae5;"><code>volt:if</code> con igualdad y desigualdad</strong>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#d1fae5;font-size:12px;line-height:1.7;">shared:draft.note == 'activar' || client:draft.note != 'pausa'</pre>
                <div volt:if="shared:draft.note == 'activar' || client:draft.note != 'pausa'"
                    style="display:grid;gap:6px;border:1px solid rgba(16,185,129,0.24);background:rgba(6,95,70,0.24);border-radius:12px;padding:14px;">
                    <strong style="color:#d1fae5;">Nodo montado por comparacion textual</strong>
                    <span style="color:#d1fae5;">Prueba guardar <code>pausa</code> en client o <code>activar</code> en
                        shared para cambiar el resultado.</span>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(124,58,237,0.24);background:rgba(76,29,149,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#ddd6fe;"><code>volt:show</code> comparando refs</strong>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#ddd6fe;font-size:12px;line-height:1.7;">client:counter >= shared:counter</pre>
                <div data-runtime-check="relational-ref-panel" volt:show="client:counter >= shared:counter"
                    style="display:grid;gap:6px;border:1px solid rgba(124,58,237,0.24);background:rgba(76,29,149,0.24);border-radius:12px;padding:14px;">
                    <strong style="color:#ddd6fe;">Visible cuando client.counter es mayor o igual que
                        shared.counter</strong>
                    <span style="color:#e9d5ff;">Sube o baja la relacion entre ambos contadores para ver el cruce entre
                        refs.</span>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(139,92,246,0.24);background:rgba(91,33,182,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#ddd6fe;"><code>volt:show</code> null vs undefined entre refs</strong>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#ddd6fe;font-size:12px;line-height:1.7;">client:edge.nullValue == shared:edge.undefinedValue</pre>
                <div data-runtime-check="null-undefined-flex-panel"
                    volt:show="client:edge.nullValue == shared:edge.undefinedValue"
                    style="display:grid;gap:6px;border:1px solid rgba(139,92,246,0.24);background:rgba(91,33,182,0.24);border-radius:12px;padding:14px;">
                    <strong style="color:#ddd6fe;">Visible cuando la comparacion flexible trata null y undefined como
                        equivalentes</strong>
                    <span style="color:#e9d5ff;">Prepara el caso borde y compara luego con la variante estricta del
                        inspector.</span>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(109,40,217,0.24);background:rgba(76,29,149,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#ddd6fe;"><code>volt:show</code> undefined vs null entre refs</strong>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#ddd6fe;font-size:12px;line-height:1.7;">client:edge.undefinedValue == shared:edge.nullValue</pre>
                <div volt:show="client:edge.undefinedValue == shared:edge.nullValue"
                    style="display:grid;gap:6px;border:1px solid rgba(109,40,217,0.24);background:rgba(76,29,149,0.24);border-radius:12px;padding:14px;">
                    <strong style="color:#ddd6fe;">Visible cuando el caso inverso tambien coincide en comparacion
                        flexible</strong>
                    <span style="color:#e9d5ff;">Sirve para confirmar que el orden de los operandos no cambia la
                        coercion flexible.</span>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(20,184,166,0.24);background:rgba(17,94,89,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#99f6e4;"><code>volt:if</code> con igualdad estricta</strong>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#99f6e4;font-size:12px;line-height:1.7;">shared:draft.note === 'activar' || client:draft.note !== 'pausa'</pre>
                <div volt:if="shared:draft.note === 'activar' || client:draft.note !== 'pausa'"
                    style="display:grid;gap:6px;border:1px solid rgba(20,184,166,0.24);background:rgba(17,94,89,0.24);border-radius:12px;padding:14px;">
                    <strong style="color:#99f6e4;">Nodo montado por comparacion textual estricta</strong>
                    <span style="color:#ccfbf1;">Sirve para comparar el comportamiento frente a valores
                        <code>null</code> o ausentes.</span>
                </div>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(148,163,184,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Inspector de expresiones</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Este panel muestra si cada expresion de la demo evalua ahora mismo a <code>true</code> o
                <code>false</code> usando las mismas directivas runtime.
            </p>
        </div>

        <div style="display:grid;gap:12px;">
            <article
                style="display:grid;gap:10px;border:1px solid rgba(59,130,246,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                    <strong style="color:#dbeafe;">client:counter >= 2 && shared:counter < 3</strong>
                            <span volt:show="client:counter >= 2 && shared:counter < 3"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                            <span volt:show.hide="client:counter >= 2 && shared:counter < 3"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
                </div>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">Se activa cuando el contador client llega a 2 o mas y
                    el contador shared sigue por debajo de 3.</p>
                <div
                    style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;color:#cbd5e1;font-size:13px;">
                        <span>client.counter = <strong volt:text="client:counter ?? 'null'"
                                style="color:#dbeafe;">null</strong></span>
                        <span>shared.counter = <strong volt:text="shared:counter ?? 'null'"
                                style="color:#dbeafe;">null</strong></span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span volt:show="client:counter >= 2"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">client:counter
                            >= 2 => true</span>
                        <span volt:show.hide="client:counter >= 2"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">client:counter
                            >= 2 => false</span>
                        <span volt:show="shared:counter < 3"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">shared:counter
                            < 3=> true
                        </span>
                        <span volt:show.hide="shared:counter < 3"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">shared:counter
                            < 3=> false
                        </span>
                    </div>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(16,185,129,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                    <strong style="color:#d1fae5;">shared:draft.note == 'activar' || client:draft.note !=
                        'pausa'</strong>
                    <span volt:show="shared:draft.note == 'activar' || client:draft.note != 'pausa'"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                    <span volt:show.hide="shared:draft.note == 'activar' || client:draft.note != 'pausa'"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
                </div>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">Se monta si shared vale exactamente
                    <code>activar</code> o si client es distinto de <code>pausa</code>.
                </p>
                <div
                    style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;color:#cbd5e1;font-size:13px;">
                        <span>shared.draft.note = <strong volt:text="shared:draft.note ?? 'null'"
                                style="color:#d1fae5;">null</strong></span>
                        <span>client.draft.note = <strong volt:text="client:draft.note ?? 'null'"
                                style="color:#d1fae5;">null</strong></span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span volt:show="shared:draft.note == 'activar'"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">shared:draft.note
                            == 'activar' => true</span>
                        <span volt:show.hide="shared:draft.note == 'activar'"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">shared:draft.note
                            == 'activar' => false</span>
                        <span volt:show="client:draft.note != 'pausa'"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">client:draft.note
                            != 'pausa' => true</span>
                        <span volt:show.hide="client:draft.note != 'pausa'"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">client:draft.note
                            != 'pausa' => false</span>
                    </div>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(124,58,237,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                    <strong style="color:#ddd6fe;">client:counter >= shared:counter</strong>
                    <span volt:show="client:counter >= shared:counter"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                    <span volt:show.hide="client:counter >= shared:counter"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
                </div>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">Aqui ambos lados salen del store runtime, sin usar
                    literales intermedios.</p>
                <div
                    style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;color:#cbd5e1;font-size:13px;">
                        <span>client.counter = <strong volt:text="client:counter ?? 'null'"
                                style="color:#ddd6fe;">null</strong></span>
                        <span>shared.counter = <strong volt:text="shared:counter ?? 'null'"
                                style="color:#ddd6fe;">null</strong></span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span volt:show="client:counter >= shared:counter"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">client:counter
                            >= shared:counter => true</span>
                        <span volt:show.hide="client:counter >= shared:counter"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">client:counter
                            >= shared:counter => false</span>
                    </div>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(139,92,246,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                    <strong style="color:#ddd6fe;">client:edge.nullValue == shared:edge.undefinedValue</strong>
                    <span volt:show="client:edge.nullValue == shared:edge.undefinedValue"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                    <span volt:show.hide="client:edge.nullValue == shared:edge.undefinedValue"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
                </div>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">Comparacion flexible entre refs: una ruta existe con
                    <code>null</code> y la otra queda ausente como <code>undefined</code>.
                </p>
                <div
                    style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;color:#cbd5e1;font-size:13px;">
                        <span>client.edge.nullValue = <strong volt:text="client:edge.nullValue ?? 'null'"
                                style="color:#ddd6fe;">null</strong></span>
                        <span>shared.edge.undefinedValue = <strong
                                volt:show="shared:edge.undefinedValue == null && shared:edge.undefinedValue !== null"
                                style="color:#ddd6fe;">undefined</strong><strong
                                volt:show.hide="shared:edge.undefinedValue == null && shared:edge.undefinedValue !== null"
                                volt:text="shared:edge.undefinedValue ?? 'null'"
                                style="color:#ddd6fe;">null</strong></span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span volt:show="client:edge.nullValue == shared:edge.undefinedValue"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">client:edge.nullValue
                            == shared:edge.undefinedValue => true</span>
                        <span volt:show.hide="client:edge.nullValue == shared:edge.undefinedValue"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">client:edge.nullValue
                            == shared:edge.undefinedValue => false</span>
                    </div>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(45,212,191,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                    <strong style="color:#99f6e4;">client:edge.nullValue === shared:edge.undefinedValue</strong>
                    <span volt:show="client:edge.nullValue === shared:edge.undefinedValue"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                    <span volt:show.hide="client:edge.nullValue === shared:edge.undefinedValue"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
                </div>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">Comparacion estricta del mismo caso borde: aqui
                    <code>null</code> y <code>undefined</code> dejan de coincidir.
                </p>
                <div
                    style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;color:#cbd5e1;font-size:13px;">
                        <span>client.edge.nullValue = <strong volt:text="client:edge.nullValue ?? 'null'"
                                style="color:#99f6e4;">null</strong></span>
                        <span>shared.edge.undefinedValue = <strong
                                volt:show="shared:edge.undefinedValue == null && shared:edge.undefinedValue !== null"
                                style="color:#99f6e4;">undefined</strong><strong
                                volt:show.hide="shared:edge.undefinedValue == null && shared:edge.undefinedValue !== null"
                                volt:text="shared:edge.undefinedValue ?? 'null'"
                                style="color:#99f6e4;">null</strong></span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span volt:show="client:edge.nullValue === shared:edge.undefinedValue"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">client:edge.nullValue
                            === shared:edge.undefinedValue => true</span>
                        <span volt:show.hide="client:edge.nullValue === shared:edge.undefinedValue"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">client:edge.nullValue
                            === shared:edge.undefinedValue => false</span>
                    </div>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(109,40,217,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                    <strong style="color:#ddd6fe;">client:edge.undefinedValue == shared:edge.nullValue</strong>
                    <span volt:show="client:edge.undefinedValue == shared:edge.nullValue"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                    <span volt:show.hide="client:edge.undefinedValue == shared:edge.nullValue"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
                </div>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">Caso inverso del mismo borde para confirmar la
                    simetria de la comparacion flexible.</p>
                <div
                    style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;color:#cbd5e1;font-size:13px;">
                        <span>client.edge.undefinedValue = <strong
                                volt:show="client:edge.undefinedValue == null && client:edge.undefinedValue !== null"
                                style="color:#ddd6fe;">undefined</strong><strong
                                volt:show.hide="client:edge.undefinedValue == null && client:edge.undefinedValue !== null"
                                volt:text="client:edge.undefinedValue ?? 'null'"
                                style="color:#ddd6fe;">null</strong></span>
                        <span>shared.edge.nullValue = <strong volt:text="shared:edge.nullValue ?? 'null'"
                                style="color:#ddd6fe;">null</strong></span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span volt:show="client:edge.undefinedValue == shared:edge.nullValue"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">client:edge.undefinedValue
                            == shared:edge.nullValue => true</span>
                        <span volt:show.hide="client:edge.undefinedValue == shared:edge.nullValue"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">client:edge.undefinedValue
                            == shared:edge.nullValue => false</span>
                    </div>
                </div>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(71,85,105,0.24);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:grid;gap:6px;">
                    <strong style="color:#e2e8f0;">Tabla de verdad rápida</strong>
                    <p style="margin:0;color:#94a3b8;line-height:1.7;">Comparacion visual de coercion flexible vs
                        igualdad estricta usando refs reales del store.</p>
                </div>
                <div style="display:grid;gap:10px;">
                    <article
                        style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                        <strong style="color:#cbd5e1;">null vs undefined</strong>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;color:#cbd5e1;font-size:13px;">
                            <span>client.edge.nullValue = <strong volt:text="client:edge.nullValue ?? 'null'"
                                    style="color:#e2e8f0;">null</strong></span>
                            <span>shared.edge.undefinedValue = <strong
                                    volt:show="shared:edge.undefinedValue == null && shared:edge.undefinedValue !== null"
                                    style="color:#e2e8f0;">undefined</strong><strong
                                    volt:show.hide="shared:edge.undefinedValue == null && shared:edge.undefinedValue !== null"
                                    volt:text="shared:edge.undefinedValue ?? 'null'"
                                    style="color:#e2e8f0;">null</strong></span>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;">
                            <span volt:show="client:edge.nullValue == shared:edge.undefinedValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">==
                                => true</span>
                            <span volt:show.hide="client:edge.nullValue == shared:edge.undefinedValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">==
                                => false</span>
                            <span volt:show="client:edge.nullValue === shared:edge.undefinedValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">===
                                => true</span>
                            <span volt:show.hide="client:edge.nullValue === shared:edge.undefinedValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">===
                                => false</span>
                        </div>
                    </article>

                    <article
                        style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                        <strong style="color:#cbd5e1;">'' vs 0</strong>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;color:#cbd5e1;font-size:13px;">
                            <span>client.edge.emptyString = <strong volt:text="client:edge.emptyString ?? 'null'"
                                    style="color:#e2e8f0;">null</strong></span>
                            <span>shared.edge.zeroValue = <strong volt:text="shared:edge.zeroValue ?? 'null'"
                                    style="color:#e2e8f0;">null</strong></span>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;">
                            <span volt:show="client:edge.emptyString == shared:edge.zeroValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">==
                                => true</span>
                            <span volt:show.hide="client:edge.emptyString == shared:edge.zeroValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">==
                                => false</span>
                            <span volt:show="client:edge.emptyString === shared:edge.zeroValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">===
                                => true</span>
                            <span volt:show.hide="client:edge.emptyString === shared:edge.zeroValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">===
                                => false</span>
                        </div>
                    </article>

                    <article
                        style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                        <strong style="color:#cbd5e1;">0 vs false</strong>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;color:#cbd5e1;font-size:13px;">
                            <span>client.edge.zeroValue = <strong volt:text="client:edge.zeroValue ?? 'null'"
                                    style="color:#e2e8f0;">null</strong></span>
                            <span>shared.edge.falseValue = <strong volt:text="shared:edge.falseValue ?? 'null'"
                                    style="color:#e2e8f0;">null</strong></span>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;">
                            <span volt:show="client:edge.zeroValue == shared:edge.falseValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">==
                                => true</span>
                            <span volt:show.hide="client:edge.zeroValue == shared:edge.falseValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">==
                                => false</span>
                            <span volt:show="client:edge.zeroValue === shared:edge.falseValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">===
                                => true</span>
                            <span volt:show.hide="client:edge.zeroValue === shared:edge.falseValue"
                                style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">===
                                => false</span>
                        </div>
                    </article>
                </div>
            </article>

            <article data-runtime-check="null-undefined-strict-panel"
                style="display:grid;gap:10px;border:1px solid rgba(20,184,166,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                    <strong style="color:#99f6e4;">shared:draft.note === 'activar' || client:draft.note !==
                        'pausa'</strong>
                    <span volt:show="shared:draft.note === 'activar' || client:draft.note !== 'pausa'"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                    <span volt:show.hide="shared:draft.note === 'activar' || client:draft.note !== 'pausa'"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
                </div>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">La variante estricta evita coerciones implícitas y
                    te deja comparar el caso <code>null</code> con más claridad.</p>
                <div
                    style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;color:#cbd5e1;font-size:13px;">
                        <span>shared.draft.note = <strong volt:text="shared:draft.note ?? 'null'"
                                style="color:#99f6e4;">null</strong></span>
                        <span>client.draft.note = <strong volt:text="client:draft.note ?? 'null'"
                                style="color:#99f6e4;">null</strong></span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span volt:show="shared:draft.note === 'activar'"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">shared:draft.note
                            === 'activar' => true</span>
                        <span volt:show.hide="shared:draft.note === 'activar'"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">shared:draft.note
                            === 'activar' => false</span>
                        <span volt:show="client:draft.note !== 'pausa'"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">client:draft.note
                            !== 'pausa' => true</span>
                        <span volt:show.hide="client:draft.note !== 'pausa'"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">client:draft.note
                            !== 'pausa' => false</span>
                    </div>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(245,158,11,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                    <strong style="color:#fde68a;">client:ui.showClientPanel && !shared:ui.showSharedPanel</strong>
                    <span volt:show="client:ui.showClientPanel && !shared:ui.showSharedPanel"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                    <span volt:show.hide="client:ui.showClientPanel && !shared:ui.showSharedPanel"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
                </div>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">Solo es true cuando el panel client esta activo y el
                    panel shared no esta visible.</p>
                <div
                    style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span volt:show="client:ui.showClientPanel"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">client:ui.showClientPanel
                            => true</span>
                        <span volt:show.hide="client:ui.showClientPanel"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">client:ui.showClientPanel
                            => false</span>
                        <span volt:show="!shared:ui.showSharedPanel"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">!shared:ui.showSharedPanel
                            => true</span>
                        <span volt:show.hide="!shared:ui.showSharedPanel"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">!shared:ui.showSharedPanel
                            => false</span>
                    </div>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(96,165,250,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                    <strong style="color:#bfdbfe;">shared:ui.mountSharedPanel || (client:ui.mountClientPanel &&
                        !shared:ui.showSharedPanel)</strong>
                    <span
                        volt:show="shared:ui.mountSharedPanel || (client:ui.mountClientPanel && !shared:ui.showSharedPanel)"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                    <span
                        volt:show.hide="shared:ui.mountSharedPanel || (client:ui.mountClientPanel && !shared:ui.showSharedPanel)"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
                </div>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">Basta con que shared este montado o que client este
                    montado mientras shared permanezca oculto.</p>
                <div
                    style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span volt:show="shared:ui.mountSharedPanel"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">shared:ui.mountSharedPanel
                            => true</span>
                        <span volt:show.hide="shared:ui.mountSharedPanel"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">shared:ui.mountSharedPanel
                            => false</span>
                        <span volt:show="client:ui.mountClientPanel && !shared:ui.showSharedPanel"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">client:ui.mountClientPanel
                            && !shared:ui.showSharedPanel => true</span>
                        <span volt:show.hide="client:ui.mountClientPanel && !shared:ui.showSharedPanel"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">client:ui.mountClientPanel
                            && !shared:ui.showSharedPanel => false</span>
                    </div>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(34,211,238,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                    <strong style="color:#a5f3fc;">client:ui.highlightClientCard && !shared:ui.lockSharedAction</strong>
                    <span volt:show="client:ui.highlightClientCard && !shared:ui.lockSharedAction"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                    <span volt:show.hide="client:ui.highlightClientCard && !shared:ui.lockSharedAction"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
                </div>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">Esta es la rama client de <code>volt:class</code>;
                    deja de aplicar si el lock shared esta activo.</p>
                <div
                    style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span volt:show="client:ui.highlightClientCard"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">client:ui.highlightClientCard
                            => true</span>
                        <span volt:show.hide="client:ui.highlightClientCard"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">client:ui.highlightClientCard
                            => false</span>
                        <span volt:show="!shared:ui.lockSharedAction"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">!shared:ui.lockSharedAction
                            => true</span>
                        <span volt:show.hide="!shared:ui.lockSharedAction"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">!shared:ui.lockSharedAction
                            => false</span>
                    </div>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(244,114,182,0.18);background:rgba(15,23,42,0.82);border-radius:16px;padding:16px;">
                <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
                    <strong style="color:#fbcfe8;">client:ui.softenClientCard && !shared:ui.softenSharedCard</strong>
                    <span volt:show="client:ui.softenClientCard && !shared:ui.softenSharedCard"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">true</span>
                    <span volt:show.hide="client:ui.softenClientCard && !shared:ui.softenSharedCard"
                        style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">false</span>
                </div>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">Esta es la rama client de <code>volt:style</code>;
                    si shared tambien esta activo, prevalece la otra regla visual.</p>
                <div
                    style="display:grid;gap:8px;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        <span volt:show="client:ui.softenClientCard"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">client:ui.softenClientCard
                            => true</span>
                        <span volt:show.hide="client:ui.softenClientCard"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">client:ui.softenClientCard
                            => false</span>
                        <span volt:show="!shared:ui.softenSharedCard"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:999px;padding:5px 9px;font-size:12px;">!shared:ui.softenSharedCard
                            => true</span>
                        <span volt:show.hide="!shared:ui.softenSharedCard"
                            style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fecaca;border-radius:999px;padding:5px 9px;font-size:12px;">!shared:ui.softenSharedCard
                            => false</span>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(14,165,233,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;"><code>volt:text</code> con fallback</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                El primer valor definido gana. Si no hay nota local, toma la compartida; si tampoco existe, usa un
                literal.
            </p>
        </div>
        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <article
                style="display:grid;gap:8px;border:1px solid rgba(14,165,233,0.20);background:rgba(8,47,73,0.18);border-radius:16px;padding:16px;">
                <strong style="color:#7dd3fc;">Contrato</strong>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#bae6fd;font-size:12px;line-height:1.7;">client:draft.note ?? shared:draft.note ?? 'Sin nota disponible'</pre>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(34,197,94,0.20);background:rgba(6,78,59,0.18);border-radius:16px;padding:16px;">
                <strong style="color:#bbf7d0;">Resultado actual</strong>
                <span data-runtime-check="text-fallback-result"
                    volt:text="client:draft.note ?? shared:draft.note ?? 'Sin nota disponible'"
                    style="min-block-size:28px;color:#ecfdf5;line-height:1.7;">Sin nota disponible</span>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(245,158,11,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Condiciones compuestas</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Aqui se validan <code>!</code>, <code>&amp;&amp;</code>, <code>||</code> y parentesis sobre el store
                runtime.
            </p>
        </div>
        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));align-items:start;">
            <article
                style="display:grid;gap:10px;border:1px solid rgba(245,158,11,0.24);background:rgba(120,53,15,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#fde68a;"><code>volt:show</code></strong>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#fef3c7;font-size:12px;line-height:1.7;">client:ui.showClientPanel && !shared:ui.showSharedPanel</pre>
                <div data-runtime-check="show-compound-panel"
                    volt:show="client:ui.showClientPanel && !shared:ui.showSharedPanel"
                    style="display:grid;gap:6px;border:1px solid rgba(245,158,11,0.24);background:rgba(120,53,15,0.24);border-radius:12px;padding:14px;">
                    <strong style="color:#fde68a;">Visible solo si client es true y shared es false</strong>
                    <span style="color:#fef3c7;">Activa ambos toggles para ver como la segunda condicion bloquea este
                        panel.</span>
                </div>
            </article>

            <article
                style="display:grid;gap:10px;border:1px solid rgba(59,130,246,0.24);background:rgba(30,64,175,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#bfdbfe;"><code>volt:if</code></strong>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#dbeafe;font-size:12px;line-height:1.7;">shared:ui.mountSharedPanel || (client:ui.mountClientPanel && !shared:ui.showSharedPanel)</pre>
                <div data-runtime-check="if-compound-panel"
                    volt:if="shared:ui.mountSharedPanel || (client:ui.mountClientPanel && !shared:ui.showSharedPanel)"
                    style="display:grid;gap:6px;border:1px solid rgba(59,130,246,0.24);background:rgba(30,64,175,0.24);border-radius:12px;padding:14px;">
                    <strong style="color:#bfdbfe;">Nodo montado por condicion compuesta</strong>
                    <span style="color:#dbeafe;">Se monta si el shared esta activo o si el client esta activo mientras
                        el
                        panel shared no este visible.</span>
                </div>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(34,211,238,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Reglas multiples por atributo</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Las tres demos siguientes usan varias reglas en el mismo atributo con <code>|</code>.
            </p>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));align-items:start;">
            <article class="transition-all duration-200" data-runtime-check="class-multi-card"
                volt:class="client:ui.highlightClientCard && !shared:ui.lockSharedAction -> ring-4 ring-cyan-400 shadow-lg shadow-cyan-950/40 | shared:ui.highlightSharedCard -> -translate-y-1 shadow-xl shadow-fuchsia-950/30"
                style="display:grid;gap:8px;border:1px solid rgba(34,211,238,0.24);background:rgba(8,47,73,0.18);border-radius:16px;padding:16px;">
                <strong style="color:#a5f3fc;"><code>volt:class</code> con dos reglas</strong>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#cffafe;font-size:12px;line-height:1.7;">client:ui.highlightClientCard && !shared:ui.lockSharedAction -> ring-4 ring-cyan-400 shadow-lg shadow-cyan-950/40 | shared:ui.highlightSharedCard -> -translate-y-1 shadow-xl shadow-fuchsia-950/30</pre>
            </article>

            <article
                style="display:grid;gap:12px;border:1px solid rgba(96,165,250,0.24);background:rgba(30,64,175,0.12);border-radius:16px;padding:16px;">
                <strong style="color:#bfdbfe;"><code>volt:attr</code> con dos reglas</strong>
                <button type="button" data-runtime-check="attr-multi-button"
                    volt:attr="client:ui.lockClientAction && !shared:ui.lockSharedAction -> disabled=disabled, aria-disabled=true, data-lock=client-only | shared:ui.lockSharedAction -> disabled=disabled, aria-disabled=true, data-lock=shared, title=Bloqueado por shared"
                    title="Disponible"
                    style="border:1px solid rgba(96,165,250,0.28);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 14px;">
                    Boton con atributos dinamicos
                </button>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#dbeafe;font-size:12px;line-height:1.7;">client:ui.lockClientAction && !shared:ui.lockSharedAction -> disabled=disabled, aria-disabled=true, data-lock=client-only | shared:ui.lockSharedAction -> disabled=disabled, aria-disabled=true, data-lock=shared, title=Bloqueado por shared</pre>
            </article>

            <article class="transition-all duration-200" data-runtime-check="style-multi-card"
                volt:style="client:ui.softenClientCard && !shared:ui.softenSharedCard -> opacity:0.55; transform:scale(0.98) translateY(6px) | shared:ui.softenSharedCard -> opacity:0.85; box-shadow:0 18px 40px rgba(217,70,239,0.22); outline:1px solid rgba(217,70,239,0.4)"
                style="display:grid;gap:8px;border:1px solid rgba(244,114,182,0.24);background:rgba(131,24,67,0.14);border-radius:16px;padding:16px;">
                <strong style="color:#fbcfe8;"><code>volt:style</code> con dos reglas</strong>
                <pre
                    style="margin:0;overflow:auto;border:1px solid rgba(51,65,85,1);background:#020617;border-radius:12px;padding:12px;color:#fce7f3;font-size:12px;line-height:1.7;">client:ui.softenClientCard && !shared:ui.softenSharedCard -> opacity:0.55; transform:scale(0.98) translateY(6px) | shared:ui.softenSharedCard -> opacity:0.85; box-shadow:0 18px 40px rgba(217,70,239,0.22); outline:1px solid rgba(217,70,239,0.4)</pre>
            </article>
        </div>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeState" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(16,185,129,0.28);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver a /runtimeState
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection
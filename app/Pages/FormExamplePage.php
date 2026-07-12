<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages;

use VoltStack\Runtime\Component\Component;
use VoltStack\Runtime\Protocol\ActionEffectOptions;
use VoltStack\Runtime\Protocol\ActionRuntimePolicyBuilder;

final class FormExamplePage extends Component
{
    public string $title = 'Form Example';

    public string $savedMessage = '';

    public function save(string $message = ''): ActionEffectOptions
    {
        $this->validate([
            'title' => $this->title,
        ], [
            'title' => ['required', 'string', 'min:3'],
        ]);

        $this->savedMessage = $message !== '' ? $message : 'Counter configuration saved.';

        return ActionEffectOptions::make()
            ->transitions()
            ->onTarget('saved-message')
            ->forTextUpdate()
            ->updateAs('glow', duration: 240, className: 'volt-transition-soft-edge')
            ->onTarget('title')
            ->forTextUpdate()
            ->pulse(260)
            ->end()
            ->policies(fn(ActionRuntimePolicyBuilder $p) => $p
                ->onTarget('title')
                ->dirty('200ms')
                ->onTarget('save-form')
                ->forSave()
                ->success('200ms', '1.2s')
                ->error('3s'))
            ->effects()
            ->onTarget('title-input')
            ->focusAndSetAttribute('data-last-save', (string) time())
            ->event('demo.saved', [
                'message' => $this->savedMessage,
                'count' => 0,
            ])
            ->end();
    }
}
?>

@extends('layouts.spa')

@section('head')
<meta name="volt-fragment-control" content="preserve" data-volt-head-key="fragment-control-preserve">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:880px;margin:0 auto;">
    <section
        style="border:1px solid rgba(34,197,94,0.24);background:rgba(6,78,59,0.16);border-radius:18px;padding:24px;color:#e2e8f0;">
        <span
            style="display:inline-flex;padding:6px 10px;border-radius:999px;border:1px solid rgba(34,197,94,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#bbf7d0;">Fragment
            Cache Destination</span>
        <h2 style="margin:16px 0 10px;font-size:28px;line-height:1.2;">Destino compatible para la preservacion</h2>
        <p style="margin:0;color:#bbf7d0;line-height:1.7;">
            Si llegaste desde <code>/fragmentCache</code>, el formulario verde con clave
            <code>draft-fragment</code> deberia conservar exactamente el estado que dejaste antes de navegar.
        </p>

        <div
            style="margin-block-start:18px;display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));">
            <form data-volt-preserve="draft-fragment"
                style="display:grid;gap:12px;border:1px solid rgba(34,197,94,0.28);background:rgba(6,78,59,0.18);border-radius:18px;padding:18px;">
                <div>
                    <span
                        style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(34,197,94,0.30);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#bbf7d0;">Preservado</span>
                    <h3 style="margin:12px 0 6px;font-size:20px;color:#f8fafc;">Borrador compartido</h3>
                    <p style="margin:0;color:#bbf7d0;line-height:1.6;">
                        Este formulario comparte la misma clave del demo principal, asi que el runtime puede reutilizar
                        el nodo vivo durante la navegacion SPA.
                    </p>
                </div>

                <label style="display:grid;gap:6px;">
                    <span style="font-size:13px;color:#dcfce7;">Nombre del borrador</span>
                    <input type="text" name="draft_name" value="Mi borrador vivo"
                        style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(34,197,94,0.25);background:#022c22;color:#f0fdf4;">
                </label>

                <label style="display:grid;gap:6px;">
                    <span style="font-size:13px;color:#dcfce7;">Notas temporales</span>
                    <textarea name="draft_notes" rows="4"
                        style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(34,197,94,0.25);background:#022c22;color:#f0fdf4;">Este texto debe quedarse al navegar con SPA.</textarea>
                </label>

                <label style="display:flex;align-items:center;gap:10px;color:#dcfce7;">
                    <input type="checkbox" name="draft_flag" checked>
                    Mantener este check despues de navegar.
                </label>
            </form>

            <form
                style="display:grid;gap:12px;border:1px solid rgba(148,163,184,0.24);background:rgba(2,6,23,0.62);border-radius:18px;padding:18px;">
                <div>
                    <span
                        style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(148,163,184,0.24);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#cbd5e1;">Control</span>
                    <h3 style="margin:12px 0 6px;font-size:20px;color:#f8fafc;">Formulario normal</h3>
                    <p style="margin:0;color:#94a3b8;line-height:1.6;">
                        Este segundo formulario no esta marcado para preservacion y deberia volver a su HTML inicial al
                        entrar en esta ruta.
                    </p>
                </div>

                <label style="display:grid;gap:6px;">
                    <span style="font-size:13px;color:#cbd5e1;">Nombre de control</span>
                    <input type="text" name="normal_name" value="Se reinicia al volver"
                        style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid #334155;background:#020617;color:#f8fafc;">
                </label>

                <label style="display:grid;gap:6px;">
                    <span style="font-size:13px;color:#cbd5e1;">Notas de control</span>
                    <textarea name="normal_notes" rows="4"
                        style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid #334155;background:#020617;color:#f8fafc;">Este contenido deberia resetearse.</textarea>
                </label>

                <label style="display:flex;align-items:center;gap:10px;color:#cbd5e1;">
                    <input type="checkbox" name="normal_flag">
                    Este check vuelve a su valor inicial.
                </label>
            </form>
        </div>
    </section>

    <section
        style="border:1px solid rgba(59,130,246,0.24);background:rgba(15,23,42,0.92);border-radius:18px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 12px;font-size:22px;">Shell vivo compartido</h2>
        <p style="margin:0 0 18px;color:#94a3b8;line-height:1.7;">
            Este segundo fragmento reutiliza la clave <code>live-shell</code> para demostrar una preservacion mas
            amplia que un formulario simple.
        </p>

        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <section data-volt-preserve="live-shell"
                style="display:grid;gap:14px;border:1px solid rgba(59,130,246,0.28);background:rgba(30,41,59,0.82);border-radius:18px;padding:18px;">
                <div>
                    <span
                        style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(59,130,246,0.30);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#bfdbfe;">Shell
                        preservado</span>
                    <h3 style="margin:12px 0 6px;font-size:20px;color:#f8fafc;">Workspace temporal</h3>
                    <p style="margin:0;color:#bfdbfe;line-height:1.6;">
                        Si cambias este bloque en <code>/fragmentCache</code>, deberia aparecer intacto al llegar aqui.
                    </p>
                </div>

                <div contenteditable="true"
                    style="min-block-size:96px;padding:12px 14px;border-radius:12px;border:1px solid rgba(59,130,246,0.24);background:#0f172a;color:#dbeafe;line-height:1.65;">
                    Nota editable: deberia mantenerse entre pantallas compatibles.
                </div>

                <details open
                    style="border:1px solid rgba(59,130,246,0.20);border-radius:12px;padding:12px 14px;background:#0f172a;">
                    <summary style="cursor:pointer;color:#dbeafe;">Panel plegable preservado</summary>
                    <p style="margin:10px 0 0;color:#93c5fd;line-height:1.6;">
                        Su estado abierto/cerrado tambien deberia sobrevivir a la navegacion SPA.
                    </p>
                </details>

                <label style="display:grid;gap:8px;">
                    <span style="font-size:13px;color:#dbeafe;">Nivel visual</span>
                    <input type="range" min="0" max="100" value="72">
                </label>
            </section>

            <section
                style="display:grid;gap:14px;border:1px solid rgba(148,163,184,0.24);background:rgba(2,6,23,0.62);border-radius:18px;padding:18px;">
                <div>
                    <span
                        style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(148,163,184,0.24);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#cbd5e1;">Control</span>
                    <h3 style="margin:12px 0 6px;font-size:20px;color:#f8fafc;">Shell normal</h3>
                    <p style="margin:0;color:#94a3b8;line-height:1.6;">
                        Esta tarjeta sirve de comparacion y vuelve siempre a su HTML inicial.
                    </p>
                </div>

                <div contenteditable="true"
                    style="min-block-size:96px;padding:12px 14px;border-radius:12px;border:1px solid #334155;background:#020617;color:#e2e8f0;line-height:1.65;">
                    Este contenido se reinicia al entrar en esta ruta.
                </div>

                <details open style="border:1px solid #334155;border-radius:12px;padding:12px 14px;background:#020617;">
                    <summary style="cursor:pointer;color:#e2e8f0;">Panel de control</summary>
                    <p style="margin:10px 0 0;color:#94a3b8;line-height:1.6;">
                        No tiene clave declarativa, asi que no participa en el reuse del runtime.
                    </p>
                </details>

                <label style="display:grid;gap:8px;">
                    <span style="font-size:13px;color:#e2e8f0;">Nivel visual</span>
                    <input type="range" min="0" max="100" value="18">
                </label>
            </section>
        </div>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:18px;padding:24px;color:#e2e8f0;">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:end;justify-content:space-between;">
            <div>
                <h2 style="margin:0 0 10px;font-size:22px;">Monitor rapido de fragmentos</h2>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">
                    Navega desde y hacia <code>/fragmentCache</code> para observar si el runtime preserva o descarta el
                    fragmento compartido.
                </p>
            </div>
            <span style="font-size:12px;color:#64748b;">Escucha `volt:fragment-preserve` y
                `volt:fragment-discard`.</span>
        </div>

        <div
            style="margin-block-start:18px;display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
            <article data-volt-hook-card="volt:fragment-preserve"
                style="display:flex;flex-direction:column;gap:12px;border:1px solid rgba(34,197,94,0.24);background:rgba(6,78,59,0.16);border-radius:16px;padding:16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                    <strong
                        style="font-size:12px;letter-spacing:0.14em;text-transform:uppercase;color:#bbf7d0;">volt:fragment-preserve</strong>
                    <span data-volt-hook-source
                        style="display:inline-flex;align-items:center;border:1px solid rgba(51,65,85,1);border-radius:999px;padding:4px 8px;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">sin
                        source</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                    <div
                        style="border:1px solid rgba(34,197,94,0.18);border-radius:12px;padding:10px;background:#022c22;">
                        <span style="display:block;font-size:12px;color:#86efac;">Veces</span>
                        <strong data-volt-hook-count
                            style="display:block;margin-top:6px;font-size:24px;color:#f0fdf4;">0</strong>
                    </div>
                    <div
                        style="border:1px solid rgba(34,197,94,0.18);border-radius:12px;padding:10px;background:#022c22;">
                        <span style="display:block;font-size:12px;color:#86efac;">Ultima</span>
                        <strong data-volt-hook-last
                            style="display:block;margin-top:10px;font-size:14px;color:#f0fdf4;">-</strong>
                    </div>
                </div>
                <pre data-volt-hook-detail
                    style="margin:0;min-block-size:100px;overflow:auto;border:1px solid rgba(34,197,94,0.18);border-radius:12px;padding:12px;background:#022c22;color:#bbf7d0;font-size:11px;line-height:1.6;">{"esperando":"evento"}</pre>
            </article>

            <article data-volt-hook-card="volt:fragment-discard"
                style="display:flex;flex-direction:column;gap:12px;border:1px solid rgba(248,113,113,0.24);background:rgba(127,29,29,0.16);border-radius:16px;padding:16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                    <strong
                        style="font-size:12px;letter-spacing:0.14em;text-transform:uppercase;color:#fecaca;">volt:fragment-discard</strong>
                    <span data-volt-hook-source
                        style="display:inline-flex;align-items:center;border:1px solid rgba(51,65,85,1);border-radius:999px;padding:4px 8px;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">sin
                        source</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                    <div
                        style="border:1px solid rgba(248,113,113,0.18);border-radius:12px;padding:10px;background:#450a0a;">
                        <span style="display:block;font-size:12px;color:#fda4af;">Veces</span>
                        <strong data-volt-hook-count
                            style="display:block;margin-top:6px;font-size:24px;color:#fff1f2;">0</strong>
                    </div>
                    <div
                        style="border:1px solid rgba(248,113,113,0.18);border-radius:12px;padding:10px;background:#450a0a;">
                        <span style="display:block;font-size:12px;color:#fda4af;">Ultima</span>
                        <strong data-volt-hook-last
                            style="display:block;margin-top:10px;font-size:14px;color:#fff1f2;">-</strong>
                    </div>
                </div>
                <pre data-volt-hook-detail
                    style="margin:0;min-block-size:100px;overflow:auto;border:1px solid rgba(248,113,113,0.18);border-radius:12px;padding:12px;background:#450a0a;color:#fecaca;font-size:11px;line-height:1.6;">{"esperando":"evento"}</pre>
            </article>
        </div>

        <ol data-volt-hook-log data-volt-hook-log-filter="fragment-only"
            style="margin:18px 0 0;padding:0;display:grid;gap:12px;list-style:none;"></ol>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:18px;padding:24px;color:#e2e8f0;">
        <span
            style="display:inline-flex;padding:6px 10px;border-radius:999px;border:1px solid rgba(59,130,246,0.28);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#93c5fd;">Form
            Demo</span>
        <h1 style="margin:16px 0 10px;font-size:32px;line-height:1.2;">Formulario para ver patch + preservacion de
            foco</h1>
        <p style="margin:0 0 16px;color:#94a3b8;line-height:1.7;">
            Esta pagina contiene solo la prueba del formulario para aislar <code>volt:model</code>,
            <code>volt:submit</code>, foco preservado y los estados <code>dirty</code>, <code>error</code> y
            <code>success</code>.
        </p>

        <div volt:dirty volt:dirty.target="title" volt:dirty.debounce="200ms"
            style="margin:0 0 16px;border:1px solid rgba(168,85,247,0.35);background:rgba(88,28,135,0.18);color:#f3e8ff;border-radius:12px;padding:12px 14px;">
            Este aviso aparece cuando el campo ligado a <code>title</code> queda modificado localmente y respeta
            <code>volt:dirty.debounce="200ms"</code>.
        </div>
        <div volt:error="save" volt:error.timeout="3s"
            style="margin:0 0 16px;border:1px solid rgba(248,113,113,0.35);background:rgba(127,29,29,0.22);color:#fecaca;border-radius:12px;padding:12px 14px;">
            Este mensaje solo aparece para errores de la accion <code>save</code> y se limpia automaticamente tras
            <code>3s</code>.
        </div>
        <div volt:success volt:success.target="save-form"
            style="margin:0 0 16px;border:1px solid rgba(16,185,129,0.35);background:rgba(6,95,70,0.18);color:#d1fae5;border-radius:12px;padding:12px 14px;">
            Este mensaje solo aparece cuando el exito vino del trigger con target <code>save-form</code>.
        </div>
        <div volt:success="save"
            style="margin:0 0 16px;border:1px dashed rgba(110,231,183,0.45);background:rgba(6,78,59,0.18);color:#d1fae5;border-radius:12px;padding:12px 14px;">
            Este aviso depende de la politica backend
            <code>policies(fn (ActionRuntimePolicyBuilder $p) =&gt; $p-&gt;forSave()-&gt;success('200ms', '1.2s'))</code>.
        </div>
        <form data-volt-target="save-form" volt-submit="save" style="display:grid;gap:14px;">
            <label style="display:grid;gap:6px;">
                <span style="font-size:14px;color:#cbd5e1;">Titulo</span>
                <input data-volt-target="title-input" data-volt-transition="glow" data-volt-transition-update="glow"
                    data-volt-transition-update-class="volt-transition-soft-edge" type="text" volt-model="title"
                    value="{{ $title }}"
                    style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid #334155;background:#020617;color:#f8fafc;">
            </label>
            <label style="display:grid;gap:6px;">
                <span style="font-size:14px;color:#cbd5e1;">Mensaje</span>
                <input data-volt-target="message-input" data-volt-transition="fade" data-volt-transition-update="glow"
                    data-volt-transition-update-class="volt-transition-soft-edge" type="text" name="message"
                    value="{{ $savedMessage }}"
                    style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid #334155;background:#020617;color:#f8fafc;">
            </label>
            <div style="border:1px solid rgba(51,65,85,1);border-radius:14px;padding:16px;background:#020617;">
                <span style="display:block;font-size:12px;color:#64748b;text-transform:uppercase;">Saved message</span>
                <strong data-volt-target="saved-message" data-volt-transition="fade" data-volt-transition-update="glow"
                    data-volt-transition-update-class="volt-transition-soft-edge"
                    style="display:block;margin-block-start:8px;font-size:15px;color:#f8fafc;">{{ $savedMessage !== '' ? $savedMessage : 'Aun no guardado.' }}</strong>
            </div>
            <button type="submit" volt:loading.class="opacity-60" volt:loading.action="save" volt:loading.delay="150ms"
                volt:loading.min-duration="700ms" volt:dirty.class="ring-2 ring-violet-400/40"
                volt:dirty.debounce="200ms" volt:success.attr="data-request-state=success"
                volt:error.attr="data-request-state=error" volt:error.target="save-form" volt:error.timeout="3s"
                style="justify-self:start;border:1px solid rgba(59,130,246,0.35);background:rgba(59,130,246,0.12);color:#dbeafe;border-radius:10px;padding:10px 16px;">
                Guardar y disparar patch
            </button>
        </form>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:18px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 10px;font-size:22px;">Navegacion para hooks SPA</h2>
        <p style="margin:0 0 16px;color:#94a3b8;line-height:1.7;">
            Usa estos enlaces para cambiar entre demos sin recargar la pagina completa.
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <a href="/" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid rgba(34,211,238,0.28);background:rgba(34,211,238,0.08);color:#cffafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Volver al inicio
            </a>
            <a href="/fragmentCache" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid rgba(34,197,94,0.28);background:rgba(34,197,94,0.12);color:#dcfce7;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Volver a fragment cache
            </a>
            <a href="/fragmentCacheReset" volt:navigate volt:prefetch="none" volt:cache="no-store"
                style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.26);background:rgba(127,29,29,0.16);color:#fecaca;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Ir a descarte forzado
            </a>
            <a href="/counterExample" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Ir a contador
            </a>
            <a href="/cacheExample" volt:navigate
                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition border-slate-700 bg-slate-950/70 text-slate-200 hover:border-slate-500 hover:text-white">
                Probar navegacion a /cacheExample
            </a>
        </div>
    </section>
</div>
@endsection
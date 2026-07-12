<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Cache;

use VoltStack\Runtime\Component\Component;

final class CachePage extends Component
{
    public string $title = 'Cache Demo';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-fragment-control" content="preserve" data-volt-head-key="fragment-control-preserve">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:980px;margin:0 auto;">
    <section
        style="border:1px solid rgba(14,165,233,0.25);background:rgba(15,23,42,0.82);border-radius:20px;padding:28px;color:#e2e8f0;">
        <span
            style="display:inline-flex;padding:6px 10px;border-radius:999px;border:1px solid rgba(14,165,233,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#7dd3fc;">Fragment
            Cache MVP</span>
        <h1 style="margin:16px 0 10px;font-size:34px;line-height:1.1;">{{ $title }}</h1>
        <p style="margin:0;color:#94a3b8;line-height:1.75;">
            Esta pagina demuestra la preservacion opt-in del runtime SPA. El fragmento marcado con
            <code>data-volt-preserve="draft-fragment"</code> deberia sobrevivir al navegar a
            <code>/formExample</code> y volver. Ademas, un segundo shell marcado con
            <code>data-volt-preserve="live-shell"</code> te deja comprobar que el runtime tambien puede reutilizar una
            zona viva no centrada solo en inputs.
        </p>

        <div
            style="margin-block-start:18px;display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));">
            <div style="border:1px solid rgba(51,65,85,1);border-radius:14px;padding:16px;background:#020617;">
                <strong style="display:block;font-size:13px;color:#f8fafc;">1. Escribe datos</strong>
                <span style="display:block;margin-block-start:8px;font-size:14px;color:#94a3b8;">Edita el formulario
                    preservado y el formulario de control.</span>
            </div>
            <div style="border:1px solid rgba(51,65,85,1);border-radius:14px;padding:16px;background:#020617;">
                <strong style="display:block;font-size:13px;color:#f8fafc;">2. Navega con SPA</strong>
                <span style="display:block;margin-block-start:8px;font-size:14px;color:#94a3b8;">Usa el enlace a
                    <code>/formExample</code> con <code>volt:navigate</code>.</span>
            </div>
            <div style="border:1px solid rgba(51,65,85,1);border-radius:14px;padding:16px;background:#020617;">
                <strong style="display:block;font-size:13px;color:#f8fafc;">3. Compara</strong>
                <span style="display:block;margin-block-start:8px;font-size:14px;color:#94a3b8;">El fragmento marcado
                    deberia mantener sus valores; el control sin marca deberia reiniciarse.</span>
            </div>
        </div>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 12px;font-size:24px;">Comparacion visual</h2>
        <p style="margin:0 0 18px;color:#94a3b8;line-height:1.7;">
            Ambos formularios empiezan con valores base. Solo el primero se marca como fragmento preservable.
        </p>

        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));">
            <form data-volt-preserve="draft-fragment"
                style="display:grid;gap:12px;border:1px solid rgba(34,197,94,0.28);background:rgba(6,78,59,0.18);border-radius:18px;padding:18px;">
                <div>
                    <span
                        style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(34,197,94,0.30);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#bbf7d0;">Preservado</span>
                    <h3 style="margin:12px 0 6px;font-size:20px;color:#f8fafc;">Borrador compartido</h3>
                    <p style="margin:0;color:#bbf7d0;line-height:1.6;">
                        Este formulario se reutiliza entre pantallas porque la pagina destino expone la misma clave
                        <code>draft-fragment</code>.
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
                        Este formulario no tiene <code>data-volt-preserve</code>, asi que debe volver a su HTML base
                        despues de cada navegacion.
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
        style="border:1px solid rgba(59,130,246,0.24);background:rgba(15,23,42,0.92);border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 12px;font-size:24px;">Shell vivo preservado</h2>
        <p style="margin:0 0 18px;color:#94a3b8;line-height:1.7;">
            Este bloque demuestra una preservacion mas cercana a un "componente vivo": texto editable, un
            <code>details</code> y un control de rango. Solo la tarjeta verde tiene clave compartida y deberia
            mantenerse al ir a <code>/formExample</code>.
        </p>

        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));">
            <section data-volt-preserve="live-shell"
                style="display:grid;gap:14px;border:1px solid rgba(59,130,246,0.28);background:rgba(30,41,59,0.82);border-radius:18px;padding:18px;">
                <div>
                    <span
                        style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(59,130,246,0.30);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#bfdbfe;">Shell
                        preservado</span>
                    <h3 style="margin:12px 0 6px;font-size:20px;color:#f8fafc;">Workspace temporal</h3>
                    <p style="margin:0;color:#bfdbfe;line-height:1.6;">
                        Edita este contenido, mueve el rango o cierra el panel. Si la clave coincide en destino, el nodo
                        vivo debe viajar contigo.
                    </p>
                </div>

                <div contenteditable="true"
                    style="min-block-size:96px;padding:12px 14px;border-radius:12px;border:1px solid rgba(59,130,246,0.24);background:#0f172a;color:#dbeafe;line-height:1.65;">
                    Nota editable: cambia este texto antes de navegar.
                </div>

                <details open
                    style="border:1px solid rgba(59,130,246,0.20);border-radius:12px;padding:12px 14px;background:#0f172a;">
                    <summary style="cursor:pointer;color:#dbeafe;">Panel plegable preservado</summary>
                    <p style="margin:10px 0 0;color:#93c5fd;line-height:1.6;">
                        Si lo cierras o abres antes de navegar, deberia conservar su estado al volver desde una ruta
                        compatible.
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
                        Esta tarjeta no esta marcada para preservacion; cualquier cambio local vuelve a su HTML inicial
                        tras navegar.
                    </p>
                </div>

                <div contenteditable="true"
                    style="min-block-size:96px;padding:12px 14px;border-radius:12px;border:1px solid #334155;background:#020617;color:#e2e8f0;line-height:1.65;">
                    Este contenido se reinicia al volver.
                </div>

                <details open style="border:1px solid #334155;border-radius:12px;padding:12px 14px;background:#020617;">
                    <summary style="cursor:pointer;color:#e2e8f0;">Panel de control</summary>
                    <p style="margin:10px 0 0;color:#94a3b8;line-height:1.6;">
                        Sirve como comparacion rapida frente al shell preservado.
                    </p>
                </details>

                <label style="display:grid;gap:8px;">
                    <span style="font-size:13px;color:#e2e8f0;">Nivel visual</span>
                    <input type="range" min="0" max="100" value="24">
                </label>
            </section>
        </div>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 10px;font-size:24px;">Navegacion para probar el MVP</h2>
        <p style="margin:0 0 16px;color:#94a3b8;line-height:1.7;">
            La ruta <code>/formExample</code> ya incluye el mismo formulario y el mismo shell preservable para que
            puedas comprobar la reutilizacion del nodo vivo. Tambien hay una ruta con politica de descarte explicita
            para verificar la invalidez por documento.
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <a href="/formExample" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid rgba(34,197,94,0.28);background:rgba(34,197,94,0.12);color:#dcfce7;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Probar preservacion en /formExample
            </a>
            <a href="/fragmentCacheReset" volt:navigate volt:prefetch="none" volt:cache="no-store"
                style="display:inline-flex;align-items:center;border:1px solid rgba(248,113,113,0.26);background:rgba(127,29,29,0.16);color:#fecaca;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Probar descarte en /fragmentCacheReset
            </a>
            <a href="/cacheExample" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid rgba(14,165,233,0.28);background:rgba(14,165,233,0.12);color:#cffafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Ver monitor de cache SPA
            </a>
            <a href="/" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Volver al inicio
            </a>
        </div>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:end;justify-content:space-between;">
            <div>
                <h2 style="margin:0 0 10px;font-size:24px;">Monitor de fragmentos</h2>
                <p style="margin:0;color:#94a3b8;line-height:1.7;">
                    Este monitor escucha <code>volt:fragment-preserve</code> y <code>volt:fragment-discard</code> para
                    mostrar si el runtime reutilizo o descarto un fragmento durante la navegacion.
                </p>
            </div>
            <span style="font-size:12px;color:#64748b;">Se actualiza automaticamente al navegar con SPA.</span>
        </div>

        <div
            style="margin-block-start:18px;display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
            <article data-volt-hook-card="volt:fragment-preserve"
                style="display:flex;flex-direction:column;gap:14px;border:1px solid rgba(34,197,94,0.25);background:rgba(6,78,59,0.16);border-radius:18px;padding:18px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                    <strong
                        style="font-size:13px;letter-spacing:0.14em;text-transform:uppercase;color:#bbf7d0;">volt:fragment-preserve</strong>
                    <span data-volt-hook-source
                        style="display:inline-flex;align-items:center;border:1px solid rgba(51,65,85,1);border-radius:999px;padding:4px 8px;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">sin
                        source</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                    <div
                        style="border:1px solid rgba(34,197,94,0.18);border-radius:12px;padding:12px;background:#022c22;">
                        <span style="display:block;font-size:12px;color:#86efac;">Veces disparado</span>
                        <strong data-volt-hook-count
                            style="display:block;margin-block-start:6px;font-size:26px;color:#f0fdf4;">0</strong>
                    </div>
                    <div
                        style="border:1px solid rgba(34,197,94,0.18);border-radius:12px;padding:12px;background:#022c22;">
                        <span style="display:block;font-size:12px;color:#86efac;">Ultima vez</span>
                        <strong data-volt-hook-last
                            style="display:block;margin-block-start:10px;font-size:14px;color:#f0fdf4;">-</strong>
                    </div>
                </div>
                <pre data-volt-hook-detail
                    style="margin:0;min-block-size:120px;overflow:auto;border:1px solid rgba(34,197,94,0.18);border-radius:12px;padding:12px;background:#022c22;color:#bbf7d0;font-size:11px;line-height:1.6;">{"esperando":"evento"}</pre>
            </article>

            <article data-volt-hook-card="volt:fragment-discard"
                style="display:flex;flex-direction:column;gap:14px;border:1px solid rgba(248,113,113,0.24);background:rgba(127,29,29,0.16);border-radius:18px;padding:18px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                    <strong
                        style="font-size:13px;letter-spacing:0.14em;text-transform:uppercase;color:#fecaca;">volt:fragment-discard</strong>
                    <span data-volt-hook-source
                        style="display:inline-flex;align-items:center;border:1px solid rgba(51,65,85,1);border-radius:999px;padding:4px 8px;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:#94a3b8;">sin
                        source</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;">
                    <div
                        style="border:1px solid rgba(248,113,113,0.18);border-radius:12px;padding:12px;background:#450a0a;">
                        <span style="display:block;font-size:12px;color:#fda4af;">Veces disparado</span>
                        <strong data-volt-hook-count
                            style="display:block;margin-block-start:6px;font-size:26px;color:#fff1f2;">0</strong>
                    </div>
                    <div
                        style="border:1px solid rgba(248,113,113,0.18);border-radius:12px;padding:12px;background:#450a0a;">
                        <span style="display:block;font-size:12px;color:#fda4af;">Ultima vez</span>
                        <strong data-volt-hook-last
                            style="display:block;margin-block-start:10px;font-size:14px;color:#fff1f2;">-</strong>
                    </div>
                </div>
                <pre data-volt-hook-detail
                    style="margin:0;min-block-size:120px;overflow:auto;border:1px solid rgba(248,113,113,0.18);border-radius:12px;padding:12px;background:#450a0a;color:#fecaca;font-size:11px;line-height:1.6;">{"esperando":"evento"}</pre>
            </article>
        </div>

        <div style="margin-block-start:18px;">
            <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
                <h3 style="margin:0;font-size:14px;letter-spacing:0.18em;text-transform:uppercase;color:#cbd5e1;">Log
                    reciente de fragmentos</h3>
                <span style="font-size:12px;color:#64748b;">Solo muestra los ultimos 8 eventos `volt:fragment-*`.</span>
            </div>
            <ol data-volt-hook-log data-volt-hook-log-filter="fragment-only"
                style="margin:16px 0 0;padding:0;display:grid;gap:12px;list-style:none;"></ol>
        </div>
    </section>
</div>
@endsection
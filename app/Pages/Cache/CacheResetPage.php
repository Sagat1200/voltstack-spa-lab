<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Cache;

use VoltStack\Runtime\Component\Component;

final class CacheResetPage extends Component
{
    public string $title = 'Cache Reset';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-cache-control" content="no-store" data-volt-head-key="cache-no-store">
<meta name="volt-cache-control" content="reset" data-volt-head-key="cache-control-reset">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:920px;margin:0 auto;">
    <section
        style="border:1px solid rgba(248,113,113,0.26);background:rgba(127,29,29,0.16);border-radius:20px;padding:28px;color:#fee2e2;">
        <span
            style="display:inline-flex;padding:6px 10px;border-radius:999px;border:1px solid rgba(248,113,113,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#fecaca;">Fragment
            Cache Reset</span>
        <h1 style="margin:16px 0 10px;font-size:34px;line-height:1.1;">{{ $title }}</h1>
        <p style="margin:0;color:#fecaca;line-height:1.75;">
            Esta ruta declara <code>&lt;meta name="volt-fragment-control" content="reset"&gt;</code>, asi que el
            runtime debe descartar cualquier fragmento preservado que venga de otra pantalla, aunque la clave y el tag
            coincidan.
        </p>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 12px;font-size:24px;">Resultado esperado</h2>
        <p style="margin:0 0 18px;color:#94a3b8;line-height:1.7;">
            Si cambiaste el formulario o el shell vivo en <code>/fragmentCache</code> o <code>/formExample</code>,
            aqui deberias ver el HTML base de esta pagina, no el nodo vivo anterior. El inspector deberia registrar
            eventos <code>volt:fragment-discard</code> con razon <code>document-policy</code>.
        </p>

        <div style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));">
            <form data-volt-preserve="draft-fragment"
                style="display:grid;gap:12px;border:1px solid rgba(248,113,113,0.26);background:rgba(69,10,10,0.34);border-radius:18px;padding:18px;">
                <div>
                    <span
                        style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(248,113,113,0.28);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#fecaca;">Descartado
                        por politica</span>
                    <h3 style="margin:12px 0 6px;font-size:20px;color:#fff1f2;">Formulario base del destino</h3>
                    <p style="margin:0;color:#fecaca;line-height:1.6;">
                        Aunque comparte la clave <code>draft-fragment</code>, esta ruta obliga a reconstruir el
                        fragmento desde su propio HTML.
                    </p>
                </div>

                <label style="display:grid;gap:6px;">
                    <span style="font-size:13px;color:#ffe4e6;">Nombre del borrador</span>
                    <input type="text" name="draft_name" value="HTML nuevo del destino"
                        style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(248,113,113,0.22);background:#450a0a;color:#fff1f2;">
                </label>

                <label style="display:grid;gap:6px;">
                    <span style="font-size:13px;color:#ffe4e6;">Notas temporales</span>
                    <textarea name="draft_notes" rows="4"
                        style="display:block;inline-size:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(248,113,113,0.22);background:#450a0a;color:#fff1f2;">Este texto confirma que la politica del documento invalido el reuse del fragmento.</textarea>
                </label>
            </form>

            <section data-volt-preserve="live-shell"
                style="display:grid;gap:14px;border:1px solid rgba(248,113,113,0.26);background:rgba(69,10,10,0.34);border-radius:18px;padding:18px;">
                <div>
                    <span
                        style="display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid rgba(248,113,113,0.28);font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#fecaca;">Shell
                        reconstruido</span>
                    <h3 style="margin:12px 0 6px;font-size:20px;color:#fff1f2;">Workspace bloqueado</h3>
                    <p style="margin:0;color:#fecaca;line-height:1.6;">
                        Este shell comparte la misma clave <code>live-shell</code>, pero la politica
                        <code>reset</code> del documento impide reutilizar el nodo anterior.
                    </p>
                </div>

                <div contenteditable="true"
                    style="min-block-size:96px;padding:12px 14px;border-radius:12px;border:1px solid rgba(248,113,113,0.22);background:#450a0a;color:#fff1f2;line-height:1.65;">
                    Texto base del destino: si ves tus cambios anteriores aqui, la politica no se aplico bien.
                </div>

                <details open
                    style="border:1px solid rgba(248,113,113,0.18);border-radius:12px;padding:12px 14px;background:#450a0a;">
                    <summary style="cursor:pointer;color:#fff1f2;">Panel del destino</summary>
                    <p style="margin:10px 0 0;color:#fecaca;line-height:1.6;">
                        Debe mostrarse segun el HTML de esta pagina y no segun el estado local del origen.
                    </p>
                </details>
            </section>
        </div>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 10px;font-size:24px;">Navegacion</h2>
        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <a href="/fragmentCache" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid rgba(34,197,94,0.28);background:rgba(34,197,94,0.12);color:#dcfce7;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Volver a fragment cache
            </a>
            <a href="{{ route('spaReactive') }}" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Inicio Sistema SPA Full Reactive
            </a>
            <a href="/cacheExample" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid rgba(14,165,233,0.28);background:rgba(14,165,233,0.12);color:#cffafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Ver monitor de cache SPA
            </a>
        </div>
    </section>
</div>
@endsection
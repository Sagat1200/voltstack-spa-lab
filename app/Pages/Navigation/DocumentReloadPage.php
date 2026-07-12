<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Navigation;

use VoltStack\Runtime\Component\Component;

final class DocumentReloadPage extends Component
{
    public string $title = 'Document Reload Policy';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = date('H:i:s') . ' #' . substr(hash('sha1', microtime(true) . random_int(1, PHP_INT_MAX)), 0, 8);
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-document" content="reload" data-volt-head-key="navigation-document-reload">
<meta name="volt-cache-control" content="no-store" data-volt-head-key="navigation-policy-no-store">
<meta name="volt-navigation-mode" content="reload" data-volt-head-key="navigation-mode-reload">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:900px;margin:0 auto;">
    <section
        style="border:1px solid rgba(248,113,113,0.26);background:rgba(127,29,29,0.16);border-radius:20px;padding:28px;color:#fee2e2;">
        <span
            style="display:inline-flex;padding:6px 10px;border-radius:999px;border:1px solid rgba(248,113,113,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#fecaca;">Document
            Reload</span>
        <h1 style="margin:16px 0 10px;font-size:34px;line-height:1.1;">{{ $title }}</h1>
        <p style="margin:0;color:#fecaca;line-height:1.75;">
            Esta ruta declara <code>volt-document=reload</code> y <code>volt-navigation-mode=reload</code>. Si vienes
            desde <code>/navigationPolicy</code>, el runtime detecta que el documento destino no participa en SPA y
            delega la carga final al navegador.
        </p>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 12px;font-size:24px;">Marca de request</h2>
        <p style="margin:0 0 16px;color:#94a3b8;line-height:1.7;">
            Este valor cambia en cada request completa, asi que te sirve para confirmar visualmente que hubo recarga
            real del documento.
        </p>
        <div data-runtime-check="document-reload-request-marker"
            style="border:1px solid rgba(248,113,113,0.22);background:#450a0a;border-radius:16px;padding:18px;color:#fff1f2;">
            <span style="display:block;font-size:12px;color:#fda4af;text-transform:uppercase;">Request marker</span>
            <strong
                style="display:block;margin-block-start:8px;font-size:28px;letter-spacing:0.04em;">{{ $requestMarker }}</strong>
        </div>
    </section>

    <section
        style="border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0 0 12px;font-size:24px;">Navegacion</h2>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <a href="/navigationPolicy" volt:navigate="spa" data-runtime-check="document-reload-back-to-lab"
                style="display:inline-flex;align-items:center;border:1px solid rgba(34,197,94,0.28);background:rgba(34,197,94,0.12);color:#dcfce7;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Volver al laboratorio
            </a>
            <a href="/counterExample" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Ir a contador
            </a>
        </div>
    </section>
</div>
@endsection
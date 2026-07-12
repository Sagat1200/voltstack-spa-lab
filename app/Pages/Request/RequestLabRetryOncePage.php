<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Request;

use RuntimeException;
use VoltStack\Runtime\Component\Component;

final class RequestLabRetryOncePage extends Component
{
    public string $title = 'Request Lab Retry Once';

    public string $requestMarker;

    public function mount(): void
    {
        $markerPath = storage_path('framework/cache/runtime-request-lab-retry-once.flag');

        if (! is_file($markerPath)) {
            file_put_contents($markerPath, (string) time());
            throw new RuntimeException('Runtime QA forced transient navigation error.');
        }

        @unlink($markerPath);

        $this->requestMarker = sprintf('request-retry-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="runtime-request-lab-retry-once-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:920px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(34,197,94,0.26);background:linear-gradient(135deg,rgba(20,83,45,0.88),rgba(15,23,42,0.96));border-radius:24px;padding:32px;color:#dcfce7;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(74,222,128,0.3);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#86efac;">Retry
            Success Target</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;line-height:1.75;max-inline-size:74ch;">
                Esta ruta falla una vez con error servidor y, en el siguiente intento inmediato, responde bien para
                validar el <code>retry</code> automatico de navegacion <code>GET</code>.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(74,222,128,0.2);background:rgba(20,83,45,0.24);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#86efac;">Request
                marker</span>
            <strong style="font-size:14px;color:#f0fdf4;">{{ $requestMarker }}</strong>
        </div>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeRequestLab" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(74,222,128,0.28);background:rgba(20,83,45,0.18);color:#dcfce7;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al laboratorio
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection
<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Request;

use VoltStack\Runtime\Component\Component;

final class RequestLabSlowPage extends Component
{
    public string $title = 'Request Lab Slow Page';

    public string $requestMarker;

    public function mount(): void
    {
        usleep(1_400_000);
        $this->requestMarker = sprintf('request-slow-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="request-lab-slow-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:920px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(250,204,21,0.26);background:linear-gradient(135deg,rgba(113,63,18,0.9),rgba(15,23,42,0.96));border-radius:24px;padding:32px;color:#fef3c7;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(250,204,21,0.3);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#fde68a;">Slow
            Navigation Target</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;line-height:1.75;max-inline-size:74ch;">
                Esta ruta duerme <code>1400ms</code> en <code>mount()</code> para que las visitas SPA puedan probar
                <code>timeout</code> y <code>stale</code> sin depender de red externa.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(250,204,21,0.18);background:rgba(113,63,18,0.24);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#fde68a;">Request
                marker</span>
            <strong style="font-size:14px;color:#fefce8;">{{ $requestMarker }}</strong>
        </div>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeRequestLab" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(250,204,21,0.28);background:rgba(113,63,18,0.18);color:#fef3c7;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al laboratorio
        </a>
        <a href="/runtimeEvents" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.18);color:#bae6fd;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Ir a runtimeEvents
        </a>
    </section>
</div>
@endsection
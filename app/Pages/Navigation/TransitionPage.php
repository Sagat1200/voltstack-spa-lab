<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Navigation;

use VoltStack\Runtime\Component\Component;

final class TransitionPage extends Component
{
    public string $title = 'Page Transition Demo';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = sprintf('source-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="navigation-transition-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1040px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(96,165,250,0.26);background:linear-gradient(135deg,rgba(15,23,42,0.94),rgba(30,41,59,0.92));border-radius:24px;padding:32px;color:#e2e8f0;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(96,165,250,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#93c5fd;">Page
            Transition Profiles</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#cbd5e1;line-height:1.75;max-inline-size:72ch;">
                Esta demo prueba transiciones reales de pagina en navegacion SPA con perfiles reutilizables. El runtime
                ejecuta <code>leave</code> antes del swap del <code>body</code> y <code>enter</code> despues de montar
                el destino, resolviendo perfiles como <code>soft</code>, <code>gentle</code>,
                <code>crisp</code> y <code>classic</code>.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(148,163,184,0.16);background:rgba(2,6,23,0.38);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#93c5fd;">Request
                marker</span>
            <strong style="font-size:14px;color:#f8fafc;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#94a3b8;">Si cambia al volver, la navegacion siguio siendo real aunque
                el efecto fuera SPA.</span>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Pruebas disponibles</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                La primera ruta deja toda la politica en el enlace. La segunda deja que el documento destino declare su
                propio perfil reusable con meta tags.
            </p>
        </div>

        <div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
            <a href="/navigationTransitionProfile" volt:navigate data-volt-page-transition-profile="soft"
                style="display:grid;gap:10px;border:1px solid rgba(56,189,248,0.24);background:rgba(14,116,144,0.16);border-radius:18px;padding:20px;text-decoration:none;">
                <strong style="font-size:18px;color:#e0f2fe;">Perfil por enlace</strong>
                <span style="color:#bae6fd;line-height:1.65;">
                    Usa <code>data-volt-page-transition-profile="soft"</code> y deja que el runtime resuelva
                    <code>fade</code>, <code>220ms</code> y <code>out-in</code>.
                </span>
            </a>

            <a href="/navigationTransitionAlt" volt:navigate
                style="display:grid;gap:10px;border:1px solid rgba(168,85,247,0.24);background:rgba(88,28,135,0.16);border-radius:18px;padding:20px;text-decoration:none;">
                <strong style="font-size:18px;color:#f5d0fe;">Perfil por documento destino</strong>
                <span style="color:#e9d5ff;line-height:1.65;">
                    El destino responde con
                    <code>&lt;meta name="volt-page-transition-profile" content="gentle"&gt;</code>
                    y el runtime lo usa para coordinar <code>leave</code> y <code>enter</code>.
                </span>
            </a>
        </div>
    </section>

    <section
        style="display:grid;gap:16px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <h2 style="margin:0;font-size:24px;">Resultado esperado</h2>
        <ul style="margin:0;padding-inline-start:18px;display:grid;gap:10px;color:#94a3b8;line-height:1.7;">
            <li><code>volt:before-leave</code> y <code>volt:after-leave</code> deben dispararse antes del cambio de
                pagina.</li>
            <li><code>volt:before-enter</code> y <code>volt:after-enter</code> deben dispararse en el destino.</li>
            <li>El inspector debe mostrar <code>pageTransitionProfile</code> y <code>pageTransitionSource</code> como
                <code>link</code> o <code>document</code>.
            </li>
            <li>La navegacion sigue siendo SPA: no hay recarga completa ni perdida del inspector global.</li>
        </ul>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <a href="/navigationPolicy" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Volver a politicas de navegacion
            </a>
            <a href="/" volt:navigate
                style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
                Volver al inicio
            </a>
        </div>
    </section>
</div>
@endsection
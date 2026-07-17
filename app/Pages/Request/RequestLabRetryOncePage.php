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
        style="display:grid;gap:18px;border:1px solid rgba(56,189,248,0.24);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Resumen visible del retry</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;max-inline-size:72ch;">
                Esta tarjeta relee el ultimo <code>volt:request-retry</code> persistido por el lab para que el retry
                automatico quede visible incluso despues de navegar a la pantalla destino.
            </p>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
            <article
                style="display:grid;gap:8px;border:1px solid rgba(74,222,128,0.2);background:rgba(20,83,45,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#bbf7d0;">Evento capturado</strong>
                <span data-runtime-check="retry-summary-event" style="font-size:15px;color:#f0fdf4;">sin resumen</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(125,211,252,0.2);background:rgba(8,47,73,0.2);border-radius:16px;padding:16px;">
                <strong style="color:#7dd3fc;">Retry attempt</strong>
                <span data-runtime-check="retry-summary-attempt" style="font-size:15px;color:#e0f2fe;">0 / 0</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(250,204,21,0.2);background:rgba(113,63,18,0.2);border-radius:16px;padding:16px;">
                <strong style="color:#fde68a;">Error previo absorbido</strong>
                <span data-runtime-check="retry-summary-error-kind" style="font-size:15px;color:#fef3c7;">sin dato</span>
            </article>
            <article
                style="display:grid;gap:8px;border:1px solid rgba(217,70,239,0.2);background:rgba(88,28,135,0.16);border-radius:16px;padding:16px;">
                <strong style="color:#f5d0fe;">Status previo</strong>
                <span data-runtime-check="retry-summary-status" style="font-size:15px;color:#faf5ff;">sin dato</span>
            </article>
        </div>

        <div
            style="display:grid;gap:10px;border:1px solid rgba(71,85,105,1);background:#020617;border-radius:16px;padding:16px;color:#cbd5e1;">
            <strong style="color:#e2e8f0;">Resumen tecnico persistido</strong>
            <span data-runtime-check="retry-summary-final-url" style="font-size:13px;color:#93c5fd;line-height:1.7;">sin finalUrl</span>
            <span data-runtime-check="retry-summary-delay" style="font-size:13px;color:#cbd5e1;line-height:1.7;">delay sin dato</span>
            <span data-runtime-check="retry-summary-captured-at" style="font-size:13px;color:#94a3b8;line-height:1.7;">captura sin dato</span>
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
<script>
    (function() {
        var storageKey = 'volt.requestLab.lastNavigationRetry';

        function updateText(selector, value) {
            var element = document.querySelector(selector);
            if (element) {
                element.textContent = value;
            }
        }

        function readSummary() {
            if (typeof sessionStorage === 'undefined') {
                return null;
            }

            try {
                var raw = sessionStorage.getItem(storageKey);
                if (!raw) {
                    return null;
                }

                var parsed = JSON.parse(raw);
                return parsed && typeof parsed === 'object' ? parsed : null;
            } catch (error) {
                return null;
            }
        }

        function renderSummary() {
            var summary = readSummary();

            if (!summary) {
                updateText('[data-runtime-check="retry-summary-event"]', 'sin resumen persistido');
                updateText('[data-runtime-check="retry-summary-attempt"]', '0 / 0');
                updateText('[data-runtime-check="retry-summary-error-kind"]', 'sin dato');
                updateText('[data-runtime-check="retry-summary-status"]', 'sin dato');
                updateText('[data-runtime-check="retry-summary-final-url"]', 'sin finalUrl');
                updateText('[data-runtime-check="retry-summary-delay"]', 'delay sin dato');
                updateText('[data-runtime-check="retry-summary-captured-at"]', 'captura sin dato');
                return;
            }

            updateText(
                '[data-runtime-check="retry-summary-event"]',
                typeof summary.eventName === 'string' && summary.eventName !== '' ? summary.eventName : 'sin resumen'
            );
            updateText(
                '[data-runtime-check="retry-summary-attempt"]',
                String(typeof summary.retryAttempt === 'number' ? summary.retryAttempt : 0) + ' / ' +
                    String(typeof summary.retryAttempts === 'number' ? summary.retryAttempts : 0)
            );
            updateText(
                '[data-runtime-check="retry-summary-error-kind"]',
                typeof summary.errorKind === 'string' && summary.errorKind !== '' ? summary.errorKind : 'sin dato'
            );
            updateText(
                '[data-runtime-check="retry-summary-status"]',
                typeof summary.status === 'number' ? String(summary.status) : 'sin dato'
            );
            updateText(
                '[data-runtime-check="retry-summary-final-url"]',
                'finalUrl = ' + (typeof summary.finalUrl === 'string' && summary.finalUrl !== '' ? summary.finalUrl : 'sin dato')
            );
            updateText(
                '[data-runtime-check="retry-summary-delay"]',
                'retryDelayMs = ' + String(typeof summary.retryDelayMs === 'number' ? summary.retryDelayMs : 0)
            );
            updateText(
                '[data-runtime-check="retry-summary-captured-at"]',
                'capturado en = ' + (typeof summary.capturedAt === 'string' && summary.capturedAt !== '' ? summary.capturedAt : 'sin dato')
            );
        }

        renderSummary();
    })();
</script>
@endsection

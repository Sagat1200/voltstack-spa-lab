<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Effects;

use VoltStack\Runtime\Component\Component;
use VoltStack\Runtime\Protocol\ActionEffectOptions;

final class RuntimeEffectsD2Page extends Component
{
    public string $title = 'Runtime Effects D2';

    public function applyAttributeSet(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->setAttribute('data-d2-flag', 'enabled', target: 'effects-d2-attr-box');
    }

    public function applyAttributeRemove(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->removeAttribute('data-d2-flag', target: 'effects-d2-attr-box');
    }

    public function applyBlur(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->blur(target: 'effects-d2-blur-input');
    }

    public function applyClientStateSet(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->stateSet('client', 'd2.counter', random_int(1, 99));
    }

    public function applyClientStateMerge(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->stateMerge('client', 'd2.payload', [
                'touchedAt' => date('H:i:s'),
                'route' => '/runtimeEffectsD2',
            ]);
    }

    public function applyClientStateDelete(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->stateDelete('client', 'd2.counter');
    }

    public function applyClientStateClear(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->stateClear('client', 'd2-clear');
    }

    public function applySharedStateSet(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->stateSet('shared', 'd2.counter', random_int(100, 199));
    }

    public function applySharedStateMerge(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->stateMerge('shared', 'd2.payload', [
                'touchedAt' => date('H:i:s'),
                'route' => '/runtimeEffectsD2',
            ]);
    }

    public function applySharedStateDelete(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->stateDelete('shared', 'd2.counter');
    }

    public function applySharedStateClear(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->stateClear('shared', 'd2-clear');
    }

    public function applySuccessPolicyFast(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->runtimePolicy('success', [
                'timeout' => '150ms',
            ]);
    }

    public function clearSuccessPolicy(): ActionEffectOptions
    {
        return ActionEffectOptions::make()
            ->effect('runtime.policy', [
                'state' => 'success',
                'timeout' => null,
                'minDuration' => null,
                'delay' => null,
                'debounce' => null,
            ]);
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="runtime-effects-d2-mode">
@endsection

@section('content')
<div style="display:grid;gap:18px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:14px;border:1px solid rgba(125,211,252,0.26);background:linear-gradient(135deg,rgba(8,47,73,0.9),rgba(15,23,42,0.96));border-radius:24px;padding:28px;color:#e0f2fe;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(125,211,252,0.3);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#7dd3fc;">Runtime
            Effects D2</span>
        <div style="display:grid;gap:8px;">
            <h1 style="margin:0;font-size:34px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#bae6fd;line-height:1.75;max-inline-size:78ch;">
                Pantalla de QA manual para validar effects extra del runtime (D2): <code>attribute.*</code>,
                <code>state.*</code>, <code>blur</code> y <code>runtime.policy</code>. Cada botón dispara una action
                reactiva que retorna un effect explícito.
            </p>
        </div>

        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(125,211,252,0.18);background:rgba(8,47,73,0.34);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#7dd3fc;">Última action</span>
            <strong data-runtime-check="effects-d2-last-action" style="font-size:14px;color:#f0f9ff;">(sin dato)</strong>
            <span style="font-size:13px;color:#bae6fd;">a las</span>
            <strong data-runtime-check="effects-d2-last-action-at" style="font-size:14px;color:#f0f9ff;">(sin dato)</strong>
        </div>
    </section>

    <section data-runtime-effects-d2="true"
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">attribute.set / attribute.remove</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Valida que el runtime aplique cambios de atributo y actualice la vista sin depender de <code>payload.html</code>.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <button type="button" volt-click="applyAttributeSet"
                style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.28);color:#dcfce7;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Aplicar attribute.set
            </button>
            <button type="button" volt-click="applyAttributeRemove"
                style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.24);color:#fee2e2;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Aplicar attribute.remove
            </button>
        </div>

        <div data-volt-target="effects-d2-attr-box"
            style="border:1px solid rgba(148,163,184,0.28);background:rgba(2,6,23,0.55);border-radius:16px;padding:16px;display:grid;gap:10px;">
            <strong style="color:#e2e8f0;">Target: data-volt-target="effects-d2-attr-box"</strong>
            <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;color:#cbd5e1;">
                <span style="letter-spacing:0.08em;text-transform:uppercase;font-size:12px;color:#94a3b8;">data-d2-flag</span>
                <span data-runtime-check="effects-d2-attr-present" style="font-weight:600;color:#e2e8f0;">(sin dato)</span>
                <span style="color:#94a3b8;">valor</span>
                <span data-runtime-check="effects-d2-attr-value" style="font-weight:600;color:#e2e8f0;">(sin dato)</span>
            </div>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">blur (focus management)</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Paso sugerido: enfoca el input y luego dispara el effect <code>blur</code>.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <input type="text" data-volt-target="effects-d2-blur-input" placeholder="Haz focus aquí y luego pulsa Blur"
                style="border:1px solid rgba(148,163,184,0.3);background:rgba(2,6,23,0.55);color:#e2e8f0;border-radius:12px;padding:10px 12px;min-inline-size:280px;">
            <button type="button" volt-click="applyBlur"
                style="border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.28);color:#bae6fd;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Aplicar blur
            </button>
            <span style="font-size:13px;color:#94a3b8;">activeElement:</span>
            <strong data-runtime-check="effects-d2-active-element" style="font-size:13px;color:#e2e8f0;">(sin dato)</strong>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">state.* (client/shared)</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Valida que la action pueda mutar <code>window.Volt.state</code> via effects (sin escribir manualmente en JS).
                Los snapshots se actualizan al recibir <code>volt:after-effect</code> y <code>volt:state-changed</code>.
            </p>
        </div>

        <div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));align-items:start;">
            <article style="display:grid;gap:12px;border:1px solid rgba(56,189,248,0.18);background:rgba(2,6,23,0.55);border-radius:16px;padding:16px;">
                <strong style="color:#e0f2fe;">Client scope</strong>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button" volt-click="applyClientStateSet"
                        style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.28);color:#dcfce7;border-radius:12px;padding:8px 12px;cursor:pointer;">
                        state.set
                    </button>
                    <button type="button" volt-click="applyClientStateMerge"
                        style="border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.28);color:#bae6fd;border-radius:12px;padding:8px 12px;cursor:pointer;">
                        state.merge
                    </button>
                    <button type="button" volt-click="applyClientStateDelete"
                        style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.24);color:#fee2e2;border-radius:12px;padding:8px 12px;cursor:pointer;">
                        state.delete
                    </button>
                    <button type="button" volt-click="applyClientStateClear"
                        style="border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.65);color:#e2e8f0;border-radius:12px;padding:8px 12px;cursor:pointer;">
                        state.clear
                    </button>
                </div>
                <pre data-runtime-check="effects-d2-client-snapshot"
                    style="margin:0;border:1px solid rgba(30,41,59,1);background:#020617;border-radius:12px;padding:12px;min-block-size:140px;overflow:auto;color:#cbd5e1;font-size:12px;line-height:1.5;">(sin dato)</pre>
            </article>

            <article style="display:grid;gap:12px;border:1px solid rgba(244,114,182,0.18);background:rgba(2,6,23,0.55);border-radius:16px;padding:16px;">
                <strong style="color:#fce7f3;">Shared scope</strong>
                <div style="display:flex;flex-wrap:wrap;gap:10px;">
                    <button type="button" volt-click="applySharedStateSet"
                        style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.28);color:#dcfce7;border-radius:12px;padding:8px 12px;cursor:pointer;">
                        state.set
                    </button>
                    <button type="button" volt-click="applySharedStateMerge"
                        style="border:1px solid rgba(56,189,248,0.28);background:rgba(8,47,73,0.28);color:#bae6fd;border-radius:12px;padding:8px 12px;cursor:pointer;">
                        state.merge
                    </button>
                    <button type="button" volt-click="applySharedStateDelete"
                        style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.24);color:#fee2e2;border-radius:12px;padding:8px 12px;cursor:pointer;">
                        state.delete
                    </button>
                    <button type="button" volt-click="applySharedStateClear"
                        style="border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.65);color:#e2e8f0;border-radius:12px;padding:8px 12px;cursor:pointer;">
                        state.clear
                    </button>
                </div>
                <pre data-runtime-check="effects-d2-shared-snapshot"
                    style="margin:0;border:1px solid rgba(30,41,59,1);background:#020617;border-radius:12px;padding:12px;min-block-size:140px;overflow:auto;color:#cbd5e1;font-size:12px;line-height:1.5;">(sin dato)</pre>
            </article>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">runtime.policy (success timeout)</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Este panel aplica una policy más agresiva para <code>success.timeout</code> y luego cualquier action
                exitosa debería limpiar el estado de success más rápido.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <button type="button" volt-click="applySuccessPolicyFast"
                style="border:1px solid rgba(34,197,94,0.28);background:rgba(20,83,45,0.28);color:#dcfce7;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Policy: timeout 150ms
            </button>
            <button type="button" volt-click="clearSuccessPolicy"
                style="border:1px solid rgba(148,163,184,0.28);background:rgba(15,23,42,0.65);color:#e2e8f0;border-radius:12px;padding:10px 16px;cursor:pointer;">
                Limpiar policy de success
            </button>
            <div volt:success volt:success.timeout="900ms"
                style="display:inline-flex;align-items:center;gap:10px;border:1px solid rgba(34,197,94,0.25);background:rgba(20,83,45,0.24);color:#dcfce7;border-radius:999px;padding:8px 14px;">
                <strong>success</strong>
                <span style="font-size:13px;color:#bbf7d0;">se limpia por timeout</span>
            </div>
        </div>
    </section>

    <section
        style="display:grid;gap:14px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Log reciente (hooks)</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Útil para confirmar que el runtime emite <code>volt:before-effect</code> y <code>volt:after-effect</code> con el detalle del effect.
            </p>
        </div>
        <ul data-volt-hook-log data-volt-hook-log-filter="all" style="display:grid;gap:10px;margin:0;padding:0;list-style:none;"></ul>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimeEvents" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(59,130,246,0.30);background:rgba(30,64,175,0.16);color:#dbeafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver a runtimeEvents
        </a>
        <a href="{{ route('spaReactive') }}" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Inicio Sistema SPA Full Reactive
        </a>
    </section>
</div>
@endsection
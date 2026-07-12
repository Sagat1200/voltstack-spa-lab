<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Portal;

use VoltStack\Runtime\Component\Component;

final class PortalAltPage extends Component
{
    public string $title = 'Portal Alt';
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="portal-alt-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1080px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(34,211,238,0.24);background:linear-gradient(135deg,rgba(8,47,73,0.90),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#ecfeff;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(34,211,238,0.30);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#67e8f9;">Runtime
            Portal Alt</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:34px;line-height:1.1;">{{ $title }}</h1>
            <p style="margin:0;color:#a5f3fc;line-height:1.75;max-inline-size:76ch;">
                Esta ruta valida navegación SPA con <code>volt:portal</code>. Si una bandera compartida sigue activa, el
                contenido portalizado debe volver a montarse aquí usando los mismos roots globales del layout.
            </p>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Reactivar portales desde la ruta alterna</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Aquí puedes abrir otra vez banner, modal y drawer para comprobar que el runtime no depende de la
                posición visual original del contenido.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button"
                volt:on="click -> state:set shared:portal.bannerOpen = true | click -> state:set shared:portal.bannerText = 'Banner abierto desde /runtimePortalAlt' | click -> state:set shared:portal.lastAction = 'alt-open-banner'"
                style="border:1px solid rgba(34,211,238,0.28);background:rgba(8,47,73,0.18);color:#cffafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Abrir banner
            </button>
            <button type="button"
                volt:on="click -> state:set shared:portal.modalOpen = true | click -> state:set shared:portal.lastAction = 'alt-open-modal'"
                style="border:1px solid rgba(168,85,247,0.28);background:rgba(88,28,135,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Abrir modal
            </button>
            <button type="button"
                volt:on="click -> state:set shared:portal.drawerOpen = true | click -> state:set shared:portal.lastAction = 'alt-open-drawer'"
                style="border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.18);color:#fde68a;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Abrir drawer
            </button>
            <button type="button" volt:on="click -> state:delete shared:portal"
                style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Reset portal state
            </button>
        </div>
    </section>

    <section style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
        <article
            style="display:grid;gap:12px;border:1px solid rgba(34,211,238,0.20);background:rgba(8,47,73,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#67e8f9;">Banner declarado en la ruta alterna</strong>
            <div volt:portal="#volt-banners-root" volt:show="shared:portal.bannerOpen === true"
                style="pointer-events:auto;inline-size:min(720px,100%);border:1px solid rgba(34,211,238,0.35);background:rgba(8,47,73,0.96);color:#cffafe;border-radius:16px;padding:14px 18px;box-shadow:0 20px 50px rgba(8,47,73,0.45);">
                <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
                    <div style="display:grid;gap:6px;">
                        <strong style="color:#a5f3fc;">Banner desde alt</strong>
                        <span volt:text="shared:portal.bannerText ?? 'Sin texto'" style="color:#cffafe;">Sin
                            texto</span>
                    </div>
                    <button type="button"
                        volt:on="click -> state:set shared:portal.bannerOpen = false | click -> state:set shared:portal.lastAction = 'alt-close-banner'"
                        style="border:1px solid rgba(34,211,238,0.28);background:rgba(15,23,42,0.72);color:#ecfeff;border-radius:10px;padding:8px 12px;cursor:pointer;">
                        Cerrar banner
                    </button>
                </div>
            </div>
            <p style="margin:0;color:#a5f3fc;line-height:1.7;">
                Si ves el banner arriba del layout en lugar de aquí, el portal ya se reubicó bien tras la navegación.
            </p>
        </article>

        <article
            style="display:grid;gap:12px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#e9d5ff;">Modal declarado en la ruta alterna</strong>
            <section volt:portal="#volt-modals-root" volt:show="shared:portal.modalOpen === true"
                style="pointer-events:auto;position:fixed;inset:0;display:grid;place-items:center;padding:24px;background:rgba(2,6,23,0.74);">
                <div
                    style="inline-size:min(560px,100%);display:grid;gap:16px;border:1px solid rgba(192,132,252,0.32);background:#1e1b4b;color:#f5d0fe;border-radius:22px;padding:24px;box-shadow:0 24px 80px rgba(15,23,42,0.65);">
                    <div style="display:grid;gap:8px;">
                        <span style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#c4b5fd;">Modal
                            desde alt</span>
                        <h3 style="margin:0;font-size:28px;color:#faf5ff;">Portal remount</h3>
                        <p style="margin:0;line-height:1.7;color:#e9d5ff;">
                            Este modal confirma que el contenido portalizado puede remountarse en otra vista SPA.
                        </p>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:12px;">
                        <button type="button"
                            volt:on="click -> state:set shared:portal.modalOpen = false | click -> state:set shared:portal.lastAction = 'alt-close-modal'"
                            style="border:1px solid rgba(244,114,182,0.28);background:rgba(131,24,67,0.20);color:#fce7f3;border-radius:10px;padding:10px 14px;cursor:pointer;">
                            Cerrar modal
                        </button>
                    </div>
                </div>
            </section>
            <p style="margin:0;color:#f5d0fe;line-height:1.7;">
                El modal visible debería seguir centrado en el viewport, no incrustado en esta tarjeta.
            </p>
        </article>

        <article
            style="display:grid;gap:12px;border:1px solid rgba(245,158,11,0.20);background:rgba(120,53,15,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#fcd34d;">Drawer declarado en la ruta alterna</strong>
            <aside volt:portal="#volt-drawers-root" volt:show="shared:portal.drawerOpen === true"
                style="pointer-events:auto;position:fixed;inset-block:0;inset-inline-end:0;block-size:100vh;inline-size:min(360px,92vw);display:grid;gap:16px;border-inline-start:1px solid rgba(245,158,11,0.28);background:#1c1917;color:#fde68a;padding:24px;box-shadow:-20px 0 60px rgba(15,23,42,0.45);">
                <div style="display:grid;gap:8px;">
                    <span style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#fcd34d;">Drawer
                        desde alt</span>
                    <h3 style="margin:0;font-size:24px;color:#fef3c7;">Portal lateral</h3>
                </div>
                <button type="button"
                    volt:on="click -> state:set shared:portal.drawerOpen = false | click -> state:set shared:portal.lastAction = 'alt-close-drawer'"
                    style="inline-size:max-content;border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.24);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                    Cerrar drawer
                </button>
            </aside>
            <p style="margin:0;color:#fde68a;line-height:1.7;">
                El drawer visible debería quedarse en el borde derecho de la pantalla.
            </p>
        </article>
    </section>

    <section
        style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <article
            style="display:grid;gap:8px;border:1px solid rgba(34,211,238,0.20);background:rgba(8,47,73,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#67e8f9;">Banner open</strong>
            <span volt:text="shared:portal.bannerOpen ?? false" style="color:#cffafe;">false</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#e9d5ff;">Modal open</strong>
            <span volt:text="shared:portal.modalOpen ?? false" style="color:#f5d0fe;">false</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(245,158,11,0.20);background:rgba(120,53,15,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#fcd34d;">Drawer open</strong>
            <span volt:text="shared:portal.drawerOpen ?? false" style="color:#fde68a;">false</span>
        </article>
        <article
            style="display:grid;gap:8px;border:1px solid rgba(16,185,129,0.20);background:rgba(6,78,59,0.14);border-radius:16px;padding:16px;">
            <strong style="color:#86efac;">Last action</strong>
            <span volt:text="shared:portal.lastAction ?? '(sin accion)'" style="color:#d1fae5;">(sin accion)</span>
        </article>
    </section>

    <section
        style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <a href="/runtimePortal" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid rgba(34,211,238,0.28);background:rgba(8,47,73,0.18);color:#cffafe;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver a runtimePortal
        </a>
        <a href="/" volt:navigate
            style="display:inline-flex;align-items:center;border:1px solid #334155;background:#020617;color:#e2e8f0;border-radius:10px;padding:10px 16px;text-decoration:none;">
            Volver al inicio
        </a>
    </section>
</div>
@endsection
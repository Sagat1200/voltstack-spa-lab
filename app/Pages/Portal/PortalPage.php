<?php

declare(strict_types=1);

namespace VoltStack\SPALab\Pages\Portal;

use VoltStack\Runtime\Component\Component;

final class PortalPage extends Component
{
    public string $title = 'Portal Demo';

    public string $requestMarker;

    public function mount(): void
    {
        $this->requestMarker = sprintf('portal-%s', substr((string) microtime(true), -6));
    }
}

?>

@extends('layouts.spa')

@section('head')
<meta name="volt-navigation-mode" content="auto" data-volt-head-key="portal-mode">
@endsection

@section('content')
<div style="display:grid;gap:20px;max-inline-size:1120px;margin:0 auto;">
    <section
        style="display:grid;gap:16px;border:1px solid rgba(16,185,129,0.24);background:linear-gradient(135deg,rgba(6,78,59,0.90),rgba(15,23,42,0.94));border-radius:24px;padding:32px;color:#ecfdf5;">
        <span
            style="display:inline-flex;inline-size:max-content;padding:6px 10px;border-radius:999px;border:1px solid rgba(110,231,183,0.32);font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#86efac;">Runtime
            Portal MVP</span>
        <div style="display:grid;gap:10px;">
            <h1 style="margin:0;font-size:36px;line-height:1.05;">{{ $title }}</h1>
            <p style="margin:0;color:#d1fae5;line-height:1.75;max-inline-size:76ch;">
                Esta pantalla valida <code>volt:portal</code> proyectando contenido real hacia roots globales del
                layout: banner superior, modal centrado y drawer lateral. Los nodos se mueven, no se clonan.
            </p>
        </div>
        <div
            style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;border:1px solid rgba(110,231,183,0.18);background:rgba(6,78,59,0.22);border-radius:16px;padding:14px 16px;">
            <span style="font-size:13px;letter-spacing:0.08em;text-transform:uppercase;color:#86efac;">Request
                marker</span>
            <strong style="font-size:14px;color:#f0fdf4;">{{ $requestMarker }}</strong>
            <span style="font-size:13px;color:#d1fae5;">Targets activos:
                <code>#volt-banners-root</code>, <code>#volt-modals-root</code>, <code>#volt-drawers-root</code>.</span>
        </div>
    </section>

    <section
        style="display:grid;gap:18px;border:1px solid rgba(51,65,85,1);background:#0f172a;border-radius:20px;padding:24px;color:#e2e8f0;">
        <div style="display:grid;gap:8px;">
            <h2 style="margin:0;font-size:24px;">Acciones del laboratorio</h2>
            <p style="margin:0;color:#94a3b8;line-height:1.7;">
                Cada botón muta estado compartido y el contenido portalizado aparece fuera de esta sección, en el root
                global correspondiente del layout.
            </p>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:12px;">
            <button type="button"
                volt:on="click -> state:set shared:portal.bannerOpen = true | click -> state:set shared:portal.bannerText = 'Banner abierto desde /runtimePortal' | click -> state:set shared:portal.lastAction = 'open-banner'"
                style="border:1px solid rgba(34,211,238,0.28);background:rgba(8,47,73,0.18);color:#cffafe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Mostrar banner portalizado
            </button>
            <button type="button"
                volt:on="click -> state:set shared:portal.modalOpen = true | click -> state:set shared:portal.lastAction = 'open-modal'"
                style="border:1px solid rgba(168,85,247,0.28);background:rgba(88,28,135,0.18);color:#f5d0fe;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Abrir modal portalizado
            </button>
            <button type="button"
                volt:on="click -> state:set shared:portal.drawerOpen = true | click -> state:set shared:portal.lastAction = 'open-drawer'"
                style="border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.18);color:#fde68a;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Abrir drawer portalizado
            </button>
            <button type="button"
                volt:on="click -> state:delete shared:portal | click -> state:set client:portal.lastReset = 'reset-runtime-portal'"
                style="border:1px solid rgba(248,113,113,0.28);background:rgba(127,29,29,0.18);color:#fee2e2;border-radius:10px;padding:10px 14px;cursor:pointer;">
                Reset portal state
            </button>
        </div>
    </section>

    <section style="display:grid;gap:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
        <article
            style="display:grid;gap:12px;border:1px solid rgba(34,211,238,0.20);background:rgba(8,47,73,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#67e8f9;">Origen declarativo del banner</strong>
            <p style="margin:0;color:#cffafe;line-height:1.7;">
                Este nodo declara <code>volt:portal="#volt-banners-root"</code>. Si funciona, el banner visible no debe
                quedar aquí, sino arriba del documento.
            </p>

            <div volt:portal="#volt-banners-root" volt:show="shared:portal.bannerOpen === true"
                style="pointer-events:auto;inline-size:min(720px,100%);border:1px solid rgba(34,211,238,0.35);background:rgba(8,47,73,0.96);color:#cffafe;border-radius:16px;padding:14px 18px;box-shadow:0 20px 50px rgba(8,47,73,0.45);">
                <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
                    <div style="display:grid;gap:6px;">
                        <strong style="color:#a5f3fc;">Banner portalizado</strong>
                        <span volt:text="shared:portal.bannerText ?? 'Sin texto de banner'" style="color:#cffafe;">Sin
                            texto de banner</span>
                    </div>
                    <button type="button"
                        volt:on="click -> state:set shared:portal.bannerOpen = false | click -> state:set shared:portal.lastAction = 'close-banner'"
                        style="border:1px solid rgba(34,211,238,0.28);background:rgba(15,23,42,0.72);color:#ecfeff;border-radius:10px;padding:8px 12px;cursor:pointer;">
                        Cerrar banner
                    </button>
                </div>
            </div>
        </article>

        <article
            style="display:grid;gap:12px;border:1px solid rgba(168,85,247,0.20);background:rgba(88,28,135,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#e9d5ff;">Origen declarativo del modal</strong>
            <p style="margin:0;color:#f5d0fe;line-height:1.7;">
                El modal completo se mueve a <code>#volt-modals-root</code>. Los botones internos siguen usando
                <code>volt:on</code> aun estando fuera de esta sección visual.
            </p>

            <section volt:portal="#volt-modals-root" volt:show="shared:portal.modalOpen === true"
                style="pointer-events:auto;position:fixed;inset:0;display:grid;place-items:center;padding:24px;background:rgba(2,6,23,0.74);">
                <div
                    style="inline-size:min(560px,100%);display:grid;gap:16px;border:1px solid rgba(192,132,252,0.32);background:#1e1b4b;color:#f5d0fe;border-radius:22px;padding:24px;box-shadow:0 24px 80px rgba(15,23,42,0.65);">
                    <div style="display:grid;gap:8px;">
                        <span style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#c4b5fd;">Modal
                            portalizado</span>
                        <h3 style="margin:0;font-size:28px;color:#faf5ff;">Confirmacion runtime</h3>
                        <p style="margin:0;line-height:1.7;color:#e9d5ff;">
                            Este contenido ya no vive donde fue declarado visualmente. Si ves este modal centrado, el
                            portal movió el nodo real al root global.
                        </p>
                    </div>
                    <div style="display:grid;gap:10px;">
                        <span style="color:#ddd6fe;">Confirmaciones actuales:</span>
                        <strong volt:text="shared:portal.confirmCount ?? 0"
                            style="font-size:28px;color:#faf5ff;">0</strong>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:12px;">
                        <button type="button"
                            volt:on="click -> state:set shared:portal.confirmCount = 0 | click -> state:set shared:portal.confirmCount = 1 | click -> state:set shared:portal.lastAction = 'confirm-modal'"
                            style="border:1px solid rgba(192,132,252,0.32);background:rgba(91,33,182,0.26);color:#faf5ff;border-radius:10px;padding:10px 14px;cursor:pointer;">
                            Confirmar desde modal
                        </button>
                        <button type="button"
                            volt:on="click -> state:set shared:portal.modalOpen = false | click -> state:set shared:portal.lastAction = 'close-modal'"
                            style="border:1px solid rgba(244,114,182,0.28);background:rgba(131,24,67,0.20);color:#fce7f3;border-radius:10px;padding:10px 14px;cursor:pointer;">
                            Cerrar modal
                        </button>
                    </div>
                </div>
            </section>
        </article>

        <article
            style="display:grid;gap:12px;border:1px solid rgba(245,158,11,0.20);background:rgba(120,53,15,0.18);border-radius:18px;padding:18px;">
            <strong style="color:#fcd34d;">Origen declarativo del drawer</strong>
            <p style="margin:0;color:#fde68a;line-height:1.7;">
                El drawer se proyecta hacia el lateral derecho del layout. Al navegar a la ruta alterna, el estado
                compartido puede volver a abrirlo allí.
            </p>

            <aside volt:portal="#volt-drawers-root" volt:show="shared:portal.drawerOpen === true"
                style="pointer-events:auto;position:fixed;inset-block:0;inset-inline-end:0;block-size:100vh;inline-size:min(360px,92vw);display:grid;gap:16px;border-inline-start:1px solid rgba(245,158,11,0.28);background:#1c1917;color:#fde68a;padding:24px;box-shadow:-20px 0 60px rgba(15,23,42,0.45);">
                <div style="display:grid;gap:8px;">
                    <span style="font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#fcd34d;">Drawer
                        portalizado</span>
                    <h3 style="margin:0;font-size:24px;color:#fef3c7;">Panel lateral runtime</h3>
                    <p style="margin:0;line-height:1.7;color:#fde68a;">
                        Si lo ves pegado al borde derecho de la ventana, el portal se resolvió correctamente.
                    </p>
                </div>
                <div style="display:grid;gap:10px;">
                    <span style="color:#fde68a;">Ultima accion compartida:</span>
                    <strong volt:text="shared:portal.lastAction ?? '(sin accion)'" style="color:#fef3c7;">(sin
                        accion)</strong>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:12px;">
                    <button type="button"
                        volt:on="click -> state:set shared:portal.drawerOpen = false | click -> state:set shared:portal.lastAction = 'close-drawer'"
                        style="border:1px solid rgba(245,158,11,0.28);background:rgba(120,53,15,0.24);color:#fef3c7;border-radius:10px;padding:10px 14px;cursor:pointer;">
                        Cerrar drawer
                    </button>
                    <a href="/runtimePortalAlt" volt:navigate
                        style="display:inline-flex;align-items:center;border:1px solid rgba(96,165,250,0.28);background:rgba(30,64,175,0.18);color:#dbeafe;border-radius:10px;padding:10px 14px;text-decoration:none;">
                        Ir a runtimePortalAlt
                    </a>
                </div>
            </aside>
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
</div>
@endsection
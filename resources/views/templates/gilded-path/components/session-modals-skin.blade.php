{{--
    Gilded-Path skin for session-modals.
    Dark gold design using the gp-* CSS classes and CSS variables from the template layout.
--}}

{{-- Backdrop --}}
<div id="sm-backdrop"
    style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,.75);opacity:0;transition:opacity .3s ease;">
</div>

<div id="sm-modal" role="dialog" aria-modal="true"
    style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:1rem;">

    <div id="sm-box"
        style="position:relative;width:100%;max-width:34rem;overflow:hidden;opacity:0;transform:scale(.95);transition:opacity .3s ease,transform .3s ease;background-color:var(--gp-surface-container);border:1px solid rgba(212,175,55,.25);box-shadow:0 0 0 1px rgba(242,202,80,.08),0 25px 60px rgba(0,0,0,.6),inset 0 1px 0 rgba(242,202,80,.15);">

        <div
            style="height:3px;background:linear-gradient(90deg,transparent,#d4af37 30%,#f2ca50 50%,#d4af37 70%,transparent);">
        </div>

        <div
            style="position:absolute;top:8px;left:8px;width:12px;height:12px;border-top:2px solid #f2ca50;border-left:2px solid #f2ca50;opacity:.7;pointer-events:none;">
        </div>
        <div
            style="position:absolute;top:8px;right:8px;width:12px;height:12px;border-top:2px solid #f2ca50;border-right:2px solid #f2ca50;opacity:.7;pointer-events:none;">
        </div>
        <div
            style="position:absolute;bottom:8px;left:8px;width:12px;height:12px;border-bottom:2px solid #f2ca50;border-left:2px solid #f2ca50;opacity:.7;pointer-events:none;">
        </div>
        <div
            style="position:absolute;bottom:8px;right:8px;width:12px;height:12px;border-bottom:2px solid #f2ca50;border-right:2px solid #f2ca50;opacity:.7;pointer-events:none;">
        </div>

        <button id="sm-close" type="button" aria-label="Close"
            style="position:absolute;top:.9rem;right:.9rem;background:none;border:none;cursor:pointer;color:var(--gp-outline);padding:.25rem;line-height:1;z-index:1;transition:color .2s;"
            onmouseover="this.style.color='var(--gp-primary)'" onmouseout="this.style.color='var(--gp-outline)'">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        <img id="sm-image" src="" alt=""
            style="display:none;width:100%;max-height:14rem;object-fit:cover;filter:brightness(.9) sepia(.1);">

        <div style="padding:1.75rem 1.75rem 1.5rem;">

            <h2 id="sm-title"
                style="display:none;font-size:1.2rem;font-weight:700;margin:0 0 .85rem;letter-spacing:.06em;text-transform:uppercase;color:var(--gp-primary);text-shadow:0 0 20px rgba(242,202,80,.2);">
            </h2>

            <div id="sm-content" style="font-size:.875rem;line-height:1.65;color:var(--gp-on-surface-variant);">
            </div>

            <div id="sm-buttons" style="display:none;margin-top:1.25rem;flex-wrap:wrap;gap:.75rem;">
            </div>

        </div>

        <div
            style="height:1px;background:linear-gradient(90deg,transparent,rgba(212,175,55,.3) 30%,rgba(242,202,80,.5) 50%,rgba(212,175,55,.3) 70%,transparent);">
        </div>

    </div>
</div>

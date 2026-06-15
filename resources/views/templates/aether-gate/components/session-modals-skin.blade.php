{{--
    Aether-Gate skin for session-modals.
    Deep navy + cyan design using ag-* CSS variables from the template layout.
--}}

{{-- Backdrop --}}
<div id="sm-backdrop"
    style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,.8);opacity:0;transition:opacity .3s ease;">
</div>

<div id="sm-modal" role="dialog" aria-modal="true"
    style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:1rem;">

    <div id="sm-box"
        style="position:relative;width:100%;max-width:34rem;overflow:hidden;opacity:0;transform:scale(.95);transition:opacity .3s ease,transform .3s ease;background-color:var(--ag-surface-container,#0d1224);border:1px solid rgba(34,211,238,.2);box-shadow:0 0 0 1px rgba(34,211,238,.06),0 0 40px rgba(34,211,238,.08),0 25px 60px rgba(0,0,0,.7);">

        {{-- Top cyan accent line --}}
        <div
            style="height:2px;background:linear-gradient(90deg,transparent,#22d3ee 30%,#67e8f9 50%,#22d3ee 70%,transparent);">
        </div>

        {{-- Corner bracket decorations --}}
        <div style="position:absolute;top:8px;left:8px;width:12px;height:12px;border-top:1.5px solid #22d3ee;border-left:1.5px solid #22d3ee;opacity:.6;pointer-events:none;"></div>
        <div style="position:absolute;top:8px;right:8px;width:12px;height:12px;border-top:1.5px solid #22d3ee;border-right:1.5px solid #22d3ee;opacity:.6;pointer-events:none;"></div>
        <div style="position:absolute;bottom:8px;left:8px;width:12px;height:12px;border-bottom:1.5px solid #22d3ee;border-left:1.5px solid #22d3ee;opacity:.6;pointer-events:none;"></div>
        <div style="position:absolute;bottom:8px;right:8px;width:12px;height:12px;border-bottom:1.5px solid #22d3ee;border-right:1.5px solid #22d3ee;opacity:.6;pointer-events:none;"></div>

        <button id="sm-close" type="button" aria-label="Close"
            style="position:absolute;top:.9rem;right:.9rem;background:none;border:none;cursor:pointer;color:rgba(34,211,238,.4);padding:.25rem;line-height:1;z-index:1;transition:color .2s;"
            onmouseover="this.style.color='#22d3ee'" onmouseout="this.style.color='rgba(34,211,238,.4)'">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        <img id="sm-image" src="" alt=""
            style="display:none;width:100%;max-height:14rem;object-fit:cover;filter:brightness(.85) saturate(.9);">

        <div style="padding:1.75rem 1.75rem 1.5rem;">

            <h2 id="sm-title"
                style="display:none;font-family:'Chakra Petch',sans-serif;font-size:1.1rem;font-weight:700;margin:0 0 .85rem;letter-spacing:.08em;text-transform:uppercase;color:#22d3ee;">
            </h2>

            <div id="sm-content" style="font-size:.875rem;line-height:1.65;color:rgba(200,220,255,0.7);">
            </div>

            <div id="sm-buttons" style="display:none;margin-top:1.25rem;flex-wrap:wrap;gap:.75rem;">
            </div>

        </div>

        {{-- Bottom subtle line --}}
        <div
            style="height:1px;background:linear-gradient(90deg,transparent,rgba(34,211,238,.15) 30%,rgba(34,211,238,.3) 50%,rgba(34,211,238,.15) 70%,transparent);">
        </div>

    </div>
</div>

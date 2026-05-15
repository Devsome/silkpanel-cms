{{--
    Neon Strike skin for session-modals.
    Cyberpunk aesthetic: black bg, violet/fuchsia/cyan glow, sharp borders, scanlines.
--}}

{{-- Backdrop --}}
<div id="sm-backdrop"
    style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,.85);opacity:0;transition:opacity .3s ease;">
</div>

{{-- Modal --}}
<div id="sm-modal" role="dialog" aria-modal="true"
    style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:1rem;">

    <div id="sm-box"
        style="position:relative;width:100%;max-width:34rem;overflow:hidden;opacity:0;transform:scale(.95);transition:opacity .3s ease,transform .3s ease;background:#09090b;border:1px solid rgba(139,92,246,.4);box-shadow:0 0 0 1px rgba(139,92,246,.08),0 0 40px rgba(139,92,246,.15),0 25px 60px rgba(0,0,0,.8);">

        {{-- Scanlines overlay --}}
        <div
            style="position:absolute;inset:0;background:repeating-linear-gradient(0deg,transparent,transparent 2px,rgba(139,92,246,.015) 2px,rgba(139,92,246,.015) 4px);pointer-events:none;z-index:0;">
        </div>

        {{-- Neon top bar --}}
        <div
            style="height:2px;background:linear-gradient(90deg,transparent,#8b5cf6 30%,#d946ef 60%,#22d3ee 100%);position:relative;z-index:1;">
        </div>

        {{-- Close button --}}
        <button id="sm-close" type="button" aria-label="Close"
            style="position:absolute;top:.85rem;right:.85rem;background:none;border:1px solid rgba(139,92,246,.3);cursor:pointer;color:#8b5cf6;padding:.3rem;line-height:1;z-index:2;transition:all .2s;"
            onmouseover="this.style.background='rgba(139,92,246,.15)';this.style.borderColor='rgba(139,92,246,.7)';this.style.boxShadow='0 0 10px rgba(139,92,246,.3)'"
            onmouseout="this.style.background='none';this.style.borderColor='rgba(139,92,246,.3)';this.style.boxShadow='none'">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        {{-- Image --}}
        <img id="sm-image" src="" alt=""
            style="display:none;width:100%;max-height:13rem;object-fit:cover;position:relative;z-index:1;filter:saturate(1.1) brightness(.85);">

        {{-- Body --}}
        <div style="padding:1.75rem 1.75rem 1.5rem;position:relative;z-index:1;">

            <h2 id="sm-title"
                style="display:none;font-size:1.1rem;font-weight:900;margin:0 0 .85rem;letter-spacing:.12em;text-transform:uppercase;background:linear-gradient(90deg,#a78bfa,#e879f9,#22d3ee);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
            </h2>

            <div id="sm-content" style="font-size:.875rem;line-height:1.65;color:#a1a1aa;">
            </div>

            <div id="sm-buttons" style="display:none;margin-top:1.25rem;flex-wrap:wrap;gap:.75rem;">
            </div>

        </div>

        {{-- Bottom corner accents --}}
        <div
            style="position:absolute;bottom:0;left:0;width:8px;height:8px;border-bottom:2px solid #8b5cf6;border-left:2px solid #8b5cf6;pointer-events:none;z-index:2;">
        </div>
        <div
            style="position:absolute;bottom:0;right:0;width:8px;height:8px;border-bottom:2px solid #22d3ee;border-right:2px solid #22d3ee;pointer-events:none;z-index:2;">
        </div>

    </div>
</div>

<style>
    #sm-buttons a,
    #sm-buttons button {
        font-family: inherit;
        letter-spacing: .1em;
        text-transform: uppercase;
        font-size: .75rem !important;
        font-weight: 700 !important;
        border-radius: 0 !important;
    }

    #sm-buttons [data-sm-style="primary"] {
        background: transparent !important;
        color: #a78bfa !important;
        border: 1px solid rgba(139, 92, 246, .5) !important;
        box-shadow: 0 0 12px rgba(139, 92, 246, .15) !important;
    }

    #sm-buttons [data-sm-style="primary"]:hover {
        background: rgba(139, 92, 246, .15) !important;
        box-shadow: 0 0 20px rgba(139, 92, 246, .3) !important;
    }

    #sm-buttons [data-sm-style="secondary"] {
        background: transparent !important;
        color: #71717a !important;
        border: 1px solid rgba(113, 113, 122, .3) !important;
    }

    #sm-buttons [data-sm-style="danger"] {
        background: transparent !important;
        color: #f87171 !important;
        border: 1px solid rgba(248, 113, 113, .4) !important;
    }
</style>

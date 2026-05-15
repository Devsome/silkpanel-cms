{{--
    Silkroad Gaming skin for session-modals.
    Dark gaming aesthetic: gray-950 bg, emerald/cyan accent, rounded cards, backdrop blur.
--}}

{{-- Backdrop --}}
<div id="sm-backdrop"
    style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(3,7,18,.8);backdrop-filter:blur(4px);opacity:0;transition:opacity .25s ease;">
</div>

{{-- Modal --}}
<div id="sm-modal" role="dialog" aria-modal="true"
    style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:1rem;">

    <div id="sm-box"
        style="position:relative;width:100%;max-width:34rem;border-radius:1.25rem;overflow:hidden;opacity:0;transform:scale(.95);transition:opacity .25s ease,transform .25s ease;background:#030712;border:1px solid rgba(17,24,39,1);box-shadow:0 0 0 1px rgba(16,185,129,.08),0 25px 60px rgba(0,0,0,.7),inset 0 1px 0 rgba(255,255,255,.04);">

        {{-- Emerald/cyan gradient top bar --}}
        <div style="height:2px;background:linear-gradient(90deg,transparent,#10b981 30%,#06b6d4 70%,transparent);"></div>

        {{-- Close button --}}
        <button id="sm-close" type="button" aria-label="Close"
            style="position:absolute;top:.85rem;right:.85rem;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:.5rem;cursor:pointer;color:#6b7280;padding:.35rem;line-height:1;z-index:1;transition:all .2s;"
            onmouseover="this.style.background='rgba(16,185,129,.1)';this.style.borderColor='rgba(16,185,129,.3)';this.style.color='#34d399'"
            onmouseout="this.style.background='rgba(255,255,255,.04)';this.style.borderColor='rgba(255,255,255,.08)';this.style.color='#6b7280'">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        {{-- Image --}}
        <img id="sm-image" src="" alt=""
            style="display:none;width:100%;max-height:14rem;object-fit:cover;">

        {{-- Body --}}
        <div style="padding:1.75rem 1.75rem 1.5rem;">

            <h2 id="sm-title"
                style="display:none;font-size:1.15rem;font-weight:700;margin:0 0 .85rem;letter-spacing:-.01em;background:linear-gradient(90deg,#34d399,#22d3ee);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">
            </h2>

            <div id="sm-content" style="font-size:.875rem;line-height:1.65;color:#9ca3af;">
            </div>

            <div id="sm-buttons" style="display:none;margin-top:1.25rem;flex-wrap:wrap;gap:.75rem;">
            </div>

        </div>

        {{-- Subtle bottom gradient --}}
        <div style="height:1px;background:linear-gradient(90deg,transparent,rgba(16,185,129,.15) 50%,transparent);">
        </div>

    </div>
</div>

<style>
    #sm-buttons a,
    #sm-buttons button {
        font-family: inherit;
        font-size: .875rem !important;
        font-weight: 600 !important;
        border-radius: .75rem !important;
    }

    #sm-buttons [data-sm-style="primary"] {
        background: linear-gradient(135deg, #10b981, #06b6d4) !important;
        color: #030712 !important;
        border: none !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, .25) !important;
    }

    #sm-buttons [data-sm-style="secondary"] {
        background: rgba(255, 255, 255, .05) !important;
        color: #34d399 !important;
        border: 1px solid rgba(16, 185, 129, .25) !important;
    }

    #sm-buttons [data-sm-style="danger"] {
        background: rgba(239, 68, 68, .1) !important;
        color: #f87171 !important;
        border: 1px solid rgba(239, 68, 68, .3) !important;
    }
</style>

{{--
    Session Modal Skin — only the HTML structure.
    Safe to override per template: just create
      resources/views/templates/{your-theme}/components/session-modals-skin.blade.php

    Required element IDs (the JS in session-modals.blade.php depends on them):
      #sm-backdrop  — full-screen overlay
      #sm-modal     — flex centering wrapper
      #sm-box       — the visible card (click here should NOT close)
      #sm-close     — close button
      #sm-image     — <img> for the optional image
      #sm-title     — heading element
      #sm-content   — inner HTML content area
      #sm-buttons   — button row container
--}}
{{-- Backdrop --}}
<div id="sm-backdrop"
    style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,.6);opacity:0;transition:opacity .25s ease;">
</div>

{{-- Modal --}}
<div id="sm-modal" role="dialog" aria-modal="true"
    style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:1rem;">
    <div id="sm-box"
        style="position:relative;width:100%;max-width:32rem;border-radius:1rem;background:#fff;box-shadow:0 25px 50px rgba(0,0,0,.25);overflow:hidden;opacity:0;transform:scale(.95);transition:opacity .25s ease,transform .25s ease;">

        {{-- Close --}}
        <button id="sm-close" type="button" aria-label="Close"
            style="position:absolute;top:.75rem;right:.75rem;background:none;border:none;cursor:pointer;color:#9ca3af;padding:.25rem;line-height:1;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </button>

        <img id="sm-image" src="" alt=""
            style="display:none;width:100%;max-height:14rem;object-fit:cover;">

        <div style="padding:1.5rem;">
            <h2 id="sm-title" style="display:none;font-size:1.25rem;font-weight:600;margin:0 0 .75rem;color:#111827;">
            </h2>
            <div id="sm-content" style="font-size:.875rem;line-height:1.6;color:#374151;"></div>
            <div id="sm-buttons" style="display:none;margin-top:1.25rem;flex-wrap:wrap;gap:.75rem;"></div>
        </div>

    </div>
</div>

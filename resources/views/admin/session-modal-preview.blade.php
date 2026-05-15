@extends('template::layouts.app')

@section('title', 'Preview: ' . ($modal->title ?? 'Session Modal'))

@push('styles')
    <style>
        .sm-preview-page-content {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: .2;
            pointer-events: none;
            user-select: none;
            padding: 2rem;
        }

        .sm-preview-badge {
            position: fixed;
            top: 1rem;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(99, 102, 241, .9);
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: .3rem .9rem;
            border-radius: 9999px;
            z-index: 10000;
            pointer-events: none;
        }

        .sm-preview-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(10, 10, 10, .92);
            border-top: 1px solid rgba(255, 255, 255, .08);
            padding: .55rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            z-index: 10000;
            font-size: .72rem;
            color: #94a3b8;
            flex-wrap: wrap;
            backdrop-filter: blur(6px);
        }

        .sm-preview-bar strong {
            color: #e2e8f0;
        }

        .sm-preview-pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            background: rgba(255, 255, 255, .06);
            padding: .2rem .55rem;
            border-radius: 9999px;
        }

        .sm-dot-on {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #22c55e;
            display: inline-block;
        }

        .sm-dot-off {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #ef4444;
            display: inline-block;
        }
    </style>
@endpush

@section('content')

    <span class="sm-preview-badge">&#9679; Preview</span>

    <div class="sm-preview-page-content">
        <p style="text-align:center;max-width:32rem;">
            This is a preview of how the modal will appear on the frontend.
        </p>
    </div>

    @include('components.session-modals-skin')

    @php
        $previewData = json_encode([
            'id' => $modal->id,
            'title' => $modal->title,
            'content' => $modal->content,
            'image' => $modal->image,
            'buttons' => $modal->buttons ?? [],
            'allow_backdrop_dismiss' => $modal->allow_backdrop_dismiss,
        ]);
    @endphp

    <script>
        (function() {
            var modal = {!! $previewData !!};

            function openPreviewModal() {
                var backdrop = document.getElementById('sm-backdrop');
                var modalEl = document.getElementById('sm-modal');
                var box = document.getElementById('sm-box');
                var imageEl = document.getElementById('sm-image');
                var titleEl = document.getElementById('sm-title');
                var contentEl = document.getElementById('sm-content');
                var buttonsEl = document.getElementById('sm-buttons');
                var closeBtn = document.getElementById('sm-close');

                if (!backdrop || !modalEl || !box) return;

                if (modal.image) {
                    imageEl.src = '/storage/' + modal.image;
                    imageEl.alt = modal.title || '';
                    imageEl.style.display = 'block';
                }

                if (modal.title) {
                    titleEl.textContent = modal.title;
                    titleEl.style.display = 'block';
                }

                contentEl.innerHTML = modal.content || '';

                buttonsEl.innerHTML = '';
                var btns = modal.buttons || [];
                if (btns.length) {
                    var styleMap = {
                        primary: 'background:#4f46e5;color:#fff;',
                        secondary: 'background:#e5e7eb;color:#1f2937;',
                        danger: 'background:#dc2626;color:#fff;',
                    };
                    var base =
                        'display:inline-flex;align-items:center;justify-content:center;padding:.5rem 1rem;border-radius:.5rem;font-size:.875rem;font-weight:500;border:none;cursor:pointer;text-decoration:none;';
                    btns.forEach(function(btn) {
                        var el = document.createElement('button');
                        el.type = 'button';
                        el.textContent = btn.label;
                        el.style.cssText = base + (styleMap[btn.style] || styleMap.primary);
                        el.dataset.smStyle = btn.style || 'primary';
                        buttonsEl.appendChild(el);
                    });
                    buttonsEl.style.display = 'flex';
                }

                backdrop.style.display = 'block';
                modalEl.style.display = 'flex';
                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        backdrop.style.opacity = '1';
                        box.style.opacity = '1';
                        box.style.transform = 'scale(1)';
                    });
                });

                box.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        location.reload();
                    });
                }
                if (modal.allow_backdrop_dismiss) {
                    modalEl.onclick = function() {
                        location.reload();
                    };
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', openPreviewModal);
            } else {
                openPreviewModal();
            }
        }());
    </script>

    <div class="sm-preview-bar">
        <span><strong>{{ $modal->title ?? '(no title)' }}</strong></span>
        <span class="sm-preview-pill">
            <span class="{{ $modal->is_active ? 'sm-dot-on' : 'sm-dot-off' }}"></span>
            {{ $modal->is_active ? 'Active' : 'Inactive' }}
        </span>
        <span class="sm-preview-pill">Frequency: <strong>{{ str_replace('_', ' ', $modal->frequency) }}</strong></span>
        @if ($modal->starts_at || $modal->ends_at)
            <span class="sm-preview-pill">
                {{ $modal->starts_at?->format('d.m.Y H:i') ?? '∞' }} &ndash;
                {{ $modal->ends_at?->format('d.m.Y H:i') ?? '∞' }}
            </span>
        @endif
        <span class="sm-preview-pill">Backdrop dismiss:
            <strong>{{ $modal->allow_backdrop_dismiss ? 'yes' : 'no' }}</strong></span>
        <span style="margin-left:auto;">
            <a href="javascript:window.close()" style="color:#6366f1;text-decoration:none;font-weight:600;">Close tab ×</a>
        </span>
    </div>

@endsection

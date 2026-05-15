@php
    use App\Services\SessionModalService;

    $service = app(SessionModalService::class);
    $currentRoute = request()->route()?->getName() ?? '';
    $sessionModals = $service->getModalsForUser(auth()->user(), $currentRoute);

    foreach ($sessionModals as $modal) {
        $service->markShown($modal);
    }
@endphp

@if ($sessionModals->isNotEmpty())
    @include('components.session-modals-skin')

    <script>
        (function() {
            var queue = {!! $sessionModals->toJson() !!};
            var dismissUrl = '{{ route('session-modals.dismiss') }}';
            var csrfToken = '{{ csrf_token() }}';

            var backdrop = document.getElementById('sm-backdrop');
            var modalEl = document.getElementById('sm-modal');
            var box = document.getElementById('sm-box');
            var closeBtn = document.getElementById('sm-close');
            var imageEl = document.getElementById('sm-image');
            var titleEl = document.getElementById('sm-title');
            var contentEl = document.getElementById('sm-content');
            var buttonsEl = document.getElementById('sm-buttons');

            var current = null;

            function show(modal) {
                current = modal;

                // Image
                if (modal.image) {
                    imageEl.src = '/storage/' + modal.image;
                    imageEl.alt = modal.title || '';
                    imageEl.style.display = 'block';
                } else {
                    imageEl.style.display = 'none';
                }

                // Title
                if (modal.title) {
                    titleEl.textContent = modal.title;
                    titleEl.style.display = 'block';
                } else {
                    titleEl.style.display = 'none';
                }

                // Content (trusted admin HTML)
                contentEl.innerHTML = modal.content || '';

                // Buttons
                buttonsEl.innerHTML = '';
                var btns = modal.buttons || [];
                if (btns.length) {
                    btns.forEach(function(btn) {
                        var el;
                        var styles = {
                            primary: 'background:#4f46e5;color:#fff;',
                            secondary: 'background:#e5e7eb;color:#1f2937;',
                            danger: 'background:#dc2626;color:#fff;',
                        };
                        var baseStyle =
                            'display:inline-flex;align-items:center;justify-content:center;padding:.5rem 1rem;border-radius:.5rem;font-size:.875rem;font-weight:500;border:none;cursor:pointer;text-decoration:none;' +
                            (styles[btn.style] || styles.primary);

                        if (btn.url) {
                            el = document.createElement('a');
                            el.href = btn.url;
                            el.addEventListener('click', function() {
                                dismiss(current);
                            });
                        } else {
                            el = document.createElement('button');
                            el.type = 'button';
                            el.addEventListener('click', function() {
                                dismiss(current);
                            });
                        }
                        el.textContent = btn.label;
                        el.style.cssText = baseStyle;
                        el.dataset.smStyle = btn.style || 'primary';
                        buttonsEl.appendChild(el);
                    });
                    buttonsEl.style.display = 'flex';
                } else {
                    buttonsEl.style.display = 'none';
                }

                // Show
                backdrop.style.display = 'block';
                modalEl.style.display = 'flex';
                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        backdrop.style.opacity = '1';
                        box.style.opacity = '1';
                        box.style.transform = 'scale(1)';
                    });
                });

                // Backdrop click — handler is on modalEl (sits on top), stop on box
                if (modal.allow_backdrop_dismiss) {
                    modalEl.onclick = function(e) {
                        dismiss(current);
                    };
                } else {
                    modalEl.onclick = null;
                }
            }

            function dismiss(modal) {
                if (!modal) return;
                var id = modal.id;
                current = null;

                backdrop.style.opacity = '0';
                box.style.opacity = '0';
                box.style.transform = 'scale(.95)';

                setTimeout(function() {
                    backdrop.style.display = 'none';
                    modalEl.style.display = 'none';
                    showNext();
                }, 260);

                fetch(dismissUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        modal_id: id
                    }),
                }).catch(function() {});
            }

            function showNext() {
                if (queue.length === 0) return;
                show(queue.shift());
            }

            closeBtn.addEventListener('click', function() {
                dismiss(current);
            });
            box.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && current) dismiss(current);
            });

            // Wait for DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', showNext);
            } else {
                showNext();
            }
        }());
    </script>
@endif

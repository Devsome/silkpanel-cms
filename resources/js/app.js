import './bootstrap';

import.meta.glob([
    '../images/**',
]);

const themeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
const reducedMotionMediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

const getPreferredTheme = () => {
    const storedTheme = window.localStorage.getItem('theme');

    if (storedTheme === 'dark' || storedTheme === 'light') {
        return storedTheme;
    }

    return themeMediaQuery.matches ? 'dark' : 'light';
};

const applyTheme = (theme) => {
    const isDark = theme === 'dark';

    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.style.colorScheme = theme;
};

const setTransitionOrigin = (originX, originY) => {
    document.documentElement.style.setProperty('--theme-transition-x', `${originX}px`);
    document.documentElement.style.setProperty('--theme-transition-y', `${originY}px`);
};

const getToggleOrigin = (triggerElement) => {
    if (!triggerElement) {
        return {
            x: window.innerWidth / 2,
            y: window.innerHeight / 2,
        };
    }

    const rect = triggerElement.getBoundingClientRect();

    return {
        x: rect.left + (rect.width / 2),
        y: rect.top + (rect.height / 2),
    };
};

const themeStore = {
    current: getPreferredTheme(),
    transitioning: false,

    get isDark() {
        return this.current === 'dark';
    },

    init() {
        applyTheme(this.current);

        themeMediaQuery.addEventListener('change', (event) => {
            if (window.localStorage.getItem('theme')) {
                return;
            }

            this.current = event.matches ? 'dark' : 'light';
            applyTheme(this.current);
        });
    },

    toggle(event) {
        if (this.transitioning) {
            return;
        }

        const nextTheme = this.isDark ? 'light' : 'dark';
        const triggerElement = event?.currentTarget ?? event?.target?.closest('[data-theme-toggle]') ?? null;
        const { x, y } = getToggleOrigin(triggerElement);

        setTransitionOrigin(x, y);

        const commitThemeChange = () => {
            this.current = nextTheme;
            window.localStorage.setItem('theme', nextTheme);
            applyTheme(nextTheme);
        };

        if (!document.startViewTransition || reducedMotionMediaQuery.matches) {
            commitThemeChange();
            return;
        }

        this.transitioning = true;

        const transition = document.startViewTransition(() => {
            commitThemeChange();
        });

        transition.ready.catch(() => {
            this.transitioning = false;
        });

        transition.finished.finally(() => {
            this.transitioning = false;
        });
    },
};

document.addEventListener('alpine:init', () => {
    Alpine.store('theme', themeStore);
    themeStore.init();

    Alpine.data('eventCountdown', (targetDate) => ({
        days: '00',
        hours: '00',
        minutes: '00',
        seconds: '00',
        target: new Date(targetDate).getTime(),
        interval: null,
        init() {
            this.update();
            this.interval = setInterval(() => this.update(), 1000);
        },
        update() {
            let diff = Math.max(0, Math.floor((this.target - Date.now()) / 1000));

            if (diff <= 0) {
                this.days = '00';
                this.hours = '00';
                this.minutes = '00';
                this.seconds = '00';
                clearInterval(this.interval);
                return;
            }

            const d = Math.floor(diff / 86400);
            diff -= d * 86400;
            this.days = String(d).padStart(2, '0');
            this.hours = String(Math.floor(diff / 3600)).padStart(2, '0');
            this.minutes = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
            this.seconds = String(diff % 60).padStart(2, '0');
        },
        destroy() {
            clearInterval(this.interval);
        },
    }));

    Alpine.data('silkroadMap', () => {
        const cfg = window._silkroadMapConfig || {};

        return {
            search: '',
            visibleCount: 0,
            totalCount: 0,
            loading: false,
            errorMsg: '',
            lastRefreshed: '',
            currentInterval: cfg.refreshInterval ?? 30,
            _allCharacters: [],
            _timer: null,

            init() {
                this.$nextTick(() => {
                    if (typeof xSROMap === 'undefined') {
                        this.errorMsg = 'Map library failed to load. Please refresh the page.';
                        return;
                    }
                    xSROMap.init();
                    this.loadCharacters();
                    if (this.currentInterval > 0) {
                        this._timer = setInterval(() => this.loadCharacters(), this.currentInterval * 1000);
                    }
                    // Reactive search: watch instead of @input to guarantee updated value
                    this.$watch('search', () => this.applySearch());
                });
            },

            destroy() {
                clearInterval(this._timer);
            },

            async loadCharacters() {
                this.loading = true;
                this.errorMsg = '';
                try {
                    const res = await fetch(cfg.apiUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const json = await res.json();
                    if (json.error) throw new Error(json.error);

                    // Remove previous player markers
                    this._allCharacters.forEach(c => xSROMap.RemovePlayer(c.char_id));

                    // Job-suit characters are already excluded server-side for this route.
                    this._allCharacters = json.data || [];
                    this.totalCount = json.total || 0;
                    this.lastRefreshed = new Date().toLocaleTimeString();
                    this.applySearch();
                } catch (err) {
                    this.errorMsg = 'Could not load map data: ' + err.message;
                } finally {
                    this.loading = false;
                }
            },

            applySearch() {
                const term = this.search.trim().toLowerCase();
                const visible = term
                    ? this._allCharacters.filter(c => c.name.toLowerCase().includes(term))
                    : this._allCharacters;

                this.visibleCount = visible.length;

                // Determine if matched results should be highlighted
                const highlight = term && visible.length >= 1 && visible.length <= 5;

                this._allCharacters.forEach(c => xSROMap.RemovePlayer(c.char_id));
                visible.forEach(c => {
                    const color = highlight ? '#facc15' : this._levelColor(c.level);
                    const popup = `<div style="min-width:140px;line-height:1.6">
                        <strong>${this._esc(c.name)}</strong><br/>
                        Level <b>${c.level}</b>
                    </div>`;
                    xSROMap.AddPlayer(c.char_id, popup, c.pos_x, c.pos_y, c.pos_z, c.region,
                        { color, highlighted: highlight });
                });

                if (highlight) {
                    xSROMap.FlyView(visible[0].pos_x, visible[0].pos_y, visible[0].pos_z, visible[0].region);
                }
            },

            resetTimer() {
                clearInterval(this._timer);
                this._timer = null;
                if (this.currentInterval > 0) {
                    this._timer = setInterval(() => this.loadCharacters(), this.currentInterval * 1000);
                }
            },

            _levelColor(lvl) {
                if (lvl < 30) return '#60a5fa';
                if (lvl < 60) return '#34d399';
                if (lvl < 90) return '#fbbf24';
                return '#f87171';
            },

            _esc(str) {
                const d = document.createElement('div');
                d.textContent = String(str);
                return d.innerHTML;
            },
        };
    });
});


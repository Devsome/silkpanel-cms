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
});


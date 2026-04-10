import './bootstrap';

import Alpine from 'alpinejs';

import.meta.glob([
    '../images/**',
]);

window.Alpine = Alpine;

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

Alpine.store('theme', themeStore);
themeStore.init();

Alpine.start();


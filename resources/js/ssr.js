import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';

import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, h } from 'vue';

import { route as ziggyRoute } from 'ziggy-js';

import themePreset from '@/theme/noir-preset';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';

import Container from '@/components/Container.vue';
import PageTitleSection from '@/components/PageTitleSection.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - ${appName}`,
        resolve: (name) =>
            resolvePageComponent(
                `./pages/${name}.vue`,
                import.meta.glob('./pages/**/*.vue')
            ),
        setup({ App, props, plugin }) {
            const app = createSSRApp({ render: () => h(App, props) });

            // Configure Ziggy for SSR...
            const ziggyConfig = {
                ...page.props.ziggy,
                location: new URL(page.props.ziggy.location),
            };

            // Create route function...
            const route = (name, params, absolute) =>
                ziggyRoute(name, params, absolute, ziggyConfig);

            // Make route function available globally...
            app.config.globalProperties.route = route;

            // Make route function available globally for SSR...
            if (typeof window === 'undefined') {
                global.route = route;
            }

            app.use(plugin)
                .use(PrimeVue, {
                    theme: 'none',
                    /* theme: {
                        preset: themePreset,
                        options: {
                            darkModeSelector: '.dark',
                            cssLayer: {
                                name: 'primevue',
                                order: 'tailwind-theme, tailwind-base, primevue, tailwind-utilities',
                            },
                        },
                    }, */
                })
                .use(ToastService)
                .component('InertiaHead', Head)
                .component('InertiaLink', Link)
                .component('Container', Container)
                .component('PageTitleSection', PageTitleSection);

            return app;
        },
    })
);

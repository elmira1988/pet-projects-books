import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { createPinia } from 'pinia';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${appName}  -  ${title}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        // 1. Создаем экземпляр Pinia
        const pinia = createPinia();

        // 2. Инициализируем Vue-приложение
        const app = createApp({ render: () => h(App, props) });

        // Сначала подключаем Inertia и Pinia к приложению! [INDEX]
        app.use(plugin);
        app.use(pinia);
        //Монтируем приложение в DOM
        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

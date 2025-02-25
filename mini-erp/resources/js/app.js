import 'bootstrap/dist/css/bootstrap.min.css';
import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';



createInertiaApp({
    resolve: async name => {
        // Substitua 'require' por import dinâmico
        const page = await import(`./Pages/${name}.vue`);
        return page.default;
    },
    setup({ el, App, props }) {
        createApp({ render: () => h(App, props) }).mount(el);
    },
});

// Se você quiser usar a barra de progresso do Inertia
InertiaProgress.init();
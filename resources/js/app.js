import "./bootstrap";
import "./fontawesome";
import "../css/app.css";

import "bootstrap/dist/css/bootstrap.css";
import "admin-lte/dist/css/adminlte.css";

import "@fontsource/poppins/400.css";
import "@fontsource/poppins/500.css";
import "@fontsource/poppins/600.css";
import "@fontsource/poppins/700.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
// import NProgress from "nprogress";
// import { router } from "@inertiajs/vue3";

// let timeout = null;

// router.on("start", () => {
//     timeout = setTimeout(() => NProgress.start(), 250);
// });

// router.on("progress", (event) => {
//     if (NProgress.isStarted() && event.detail.progress.percentage) {
//         NProgress.set((event.detail.progress.percentage / 100) * 0.9);
//     }
// });

// router.on("finish", (event) => {
//     clearTimeout(timeout);
//     if (!NProgress.isStarted()) {
//         return;
//     } else if (event.detail.visit.completed) {
//         NProgress.done();
//     } else if (event.detail.visit.interrupted) {
//         NProgress.set(0);
//     } else if (event.detail.visit.cancelled) {
//         NProgress.done();
//         NProgress.remove();
//     }
// });

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    // progress: false,
    progress: {
        color: "#8B1E3F",
        showSpinner: true,
        delay: 250,
        includeCSS: true,
    },
});

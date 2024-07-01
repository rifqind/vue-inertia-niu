import "./bootstrap";
// import "./fontawesome";
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
import { library, config } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faSearchengin } from "@fortawesome/free-brands-svg-icons";
import {
    faAngleLeft,
    faAngleUp,
    faAngleDown,
    faBan,
    faBars,
    faBarsStaggered,
    faBookBookmark,
    faBuilding,
    faCheck,
    faCheckDouble,
    faChevronLeft,
    faCircle,
    faCircleDown,
    faComputer,
    faDiceD6,
    faDisplay,
    faEye,
    faFlagCheckered,
    faGraduationCap,
    faHome,
    faHourglassHalf,
    faKey,
    faListOl,
    faLock,
    faMagnifyingGlass,
    faPaperPlane,
    faPen,
    faPenNib,
    faPencil,
    faPlus,
    faPlusCircle,
    faRecycle,
    faRightToBracket,
    faRotateLeft,
    faRotateRight,
    faSave,
    faScrewdriverWrench,
    faSignOutAlt,
    faSort,
    faTable,
    faTachometerAlt,
    faTags,
    faThList,
    faTrashCan,
    faUser,
    faUserTie,
    faUsers,
} from "@fortawesome/free-solid-svg-icons";
library.add(
    faAngleLeft,
    faAngleUp,
    faAngleDown,
    faBan,
    faBars,
    faBarsStaggered,
    faBookBookmark,
    faBuilding,
    faCheck,
    faCheckDouble,
    faChevronLeft,
    faCircle,
    faCircleDown,
    faComputer,
    faDiceD6,
    faDisplay,
    faEye,
    faFlagCheckered,
    faGraduationCap,
    faHome,
    faHourglassHalf,
    faKey,
    faListOl,
    faLock,
    faMagnifyingGlass,
    faPaperPlane,
    faPen,
    faPenNib,
    faPencil,
    faPlus,
    faPlusCircle,
    faRecycle,
    faRightToBracket,
    faRotateLeft,
    faRotateRight,
    faSave,
    faScrewdriverWrench,
    faSignOutAlt,
    faSort,
    faSearchengin,
    faTable,
    faTachometerAlt,
    faTags,
    faThList,
    faTrashCan,
    faUser,
    faUserTie,
    faUsers
);
config.autoAddCss = false;
const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component("font-awesome-icon", FontAwesomeIcon);

        // vueApp.config.globalProperties.debounce = debounce;
        return vueApp.mount(el);
    },
    // progress: false,
    progress: {
        color: "#8B1E3F",
        showSpinner: true,
        delay: 250,
        includeCSS: false,
    },
});

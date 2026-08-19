import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'bootstrap'

import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { createPinia } from 'pinia'
import { formatPriceDirective } from './@core/directives'

createInertiaApp({
    resolve: async (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue')
        const pagePath = name.replace(/\./g, '/')
        return await pages[`./Pages/${pagePath}.vue`]()
    },

    setup({ el, App, props, plugin }) {
        const pinia = createPinia()

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(formatPriceDirective)
            .component('Link', Link)
            .component('Head', Head)
            .mount(el)
    },

    defaults: {
        prefetch: {
            hoverDelay: 75,
        },
    },
})

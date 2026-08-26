import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'leaflet/dist/leaflet.css'
import 'qalendar/dist/style.css'
import 'bootstrap'
import '../../Modules/VCards/resources/assets/less/vcards.less'

import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { createPinia } from 'pinia'
import Toastify from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

import './@core/vee-validate'
import { formatPriceDirective } from './@core/directives'

createInertiaApp({
    resolve: async (name) => {
        const pagePath = name.replace(/\./g, '/')

        const pages = import.meta.glob('./Pages/**/*.vue')
        if (pages[`./Pages/${pagePath}.vue`]) {
            return await pages[`./Pages/${pagePath}.vue`]()
        }

        const modulePages = import.meta.glob('../../Modules/*/resources/assets/js/Pages/**/*.vue')
        for (const [path, loader] of Object.entries(modulePages)) {
            const normalizedPath = path.replace('../../Modules/', '').replace('/resources/assets/js/Pages/', '/')
            const normalizedName = pagePath.replace(/^Member\//, 'Member/')
            if (normalizedPath.includes(normalizedName) || normalizedPath.endsWith(normalizedName.split('/').pop())) {
                return await loader()
            }
        }

        return await pages[`./Pages/${pagePath}.vue`]()
    },

    setup({ el, App, props, plugin }) {
        const pinia = createPinia()

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(formatPriceDirective)
            .use(Toastify, {
                position: 'top-right',
                duration: 4000,
                theme: 'colored',
            })
            .component('Link', Link)
            .component('Head', Head)
            .mount(el)
    },

    defaults: {
        prefetch: {
            hoverDelay: 75,
        },
        visitOptions: (href, options) => ({
            ...options,
            viewTransition: true,
        }),
    },

    progress: {
        color: '#0d6efd',
    },
})

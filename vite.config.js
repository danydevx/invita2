import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import less from 'less'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/less/admin.less', 'resources/less/minisite.less', 'resources/less/directory.less', 'resources/js/app.js', 'resources/js/minisite.js', 'resources/js/booking-widget.js'],
            refresh: true,
        }),
        vue({
            template: {
                compilerOptions: {
                    isCustomElement: (tag) => tag.startsWith('trix-')
                }
            }
        }),
    ],
    css: {
        preprocessorOptions: {
            less: {
                javascriptEnabled: true,
            }
        }
    },
    server: {
         host: 'saas.local',
            port: 5173,
            hmr: {
                host: 'saas.local',
            },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

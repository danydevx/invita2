import { defineStore } from 'pinia'

export const useAppStore = defineStore('app', {
    state: () => ({
        sidebarOpen: true,
        loading: false,
    }),

    actions: {
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen
        },

        setLoading(value) {
            this.loading = value
        },
    },
})
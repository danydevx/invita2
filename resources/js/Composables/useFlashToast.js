import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'

export function useFlashToast() {
    const page = usePage()

    const showFlash = (type, message) => {
        if (!message) return
        const options = {
            success: { type: 'success' },
            error: { type: 'error' },
            warning: { type: 'warning' },
            info: { type: 'info' },
        }
        toast(message, options[type] || { type: 'info' })
    }

    watch(
        () => page.props.flash,
        (flash) => {
            if (flash?.success) showFlash('success', flash.success)
            if (flash?.error) showFlash('error', flash.error)
            if (flash?.warning) showFlash('warning', flash.warning)
            if (flash?.info) showFlash('info', flash.info)
        },
        { immediate: true, deep: true }
    )
}

<template>
    <div
        class="public-site"
        :style="{
            '--primary': theme?.primary_color || '#111827',
            '--secondary': theme?.secondary_color || '#6B7280',
            '--bg': theme?.background_color || '#FFFFFF',
            '--text': theme?.text_color || '#111827',
            '--accent': theme?.accent_color || theme?.primary_color || '#3B82F6',
        }"
    >
        <Head :title="`Cita Confirmada - ${business.name}`" />

        <header class="site-header">
            <div class="container">
                <div class="d-flex align-items-center gap-3">
                    <img
                        v-if="business.logo"
                        :src="business.logo"
                        :alt="business.name"
                        class="business-logo"
                    />
                    <h1 class="business-name mb-0">{{ business.name }}</h1>
                </div>
            </div>
        </header>

        <div class="container py-5 text-center">
            <div class="check-icon mb-4">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <h2 class="mb-3">¡Cita Solicitada!</h2>
            <p class="text-muted mb-4">
                Tu cita ha sido {{ appointment.status === 'confirmed' ? 'confirmada' : 'solicitada' }}.
                {{ business.name }} se pondrá en contacto contigo pronto.
            </p>

            <div class="card mx-auto mb-4" style="max-width: 400px;">
                <div class="card-body">
                    <h5 class="card-title">Detalles de tu Cita</h5>
                    <hr>
                    <p class="mb-2">
                        <strong>Servicio:</strong> {{ appointment.service }}
                    </p>
                    <p class="mb-2">
                        <strong>Fecha:</strong> {{ appointment.date }}
                    </p>
                    <p class="mb-2">
                        <strong>Hora:</strong> {{ appointment.time }}
                    </p>
                    <p class="mb-0">
                        <strong>Cliente:</strong> {{ appointment.customer_name }}
                    </p>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a
                    :href="route('public.business.site', { slug: business.slug })"
                    class="site-btn-secondary"
                >
                    Volver al Inicio
                </a>
                <a
                    v-if="business.whatsapp"
                    :href="`https://wa.me/${business.whatsapp.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(`Hola, confirmo mi cita del ${appointment.date} a las ${appointment.time}`)}`"
                    target="_blank"
                    class="site-btn-primary"
                >
                    <i class="bi bi-whatsapp me-2"></i>
                    Confirmar por WhatsApp
                </a>
            </div>
        </div>

        <footer class="site-footer">
            <div class="container">
                <p class="powered-by mb-0">
                    Hecho con <a href="/" class="text-white">TuSaaS</a>
                </p>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
    business: Object,
    theme: Object,
    appointment: Object,
})
</script>

<style scoped>
.check-icon {
    font-size: 4rem;
    color: #10B981;
}
</style>

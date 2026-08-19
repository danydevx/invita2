<template>
  <div class="min-vh-100 d-flex align-items-center bg-body-tertiary">
    <Head title="Mantenimiento" />

    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5 text-center">
              <div class="display-6 mb-2">{{ title }}</div>
              <p class="text-muted mb-4">{{ message }}</p>
              <div class="text-muted small">Gracias por tu paciencia.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
  title: {
    type: String,
    default: 'Mantenimiento en progreso',
  },
  message: {
    type: String,
    default: 'El sistema esta en mantenimiento. Intente nuevamente mas tarde.',
  },
})
</script>

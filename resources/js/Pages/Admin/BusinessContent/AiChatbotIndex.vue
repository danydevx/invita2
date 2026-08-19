<template>
  <AdminLayout>
    <Head :title="`AI Chatbot - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin/businesses" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Negocios
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - AI Chatbot</h1>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="text-center py-5">
          <i class="bi bi-robot display-1 text-muted"></i>
          <h3 class="h5 mt-3">Modulo de AI Chatbot</h3>
          <p class="text-muted">Este modulo esta en desarrollo.</p>
          <p class="text-muted small">Pronto podras configurar el chatbot de inteligencia artificial para tu negocio.</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const business = computed(() => page.props.business)
</script>

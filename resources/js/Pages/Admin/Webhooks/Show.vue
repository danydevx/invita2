<template>
  <AdminLayout>
    <Head title="Detalle webhook" />

    <PageHeader :title="'Detalle webhook'" :breadcrumbs="breadcrumbs" backHref="/admin/webhooks" />

    <div class="row g-3">
      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
              <span v-if="webhook.is_active" class="badge text-bg-success">Activo</span>
              <span v-else class="badge text-bg-secondary">Inactivo</span>
            </div>
            <h2 class="h5 mb-1">{{ webhook.name }}</h2>
            <p class="text-muted mb-0">{{ webhook.url }}</p>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Detalle</h3>
            <dl class="row mb-0">
              <dt class="col-5 text-muted">Usuario</dt>
              <dd class="col-7">{{ webhook.user?.name || '-' }}</dd>
              <dt class="col-5 text-muted">Email</dt>
              <dd class="col-7">{{ webhook.user?.email || '-' }}</dd>
              <dt class="col-5 text-muted">Ultimo uso</dt>
              <dd class="col-7">{{ webhook.last_used_at || '-' }}</dd>
              <dt class="col-5 text-muted">Fallos</dt>
              <dd class="col-7">{{ webhook.failure_count }}</dd>
            </dl>
          </div>
        </div>
        <div class="card border-0 shadow-sm mt-3">
          <div class="card-body">
            <h3 class="h6">Eventos</h3>
            <div class="d-flex flex-wrap gap-2">
              <span v-for="event in webhook.events" :key="event" class="badge text-bg-light border">
                {{ event }}
              </span>
            </div>
            <div class="mt-3">
              <Link :href="`/admin/webhooks/${webhook.id}/deliveries`" class="btn btn-outline-primary btn-sm">
                Ver entregas
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  webhook: {
    type: Object,
    required: true,
  },
})

const breadcrumbs = [
  { label: 'Webhooks', href: '/admin/webhooks' },
  { label: `#${props.webhook.id}`, active: true },
]
</script>

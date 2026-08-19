<template>
  <AdminLayout>
    <Head title="Detalle evento" />

    <PageHeader :title="'Detalle evento'" :breadcrumbs="breadcrumbs" backHref="/admin/security-events" />

    <div class="row g-3">
      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
              <span class="badge text-bg-secondary">{{ event.event_type }}</span>
              <span class="text-muted small">{{ event.created_at }}</span>
            </div>
            <p class="mb-0">{{ event.description || 'Sin descripcion.' }}</p>
          </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
          <div class="card-body">
            <h3 class="h6">Metadata</h3>
            <div v-if="formattedMetadata" class="bg-light border rounded p-3" style="white-space: pre-wrap">
              {{ formattedMetadata }}
            </div>
            <div v-else class="text-muted">Sin metadata adicional.</div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Detalle</h3>
            <dl class="row mb-0">
              <dt class="col-5 text-muted">Usuario</dt>
              <dd class="col-7">{{ event.user?.name || '-' }}</dd>
              <dt class="col-5 text-muted">Email</dt>
              <dd class="col-7">{{ event.user?.email || '-' }}</dd>
              <dt class="col-5 text-muted">IP</dt>
              <dd class="col-7">{{ event.ip_address || '-' }}</dd>
              <dt class="col-5 text-muted">User Agent</dt>
              <dd class="col-7">{{ event.user_agent || '-' }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  event: {
    type: Object,
    required: true,
  },
})

const breadcrumbs = [
  { label: 'Eventos de seguridad', href: '/admin/security-events' },
  { label: `#${props.event.id}`, active: true },
]

const formattedMetadata = computed(() => {
  if (!props.event.metadata || Object.keys(props.event.metadata).length === 0) {
    return ''
  }
  return JSON.stringify(props.event.metadata, null, 2)
})
</script>

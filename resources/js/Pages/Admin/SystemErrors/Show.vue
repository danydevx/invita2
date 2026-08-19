<template>
  <AdminLayout>
    <Head title="Detalle de error" />

    <PageHeader :title="'Detalle de error'" :breadcrumbs="breadcrumbs" backHref="/admin/system-errors">
      <template #actions>
        <button
          v-if="!error.is_resolved"
          class="btn btn-success"
          type="button"
          @click="resolveError"
        >
          Marcar como resuelto
        </button>
      </template>
    </PageHeader>

    <div class="row g-3">
      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
              <span class="badge text-bg-secondary">{{ error.type }}</span>
              <span v-if="error.is_resolved" class="badge text-bg-success">Resuelto</span>
              <span v-else class="badge text-bg-warning">Pendiente</span>
              <span class="text-muted small">{{ error.created_at }}</span>
            </div>
            <h2 class="h5">{{ error.message }}</h2>
            <p v-if="error.exception_class" class="text-muted mb-0">
              {{ error.exception_class }}
            </p>
          </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <h3 class="h6">Contexto</h3>
            <div v-if="formattedContext" class="bg-light border rounded p-3 text-muted" style="white-space: pre-wrap">
              {{ formattedContext }}
            </div>
            <div v-else class="text-muted">Sin contexto adicional.</div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Trace</h3>
            <div v-if="error.trace" class="bg-dark text-light rounded p-3" style="white-space: pre-wrap">
              {{ error.trace }}
            </div>
            <div v-else class="text-muted">No se registro trace.</div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <h3 class="h6">Detalle</h3>
            <dl class="row mb-0">
              <dt class="col-5 text-muted">Archivo</dt>
              <dd class="col-7">{{ error.file || '-' }}</dd>
              <dt class="col-5 text-muted">Linea</dt>
              <dd class="col-7">{{ error.line || '-' }}</dd>
              <dt class="col-5 text-muted">URL</dt>
              <dd class="col-7">{{ error.url || '-' }}</dd>
              <dt class="col-5 text-muted">Metodo</dt>
              <dd class="col-7">{{ error.method || '-' }}</dd>
              <dt class="col-5 text-muted">IP</dt>
              <dd class="col-7">{{ error.ip_address || '-' }}</dd>
              <dt class="col-5 text-muted">Resolucion</dt>
              <dd class="col-7">{{ error.resolved_at || '-' }}</dd>
            </dl>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Usuario</h3>
            <div v-if="error.user">
              <div class="fw-semibold">{{ error.user.name }}</div>
              <div class="text-muted small">{{ error.user.email }}</div>
            </div>
            <div v-else class="text-muted">No asociado.</div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  error: {
    type: Object,
    required: true,
  },
})

const breadcrumbs = [
  { label: 'Errores del sistema', href: '/admin/system-errors' },
  { label: `#${props.error.id}`, active: true },
]

const formattedContext = computed(() => {
  if (!props.error.context || Object.keys(props.error.context).length === 0) {
    return ''
  }
  return JSON.stringify(props.error.context, null, 2)
})

const resolveError = () => {
  router.put(`/admin/system-errors/${props.error.id}/resolve`, {}, { preserveScroll: true })
}
</script>

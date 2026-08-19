<template>
  <AdminLayout>
    <Head title="Detalle API key" />

    <PageHeader :title="'Detalle API key'" :breadcrumbs="breadcrumbs" backHref="/admin/api-keys">
      <template #actions>
        <button
          v-if="!apiKey.revoked_at"
          class="btn btn-outline-danger"
          type="button"
          @click="revokeKey"
        >
          Revocar
        </button>
      </template>
    </PageHeader>

    <div class="row g-3">
      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
              <span class="badge text-bg-secondary">{{ apiKey.key_prefix || '-' }}</span>
              <span v-if="apiKey.revoked_at" class="badge text-bg-secondary">Revocada</span>
              <span v-else-if="apiKey.is_active" class="badge text-bg-success">Activa</span>
              <span v-else class="badge text-bg-warning">Inactiva</span>
            </div>
            <h2 class="h5 mb-1">{{ apiKey.name }}</h2>
            <p class="text-muted mb-0">Creada: {{ apiKey.created_at }}</p>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Detalle</h3>
            <dl class="row mb-0">
              <dt class="col-5 text-muted">Usuario</dt>
              <dd class="col-7">{{ apiKey.user?.name || '-' }}</dd>
              <dt class="col-5 text-muted">Email</dt>
              <dd class="col-7">{{ apiKey.user?.email || '-' }}</dd>
              <dt class="col-5 text-muted">Ultimo uso</dt>
              <dd class="col-7">{{ apiKey.last_used_at || '-' }}</dd>
              <dt class="col-5 text-muted">IP ultimo uso</dt>
              <dd class="col-7">{{ apiKey.last_used_ip || '-' }}</dd>
              <dt class="col-5 text-muted">Expira</dt>
              <dd class="col-7">{{ apiKey.expires_at || '-' }}</dd>
              <dt class="col-5 text-muted">Revocada</dt>
              <dd class="col-7">{{ apiKey.revoked_at || '-' }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  apiKey: {
    type: Object,
    required: true,
  },
})

const breadcrumbs = [
  { label: 'API Keys', href: '/admin/api-keys' },
  { label: `#${props.apiKey.id}`, active: true },
]

const revokeKey = () => {
  if (!confirm('Vas a revocar esta API key. Esta accion no se puede deshacer.')) return
  router.put(`/admin/api-keys/${props.apiKey.id}/revoke`, {}, { preserveScroll: true })
}
</script>

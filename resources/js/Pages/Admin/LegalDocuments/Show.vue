<template>
  <AdminLayout>
    <Head title="Detalle documento legal" />

    <PageHeader :title="'Detalle documento legal'" :breadcrumbs="breadcrumbs" backHref="/admin/legal-documents">
      <template #actions>
        <Link :href="`/admin/legal-documents/${document.id}/edit`" class="btn btn-outline-primary">Editar</Link>
      </template>
    </PageHeader>

    <div class="row g-3">
      <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h2 class="h5 mb-0">{{ document.title }}</h2>
              <span class="badge text-bg-light">v{{ document.version }}</span>
            </div>
            <div class="text-muted mb-2">Key: {{ document.key }}</div>
            <div class="d-flex flex-wrap gap-2 mb-3">
              <span v-if="document.is_required" class="badge text-bg-primary">Obligatorio</span>
              <span v-else class="badge text-bg-secondary">Opcional</span>
              <span v-if="document.requires_reaccept" class="badge text-bg-warning">Reaceptacion</span>
              <span v-else class="badge text-bg-secondary">Sin reaceptacion</span>
              <span v-if="document.is_active" class="badge text-bg-success">Activo</span>
              <span v-else class="badge text-bg-secondary">Inactivo</span>
            </div>
            <div class="bg-light border rounded p-3" style="white-space: pre-wrap">
              {{ document.content }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6 mb-3">Aceptaciones</h3>
            <div v-if="acceptances.data.length === 0" class="text-muted">Sin aceptaciones registradas.</div>
            <div v-else class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th scope="col">Usuario</th>
                    <th scope="col">Version</th>
                    <th scope="col">Fecha</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="acceptance in acceptances.data" :key="acceptance.id">
                    <td class="text-muted">
                      <div class="fw-semibold">{{ acceptance.user?.name || '-' }}</div>
                      <div class="small">{{ acceptance.user?.email || '' }}</div>
                    </td>
                    <td class="text-muted">v{{ acceptance.version }}</td>
                    <td class="text-muted">{{ acceptance.accepted_at }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div v-if="acceptances.data.length" class="card-footer">
            <Pagination :links="acceptances.links" />
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
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  document: {
    type: Object,
    required: true,
  },
  acceptances: {
    type: Object,
    required: true,
  },
})

const breadcrumbs = [
  { label: 'Documentos legales', href: '/admin/legal-documents' },
  { label: props.document.key, active: true },
]
</script>

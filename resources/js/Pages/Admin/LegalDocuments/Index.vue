<template>
  <AdminLayout>
    <Head title="Documentos legales" />

    <PageHeader :title="'Documentos legales'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <Link href="/admin/legal-documents/create" class="btn btn-primary">Nuevo documento</Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input v-model="search" type="text" class="form-control" placeholder="Key o titulo" />
          </div>
          <div class="col-12 col-md-3 d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit">Filtrar</button>
            <button class="btn btn-outline-secondary" type="button" @click="clearFilters">Limpiar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Key</th>
              <th scope="col">Titulo</th>
              <th scope="col">Version</th>
              <th scope="col">Requiere</th>
              <th scope="col">Activo</th>
              <th scope="col">Aceptaciones</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="documents.data.length === 0">
              <td colspan="7" class="text-center text-muted py-4">No hay documentos registrados.</td>
            </tr>
            <tr v-for="doc in documents.data" :key="doc.id">
              <td class="fw-semibold">{{ doc.key }}</td>
              <td>{{ doc.title }}</td>
              <td class="text-muted">v{{ doc.version }}</td>
              <td>
                <span v-if="doc.is_required" class="badge text-bg-primary">Obligatorio</span>
                <span v-else class="badge text-bg-secondary">Opcional</span>
              </td>
              <td>
                <span v-if="doc.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </td>
              <td class="text-muted">{{ doc.acceptances_count }}</td>
              <td class="text-end">
                <Link :href="`/admin/legal-documents/${doc.id}`" class="btn btn-sm btn-outline-secondary me-2">
                  Ver
                </Link>
                <Link :href="`/admin/legal-documents/${doc.id}/edit`" class="btn btn-sm btn-outline-primary">
                  Editar
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ documents.data.length }} de {{ documents.total }} registros</div>
        <Pagination :links="documents.links" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  documents: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const search = ref(props.filters.search ?? '')

const breadcrumbs = [
  { label: 'Documentos legales', active: true },
]

const submitSearch = () => {
  router.get('/admin/legal-documents', { search: search.value }, { preserveState: true, replace: true })
}

const clearFilters = () => {
  search.value = ''
  submitSearch()
}
</script>

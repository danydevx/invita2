<template>
  <AdminLayout>
    <Head title="Plantillas" />

    <PageHeader :title="'Plantillas'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <Link href="/admin/message-templates/create" class="btn btn-primary">Nueva plantilla</Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input v-model="search" type="text" class="form-control" placeholder="Key o nombre" />
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
              <th scope="col">Clave</th>
              <th scope="col">Nombre</th>
              <th scope="col">Activo</th>
              <th scope="col">Actualizada</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="templates.data.length === 0">
              <td colspan="5" class="text-center text-muted py-4">No hay plantillas.</td>
            </tr>
            <tr v-for="template in templates.data" :key="template.id">
              <td class="fw-semibold">{{ template.key }}</td>
              <td>{{ template.name }}</td>
              <td>
                <span v-if="template.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </td>
              <td class="text-muted">{{ template.updated_at }}</td>
              <td class="text-end">
                <Link :href="`/admin/message-templates/${template.id}/edit`" class="btn btn-sm btn-outline-primary">Editar</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ templates.data.length }} de {{ templates.total }} registros</div>
        <Pagination :links="templates.links" />
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
  templates: {
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
  { label: 'Plantillas', active: true },
]

const submitSearch = () => {
  router.get('/admin/message-templates', { search: search.value }, { preserveState: true, replace: true })
}

const clearFilters = () => {
  search.value = ''
  submitSearch()
}
</script>

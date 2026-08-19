<template>
  <AdminLayout>
    <Head title="Feature Flags" />

    <PageHeader :title="'Feature Flags'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <Link href="/admin/feature-flags/create" class="btn btn-primary">Nuevo flag</Link>
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
              <th scope="col">Key</th>
              <th scope="col">Nombre</th>
              <th scope="col">Tipo</th>
              <th scope="col">Default</th>
              <th scope="col">Activo</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="flags.data.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay flags registrados.</td>
            </tr>
            <tr v-for="flag in flags.data" :key="flag.id">
              <td class="fw-semibold">{{ flag.key }}</td>
              <td>{{ flag.name }}</td>
              <td class="text-muted">{{ flag.type }}</td>
              <td class="text-muted">{{ flag.default_value || '-' }}</td>
              <td>
                <span v-if="flag.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </td>
              <td class="text-end">
                <Link :href="`/admin/feature-flags/${flag.id}/edit`" class="btn btn-sm btn-outline-primary">
                  Editar
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ flags.data.length }} de {{ flags.total }} registros</div>
        <Pagination :links="flags.links" />
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
  flags: {
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
  { label: 'Feature Flags', active: true },
]

const submitSearch = () => {
  router.get('/admin/feature-flags', { search: search.value }, { preserveState: true, replace: true })
}

const clearFilters = () => {
  search.value = ''
  submitSearch()
}
</script>

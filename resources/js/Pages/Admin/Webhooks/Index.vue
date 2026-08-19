<template>
  <AdminLayout>
    <Head title="Webhooks" />

    <PageHeader :title="'Webhooks'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input v-model="q" type="text" class="form-control" placeholder="Nombre, URL o usuario" />
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label">Estado</label>
            <select v-model="status" class="form-select">
              <option value="">Todos</option>
              <option value="active">Activo</option>
              <option value="inactive">Inactivo</option>
            </select>
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
              <th scope="col">Usuario</th>
              <th scope="col">Nombre</th>
              <th scope="col">URL</th>
              <th scope="col">Estado</th>
              <th scope="col">Ultimo uso</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="endpoints.data.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay webhooks registrados.</td>
            </tr>
            <tr v-for="endpoint in endpoints.data" :key="endpoint.id">
              <td>
                <div v-if="endpoint.user">
                  <div class="fw-semibold">{{ endpoint.user.name }}</div>
                  <div class="text-muted small">{{ endpoint.user.email }}</div>
                </div>
                <span v-else class="text-muted">-</span>
              </td>
              <td class="fw-semibold">{{ endpoint.name }}</td>
              <td class="text-muted">{{ endpoint.url }}</td>
              <td>
                <span v-if="endpoint.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </td>
              <td class="text-muted">{{ endpoint.last_used_at || '-' }}</td>
              <td class="text-end">
                <Link :href="`/admin/webhooks/${endpoint.id}`" class="btn btn-sm btn-outline-primary">Ver</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ endpoints.data.length }} de {{ endpoints.total }} registros</div>
        <Pagination :links="endpoints.links" />
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
  endpoints: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const q = ref(props.filters.q ?? '')
const status = ref(props.filters.status ?? '')

const breadcrumbs = [
  { label: 'Webhooks', active: true },
]

const submitSearch = () => {
  router.get(
    '/admin/webhooks',
    { q: q.value, status: status.value },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  q.value = ''
  status.value = ''
  submitSearch()
}
</script>

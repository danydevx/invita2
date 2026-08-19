<template>
  <AdminLayout>
    <Head title="API Keys" />

    <PageHeader :title="'API Keys'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input v-model="q" type="text" class="form-control" placeholder="Nombre, prefijo o usuario" />
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label">Estado</label>
            <select v-model="status" class="form-select">
              <option value="">Todos</option>
              <option value="active">Activa</option>
              <option value="inactive">Inactiva</option>
              <option value="revoked">Revocada</option>
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
              <th scope="col">Prefijo</th>
              <th scope="col">Estado</th>
              <th scope="col">Ultimo uso</th>
              <th scope="col">Creada</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="apiKeys.data.length === 0">
              <td colspan="7" class="text-center text-muted py-4">No hay API keys registradas.</td>
            </tr>
            <tr v-for="key in apiKeys.data" :key="key.id">
              <td>
                <div v-if="key.user">
                  <div class="fw-semibold">{{ key.user.name }}</div>
                  <div class="text-muted small">{{ key.user.email }}</div>
                </div>
                <span v-else class="text-muted">-</span>
              </td>
              <td class="fw-semibold">{{ key.name }}</td>
              <td class="text-muted">{{ key.key_prefix || '-' }}</td>
              <td>
                <span v-if="key.revoked_at" class="badge text-bg-secondary">Revocada</span>
                <span v-else-if="key.is_active" class="badge text-bg-success">Activa</span>
                <span v-else class="badge text-bg-warning">Inactiva</span>
              </td>
              <td class="text-muted">{{ key.last_used_at || '-' }}</td>
              <td class="text-muted">{{ key.created_at }}</td>
              <td class="text-end">
                <Link :href="`/admin/api-keys/${key.id}`" class="btn btn-sm btn-outline-primary">Ver</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ apiKeys.data.length }} de {{ apiKeys.total }} registros</div>
        <Pagination :links="apiKeys.links" />
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
  apiKeys: {
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
  { label: 'API Keys', active: true },
]

const submitSearch = () => {
  router.get(
    '/admin/api-keys',
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

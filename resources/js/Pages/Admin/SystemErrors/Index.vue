<template>
  <AdminLayout>
    <Head title="Errores del sistema" />

    <PageHeader :title="'Errores del sistema'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-4">
            <label class="form-label">Buscar</label>
            <input v-model="q" type="text" class="form-control" placeholder="Mensaje o clase" />
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Tipo</label>
            <select v-model="type" class="form-select">
              <option value="">Todos</option>
              <option v-for="item in types" :key="item" :value="item">
                {{ item }}
              </option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Estado</label>
            <select v-model="isResolved" class="form-select">
              <option value="">Todos</option>
              <option value="true">Resuelto</option>
              <option value="false">Pendiente</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Desde</label>
            <input v-model="dateFrom" type="date" class="form-control" />
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Hasta</label>
            <input v-model="dateTo" type="date" class="form-control" />
          </div>
          <div class="col-12 d-flex gap-2">
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
              <th scope="col">Fecha</th>
              <th scope="col">Tipo</th>
              <th scope="col">Mensaje</th>
              <th scope="col">Usuario</th>
              <th scope="col">URL</th>
              <th scope="col">Estado</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="errors.data.length === 0">
              <td colspan="7" class="text-center text-muted py-4">No hay errores registrados.</td>
            </tr>
            <tr v-for="error in errors.data" :key="error.id">
              <td class="text-muted">{{ error.created_at }}</td>
              <td class="fw-semibold">{{ error.type }}</td>
              <td class="text-muted">{{ truncate(error.message) }}</td>
              <td>
                <div v-if="error.user">
                  <div class="fw-semibold">{{ error.user.name }}</div>
                  <div class="text-muted small">{{ error.user.email }}</div>
                </div>
                <span v-else class="text-muted">-</span>
              </td>
              <td class="text-muted">{{ error.url || '-' }}</td>
              <td>
                <span v-if="error.is_resolved" class="badge text-bg-success">Resuelto</span>
                <span v-else class="badge text-bg-warning">Pendiente</span>
              </td>
              <td class="text-end">
                <Link :href="`/admin/system-errors/${error.id}`" class="btn btn-sm btn-outline-primary">
                  Ver
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ errors.data.length }} de {{ errors.total }} registros
        </div>
        <Pagination :links="errors.links" />
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
  errors: {
    type: Object,
    required: true,
  },
  types: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const q = ref(props.filters.q ?? '')
const type = ref(props.filters.type ?? '')
const isResolved = ref(props.filters.is_resolved ?? '')
const dateFrom = ref(props.filters.date_from ?? '')
const dateTo = ref(props.filters.date_to ?? '')

const breadcrumbs = [
  { label: 'Errores del sistema', active: true },
]

const submitSearch = () => {
  router.get(
    '/admin/system-errors',
    {
      q: q.value,
      type: type.value,
      is_resolved: isResolved.value,
      date_from: dateFrom.value,
      date_to: dateTo.value,
    },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  q.value = ''
  type.value = ''
  isResolved.value = ''
  dateFrom.value = ''
  dateTo.value = ''
  submitSearch()
}

const truncate = (value) => {
  if (!value) return '-'
  if (value.length <= 80) return value
  return `${value.slice(0, 80)}...`
}
</script>

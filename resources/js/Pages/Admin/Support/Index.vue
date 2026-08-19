<template>
  <AdminLayout>
    <Head title="Soporte" />

    <PageHeader :title="'Soporte'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-4">
            <label class="form-label">Buscar</label>
            <input v-model="search" type="text" class="form-control" placeholder="Asunto, categoria o usuario" />
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Estado</label>
            <select v-model="status" class="form-select">
              <option value="">Todos</option>
              <option v-for="item in statuses" :key="item" :value="item">{{ item }}</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Prioridad</label>
            <select v-model="priority" class="form-select">
              <option value="">Todas</option>
              <option v-for="item in priorities" :key="item" :value="item">{{ item }}</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Categoria</label>
            <select v-model="category" class="form-select">
              <option value="">Todas</option>
              <option v-for="item in categories" :key="item" :value="item">{{ item }}</option>
            </select>
          </div>
          <div class="col-12 col-md-2 d-flex gap-2">
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
              <th scope="col">Asunto</th>
              <th scope="col">Usuario</th>
              <th scope="col">Estado</th>
              <th scope="col">Prioridad</th>
              <th scope="col">Ultima respuesta</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="tickets.data.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay tickets registrados.</td>
            </tr>
            <tr v-for="ticket in tickets.data" :key="ticket.id">
              <td class="fw-semibold">{{ ticket.subject }}</td>
              <td>
                <div v-if="ticket.user">
                  <div class="fw-semibold">{{ ticket.user.name }}</div>
                  <div class="text-muted small">{{ ticket.user.email }}</div>
                </div>
                <span v-else class="text-muted">-</span>
              </td>
              <td>
                <span class="badge" :class="statusClass(ticket.status)">{{ ticket.status }}</span>
              </td>
              <td class="text-muted">{{ ticket.priority || '-' }}</td>
              <td class="text-muted">{{ ticket.last_reply_at || ticket.created_at }}</td>
              <td class="text-end">
                <Link :href="`/admin/support/${ticket.id}`" class="btn btn-sm btn-outline-primary">
                  Ver
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ tickets.data.length }} de {{ tickets.total }} registros
        </div>
        <Pagination :links="tickets.links" />
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
  tickets: {
    type: Object,
    required: true,
  },
  categories: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const statuses = ['open', 'pending', 'answered', 'closed']
const priorities = ['low', 'medium', 'high']

const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')
const priority = ref(props.filters.priority ?? '')
const category = ref(props.filters.category ?? '')

const breadcrumbs = [
  { label: 'Soporte' },
  { label: 'Registros', active: true },
]

const submitSearch = () => {
  router.get(
    '/admin/support',
    {
      search: search.value,
      status: status.value,
      priority: priority.value,
      category: category.value,
    },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  search.value = ''
  status.value = ''
  priority.value = ''
  category.value = ''
  submitSearch()
}

const statusClass = (value) => {
  if (value === 'open') return 'text-bg-success'
  if (value === 'pending') return 'text-bg-warning'
  if (value === 'answered') return 'text-bg-primary'
  if (value === 'closed') return 'text-bg-secondary'
  return 'text-bg-secondary'
}
</script>

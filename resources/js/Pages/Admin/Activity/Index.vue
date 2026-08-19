<template>
  <AdminLayout>
    <Head title="Actividad" />

    <PageHeader :title="'Actividad'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-4">
            <label class="form-label">Usuario</label>
            <input
              v-model="user"
              type="text"
              class="form-control"
              placeholder="Nombre o email"
            />
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label">Tipo</label>
            <select v-model="type" class="form-select">
              <option value="">Todos</option>
              <option v-for="item in types" :key="item" :value="item">
                {{ item }}
              </option>
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
          <div class="col-12 col-md-2 d-flex gap-2">
            <button class="btn btn-outline-primary flex-fill" type="submit">Filtrar</button>
            <button class="btn btn-outline-secondary flex-fill" type="button" @click="clearFilters">
              Limpiar
            </button>
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
              <th scope="col">Usuario</th>
              <th scope="col">Tipo</th>
              <th scope="col">Descripcion</th>
              <th scope="col">IP</th>
              <th scope="col">Entidad</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="activities.data.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay actividad registrada.</td>
            </tr>
            <tr v-for="activity in activities.data" :key="activity.id">
              <td class="text-muted">{{ activity.created_at }}</td>
              <td>
                <div v-if="activity.user">
                  <div class="fw-semibold">{{ activity.user.name }}</div>
                  <div class="text-muted small">{{ activity.user.email }}</div>
                </div>
                <span v-else class="text-muted">-</span>
              </td>
              <td class="fw-semibold">{{ activity.type }}</td>
              <td class="text-muted">{{ activity.description || '-' }}</td>
              <td class="text-muted">{{ activity.ip_address || '-' }}</td>
              <td class="text-muted">
                <span v-if="activity.subject_type">{{ activity.subject_type }} #{{ activity.subject_id }}</span>
                <span v-else>-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ activities.data.length }} de {{ activities.total }} registros
        </div>
        <Pagination :links="activities.links" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  activities: {
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

const user = ref(props.filters.user ?? '')
const type = ref(props.filters.type ?? '')
const dateFrom = ref(props.filters.date_from ?? '')
const dateTo = ref(props.filters.date_to ?? '')

const breadcrumbs = [
  { label: 'Actividad' },
  { label: 'Registros', active: true },
]

const submitSearch = () => {
  router.get(
    '/admin/activity',
    {
      user: user.value,
      type: type.value,
      date_from: dateFrom.value,
      date_to: dateTo.value,
    },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  user.value = ''
  type.value = ''
  dateFrom.value = ''
  dateTo.value = ''
  submitSearch()
}
</script>

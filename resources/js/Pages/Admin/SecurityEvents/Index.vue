<template>
  <AdminLayout>
    <Head title="Eventos de seguridad" />

    <PageHeader :title="'Eventos de seguridad'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-3">
            <label class="form-label">Tipo</label>
            <select v-model="eventType" class="form-select">
              <option value="">Todos</option>
              <option v-for="item in types" :key="item" :value="item">{{ item }}</option>
            </select>
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label">Usuario ID</label>
            <input v-model="userId" type="number" class="form-control" placeholder="ID" />
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
              <th scope="col">Usuario</th>
              <th scope="col">Tipo</th>
              <th scope="col">IP</th>
              <th scope="col">Descripcion</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="events.data.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay eventos registrados.</td>
            </tr>
            <tr v-for="event in events.data" :key="event.id">
              <td class="text-muted">{{ event.created_at }}</td>
              <td>
                <div v-if="event.user">
                  <div class="fw-semibold">{{ event.user.name }}</div>
                  <div class="text-muted small">{{ event.user.email }}</div>
                </div>
                <span v-else class="text-muted">-</span>
              </td>
              <td class="fw-semibold">{{ event.event_type }}</td>
              <td class="text-muted">{{ event.ip_address || '-' }}</td>
              <td class="text-muted">{{ event.description || '-' }}</td>
              <td class="text-end">
                <Link :href="`/admin/security-events/${event.id}`" class="btn btn-sm btn-outline-primary">Ver</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ events.data.length }} de {{ events.total }} registros</div>
        <Pagination :links="events.links" />
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
  events: {
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

const eventType = ref(props.filters.event_type ?? '')
const userId = ref(props.filters.user_id ?? '')
const dateFrom = ref(props.filters.date_from ?? '')
const dateTo = ref(props.filters.date_to ?? '')

const breadcrumbs = [
  { label: 'Eventos de seguridad', active: true },
]

const submitSearch = () => {
  router.get(
    '/admin/security-events',
    {
      event_type: eventType.value,
      user_id: userId.value,
      date_from: dateFrom.value,
      date_to: dateTo.value,
    },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  eventType.value = ''
  userId.value = ''
  dateFrom.value = ''
  dateTo.value = ''
  submitSearch()
}
</script>

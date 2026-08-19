<template>
  <AdminLayout>
    <Head title="Automatizaciones" />

    <PageHeader :title="'Automatizaciones'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input v-model="search" type="text" class="form-control" placeholder="Nombre o evento" />
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
              <th scope="col">Nombre</th>
              <th scope="col">Evento</th>
              <th scope="col">Accion</th>
              <th scope="col">Activo</th>
              <th scope="col">Ultima ejecucion</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="automations.data.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay automatizaciones.</td>
            </tr>
            <tr v-for="automation in automations.data" :key="automation.id">
              <td class="fw-semibold">{{ automation.name }}</td>
              <td class="text-muted">{{ automation.event_key }}</td>
              <td class="text-muted">{{ automation.action_key }}</td>
              <td>
                <span v-if="automation.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </td>
              <td class="text-muted">
                <div v-if="automation.last_run">
                  {{ automation.last_run.status }} - {{ automation.last_run.executed_at }}
                </div>
                <div v-else>-</div>
              </td>
              <td class="text-end">
                <Link :href="`/admin/automations/${automation.id}`" class="btn btn-sm btn-outline-primary">Ver</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ automations.data.length }} de {{ automations.total }} registros</div>
        <Pagination :links="automations.links" />
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
  automations: {
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
  { label: 'Automatizaciones', active: true },
]

const submitSearch = () => {
  router.get('/admin/automations', { search: search.value }, { preserveState: true, replace: true })
}

const clearFilters = () => {
  search.value = ''
  submitSearch()
}
</script>

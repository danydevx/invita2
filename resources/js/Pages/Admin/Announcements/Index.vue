<template>
  <AdminLayout>
    <Head title="Anuncios" />

    <PageHeader :title="'Anuncios'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <Link href="/admin/announcements/create" class="btn btn-primary">Nuevo anuncio</Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input v-model="search" type="text" class="form-control" placeholder="Titulo o mensaje" />
          </div>
          <div class="col-12 col-md-3">
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
              <th scope="col">Titulo</th>
              <th scope="col">Tipo</th>
              <th scope="col">Audiencia</th>
              <th scope="col">Prioridad</th>
              <th scope="col">Activo</th>
              <th scope="col">Fechas</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="announcements.data.length === 0">
              <td colspan="7" class="text-center text-muted py-4">No hay anuncios.</td>
            </tr>
            <tr v-for="announcement in announcements.data" :key="announcement.id">
              <td class="fw-semibold">{{ announcement.title }}</td>
              <td class="text-muted">{{ announcement.type }}</td>
              <td class="text-muted">{{ announcement.audience }}</td>
              <td class="text-muted">{{ announcement.priority || 'normal' }}</td>
              <td>
                <span v-if="announcement.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </td>
              <td class="text-muted">
                <div>Inicio: {{ announcement.starts_at || '-' }}</div>
                <div>Fin: {{ announcement.ends_at || '-' }}</div>
              </td>
              <td class="text-end">
                <Link :href="`/admin/announcements/${announcement.id}/edit`" class="btn btn-sm btn-outline-primary">
                  Editar
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ announcements.data.length }} de {{ announcements.total }} registros</div>
        <Pagination :links="announcements.links" />
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
  announcements: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')

const breadcrumbs = [
  { label: 'Anuncios', active: true },
]

const submitSearch = () => {
  router.get('/admin/announcements', { search: search.value, status: status.value }, { preserveState: true, replace: true })
}

const clearFilters = () => {
  search.value = ''
  status.value = ''
  submitSearch()
}
</script>

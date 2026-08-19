<template>
  <AdminLayout>
    <Head title="Invitaciones" />

    <PageHeader :title="'Invitaciones'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <Link href="/admin/invitations/create" class="btn btn-primary">Nueva invitacion</Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar email</label>
            <input v-model="search" type="text" class="form-control" placeholder="email@dominio.com" />
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label">Estado</label>
            <select v-model="status" class="form-select">
              <option value="">Todos</option>
              <option value="pending">pending</option>
              <option value="accepted">accepted</option>
              <option value="expired">expired</option>
              <option value="revoked">revoked</option>
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
              <th scope="col">Email</th>
              <th scope="col">Estado</th>
              <th scope="col">Expira</th>
              <th scope="col">Invitado por</th>
              <th scope="col">Fecha</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="invitations.data.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay invitaciones.</td>
            </tr>
            <tr v-for="invite in invitations.data" :key="invite.id">
              <td class="fw-semibold">{{ invite.email }}</td>
              <td class="text-muted">{{ invite.status }}</td>
              <td class="text-muted">{{ invite.expires_at || '-' }}</td>
              <td class="text-muted">
                <span v-if="invite.invited_by">{{ invite.invited_by.name }}</span>
                <span v-else>-</span>
              </td>
              <td class="text-muted">{{ invite.created_at }}</td>
              <td class="text-end">
                <Link :href="`/admin/invitations/${invite.id}`" class="btn btn-sm btn-outline-primary">
                  Ver
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">Mostrando {{ invitations.data.length }} de {{ invitations.total }} registros</div>
        <Pagination :links="invitations.links" />
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
  invitations: {
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
  { label: 'Invitaciones', active: true },
]

const submitSearch = () => {
  router.get('/admin/invitations', { search: search.value, status: status.value }, { preserveState: true, replace: true })
}

const clearFilters = () => {
  search.value = ''
  status.value = ''
  submitSearch()
}
</script>

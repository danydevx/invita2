<template>
  <AdminLayout>
    <Head title="Suscripciones" />

    <PageHeader title="Suscripciones" :breadcrumbs="breadcrumbs" backHref="/admin" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="doSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input
              v-model="searchQuery"
              type="text"
              class="form-control"
              placeholder="Nombre o email del usuario"
            />
          </div>
          <div class="col-12 col-md-2 d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit">Buscar</button>
            <button class="btn btn-outline-secondary" type="button" @click="clearSearch">Limpiar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Usuario</th>
              <th scope="col">Plan</th>
              <th scope="col">Estado</th>
              <th scope="col">Inicio</th>
              <th scope="col">Fin</th>
              <th scope="col">Trial</th>
              <th scope="col">Precio</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="subscriptions.data.length === 0">
              <td colspan="8" class="text-center text-muted py-4">No hay suscripciones.</td>
            </tr>
            <tr v-for="sub in subscriptions.data" :key="sub.id">
              <td class="text-muted">{{ sub.id }}</td>
              <td>
                <Link :href="`/admin/users/${sub.user_id}/subscriptions`" class="text-decoration-none">
                  {{ sub.user?.name || 'N/A' }}
                  <span class="text-muted small">{{ sub.user?.email }}</span>
                </Link>
              </td>
              <td>{{ sub.plan?.name || sub.plan_id }}</td>
              <td>
                <span :class="statusClass(sub.status)">{{ sub.status }}</span>
              </td>
              <td>{{ sub.starts_at || '-' }}</td>
              <td>{{ sub.ends_at || '-' }}</td>
              <td>{{ sub.trial_ends_at || '-' }}</td>
              <td>{{ sub.price ? '$' + sub.price : '-' }}</td>
              <td class="text-end">
                <Link :href="`/admin/users/${sub.user_id}/subscriptions`" class="btn btn-sm btn-outline-primary">
                  Editar
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="subscriptions.links" class="d-flex justify-content-center mt-4">
        <Pagination :links="subscriptions.links" />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const props = defineProps({
  subscriptions: {
    type: Object,
    default: () => ({ data: [], links: [] }),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const searchQuery = ref(props.filters.search || '')

const breadcrumbs = [
  { label: 'Admin', href: '/admin' },
  { label: 'Comercial', href: '/admin' },
  { label: 'Suscripciones', active: true },
]

const statusClass = (status) => {
  const classes = {
    'active': 'badge bg-success',
    'trial': 'badge bg-info',
    'pending': 'badge bg-warning',
    'expired': 'badge bg-secondary',
    'canceled': 'badge bg-danger',
  }
  return classes[status] || 'badge bg-secondary'
}

const doSearch = () => {
  router.get('/admin/subscriptions', { search: searchQuery.value }, { preserveState: true })
}

const clearSearch = () => {
  searchQuery.value = ''
  router.get('/admin/subscriptions', {}, { preserveState: true })
}
</script>

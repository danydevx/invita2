<template>
  <AdminLayout>
    <Head title="Cupones" />

    <PageHeader :title="'Cupones'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <Link href="/admin/coupons/create" class="btn btn-primary">Crear cupon</Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-6">
            <label class="form-label">Buscar</label>
            <input
              v-model="search"
              type="text"
              class="form-control"
              placeholder="Codigo o nombre"
            />
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Estado</label>
            <select v-model="status" class="form-select">
              <option value="">Todos</option>
              <option value="active">Activo</option>
              <option value="inactive">Inactivo</option>
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
              <th scope="col">Codigo</th>
              <th scope="col">Nombre</th>
              <th scope="col">Tipo</th>
              <th scope="col">Valor</th>
              <th scope="col">Uso</th>
              <th scope="col">Estado</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="coupons.data.length === 0">
              <td colspan="7" class="text-center text-muted py-4">No hay cupones registrados.</td>
            </tr>
            <tr v-for="coupon in coupons.data" :key="coupon.id">
              <td class="fw-semibold">{{ coupon.code }}</td>
              <td>{{ coupon.name }}</td>
              <td class="text-muted">{{ coupon.type }}</td>
              <td>{{ formatValue(coupon) }}</td>
              <td class="text-muted">{{ coupon.used_count }} / {{ coupon.usage_limit || 'sin limite' }}</td>
              <td>
                <span v-if="coupon.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </td>
              <td class="text-end">
                <Link :href="`/admin/coupons/${coupon.id}/edit`" class="btn btn-sm btn-outline-primary">
                  Editar
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ coupons.data.length }} de {{ coupons.total }} registros
        </div>
        <Pagination :links="coupons.links" />
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
  coupons: {
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
  { label: 'Cupones' },
  { label: 'Registros', active: true },
]

const submitSearch = () => {
  router.get(
    '/admin/coupons',
    { search: search.value, status: status.value },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  search.value = ''
  status.value = ''
  submitSearch()
}

const formatValue = (coupon) => {
  if (coupon.type === 'percent') {
    return `${coupon.value}%`
  }
  return coupon.value
}
</script>

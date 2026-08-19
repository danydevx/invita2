<template>
  <AdminLayout>
    <Head title="Pagos" />

    <PageHeader :title="'Pagos'" :breadcrumbs="breadcrumbs" backHref="/dashboard">
      <template #actions>
        <Link href="/admin/payments/create" class="btn btn-primary">Registrar pago</Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-4">
            <label class="form-label">Buscar</label>
            <input
              v-model="search"
              type="text"
              class="form-control"
              placeholder="Usuario, referencia o descripcion"
            />
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Estado</label>
            <select v-model="status" class="form-select">
              <option value="">Todos</option>
              <option v-for="item in statuses" :key="item" :value="item">{{ item }}</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label">Proveedor</label>
            <select v-model="provider" class="form-select">
              <option value="">Todos</option>
              <option v-for="item in providers" :key="item" :value="item">{{ item }}</option>
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
              <th scope="col">Usuario</th>
              <th scope="col">Plan</th>
              <th scope="col">Monto</th>
              <th scope="col">Estado</th>
              <th scope="col">Proveedor</th>
              <th scope="col">Referencia</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="payments.data.length === 0">
              <td colspan="8" class="text-center text-muted py-4">No hay pagos registrados.</td>
            </tr>
            <tr v-for="payment in payments.data" :key="payment.id">
              <td class="text-muted">{{ payment.created_at }}</td>
              <td>
                <div v-if="payment.user">
                  <div class="fw-semibold">{{ payment.user.name }}</div>
                  <div class="text-muted small">{{ payment.user.email }}</div>
                </div>
                <span v-else class="text-muted">-</span>
              </td>
              <td>{{ payment.plan?.name || '-' }}</td>
              <td class="fw-semibold">{{ formatAmount(payment.amount, payment.currency) }}</td>
              <td>
                <span class="badge" :class="statusClass(payment.status)">{{ payment.status }}</span>
              </td>
              <td>{{ payment.provider || '-' }}</td>
              <td class="text-muted">{{ payment.provider_reference || '-' }}</td>
              <td class="text-end">
                <Link :href="`/admin/payments/${payment.id}/edit`" class="btn btn-sm btn-outline-primary">
                  Editar
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ payments.data.length }} de {{ payments.total }} registros
        </div>
        <Pagination :links="payments.links" />
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
  payments: {
    type: Object,
    required: true,
  },
  providers: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const statuses = ['pending', 'paid', 'failed', 'canceled', 'refunded']

const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')
const provider = ref(props.filters.provider ?? '')
const dateFrom = ref(props.filters.date_from ?? '')
const dateTo = ref(props.filters.date_to ?? '')

const breadcrumbs = [
  { label: 'Pagos' },
  { label: 'Registros', active: true },
]

const submitSearch = () => {
  router.get(
    '/admin/payments',
    {
      search: search.value,
      status: status.value,
      provider: provider.value,
      date_from: dateFrom.value,
      date_to: dateTo.value,
    },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  search.value = ''
  status.value = ''
  provider.value = ''
  dateFrom.value = ''
  dateTo.value = ''
  submitSearch()
}

const formatAmount = (amount, currency) => {
  if (amount === null || amount === undefined) return '-'
  const value = Number(amount)
  if (Number.isNaN(value)) return String(amount)
  const formatted = value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  return currency ? `${formatted} ${currency.toUpperCase()}` : formatted
}

const statusClass = (value) => {
  if (value === 'paid') return 'text-bg-success'
  if (value === 'pending') return 'text-bg-warning'
  if (value === 'failed') return 'text-bg-danger'
  if (value === 'refunded') return 'text-bg-secondary'
  if (value === 'canceled') return 'text-bg-light border'
  return 'text-bg-secondary'
}
</script>

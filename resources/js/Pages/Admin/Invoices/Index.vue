<template>
  <AdminLayout>
    <Head title="Comprobantes" />

    <PageHeader :title="'Comprobantes'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitSearch">
          <div class="col-12 col-md-4">
            <label class="form-label">Buscar</label>
            <input
              v-model="search"
              type="text"
              class="form-control"
              placeholder="Numero, referencia o usuario"
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
            <label class="form-label">Tipo</label>
            <select v-model="type" class="form-select">
              <option value="">Todos</option>
              <option v-for="item in types" :key="item" :value="item">{{ item }}</option>
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
              <th scope="col">Numero</th>
              <th scope="col">Usuario</th>
              <th scope="col">Tipo</th>
              <th scope="col">Monto</th>
              <th scope="col">Estado</th>
              <th scope="col">Fecha</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="invoices.data.length === 0">
              <td colspan="7" class="text-center text-muted py-4">No hay comprobantes registrados.</td>
            </tr>
            <tr v-for="invoice in invoices.data" :key="invoice.id">
              <td class="fw-semibold">{{ invoice.number || invoice.id }}</td>
              <td>
                <div v-if="invoice.user">
                  <div class="fw-semibold">{{ invoice.user.name }}</div>
                  <div class="text-muted small">{{ invoice.user.email }}</div>
                </div>
                <span v-else class="text-muted">-</span>
              </td>
              <td class="text-muted">{{ invoice.type }}</td>
              <td class="fw-semibold">{{ formatAmount(invoice.amount, invoice.currency) }}</td>
              <td>
                <span class="badge" :class="statusClass(invoice.status)">{{ invoice.status }}</span>
              </td>
              <td class="text-muted">{{ invoice.issued_at || '-' }}</td>
              <td class="text-end">
                <Link :href="`/admin/invoices/${invoice.id}`" class="btn btn-sm btn-outline-primary">
                  Ver
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ invoices.data.length }} de {{ invoices.total }} registros
        </div>
        <Pagination :links="invoices.links" />
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
  invoices: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const statuses = ['pending', 'issued', 'paid', 'canceled']
const types = ['receipt', 'invoice']

const search = ref(props.filters.search ?? '')
const status = ref(props.filters.status ?? '')
const type = ref(props.filters.type ?? '')
const dateFrom = ref(props.filters.date_from ?? '')
const dateTo = ref(props.filters.date_to ?? '')

const breadcrumbs = [
  { label: 'Comprobantes' },
  { label: 'Registros', active: true },
]

const submitSearch = () => {
  router.get(
    '/admin/invoices',
    {
      search: search.value,
      status: status.value,
      type: type.value,
      date_from: dateFrom.value,
      date_to: dateTo.value,
    },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  search.value = ''
  status.value = ''
  type.value = ''
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
  if (value === 'issued') return 'text-bg-primary'
  if (value === 'pending') return 'text-bg-warning'
  if (value === 'canceled') return 'text-bg-secondary'
  return 'text-bg-secondary'
}
</script>

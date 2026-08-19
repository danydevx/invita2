<template>
  <AdminLayout>
    <Head title="Reportes" />

    <PageHeader :title="'Reportes'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" @submit.prevent="submitFilters">
          <div class="col-12 col-md-3">
            <label class="form-label">Desde</label>
            <input v-model="dateFrom" type="date" class="form-control" />
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label">Hasta</label>
            <input v-model="dateTo" type="date" class="form-control" />
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label">Plan</label>
            <select v-model="planId" class="form-select">
              <option value="">Todos</option>
              <option v-for="plan in plans" :key="plan.id" :value="plan.id">{{ plan.name }}</option>
            </select>
          </div>
          <div class="col-12 col-md-3 d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit">Aplicar</button>
            <button class="btn btn-outline-secondary" type="button" @click="clearFilters">Limpiar</button>
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label">Pagos</label>
            <select v-model="paymentStatus" class="form-select">
              <option value="">Todos</option>
              <option value="paid">paid</option>
              <option value="failed">failed</option>
              <option value="pending">pending</option>
            </select>
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label">Tickets</label>
            <select v-model="ticketStatus" class="form-select">
              <option value="">Todos</option>
              <option value="open">open</option>
              <option value="pending">pending</option>
              <option value="answered">answered</option>
              <option value="closed">closed</option>
            </select>
          </div>
        </form>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-3">Usuarios</h2>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Registrados</span>
              <span class="fw-semibold">{{ reports.users.total }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Activos</span>
              <span class="fw-semibold">{{ reports.users.active }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Inactivos</span>
              <span class="fw-semibold">{{ reports.users.inactive }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Verificados</span>
              <span class="fw-semibold">{{ reports.users.verified }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between">
              <span class="text-muted">Sin verificar</span>
              <span class="fw-semibold">{{ reports.users.unverified }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-3">Suscripciones</h2>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Total</span>
              <span class="fw-semibold">{{ reports.subscriptions.total }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Activas</span>
              <span class="fw-semibold">{{ reports.subscriptions.active }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Trial</span>
              <span class="fw-semibold">{{ reports.subscriptions.trial }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Vencidas</span>
              <span class="fw-semibold">{{ reports.subscriptions.expired }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between">
              <span class="text-muted">Canceladas</span>
              <span class="fw-semibold">{{ reports.subscriptions.canceled }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-3">Pagos</h2>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Total</span>
              <span class="fw-semibold">{{ reports.payments.total }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Exitosos</span>
              <span class="fw-semibold">{{ reports.payments.paid }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Fallidos</span>
              <span class="fw-semibold">{{ reports.payments.failed }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between">
              <span class="text-muted">Monto total</span>
              <span class="fw-semibold">{{ formatAmount(reports.payments.amount_total) }}</span>
            </div>

            <div class="mt-3" v-if="reports.payments.by_plan.length">
              <div class="fw-semibold mb-2">Monto por plan</div>
              <div class="d-flex flex-column gap-1">
                <div v-for="row in reports.payments.by_plan" :key="row.plan" class="d-flex justify-content-between">
                  <span class="text-muted">{{ row.plan }}</span>
                  <span class="fw-semibold">{{ formatAmount(row.total) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-3">Soporte</h2>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Total</span>
              <span class="fw-semibold">{{ reports.tickets.total }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Abiertos</span>
              <span class="fw-semibold">{{ reports.tickets.open }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Pendientes</span>
              <span class="fw-semibold">{{ reports.tickets.pending }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between">
              <span class="text-muted">Cerrados</span>
              <span class="fw-semibold">{{ reports.tickets.closed }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  reports: {
    type: Object,
    required: true,
  },
  plans: {
    type: Array,
    default: () => [],
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const dateFrom = ref(props.filters.date_from || '')
const dateTo = ref(props.filters.date_to || '')
const planId = ref(props.filters.plan_id || '')
const paymentStatus = ref(props.filters.payment_status || '')
const ticketStatus = ref(props.filters.ticket_status || '')

const breadcrumbs = [
  { label: 'Reportes', active: true },
]

const submitFilters = () => {
  router.get(
    '/admin/reports',
    {
      date_from: dateFrom.value,
      date_to: dateTo.value,
      plan_id: planId.value,
      payment_status: paymentStatus.value,
      ticket_status: ticketStatus.value,
    },
    { preserveState: true, replace: true, preserveScroll: true }
  )
}

const clearFilters = () => {
  dateFrom.value = ''
  dateTo.value = ''
  planId.value = ''
  paymentStatus.value = ''
  ticketStatus.value = ''
  submitFilters()
}

const formatAmount = (value) => {
  if (value === null || value === undefined) return '-'
  const amount = Number(value)
  if (Number.isNaN(amount)) return String(value)
  return `$${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
}
</script>

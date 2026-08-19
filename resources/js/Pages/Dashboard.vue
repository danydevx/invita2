<template>
  <AdminLayout>
    <Head title="Dashboard" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Resumen operativo del SaaS.</p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <Link href="/admin/users" class="btn btn-outline-secondary btn-sm">Usuarios</Link>
        <Link href="/admin/plans" class="btn btn-outline-secondary btn-sm">Planes</Link>
        <Link href="/admin/payments" class="btn btn-outline-secondary btn-sm">Pagos</Link>
        <Link href="/admin/support" class="btn btn-outline-secondary btn-sm">Soporte</Link>
        <Link href="/admin/settings" class="btn btn-outline-secondary btn-sm">Settings</Link>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-2">Usuarios</h2>
            <div class="display-6 fw-semibold">{{ metrics.users.total }}</div>
            <div class="text-muted small">Activos: {{ metrics.users.active }}</div>
            <div class="text-muted small">Nuevos 7d: {{ metrics.users.recent }}</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-2">Suscripciones</h2>
            <div class="display-6 fw-semibold">{{ metrics.subscriptions.total }}</div>
            <div class="text-muted small">Activas: {{ metrics.subscriptions.active }}</div>
            <div class="text-muted small">Canceladas: {{ metrics.subscriptions.canceled }}</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-2">Pagos</h2>
            <div class="display-6 fw-semibold">{{ metrics.payments.paid }}</div>
            <div class="text-muted small">Fallidos: {{ metrics.payments.failed }}</div>
            <div class="text-muted small">Total: {{ formatAmount(metrics.payments.amount_total) }}</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-2">Soporte</h2>
            <div class="display-6 fw-semibold">{{ metrics.tickets.open }}</div>
            <div class="text-muted small">Pendientes: {{ metrics.tickets.pending }}</div>
            <div class="text-muted small">Cerrados: {{ metrics.tickets.closed }}</div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-3">Resumen general</h2>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Usuarios verificados</span>
              <span class="fw-semibold">{{ metrics.users.verified }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Usuarios sin verificar</span>
              <span class="fw-semibold">{{ metrics.users.unverified }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Planes activos</span>
              <span class="fw-semibold">{{ metrics.plans.active }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Plan mas usado</span>
              <span class="fw-semibold">{{ metrics.plans.top_plan || '-' }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between">
              <span class="text-muted">Ingresos mes</span>
              <span class="fw-semibold">{{ formatAmount(metrics.payments.amount_month) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-3">Tickets recientes</h2>
            <div v-if="recentTickets.length === 0" class="text-muted">No hay tickets recientes.</div>
            <div v-else class="list-group list-group-flush">
              <div v-for="ticket in recentTickets" :key="ticket.id" class="list-group-item px-0">
                <div class="d-flex align-items-start justify-content-between gap-2">
                  <div>
                    <div class="fw-semibold">{{ ticket.subject }}</div>
                    <div class="text-muted small">{{ ticket.user?.name || 'Usuario' }}</div>
                  </div>
                  <span class="badge" :class="statusClass(ticket.status)">{{ ticket.status }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Actividad reciente</h2>
            <div v-if="recentActivity.length === 0" class="text-muted">No hay actividad reciente.</div>
            <div v-else class="list-group list-group-flush">
              <div v-for="item in recentActivity" :key="item.id" class="list-group-item px-0">
                <div class="d-flex align-items-start justify-content-between gap-2">
                  <div>
                    <div class="fw-semibold">{{ item.description || item.type }}</div>
                    <div class="text-muted small">{{ item.actor?.name || 'Sistema' }}</div>
                  </div>
                  <div class="text-muted small">{{ item.created_at }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  metrics: {
    type: Object,
    required: true,
  },
  recentTickets: {
    type: Array,
    default: () => [],
  },
  recentActivity: {
    type: Array,
    default: () => [],
  },
})

const formatAmount = (value) => {
  if (value === null || value === undefined) return '-'
  const amount = Number(value)
  if (Number.isNaN(amount)) return String(value)
  return `$${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
}

const statusClass = (value) => {
  if (value === 'open') return 'text-bg-success'
  if (value === 'pending') return 'text-bg-warning'
  if (value === 'answered') return 'text-bg-primary'
  if (value === 'closed') return 'text-bg-secondary'
  return 'text-bg-secondary'
}
</script>

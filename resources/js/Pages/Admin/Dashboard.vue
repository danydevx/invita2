<template>
  <AdminLayout>
    <Head title="Dashboard" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Panel de control</h1>
        <p class="text-muted mb-0">Resumen del sistema.</p>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                <i class="bi bi-people fs-4 text-primary"></i>
              </div>
              <div>
                <div class="text-muted small">Total usuarios</div>
                <div class="fs-4 fw-semibold">{{ stats.totalUsers }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-success bg-opacity-10 rounded-3 p-3">
                <i class="bi bi-credit-card fs-4 text-success"></i>
              </div>
              <div>
                <div class="text-muted small">Suscripciones activas</div>
                <div class="fs-4 fw-semibold">{{ stats.activeSubscriptions }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                <i class="bi bi-question-circle fs-4 text-warning"></i>
              </div>
              <div>
                <div class="text-muted small">Tickets abiertos</div>
                <div class="fs-4 fw-semibold">{{ stats.openTickets }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3">
              <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
              </div>
              <div>
                <div class="text-muted small">Errores (7 dias)</div>
                <div class="fs-4 fw-semibold">{{ stats.recentErrors }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
              <h2 class="h6 mb-0">Tickets abiertos</h2>
              <Link href="/admin/support" class="btn btn-sm btn-outline-secondary">Ver todos</Link>
            </div>
            <div v-if="openTickets.length" class="list-group list-group-flush">
              <div
                v-for="ticket in openTickets"
                :key="ticket.id"
                class="list-group-item px-0"
              >
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                  <div>
                    <div class="fw-semibold">{{ ticket.subject }}</div>
                    <div class="text-muted small">{{ ticket.user?.name || 'Usuario' }}</div>
                  </div>
                  <span class="badge" :class="ticketStatusClass(ticket.status)">
                    {{ ticket.status }}
                  </span>
                </div>
              </div>
            </div>
            <div v-else class="text-muted small">No hay tickets abiertos.</div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
              <h2 class="h6 mb-0">Errores recientes</h2>
              <Link href="/admin/system-errors" class="btn btn-sm btn-outline-secondary">Ver todos</Link>
            </div>
            <div v-if="recentErrors.length" class="list-group list-group-flush">
              <div
                v-for="error in recentErrors"
                :key="error.id"
                class="list-group-item px-0"
              >
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                  <div>
                    <div class="fw-semibold">{{ error.type }}</div>
                    <div class="text-muted small text-truncate" style="max-width: 300px;">
                      {{ error.message }}
                    </div>
                  </div>
                  <span class="badge text-bg-danger">{{ formatDate(error.created_at) }}</span>
                </div>
              </div>
            </div>
            <div v-else class="text-muted small">No hay errores recientes.</div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
              <h2 class="h6 mb-0">Actividad reciente</h2>
              <Link href="/admin/activity" class="btn btn-sm btn-outline-secondary">Ver todos</Link>
            </div>
            <div v-if="recentActivity.length" class="list-group list-group-flush">
              <div
                v-for="(activity, index) in recentActivity"
                :key="`${activity.type}-${activity.created_at}-${index}`"
                class="list-group-item px-0"
              >
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                  <div>
                    <div class="fw-semibold">{{ activity.description || activity.type }}</div>
                    <div v-if="activity.description" class="text-muted small">{{ activity.type }}</div>
                  </div>
                  <span class="text-muted small">{{ formatDate(activity.created_at) }}</span>
                </div>
              </div>
            </div>
            <div v-else class="text-muted small">No hay actividad reciente.</div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const stats = computed(() => page.props.stats || {})
const recentActivity = computed(() => page.props.recentActivity || [])
const recentErrors = computed(() => page.props.recentErrors || [])
const openTickets = computed(() => page.props.openTickets || [])

const ticketStatusClass = (status) => {
  const classes = {
    open: 'text-bg-warning',
    'in-progress': 'text-bg-info',
    pending: 'text-bg-secondary',
    closed: 'text-bg-success',
    resolved: 'text-bg-success',
  }
  return classes[status] || 'text-bg-secondary'
}

const formatDate = (value) => {
  if (!value) return '-'
  const parsed = new Date(value)
  if (Number.isNaN(parsed.getTime())) {
    return value
  }
  return parsed.toLocaleDateString()
}
</script>

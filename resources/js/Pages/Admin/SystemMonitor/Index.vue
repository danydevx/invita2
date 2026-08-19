<template>
  <AdminLayout>
    <Head title="Monitor" />

    <PageHeader :title="'Monitor del sistema'" :breadcrumbs="breadcrumbs" backHref="/dashboard" />

    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">System Health</h3>
            <div class="d-flex justify-content-between text-muted">
              <span>Database</span>
              <span class="badge text-bg-success">ok</span>
            </div>
            <div class="d-flex justify-content-between text-muted mt-2">
              <span>Queue</span>
              <span class="badge text-bg-success">ok</span>
            </div>
            <div class="d-flex justify-content-between text-muted mt-2">
              <span>Cache</span>
              <span class="badge text-bg-success">ok</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Metricas</h3>
            <div class="row g-3">
              <div class="col-6 col-md-3">
                <div class="text-muted small">Usuarios</div>
                <div class="h5 mb-0">{{ metrics.users_total }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="text-muted small">Nuevos 7d</div>
                <div class="h5 mb-0">{{ metrics.users_last_7_days }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="text-muted small">Suscripciones activas</div>
                <div class="h5 mb-0">{{ metrics.subscriptions_active }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="text-muted small">Suscripciones trial</div>
                <div class="h5 mb-0">{{ metrics.subscriptions_trial }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="text-muted small">Canceladas</div>
                <div class="h5 mb-0">{{ metrics.subscriptions_canceled }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="text-muted small">Pagos hoy</div>
                <div class="h5 mb-0">{{ metrics.payments_today }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="text-muted small">Fallidos hoy</div>
                <div class="h5 mb-0">{{ metrics.payments_failed_today }}</div>
              </div>
              <div class="col-6 col-md-3">
                <div class="text-muted small">Ingreso mes</div>
                <div class="h5 mb-0">{{ formatAmount(metrics.revenue_month) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Queues</h3>
            <div class="d-flex justify-content-between text-muted">
              <span>Pendientes</span>
              <span class="fw-semibold">{{ metrics.jobs_pending }}</span>
            </div>
            <div class="d-flex justify-content-between text-muted mt-2">
              <span>Fallidos</span>
              <span class="fw-semibold">{{ metrics.jobs_failed }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Errores</h3>
            <div class="d-flex justify-content-between text-muted">
              <span>Ultimas 24h</span>
              <span class="fw-semibold">{{ metrics.errors_last_24h }}</span>
            </div>
            <div class="d-flex justify-content-between text-muted mt-2">
              <span>No resueltos</span>
              <span class="fw-semibold">{{ metrics.errors_unresolved }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Tickets</h3>
            <div class="d-flex justify-content-between text-muted">
              <span>Abiertos</span>
              <span class="fw-semibold">{{ metrics.tickets_open }}</span>
            </div>
            <div class="d-flex justify-content-between text-muted mt-2">
              <span>Pendientes</span>
              <span class="fw-semibold">{{ metrics.tickets_pending }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Errores recientes</h3>
            <div v-if="recentErrors.length === 0" class="text-muted">Sin errores recientes.</div>
            <div v-else class="list-group list-group-flush">
              <div v-for="error in recentErrors" :key="error.id" class="list-group-item">
                <div class="d-flex justify-content-between">
                  <span class="fw-semibold">{{ error.type }}</span>
                  <span class="text-muted small">{{ error.created_at }}</span>
                </div>
                <div class="text-muted small">{{ error.message }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Webhooks fallidos</h3>
            <div v-if="webhookFailures.length === 0" class="text-muted">Sin fallos recientes.</div>
            <div v-else class="list-group list-group-flush">
              <div v-for="delivery in webhookFailures" :key="delivery.id" class="list-group-item">
                <div class="d-flex justify-content-between">
                  <span class="fw-semibold">{{ delivery.event }}</span>
                  <span class="text-muted small">{{ delivery.failed_at }}</span>
                </div>
                <div class="text-muted small">{{ delivery.error_message || delivery.response_status }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Actividad reciente</h3>
            <div v-if="recentActivity.length === 0" class="text-muted">Sin actividad reciente.</div>
            <div v-else class="list-group list-group-flush">
              <div v-for="item in recentActivity" :key="item.id" class="list-group-item">
                <div class="d-flex justify-content-between">
                  <span class="fw-semibold">{{ item.type }}</span>
                  <span class="text-muted small">{{ item.created_at }}</span>
                </div>
                <div class="text-muted small">{{ item.description || '-' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  metrics: {
    type: Object,
    required: true,
  },
  recentErrors: {
    type: Array,
    default: () => [],
  },
  webhookFailures: {
    type: Array,
    default: () => [],
  },
  recentActivity: {
    type: Array,
    default: () => [],
  },
})

const breadcrumbs = [
  { label: 'Monitor', active: true },
]

const formatAmount = (value) => {
  if (!value) return '0'
  return Number(value).toFixed(2)
}
</script>

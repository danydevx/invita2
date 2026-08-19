<template>
  <MemberLayout>
    <Head title="Mi cuenta" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Mi cuenta</h1>
        <p class="text-muted mb-0">Resumen de tu cuenta y suscripcion.</p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <Link href="/member/profile" class="btn btn-outline-secondary btn-sm">Editar perfil</Link>
        <Link href="/member/password" class="btn btn-outline-secondary btn-sm">Cambiar password</Link>
        <Link href="/member/notifications" class="btn btn-outline-secondary btn-sm">Notificaciones</Link>
        <Link href="/member/invoices" class="btn btn-outline-secondary btn-sm">Comprobantes</Link>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-3">Cuenta</h2>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Nombre</span>
              <span class="fw-semibold">{{ account.name }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Email</span>
              <span>{{ account.email }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Estado</span>
              <span :class="account.is_active ? 'text-success' : 'text-danger'">
                {{ account.is_active ? 'Activa' : 'Inactiva' }}
              </span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Email verificado</span>
              <span :class="account.email_verified_at ? 'text-success' : 'text-warning'">
                {{ account.email_verified_at ? 'Verificado' : 'Pendiente' }}
              </span>
            </div>
            <div class="d-flex flex-wrap justify-content-between">
              <span class="text-muted">Registro</span>
              <span>{{ formatDate(account.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-3">Suscripcion</h2>
            <div v-if="subscription">
              <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
                <span class="text-muted">Plan</span>
                <span class="fw-semibold">{{ subscription.plan_name || '-' }}</span>
              </div>
              <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
                <span class="text-muted">Estado</span>
                <span>{{ subscription.status }}</span>
              </div>
              <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
                <span class="text-muted">Precio</span>
                <span>{{ formatPrice(subscription.price) }}</span>
              </div>
              <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
                <span class="text-muted">Periodo</span>
                <span>{{ subscription.billing_period || '-' }}</span>
              </div>
              <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
                <span class="text-muted">Inicio</span>
                <span>{{ formatDate(subscription.starts_at) }}</span>
              </div>
              <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
                <span class="text-muted">Termino</span>
                <span>{{ formatDate(subscription.ends_at) }}</span>
              </div>
              <div class="d-flex flex-wrap justify-content-between">
                <span class="text-muted">Trial</span>
                <span>{{ formatDate(subscription.trial_ends_at) }}</span>
              </div>
              <div class="mt-3">
                <button
                  type="button"
                  class="btn btn-primary btn-sm"
                  :disabled="!subscription.can_manage"
                  @click="openBillingPortal"
                >
                  Gestionar suscripcion
                </button>
                <div v-if="!subscription.can_manage" class="text-muted small mt-2">
                  La gestion automatica no esta disponible por ahora.
                </div>
              </div>
            </div>
            <div v-else class="text-muted">
              No tienes una suscripcion activa por ahora.
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6 mb-3">Limites del plan</h2>
            <div class="row g-2">
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="text-muted small">Max items</div>
                  <div class="fw-semibold">{{ limits.max_items ?? '-' }}</div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="text-muted small">Max requests/dia</div>
                  <div class="fw-semibold">{{ limits.max_requests_per_day ?? '-' }}</div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="text-muted small">Puede usar AI</div>
                  <div class="fw-semibold">{{ limits.can_use_ai ? 'Si' : 'No' }}</div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="text-muted small">Puede exportar</div>
                  <div class="fw-semibold">{{ limits.can_export ? 'Si' : 'No' }}</div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="border rounded-3 p-3 h-100">
                  <div class="text-muted small">Puede subir archivos</div>
                  <div class="fw-semibold">{{ limits.can_upload_files ? 'Si' : 'No' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  account: {
    type: Object,
    required: true,
  },
  subscription: {
    type: Object,
    default: null,
  },
  limits: {
    type: Object,
    default: () => ({}),
  },
})

const formatDate = (value) => {
  if (!value) return '-'
  const parsed = new Date(value)
  if (Number.isNaN(parsed.getTime())) {
    return value
  }
  return parsed.toLocaleDateString()
}

const formatPrice = (value) => {
  if (value === null || value === undefined || value === '') return '-'
  return Number(value).toLocaleString()
}

const openBillingPortal = () => {
  router.post('/member/billing/portal', {}, {
    preserveScroll: true,
  })
}
</script>

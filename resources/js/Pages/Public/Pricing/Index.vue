<template>
  <div class="min-vh-100 bg-body-tertiary">
    <Head title="Planes y precios" />

    <nav class="navbar navbar-expand-lg bg-white border-bottom">
      <div class="container">
        <Link href="/" class="navbar-brand">Mi SaaS</Link>
        <div class="ms-auto d-flex gap-2">
          <Link v-if="!isAuthenticated" href="/login" class="btn btn-outline-secondary btn-sm">Iniciar sesion</Link>
          <Link v-if="!isAuthenticated" href="/register" class="btn btn-primary btn-sm">Crear cuenta</Link>
          <Link v-else :href="dashboardHref" class="btn btn-outline-secondary btn-sm">Ir al panel</Link>
        </div>
      </div>
    </nav>

    <section class="py-5">
      <div class="container">
        <div class="text-center mb-5">
          <h1 class="display-6 fw-semibold">Planes que crecen contigo</h1>
          <p class="text-muted mx-auto" style="max-width: 720px;">
            Elige el plan que mejor se adapta a tu equipo. Puedes conocer los limites principales ahora y conectar el
            checkout cuando estes listo.
          </p>
        </div>

        <div v-if="plans.length === 0" class="card border-0 shadow-sm">
          <div class="card-body text-center text-muted py-5">
            Por ahora no hay planes disponibles. Vuelve mas tarde.
          </div>
        </div>

        <div v-else class="row g-3">
          <div v-for="plan in plans" :key="plan.id" class="col-12 col-md-6 col-lg-4">
            <div
              class="card border-0 shadow-sm h-100"
              :class="{ 'border border-primary': plan.id === recommendedPlanId }"
            >
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-start justify-content-between">
                  <div>
                    <h2 class="h5 mb-1">{{ plan.name }}</h2>
                    <p class="text-muted mb-0">{{ plan.description || 'Plan pensado para equipos en crecimiento.' }}</p>
                  </div>
                  <span v-if="plan.id === recommendedPlanId" class="badge text-bg-primary">Recomendado</span>
                </div>

                <div class="mt-4">
                  <div class="display-6 fw-semibold">
                    {{ formatPrice(plan.price) }}
                  </div>
                  <div class="text-muted">{{ formatPeriod(plan.billing_period) }}</div>
                </div>

                <ul class="list-unstyled mt-4 mb-4">
                  <li v-for="(item, index) in buildHighlights(plan)" :key="`${plan.id}-${index}`" class="d-flex gap-2 mb-2">
                    <i class="bi bi-check-circle text-success"></i>
                    <span>{{ item }}</span>
                  </li>
                </ul>

                <div class="mt-auto">
                  <button
                    type="button"
                    class="btn btn-primary w-100"
                    :disabled="isAdmin || !plan.is_checkout_available"
                    @click="selectPlan(plan)"
                  >
                    {{ actionLabel(plan) }}
                  </button>
                  <div v-if="isAuthenticated && isMember" class="text-muted small text-center mt-2">
                    No puedes cambiar de plan desde aqui aun.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  plans: {
    type: Array,
    default: () => [],
  },
  recommendedPlanId: {
    type: Number,
    default: null,
  },
})

const page = usePage()
const isAuthenticated = computed(() => !!page.props.auth?.user)
const roles = computed(() => page.props.auth?.roles || [])
const isMember = computed(() => roles.value.includes('member'))
const isAdmin = computed(() => roles.value.includes('admin') || roles.value.includes('superadmin'))

const dashboardHref = computed(() => {
  if (isMember.value) return '/member'
  if (isAdmin.value) return '/dashboard'
  return '/member'
})

const actionLabel = (plan) => {
  if (isAdmin.value) return 'No disponible'
  if (!plan?.is_checkout_available) return 'No disponible'
  return 'Elegir plan'
}

const limitLabels = {
  max_items: 'Max items',
  max_requests_per_day: 'Max requests/dia',
  can_use_ai: 'Acceso a AI',
  can_export: 'Exportar datos',
  can_upload_files: 'Subida de archivos',
}

const formatLimitValue = (value) => {
  if (value === null || value === undefined) return null
  if (typeof value === 'boolean') return value ? 'Si' : 'No'
  return String(value)
}

const buildHighlights = (plan) => {
  const items = []
  const features = Array.isArray(plan.features) ? plan.features : []
  const limits = plan.limits && typeof plan.limits === 'object' ? plan.limits : {}

  features.forEach((feature) => {
    if (items.length >= 5) return
    if (typeof feature === 'string') {
      items.push(feature)
    } else if (feature && typeof feature.label === 'string') {
      items.push(feature.label)
    }
  })

  Object.keys(limitLabels).forEach((key) => {
    if (items.length >= 5) return
    if (!(key in limits)) return
    const value = formatLimitValue(limits[key])
    if (value === null) return
    items.push(`${limitLabels[key]}: ${value}`)
  })

  if (items.length === 0) {
    items.push('Soporte basico incluido')
    items.push('Acceso a metricas clave')
  }

  return items
}

const formatPrice = (value) => {
  if (value === null || value === undefined || value === '') return 'Consultar'
  const amount = Number(value)
  if (Number.isNaN(amount)) return String(value)
  if (amount === 0) return 'Gratis'
  return `$${amount.toLocaleString()}`
}

const formatPeriod = (value) => {
  if (!value) return 'Por mes'
  return `/${value}`
}

const selectPlan = (plan) => {
  if (isAdmin.value) return
  if (!plan.is_checkout_available) return
  router.post(`/pricing/select/${plan.id}`, {}, {
    preserveScroll: true,
  })
}
</script>

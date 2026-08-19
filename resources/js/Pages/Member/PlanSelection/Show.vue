<template>
  <MemberLayout>
    <Head title="Seleccion de plan" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Seleccion de plan</h1>
        <p class="text-muted mb-0">Confirmamos tu interes antes de habilitar el checkout.</p>
      </div>
      <Link href="/pricing" class="btn btn-outline-secondary btn-sm">Volver a planes</Link>
    </div>

    <div v-if="!plan" class="card border-0 shadow-sm">
      <div class="card-body text-center text-muted py-5">
        No hay un plan seleccionado. Elige uno desde la pagina de planes.
      </div>
    </div>

    <div v-else class="row g-3">
      <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h5 mb-2">{{ plan.name }}</h2>
            <p class="text-muted">{{ plan.description || 'Plan pensado para tu crecimiento.' }}</p>

            <div class="display-6 fw-semibold">
              {{ formatPrice(plan.price) }}
            </div>
            <div class="text-muted mb-4">{{ formatPeriod(plan.billing_period) }}</div>

            <ul class="list-unstyled">
              <li v-for="(item, index) in buildHighlights(plan)" :key="index" class="d-flex gap-2 mb-2">
                <i class="bi bi-check-circle text-success"></i>
                <span>{{ item }}</span>
              </li>
            </ul>

            <div class="border-top pt-3 mt-3">
              <div class="fw-semibold mb-2">Cupon</div>
              <div v-if="coupon" class="d-flex flex-wrap align-items-center justify-content-between">
                <div>
                  <div class="fw-semibold">{{ coupon.code }}</div>
                  <div class="text-muted small">{{ coupon.name }}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" @click="clearCoupon">
                  Quitar
                </button>
              </div>
              <form v-else class="d-flex flex-wrap gap-2" @submit.prevent="applyCoupon">
                <input
                  v-model="couponCode"
                  type="text"
                  class="form-control"
                  placeholder="Codigo de cupon"
                />
                <button type="submit" class="btn btn-outline-primary" :disabled="processingCoupon">
                  Aplicar
                </button>
              </form>
              <div v-if="coupon && price !== null" class="mt-3">
                <div class="d-flex justify-content-between text-muted">
                  <span>Subtotal</span>
                  <span>{{ formatPrice(price) }}</span>
                </div>
                <div class="d-flex justify-content-between text-muted">
                  <span>Descuento</span>
                  <span>-{{ formatPrice(coupon.discount_amount) }}</span>
                </div>
                <div class="d-flex justify-content-between fw-semibold">
                  <span>Total estimado</span>
                  <span>{{ formatPrice(totalAfterDiscount) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h3 class="h6 mb-2">Siguiente paso</h3>
            <p class="text-muted" v-if="plan.stripe_price_id">
              Estas a un paso de activar tu suscripcion. Continua al checkout seguro.
            </p>
            <p class="text-muted" v-else>
              Este plan aun no tiene checkout disponible. Te avisaremos cuando puedas finalizar el pago.
            </p>

            <div v-if="subscription" class="alert alert-light border">
              <div class="fw-semibold">Suscripcion actual</div>
              <div class="text-muted small">Plan: {{ subscription.plan_name || '-' }}</div>
              <div class="text-muted small">Estado: {{ subscription.status }}</div>
            </div>

            <button
              v-if="plan.stripe_price_id"
              type="button"
              class="btn btn-primary w-100"
              @click="goToCheckout"
            >
              Continuar a pago
            </button>
            <button
              v-else
              type="button"
              class="btn btn-outline-secondary w-100"
              @click="clearSelection"
            >
              Entendido
            </button>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  plan: {
    type: Object,
    default: null,
  },
  coupon: {
    type: Object,
    default: null,
  },
  price: {
    type: [Number, String, null],
    default: null,
  },
  subscription: {
    type: Object,
    default: null,
  },
})

const couponCode = ref('')
const processingCoupon = ref(false)

const totalAfterDiscount = computed(() => {
  if (props.price === null || !props.coupon) return props.price
  const priceValue = Number(props.price)
  if (Number.isNaN(priceValue)) return props.price
  const discountValue = Number(props.coupon.discount_amount || 0)
  return Math.max(0, priceValue - discountValue)
})

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
    if (items.length >= 6) return
    if (typeof feature === 'string') {
      items.push(feature)
    } else if (feature && typeof feature.label === 'string') {
      items.push(feature.label)
    }
  })

  Object.keys(limitLabels).forEach((key) => {
    if (items.length >= 6) return
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

const applyCoupon = () => {
  if (!couponCode.value.trim() || !props.plan) return
  processingCoupon.value = true
  router.post('/member/checkout/coupon/validate', {
    code: couponCode.value.trim(),
    plan_id: props.plan.id,
  }, {
    preserveScroll: true,
    onFinish: () => {
      processingCoupon.value = false
    },
  })
}

const clearCoupon = () => {
  router.put('/member/checkout/coupon/clear', {}, {
    preserveScroll: true,
  })
}

const clearSelection = () => {
  router.put('/member/plan-selection/clear', {}, {
    preserveScroll: true,
  })
}

const goToCheckout = () => {
  router.post(`/member/checkout/${props.plan.id}`, {}, {
    preserveScroll: true,
  })
}
</script>

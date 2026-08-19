<template>
  <MemberLayout>
    <Head title="Pago confirmado" />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
          <span class="badge text-bg-success">Exitoso</span>
          <h1 class="h4 mb-0">Pago confirmado</h1>
        </div>

        <p class="text-muted">
          Tu suscripcion fue activada correctamente. Ya puedes usar todas las funciones del plan.
        </p>

        <div class="row g-3 mt-3">
          <div class="col-12 col-md-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small">Plan</div>
              <div class="fw-semibold">{{ plan.name }}</div>
              <div class="text-muted small">{{ plan.description || '-' }}</div>
              <div class="mt-2">
                <span class="fw-semibold">{{ formatPrice(plan.price) }}</span>
                <span class="text-muted"> {{ formatPeriod(plan.billing_period) }}</span>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small">Suscripcion</div>
              <div class="fw-semibold">Estado: {{ subscription.status }}</div>
              <div class="text-muted small">Inicio: {{ subscription.starts_at || '-' }}</div>
              <div class="text-muted small">Termino: {{ subscription.ends_at || '-' }}</div>
            </div>
          </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
          <Link href="/member" class="btn btn-primary">Ir al dashboard</Link>
          <Link href="/member/account" class="btn btn-outline-secondary">Ver cuenta</Link>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

defineProps({
  plan: {
    type: Object,
    required: true,
  },
  subscription: {
    type: Object,
    required: true,
  },
})

const formatPrice = (value) => {
  if (value === null || value === undefined || value === '') return 'Consultar'
  const amount = Number(value)
  if (Number.isNaN(amount)) return String(value)
  if (amount === 0) return 'Gratis'
  return `$${amount.toLocaleString()}`
}

const formatPeriod = (value) => {
  if (!value) return ''
  return `/${value}`
}
</script>

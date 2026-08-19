<template>
  <MemberLayout>
    <Head title="Mis pagos" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Mis pagos</h1>
        <p class="text-muted mb-0">Resumen basico de tus cobros registrados.</p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <Link href="/member/account" class="btn btn-outline-secondary btn-sm">Ver cuenta</Link>
        <Link href="/member/invoices" class="btn btn-outline-secondary btn-sm">Comprobantes</Link>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Fecha</th>
              <th scope="col">Plan</th>
              <th scope="col">Monto</th>
              <th scope="col">Estado</th>
              <th scope="col">Referencia</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="payments.data.length === 0">
              <td colspan="5" class="text-center text-muted py-4">No hay pagos registrados.</td>
            </tr>
            <tr v-for="payment in payments.data" :key="payment.id">
              <td class="text-muted">{{ payment.paid_at || payment.created_at }}</td>
              <td>{{ payment.plan?.name || '-' }}</td>
              <td class="fw-semibold">{{ formatAmount(payment.amount, payment.currency) }}</td>
              <td>
                <span class="badge" :class="statusClass(payment.status)">{{ payment.status }}</span>
              </td>
              <td class="text-muted">{{ payment.provider_reference || '-' }}</td>
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
  </MemberLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import Pagination from '@/Components/Member/Pagination.vue'

const props = defineProps({
  payments: {
    type: Object,
    required: true,
  },
})

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

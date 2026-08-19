<template>
  <MemberLayout>
    <Head title="Mis comprobantes" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Mis comprobantes</h1>
        <p class="text-muted mb-0">Consulta tus comprobantes de pago.</p>
      </div>
      <Link href="/member/payments" class="btn btn-outline-secondary btn-sm">Ver pagos</Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Numero</th>
              <th scope="col">Fecha</th>
              <th scope="col">Monto</th>
              <th scope="col">Estado</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="invoices.data.length === 0">
              <td colspan="5" class="text-center text-muted py-4">No hay comprobantes registrados.</td>
            </tr>
            <tr v-for="invoice in invoices.data" :key="invoice.id">
              <td class="fw-semibold">{{ invoice.number || invoice.id }}</td>
              <td class="text-muted">{{ invoice.issued_at || invoice.paid_at || '-' }}</td>
              <td class="fw-semibold">{{ formatAmount(invoice.amount, invoice.currency) }}</td>
              <td>
                <span class="badge" :class="statusClass(invoice.status)">{{ invoice.status }}</span>
              </td>
              <td class="text-end">
                <Link :href="`/member/invoices/${invoice.id}`" class="btn btn-sm btn-outline-primary">
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
  </MemberLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import Pagination from '@/Components/Member/Pagination.vue'

const props = defineProps({
  invoices: {
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
  if (value === 'issued') return 'text-bg-primary'
  if (value === 'pending') return 'text-bg-warning'
  if (value === 'canceled') return 'text-bg-secondary'
  return 'text-bg-secondary'
}
</script>

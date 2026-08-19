<template>
  <MemberLayout>
    <Head title="Comprobante" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Comprobante</h1>
        <p class="text-muted mb-0">Detalle del comprobante de pago.</p>
      </div>
      <Link href="/member/invoices" class="btn btn-outline-secondary btn-sm">Volver</Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small">Numero</div>
              <div class="fw-semibold">{{ invoice.number || invoice.id }}</div>
              <div class="text-muted small mt-2">Tipo: {{ invoice.type }}</div>
              <div class="text-muted small">Estado: {{ invoice.status }}</div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small">Monto</div>
              <div class="fw-semibold">{{ formatAmount(invoice.amount, invoice.currency) }}</div>
              <div class="text-muted small mt-2">Emitido: {{ invoice.issued_at || '-' }}</div>
              <div class="text-muted small">Pagado: {{ invoice.paid_at || '-' }}</div>
            </div>
          </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
          <a
            v-if="invoice.file_url"
            :href="invoice.file_url"
            class="btn btn-outline-primary"
            target="_blank"
            rel="noreferrer"
          >
            Ver documento
          </a>
          <Link
            :href="`/member/invoices/${invoice.id}/download`"
            class="btn btn-primary"
          >
            Descargar
          </Link>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  invoice: {
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
</script>

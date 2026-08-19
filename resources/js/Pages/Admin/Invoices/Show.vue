<template>
  <AdminLayout>
    <Head title="Comprobante" />

    <PageHeader :title="'Comprobante'" :breadcrumbs="breadcrumbs" backHref="/admin/invoices" />

    <div class="row g-3">
      <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6 mb-3">Detalle</h2>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Numero</span>
              <span class="fw-semibold">{{ invoice.number || invoice.id }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Tipo</span>
              <span>{{ invoice.type }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Estado</span>
              <span class="badge" :class="statusClass(invoice.status)">{{ invoice.status }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Monto</span>
              <span class="fw-semibold">{{ formatAmount(invoice.amount, invoice.currency) }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Emitido</span>
              <span>{{ invoice.issued_at || '-' }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Vence</span>
              <span>{{ invoice.due_at || '-' }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between">
              <span class="text-muted">Pagado</span>
              <span>{{ invoice.paid_at || '-' }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h3 class="h6 mb-3">Relacion</h3>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Usuario</span>
              <span>{{ invoice.user?.name || '-' }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Email</span>
              <span>{{ invoice.user?.email || '-' }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Plan</span>
              <span>{{ invoice.plan?.name || '-' }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between border-bottom pb-2 mb-2">
              <span class="text-muted">Pago</span>
              <span>{{ invoice.payment?.provider_reference || '-' }}</span>
            </div>
            <div class="d-flex flex-wrap justify-content-between">
              <span class="text-muted">Referencia</span>
              <span>{{ invoice.provider_reference || '-' }}</span>
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
                :href="`/admin/invoices/${invoice.id}/download`"
                class="btn btn-primary"
              >
                Descargar
              </Link>
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
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  invoice: {
    type: Object,
    required: true,
  },
})

const breadcrumbs = [
  { label: 'Comprobantes', href: '/admin/invoices' },
  { label: `Comprobante #${props.invoice.id}`, active: true },
]

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

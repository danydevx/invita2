<template>
  <MemberLayout>
    <Head title="Mis pagos" />

    <PageHeader
      title="Mis pagos"
      :breadcrumbs="breadcrumbs"
    >
      <template #actions>
        <Link href="/member/account" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-wallet2 me-1"></i>Ver cuenta
        </Link>
        <Link href="/member/invoices" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-receipt me-1"></i>Comprobantes
        </Link>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      endpoint="/member/payments"
      :columns="columns"
      :initial-data="payments"
      search-placeholder="Buscar pagos..."
      empty-title="No hay pagos"
      empty-text="Resumen básico de tus cobros registrados."
      @updated="onDataTableUpdated"
    >
      <template #cell-paid_at="{ row }">
        <span class="text-muted">{{ row.paid_at || row.created_at }}</span>
      </template>

      <template #cell-plan="{ row }">
        <span>{{ row.plan?.name || '-' }}</span>
      </template>

      <template #cell-amount="{ row }">
        <span class="fw-semibold">{{ formatAmount(row.amount, row.currency) }}</span>
      </template>

      <template #cell-status="{ row }">
        <span class="badge" :class="statusClass(row.status)">
          {{ statusLabel(row.status) }}
        </span>
      </template>

      <template #cell-provider_reference="{ row }">
        <span class="text-muted font-monospace small">{{ row.provider_reference || '-' }}</span>
      </template>
    </BaseDataTable>
  </MemberLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'

const props = defineProps({
  payments: {
    type: Object,
    required: true,
  },
})

const dataTableRef = ref(null)

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Pagos', active: true },
])

const columns = [
  { key: 'paid_at', label: 'Fecha', sortable: true },
  { key: 'plan', label: 'Plan', sortable: false },
  { key: 'amount', label: 'Monto', sortable: true },
  { key: 'status', label: 'Estado', sortable: true },
  { key: 'provider_reference', label: 'Referencia', sortable: false },
]

const onDataTableUpdated = () => {}

const formatAmount = (amount, currency) => {
  if (amount === null || amount === undefined) return '-'
  const value = Number(amount)
  if (Number.isNaN(value)) return String(amount)
  const formatted = value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  return currency ? `${formatted} ${currency.toUpperCase()}` : formatted
}

const statusClass = (value) => {
  if (value === 'paid') return 'bg-success-subtle text-success'
  if (value === 'pending') return 'bg-warning-subtle text-warning'
  if (value === 'failed') return 'bg-danger-subtle text-danger'
  if (value === 'refunded') return 'bg-secondary-subtle text-secondary'
  if (value === 'canceled') return 'bg-light text-muted border'
  return 'bg-secondary-subtle text-secondary'
}

const statusLabel = (value) => {
  const labels = {
    paid: 'Pagado',
    pending: 'Pendiente',
    failed: 'Fallido',
    refunded: 'Reembolsado',
    canceled: 'Cancelado',
  }
  return labels[value] || value
}
</script>

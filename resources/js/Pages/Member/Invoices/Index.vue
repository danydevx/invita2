<template>
  <MemberLayout>
    <Head title="Mis comprobantes" />

    <PageHeader
      title="Mis comprobantes"
      :breadcrumbs="breadcrumbs"
    >
      <template #actions>
        <Link href="/member/payments" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-credit-card me-1"></i>Ver pagos
        </Link>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      endpoint="/member/invoices"
      :columns="columns"
      :initial-data="invoices"
      search-placeholder="Buscar comprobantes..."
      empty-title="No hay comprobantes"
      empty-text="Consulta tus comprobantes de pago."
      @updated="onDataTableUpdated"
    >
      <template #cell-number="{ row }">
        <strong>{{ row.number || row.id }}</strong>
      </template>

      <template #cell-issued_at="{ row }">
        <span class="text-muted">{{ row.issued_at || row.paid_at || '-' }}</span>
      </template>

      <template #cell-amount="{ row }">
        <span class="fw-semibold">{{ formatAmount(row.amount, row.currency) }}</span>
      </template>

      <template #cell-status="{ row }">
        <span class="badge" :class="statusClass(row.status)">
          {{ statusLabel(row.status) }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link :href="`/member/invoices/${row.id}`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-eye"></i>
          </Link>
        </div>
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
  invoices: {
    type: Object,
    required: true,
  },
})

const dataTableRef = ref(null)

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Facturas', active: true },
])

const columns = [
  { key: 'number', label: 'Número', sortable: true },
  { key: 'issued_at', label: 'Fecha', sortable: true },
  { key: 'amount', label: 'Monto', sortable: true },
  { key: 'status', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false, class: 'text-end' },
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
  if (value === 'issued') return 'bg-primary-subtle text-primary'
  if (value === 'pending') return 'bg-warning-subtle text-warning'
  if (value === 'canceled') return 'bg-secondary-subtle text-secondary'
  return 'bg-secondary-subtle text-secondary'
}

const statusLabel = (value) => {
  const labels = {
    paid: 'Pagado',
    issued: 'Emitido',
    pending: 'Pendiente',
    canceled: 'Cancelado',
  }
  return labels[value] || value
}
</script>

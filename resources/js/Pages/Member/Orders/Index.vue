<template>
  <MemberLayout>
    <Head :title="`Pedidos - ${listing?.name || ''}`" />

    <PageHeader
      title="Pedidos"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/listings'"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/orders/settings`" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-gear me-1"></i>
          Configuración
        </Link>
      </template>
    </PageHeader>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="row mb-3 align-items-center">
      <div class="col">
        <div class="d-flex gap-2 align-items-center flex-wrap">
          <select v-model="statusFilter" class="form-select form-select-sm" @change="filterByStatus" style="max-width: 200px;">
            <option :value="null">Todos los estados</option>
            <option v-for="status in statuses" :key="status.value" :value="status.value">
              {{ status.label }}
            </option>
          </select>
          <button v-if="statusFilter" type="button" class="btn btn-outline-secondary btn-sm" @click="clearFilter">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
    </div>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/orders`"
      :columns="columns"
      :initial-data="dataTable"
      search-placeholder="Buscar por orden, cliente..."
      empty-title="No hay pedidos"
      empty-text="Comienza recibiendo tu primer pedido."
      @updated="onDataTableUpdated"
    >
      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>

      <template #cell-order_number="{ row }">
        <span class="fw-bold">{{ row.order_number }}</span>
      </template>

      <template #cell-created_at="{ row }">
        {{ formatDate(row.created_at) }}
      </template>

      <template #cell-customer="{ row }">
        <div>{{ row.customer_name }}</div>
        <small class="text-muted">{{ row.customer_phone }}</small>
      </template>

      <template #cell-order_type="{ row }">
        <span :class="row.order_type === 'delivery' ? 'badge bg-info' : 'badge bg-secondary'">
          {{ row.order_type === 'delivery' ? 'Delivery' : 'Pickup' }}
        </span>
      </template>

      <template #cell-total="{ row }">
        <span class="fw-bold">${{ parseFloat(row.total).toFixed(2) }}</span>
      </template>

      <template #cell-status="{ row }">
        <span :class="getStatusClass(row.status)" class="badge">
          {{ row.status_label }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link
            :href="`/member/listings/${listing?.id}/orders/${row.id}`"
            class="btn btn-sm btn-outline-primary"
          >
            <i class="bi bi-eye"></i>
          </Link>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deleteOrder(row)"
          >
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>

      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/listings/${listing?.id}/orders/bulk-delete`"
          item-name="pedidos"
          @deleted="onBulkDeleted"
        />
      </template>
    </BaseDataTable>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import { BulkSelect, BulkSelectRowCheckbox } from '@/Components/BulkSelect'

const page = usePage()
const listing = computed(() => page.props.listing)
const dataTable = computed(() => page.props.dataTable || { data: [] })
const statuses = computed(() => page.props.statuses || [])
const businessMenu = computed(() => page.props.businessMenu || [])
const filters = computed(() => page.props.filters || {})

const dataTableRef = ref(null)
const selectedIds = ref([])

const statusFilter = ref(filters.value.status || null)

watch(() => filters.value.status, (newVal) => {
  statusFilter.value = newVal || null
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Pedidos' },
])

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'order_number', label: 'Orden', sortable: true },
  { key: 'created_at', label: 'Fecha', sortable: true },
  { key: 'customer', label: 'Cliente', sortable: false },
  { key: 'order_type', label: 'Tipo', sortable: false },
  { key: 'total', label: 'Total', sortable: true },
  { key: 'status', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const currentPageIds = computed(() => {
  if (!dataTable.value?.data) return []
  return dataTable.value.data.map(row => row.id)
})

const onDataTableUpdated = (data) => {
  selectedIds.value = []
}

const onBulkDeleted = () => {
  if (dataTableRef.value) {
    dataTableRef.value.reload()
  }
}

const filterByStatus = () => {
  const params = {}
  if (statusFilter.value) {
    params.status = statusFilter.value
  }
  router.get(`/member/listings/${listing.value.id}/orders`, params, {
    preserveScroll: true,
  })
}

const clearFilter = () => {
  statusFilter.value = null
  router.get(`/member/listings/${listing.value.id}/orders`, {}, {
    preserveScroll: true,
  })
}

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-warning text-dark',
    confirmed: 'bg-info',
    preparing: 'bg-primary',
    ready: 'bg-success',
    completed: 'bg-secondary',
    cancelled: 'bg-danger',
  }
  return classes[status] || 'bg-secondary'
}

const deleteOrder = (order) => {
  if (!confirm(`Eliminar el pedido ${order.order_number}?`)) return

  router.delete(`/member/listings/${listing.value.id}/orders/${order.id}`, {
    preserveScroll: true,
  })
}
</script>

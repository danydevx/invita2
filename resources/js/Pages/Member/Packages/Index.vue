<template>
  <MemberLayout>
    <Head :title="`Paquetes - ${listing?.name || ''}`" />

    <PageHeader
      title="Paquetes"
      :breadcrumbs="breadcrumbs"
      backHref="/member/dashboard"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/packages/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nuevo Paquete
        </Link>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/packages`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      :reorderable="true"
      :reorder-endpoint="`/member/listings/${listing?.id}/packages/reorder`"
      search-placeholder="Buscar paquetes..."
      empty-title="No hay paquetes"
      empty-text="Comienza creando tu primer paquete."
      @updated="onDataTableUpdated"
    >
      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/listings/${listing?.id}/packages/bulk-delete`"
          item-name="paquetes"
          @deleted="onBulkDeleted"
        />
      </template>

      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>
      <template #cell-title="{ row }">
        <div class="d-flex align-items-center gap-2">
          <img
            v-if="row.image"
            :src="row.image"
            class="rounded"
            style="width: 40px; height: 40px; object-fit: cover;"
          />
          <div v-else class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-box text-muted"></i>
          </div>
          <div>
            <strong>{{ row.title }}</strong>
            <p class="text-muted small mb-0">{{ row.short_description?.substring(0, 50) }}{{ row.short_description?.length > 50 ? '...' : '' }}</p>
          </div>
        </div>
      </template>

      <template #cell-price="{ row }">
        <span v-if="row.promo_price">
          <span class="text-decoration-line-through text-muted" v-format-price="row.price"></span>
          <span class="text-success fw-bold ms-1" v-format-price="row.promo_price"></span>
        </span>
        <span v-else-if="row.price" v-format-price="row.price"></span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-features_count="{ value }">
        <span class="badge bg-light text-dark">{{ value }} características</span>
      </template>

      <template #cell-is_active="{ value }">
        <span v-if="value" class="badge bg-success">Activo</span>
        <span v-else class="badge bg-secondary">Inactivo</span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button class="btn btn-sm btn-outline-secondary" @click="clonePackage(row)" title="Clonar">
            <i class="bi bi-copy"></i>
          </button>
          <Link :href="`/member/listings/${listing?.id}/packages/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button class="btn btn-sm btn-outline-danger" @click="deletePackage(row)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>
    </BaseDataTable>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import { BulkSelect, BulkSelectRowCheckbox } from '@/Components/BulkSelect'

const page = usePage()
const listing = computed(() => page.props.listing)
const dataTable = computed(() => page.props.dataTable || { data: [] })
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/listings\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Inicio', href: `/member/listings/${biz.id}/modules` },
        { label: 'Paquetes', active: true },
      ]
    }
  }
  return [
    { label: 'Inicio', href: '/member/dashboard' },
    { label: 'Paquetes', active: true },
  ]
})

const perPage = ref(10)

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'title', label: 'Paquete', sortable: true },
  { key: 'price', label: 'Precio', sortable: true },
  { key: 'features_count', label: 'Características', sortable: false },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const selectedIds = ref([])

const currentPageIds = computed(() => {
  if (!dataTable.value?.data) return []
  return dataTable.value.data.map(row => row.id)
})

const onDataTableUpdated = (data) => {
  perPage.value = data.per_page
  selectedIds.value = []
}

const onBulkDeleted = () => {
  if (dataTableRef.value) {
    dataTableRef.value.reload()
  }
}

const deletePackage = (pkg) => {
  if (!confirm(`¿Estás seguro de eliminar "${pkg.title}"?`)) {
    return
  }

  router.delete(`/member/listings/${listing.value.id}/packages/${pkg.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const clonePackage = (pkg) => {
  if (!confirm(`¿Clonar el paquete "${pkg.title}"?`)) {
    return
  }
  router.post(`/member/listings/${listing.value.id}/packages/${pkg.id}/clone`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}
</script>

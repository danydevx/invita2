<template>
  <MemberLayout>
    <Head :title="`Ubicaciones - ${listing?.name || ''}`" />

    <PageHeader
      title="Ubicaciones"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/listings'"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/locations/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Ubicacion
        </Link>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/locations`"
      :columns="columns"
      :initial-data="dataTable"
      search-placeholder="Buscar ubicaciones..."
      empty-title="No hay ubicaciones"
      empty-text="Comienza creando tu primera ubicacion."
      @updated="onDataTableUpdated"
    >
      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/listings/${listing?.id}/locations/bulk-delete`"
          item-name="ubicaciones"
          @deleted="onBulkDeleted"
        />
      </template>

      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>

      <template #cell-image="{ row }">
        <img
          v-if="row.image"
          :src="row.image"
          class="rounded"
          style="width: 40px; height: 40px; object-fit: cover;"
        />
        <div v-else class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
          <i class="bi bi-geo-alt text-muted"></i>
        </div>
      </template>

      <template #cell-name="{ row }">
        <div>
          <strong>{{ row.name }}</strong>
          <span v-if="row.is_primary" class="badge bg-warning ms-2">Principal</span>
        </div>
      </template>

      <template #cell-address_line_1="{ row }">
        <div>
          {{ row.address_line_1 }}
          <span v-if="row.city">, {{ row.city }}</span>
        </div>
      </template>

      <template #cell-phone="{ row }">
        {{ row.phone || '-' }}
      </template>

      <template #cell-is_primary="{ row }">
        <span v-if="row.is_primary" class="badge bg-warning">Principal</span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.is_active ? 'Activa' : 'Inactiva' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link :href="`/member/listings/${listing?.id}/locations/${row.id}/schedules`" class="btn btn-sm btn-outline-secondary" title="Horarios">
            <i class="bi bi-clock"></i>
          </Link>
          <Link :href="`/member/listings/${listing?.id}/locations/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
        </div>
      </template>
    </BaseDataTable>
  </MemberLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import { BulkSelect, BulkSelectRowCheckbox } from '@/Components/BulkSelect'

const page = usePage()
const listing = computed(() => page.props.listing)
const dataTable = computed(() => page.props.dataTable)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Ubicaciones' },
])

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'image', label: '', sortable: false, width: '50px' },
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'address_line_1', label: 'Direccion', sortable: false },
  { key: 'phone', label: 'Telefono', sortable: false },
  { key: 'is_primary', label: 'Principal', sortable: true },
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
  selectedIds.value = []
}

const onBulkDeleted = () => {
  if (dataTableRef.value) {
    dataTableRef.value.reload()
  }
}

const deleteSelected = () => {
  if (selectedIds.value.length === 0) return

  const count = selectedIds.value.length
  if (confirm(`Eliminar ${count} ubicacion${count > 1 ? 'es' : ''} seleccionada${count > 1 ? 's' : ''}?`)) {
    router.post(`/member/listings/${listing.value.id}/locations/bulk-delete`, {
      ids: selectedIds.value,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        selectedIds.value = []
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}
</script>

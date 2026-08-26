<template>
  <MemberLayout>
    <Head :title="`Servicios - ${listing?.name || ''}`" />

    <PageHeader
      title="Servicios"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/listings'"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/services/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nuevo servicio
        </Link>
      </template>
    </PageHeader>

    <div class="mb-3 d-flex gap-2">
      <Link
        :href="`/member/listings/${listing?.id}/services`"
        class="btn btn-outline-secondary btn-sm"
      >
        Todas
      </Link>
      <Link
        :href="`/member/listings/${listing?.id}/service-categories`"
        class="btn btn-outline-secondary btn-sm"
      >
        <i class="bi bi-folder me-1"></i>Categorias
      </Link>
    </div>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/services`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      :reorderable="true"
      :reorder-endpoint="`/member/listings/${listing?.id}/services/reorder`"
      search-placeholder="Buscar servicios..."
      empty-title="No hay servicios"
      empty-text="Comienza creando tu primer servicio."
      @updated="onDataTableUpdated"
    >
      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/listings/${listing?.id}/services/bulk-delete`"
          item-name="servicios"
          @deleted="onBulkDeleted"
        />
      </template>

      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>

      <template #cell-name="{ row }">
        <div class="d-flex align-items-center gap-2">
          <img
            v-if="row.image"
            :src="row.image"
            class="rounded"
            style="width: 40px; height: 40px; object-fit: cover;"
          />
          <div v-else class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-briefcase text-muted"></i>
          </div>
          <div>
            <strong>{{ row.name }}</strong>
            <p v-if="row.description" class="text-muted small mb-0">{{ row.description.substring(0, 50) }}...</p>
          </div>
        </div>
      </template>

      <template #cell-price="{ value, row }">
        <span v-if="row.price" class="fw-semibold" v-format-price="row.price"></span>
        <span v-else class="text-muted">—</span>
      </template>

      <template #cell-duration_minutes="{ value }">
        {{ value }} min
      </template>

      <template #cell-allows_online_booking="{ value }">
        <span v-if="value" class="badge bg-success">Si</span>
        <span v-else class="badge bg-secondary">No</span>
      </template>

      <template #cell-is_active="{ value }">
        <span v-if="value" class="badge bg-success">Activo</span>
        <span v-else class="badge bg-secondary">Inactivo</span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button
            class="btn btn-sm btn-outline-secondary"
            @click="cloneService(row)"
            :disabled="cloning === row.id"
            title="Clonar servicio"
          >
            <i class="bi bi-copy"></i>
          </button>
          <Link :href="`/member/listings/${listing?.id}/services/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button class="btn btn-sm btn-outline-danger" @click="deleteService(row)">
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

const props = defineProps({
  listing: Object,
  services: Object,
  locations: { type: Array, default: () => [] },
  dataTable: Object,
})

const page = usePage()
const listing = computed(() => page.props.listing)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Servicios' },
])

const perPage = ref(10)

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'price', label: 'Precio', sortable: true },
  { key: 'duration_minutes', label: 'Duracion', sortable: true },
  { key: 'allows_online_booking', label: 'Reservas online', sortable: true },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const selectedIds = ref([])
const cloning = ref(null)

const currentPageIds = computed(() => {
  if (!props.dataTable?.data) return []
  return props.dataTable.data.map(row => row.id)
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

const deleteService = (service) => {
  if (!confirm(`¿Estas seguro de eliminar "${service.name}"?`)) {
    return
  }

  router.delete(`/member/listings/${listing.value.id}/services/${service.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const cloneService = (service) => {
  if (!confirm(`Clonar el servicio "${service.name}"?`)) {
    return
  }

  cloning.value = service.id
  router.post(`/member/listings/${listing.value.id}/services/${service.id}/clone`, {}, {
    preserveScroll: true,
    onFinish: () => {
      cloning.value = null
    },
  })
}

const deleteSelected = () => {
  if (selectedIds.value.length === 0) return

  const count = selectedIds.value.length
  if (confirm(`Eliminar ${count} servicio${count > 1 ? 's' : ''} seleccionado${count > 1 ? 's' : ''}?`)) {
    router.post(`/member/listings/${listing.value.id}/services/bulk-delete`, {
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

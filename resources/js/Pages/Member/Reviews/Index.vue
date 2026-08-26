<template>
  <MemberLayout>
    <Head :title="`Resenas - ${listing?.name || ''}`" />

    <PageHeader
      title="Resenas"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/listings'"
    >
      <template #actions>
        <button
          v-if="selectedIds.length > 0"
          class="btn btn-danger btn-sm"
          @click="deleteSelected"
          :disabled="deleting"
        >
          <i class="bi bi-trash me-1"></i>
          Eliminar ({{ selectedIds.length }})
        </button>
        <Link :href="`/member/listings/${listing?.id}/reviews/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Resena
        </Link>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/reviews`"
      :columns="columns"
      :initial-data="dataTable"
      :reorderable="true"
      :reorder-endpoint="`/member/listings/${listing?.id}/reviews/reorder`"
      search-placeholder="Buscar resenas..."
      empty-title="No hay resenas"
      empty-text="Comienza creando tu primera resena."
      @updated="onDataTableUpdated"
    >
      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>

      <template #cell-client_name="{ row }">
        <div>{{ row.client_name }}</div>
        <small v-if="row.company" class="text-muted">{{ row.company }}</small>
      </template>

      <template #cell-rating="{ row }">
        <span class="text-warning">
          <i v-for="n in row.rating" :key="n" class="bi bi-star-fill"></i>
          <i v-for="n in (5 - row.rating)" :key="'empty-' + n" class="bi bi-star text-muted"></i>
        </span>
      </template>

      <template #cell-comment="{ row }">
        <span v-if="row.comment">{{ row.comment.substring(0, 60) }}{{ row.comment.length > 60 ? '...' : '' }}</span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-location="{ row }">
        {{ row.location?.name || '-' }}
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.is_active ? 'Activa' : 'Inactiva' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button
            class="btn btn-sm btn-outline-secondary"
            @click="cloneReview(row)"
            :disabled="cloning === row.id"
            title="Clonar resena"
          >
            <i class="bi bi-copy"></i>
          </button>
          <Link :href="`/member/listings/${listing?.id}/reviews/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deleteReview(row)"
          >
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>

      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/listings/${listing?.id}/reviews/bulk-delete`"
          item-name="resenas"
          @deleted="onBulkDeleted"
        />
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
  { label: 'Reseñas' },
])

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'sort_order', label: 'Orden', sortable: true, width: '70px' },
  { key: 'client_name', label: 'Cliente', sortable: true },
  { key: 'rating', label: 'Calificacion', sortable: true },
  { key: 'comment', label: 'Comentario', sortable: false },
  { key: 'location', label: 'Ubicacion', sortable: false },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const deleting = ref(null)
const cloning = ref(null)
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

const deleteReview = (review) => {
  if (!confirm(`Eliminar la resena de "${review.client_name}"?`)) return
  deleting.value = review.id
  router.delete(`/member/listings/${listing.value.id}/reviews/${review.id}`, {
    preserveScroll: true,
    onFinish: () => {
      deleting.value = null
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const cloneReview = (review) => {
  if (!confirm(`Clonar la resena de "${review.client_name}"?`)) return
  cloning.value = review.id
  router.post(`/member/listings/${listing.value.id}/reviews/${review.id}/clone`, {}, {
    preserveScroll: true,
    onFinish: () => {
      cloning.value = null
    },
  })
}

const deleteSelected = () => {
  if (selectedIds.value.length === 0) return

  const count = selectedIds.value.length
  if (confirm(`Eliminar ${count} resena${count > 1 ? 's' : ''} seleccionada${count > 1 ? 's' : ''}?`)) {
    deleting.value = true
    router.post(`/member/listings/${listing.value.id}/reviews/bulk-delete`, {
      ids: selectedIds.value,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        selectedIds.value = []
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
      onFinish: () => {
        deleting.value = false
      },
    })
  }
}
</script>

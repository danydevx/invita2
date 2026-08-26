<template>
  <MemberLayout>
    <Head :title="`Fidelidad - ${listing?.name || ''}`" />

    <PageHeader
      title="Tarjetas de Fidelidad"
      :breadcrumbs="breadcrumbs"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/fidelity-cards/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nueva
        </Link>
      </template>
    </PageHeader>

    <div class="mb-3 d-flex gap-2">
      <Link
        :href="`/member/listings/${listing?.id}/fidelity-cards`"
        class="btn btn-secondary btn-sm"
      >
        <i class="bi bi-credit-card me-1"></i>Tarjetas
      </Link>
      <Link
        :href="`/member/listings/${listing?.id}/fidelity-cards/scan-view`"
        class="btn btn-outline-secondary btn-sm"
      >
        <i class="bi bi-qr-code-scan me-1"></i>Escanear
      </Link>
    </div>

    <div class="row mb-3 align-items-center">
      <div class="col">
        <div class="d-flex gap-2">
          <select
            v-model="selectedFilter"
            class="form-select form-select-sm"
            @change="filterCards"
            style="max-width: 200px;"
          >
            <option value="all">Todas</option>
            <option value="active">Activas</option>
            <option value="completed">Completadas</option>
          </select>
        </div>
      </div>
    </div>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/fidelity-cards`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      search-placeholder="Buscar tarjetas..."
      empty-title="No hay tarjetas"
      empty-text="Comienza creando tu primera tarjeta de fidelidad."
      @updated="onDataTableUpdated"
    >
      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
           :delete-endpoint="`/member/listings/${listing?.id}/fidelity-cards/bulk-delete`"
          item-name="tarjetas"
          @deleted="onBulkDeleted"
        />
      </template>

      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>

      <template #cell-client_name="{ row }">
        <div class="d-flex align-items-center gap-2">
          <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-person text-muted"></i>
          </div>
          <div>
            <strong>{{ row.client_name }}</strong>
            <p v-if="row.client_email" class="text-muted small mb-0">
              {{ row.client_email }}
            </p>
          </div>
        </div>
      </template>

      <template #cell-progress="{ row }">
        <div class="d-flex align-items-center gap-2">
          <div class="progress flex-grow-1" style="height: 8px; min-width: 80px;">
            <div
              class="progress-bar"
              :class="row.is_completed ? 'bg-success' : 'bg-primary'"
              :style="{ width: row.progress_percentage + '%' }"
            ></div>
          </div>
          <span class="small text-muted">{{ row.current_visits }}/{{ row.max_visits }}</span>
        </div>
      </template>

      <template #cell-status="{ row }">
        <span v-if="row.is_completed" class="badge bg-success">Completada</span>
        <span v-else-if="row.is_active" class="badge bg-primary">Activa</span>
        <span v-else class="badge bg-secondary">Inactiva</span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link :href="`/member/listings/${listing?.id}/fidelity-cards/${row.id}`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-eye"></i>
          </Link>
          <Link :href="`/member/listings/${listing?.id}/fidelity-cards/${row.id}/edit`" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button class="btn btn-sm btn-outline-danger" @click="deleteCard(row)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
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
const businessMenu = computed(() => page.props.businessMenu || [])

const filters = computed(() => page.props.filters || {})
const getInitialFilter = () => {
  const params = new URLSearchParams(window.location.search)
  return params.get('filter') || 'all'
}
const selectedFilter = ref(getInitialFilter())

watch(filters, (newFilters) => {
  selectedFilter.value = newFilters.filter || 'all'
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Fidelización' },
])

const perPage = ref(10)

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'client_name', label: 'Cliente', sortable: true },
  { key: 'public_code', label: 'Código', sortable: true },
  { key: 'progress', label: 'Progreso', sortable: false },
  { key: 'status', label: 'Estado', sortable: true },
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

const filterCards = () => {
  const params = {}
  if (selectedFilter.value !== 'all') {
    params.filter = selectedFilter.value
  }
  router.get(`/member/listings/${listing.value.id}/fidelity-cards`, params, {
    preserveScroll: true,
  })
}

const deleteCard = (card) => {
  if (!confirm(`¿Estás seguro de eliminar la tarjeta de "${card.client_name}"?`)) {
    return
  }
  router.delete(`/member/listings/${listing.value.id}/fidelity-cards/${card.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}
</script>

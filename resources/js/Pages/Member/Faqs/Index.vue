<template>
  <MemberLayout>
    <Head :title="`Preguntas Frecuentes - ${listing?.name || ''}`" />

    <PageHeader
      title="Preguntas Frecuentes"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/listings'"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/faqs/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nueva Pregunta
        </Link>
      </template>
    </PageHeader>

    <div class="mb-3 d-flex gap-2">
      <Link
        :href="`/member/listings/${listing?.id}/faqs`"
        class="btn btn-outline-secondary btn-sm"
      >
        Todas
      </Link>
      <Link
        :href="`/member/listings/${listing?.id}/faq-categories`"
        class="btn btn-outline-secondary btn-sm"
      >
        <i class="bi bi-folder me-1"></i>Categorías
      </Link>
    </div>

    <div class="row mb-3 align-items-center">
      <div class="col">
        <div class="d-flex gap-2 align-items-center">
          <select
            v-model="selectedCategory"
            class="form-select form-select-sm"
            @change="filterByCategory"
            style="max-width: 200px;"
          >
            <option :value="null">Todas las categorías</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/faqs`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      :reorderable="true"
      :reorder-endpoint="`/member/listings/${listing?.id}/faqs/reorder`"
      search-placeholder="Buscar preguntas..."
      empty-title="No hay preguntas frecuentes"
      empty-text="Comienza creando tu primera pregunta frecuente."
      @updated="onDataTableUpdated"
    >
      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/listings/${listing?.id}/faqs/bulk-delete`"
          item-name="preguntas"
          @deleted="onBulkDeleted"
        />
      </template>

      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>
      <template #cell-question="{ row }">
        <div class="d-flex align-items-center gap-2">
          <img
            v-if="row.image"
            :src="row.image"
            class="rounded"
            style="width: 40px; height: 40px; object-fit: cover;"
          />
          <div v-else class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-question-circle text-muted"></i>
          </div>
          <div>
            <strong>{{ row.question }}</strong>
            <p v-if="row.category" class="text-muted small mb-0">
              <span class="badge bg-light text-dark">{{ row.category.name }}</span>
            </p>
          </div>
        </div>
      </template>

      <template #cell-answer="{ value }">
        <span class="text-muted small">{{ value?.substring(0, 80) }}{{ value?.length > 80 ? '...' : '' }}</span>
      </template>

      <template #cell-category="{ row }">
        <span v-if="row.category" class="badge bg-info">{{ row.category.name }}</span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-is_active="{ value }">
        <span v-if="value" class="badge bg-success">Activo</span>
        <span v-else class="badge bg-secondary">Inactivo</span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button class="btn btn-sm btn-outline-secondary" @click="cloneFaq(row)" title="Clonar">
            <i class="bi bi-copy"></i>
          </button>
          <Link :href="`/member/listings/${listing?.id}/faqs/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button class="btn btn-sm btn-outline-danger" @click="deleteFaq(row)">
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
const categories = computed(() => page.props.categories || [])
const dataTable = computed(() => page.props.dataTable || { data: [] })
const businessMenu = computed(() => page.props.businessMenu || [])

const filters = computed(() => page.props.filters || {})
const getInitialCategory = () => {
  const params = new URLSearchParams(window.location.search)
  return params.get('category') ? Number(params.get('category')) : null
}
const selectedCategory = ref(getInitialCategory())

watch(filters, (newFilters) => {
  selectedCategory.value = newFilters.category
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Preguntas Frecuentes' },
])

const perPage = ref(10)

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'question', label: 'Pregunta', sortable: true },
  { key: 'answer', label: 'Respuesta', sortable: false },
  { key: 'category', label: 'Categoría', sortable: false },
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

const filterByCategory = () => {
  const params = {}
  if (selectedCategory.value) {
    params.category = selectedCategory.value
  }
  router.get(`/member/listings/${listing.value.id}/faqs`, params, {
    preserveScroll: true,
  })
}

const deleteFaq = (faq) => {
  if (!confirm(`¿Estás seguro de eliminar "${faq.question}"?`)) {
    return
  }

  router.delete(`/member/listings/${listing.value.id}/faqs/${faq.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const cloneFaq = (faq) => {
  if (!confirm(`¿Clonar "${faq.question}"?`)) {
    return
  }

  router.post(`/member/listings/${listing.value.id}/faqs/${faq.id}/clone`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}
</script>

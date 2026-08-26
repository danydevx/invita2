<template>
  <MemberLayout>
    <Head :title="`Productos - ${listing?.name || ''}`" />

    <PageHeader
      title="Productos"
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
        <Link :href="`/member/listings/${listing?.id}/product-categories`" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-folder me-1"></i>Categorias
        </Link>
        <Link :href="`/member/listings/${listing?.id}/products/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nuevo Producto
        </Link>
      </template>
    </PageHeader>

    <div class="row mb-3 align-items-center">
      <div class="col">
        <div class="d-flex gap-2 align-items-center flex-wrap">
          <select v-model="filterCategory" class="form-select form-select-sm" @change="filterProducts" style="max-width: 200px;">
            <option :value="null">Todas las categorias</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <button v-if="filterCategory" type="button" class="btn btn-outline-secondary btn-sm" @click="clearFilter">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
    </div>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/products`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      :reorderable="true"
      :reorder-endpoint="`/member/listings/${listing?.id}/products/reorder`"
      search-placeholder="Buscar productos..."
      empty-title="No hay productos"
      :empty-text="selectedCategoryName ? `No hay productos en la categoria '${selectedCategoryName}'.` : 'Comienza creando tu primer producto.'"
      @updated="onDataTableUpdated"
    >
      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/listings/${listing?.id}/products/bulk-delete`"
          item-name="productos"
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
          style="width: 48px; height: 48px; object-fit: cover;"
        />
        <div v-else class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
          <i class="bi bi-image text-muted"></i>
        </div>
      </template>

      <template #cell-name="{ row }">
        <strong>{{ row.name }}</strong>
        <p v-if="row.description" class="text-muted small mb-0">{{ row.description.substring(0, 60) }}...</p>
      </template>

      <template #cell-category="{ row }">
        <span v-if="row.category">{{ row.category.name }}</span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-price="{ row }">
        <span v-if="row.price" class="fw-semibold" v-format-price="row.price"></span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-quantity="{ row }">
        <span v-if="row.quantity !== null">{{ row.quantity }}</span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-location="{ row }">
        <span v-if="row.location">{{ row.location.name }}</span>
        <span v-else class="text-muted">Todas</span>
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.is_active ? 'Activo' : 'Inactivo' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button
            class="btn btn-sm btn-outline-secondary"
            @click="cloneProduct(row)"
            :disabled="cloning === row.id"
            title="Clonar producto"
          >
            <i class="bi bi-copy"></i>
          </button>
          <Link
            :href="`/member/listings/${listing?.id}/products/${row.id}/edit`"
            class="btn btn-sm btn-outline-primary"
          >
            <i class="bi bi-pencil"></i>
          </Link>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deleteProduct(row)"
            :disabled="deleting === row.id"
          >
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
  selectedCategory: [Number, String],
})

const page = usePage()
const listing = computed(() => page.props.listing)
const dataTable = computed(() => page.props.dataTable)
const categories = computed(() => page.props.categories || [])
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Productos' },
])

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'image', label: '', sortable: false, width: '60px' },
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'category', label: 'Categoria', sortable: false },
  { key: 'price', label: 'Precio', sortable: true },
  { key: 'quantity', label: 'Stock', sortable: true },
  { key: 'location', label: 'Ubicacion', sortable: false },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const deleting = ref(null)
const cloning = ref(null)
const perPage = ref(10)
const selectedIds = ref([])
const filterCategory = ref(props.selectedCategory)

const selectedCategoryName = computed(() => {
  if (!filterCategory.value) return null
  const cat = categories.value.find(c => c.id === filterCategory.value)
  return cat?.name
})

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

const filterProducts = () => {
  let url = `/member/listings/${listing.value.id}/products`
  if (filterCategory.value) {
    url += `?category=${filterCategory.value}`
  }
  window.location.href = url
}

const clearFilter = () => {
  filterCategory.value = null
  window.location.href = `/member/listings/${listing.value.id}/products`
}

const deleteProduct = (product) => {
  if (confirm(`Eliminar el producto "${product.name}"?`)) {
    deleting.value = product.id
    router.delete(`/member/listings/${listing.value.id}/products/${product.id}`, {
      preserveScroll: true,
      onFinish: () => {
        deleting.value = null
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}

const cloneProduct = (product) => {
  if (confirm(`Clonar el producto "${product.name}"?`)) {
    cloning.value = product.id
    router.post(`/member/listings/${listing.value.id}/products/${product.id}/clone`, {}, {
      preserveScroll: true,
      onFinish: () => {
        cloning.value = null
      },
    })
  }
}

const deleteSelected = () => {
  if (selectedIds.value.length === 0) return

  const count = selectedIds.value.length
  if (confirm(`Eliminar ${count} producto${count > 1 ? 's' : ''} seleccionado${count > 1 ? 's' : ''}?`)) {
    deleting.value = true
    router.post(`/member/listings/${listing.value.id}/products/bulk-delete`, {
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

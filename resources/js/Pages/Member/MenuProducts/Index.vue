<template>
  <MemberLayout>
    <Head :title="listing ? `Productos - ${listing.name}` : 'Productos'" />

    <PageHeader
      title="Menu del Restaurant"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/listings'"
    >
      <template #description>
        <p class="text-muted mb-0">Gestiona los productos de tu menu. Arrastra para reordenar.</p>
      </template>
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
        <Link :href="`/member/listings/${listing?.id}/menu-categories`" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-folder me-1"></i>Categorias
        </Link>
        <Link :href="`/member/listings/${listing?.id}/menu-products/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
        </Link>
      </template>
    </PageHeader>

    <div class="row mb-4 align-items-center">
      <div class="col">
        <div class="d-flex gap-2 align-items-center flex-wrap">
          <div style="max-width: 200px;">
            <input
              type="text"
              class="form-control form-control-sm"
              v-model="searchQuery"
              placeholder="Buscar productos..."
              @keyup.enter="filterProducts"
            />
          </div>
          <select v-model="filterCategory" class="form-select form-select-sm" @change="filterProducts" style="max-width: 200px;">
            <option :value="null">Todas las categorias</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.title }}</option>
          </select>
          <button v-if="filterCategory || searchQuery" type="button" class="btn btn-outline-secondary btn-sm" @click="clearFilters">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
      <div class="col-auto">
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/listings/${listing?.id}/menu-products/bulk-delete`"
          item-name="productos"
          @deleted="onBulkDeleted"
        />
      </div>
    </div>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <SortableCards
      ref="sortableCardsRef"
      :items="productsList"
      item-class="col-6 col-md-4 col-lg-2"
      :reorderable="true"
      :reorder-endpoint="`/member/listings/${listing?.id}/menu-products/reorder`"
      :loading="loading"
      empty-title="No hay productos"
      :empty-text="selectedCategoryName ? 'No hay productos en esta categoria.' : 'Comienza creando tu primer producto.'"
      toast-message="Orden actualizado"
      @reordered="onReordered"
    >
      <template #item="{ item: product }">
        <div class="sortable-cards__checkbox">
          <BulkSelectRowCheckbox
            :id="product.id"
            v-model:selectedIds="selectedIds"
          />
        </div>
        <div class="card-img-top ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center overflow-hidden">
          <img
            v-if="product.image"
            :src="product.image"
            :alt="product.title"
            class="w-100 h-100"
            style="object-fit: cover;"
          />
          <img
            v-else
            src="https://placehold.co/400x300/e9ecef/868e96?text=Sin+imagen"
            :alt="product.title"
            class="w-100 h-100"
            style="object-fit: cover;"
          />
        </div>
        <div class="card-body py-2">
          <h6 class="card-title mb-1 text-truncate">{{ product.title }}</h6>
          <p class="card-text small text-muted mb-1 text-truncate">{{ product.category?.title }}</p>
          <div class="d-flex justify-content-between align-items-center">
            <span v-if="product.show_price && product.display_price" class="fw-bold small">${{ product.display_price }}</span>
            <span v-else-if="product.show_price && product.variants?.length" class="fw-bold small">Desde ${{ product.variants[0].price }}</span>
            <span v-else class="text-muted small">-</span>
            <span v-if="product.featured" class="badge bg-warning badge-sm">Dest.</span>
          </div>
        </div>
        <div class="card-footer bg-transparent py-1">
          <div class="d-flex gap-1">
            <button @click="cloneProduct(product)" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-copy"></i>
            </button>
            <Link :href="`/member/listings/${listing?.id}/menu-products/${product.id}/edit`" class="btn btn-sm btn-outline-primary flex-grow-1">
              <i class="bi bi-pencil"></i>
            </Link>
            <button @click="deleteProduct(product)" class="btn btn-sm btn-outline-danger">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </template>
    </SortableCards>

    <div v-if="products.total > products.per_page" class="d-flex justify-content-center mt-4">
      <Pagination :links="products.links" />
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import SortableCards from '@/Components/DataTable/SortableCards.vue'
import Pagination from '@/Components/Member/Pagination.vue'
import { BulkSelect, BulkSelectRowCheckbox } from '@/Components/BulkSelect'

const props = defineProps({
  listing: Object,
  products: Object,
  categories: Array,
  selectedCategory: [Number, String],
  searchQuery: String,
})

const page = usePage()
const listing = computed(() => page.props.listing)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Menú' },
])

const sortableCardsRef = ref(null)

const loading = ref(false)
const deleting = ref(false)
const filterCategory = ref(props.selectedCategory)
const searchQuery = ref(props.searchQuery || '')
const selectedIds = ref([])

const productsList = computed(() => {
  if (!props.products) return []
  if (Array.isArray(props.products)) return props.products
  if (Array.isArray(props.products.data)) return props.products.data
  return []
})

const currentPageIds = computed(() => {
  return productsList.value.map(p => p.id)
})

const selectedCategoryName = computed(() => {
  if (!filterCategory.value) return null
  if (filterCategory.value === 'uncategorized') return 'Sin categoria'
  const cat = props.categories.find(c => c.id === filterCategory.value)
  return cat?.title
})

const onReordered = (ids) => {
  console.log('Reordered:', ids)
}

const onBulkDeleted = () => {
  if (sortableCardsRef.value) {
    sortableCardsRef.value.reload()
  }
}

const filterProducts = () => {
  let url = `/member/listings/${props.listing.id}/menu-products`
  const params = []
  if (filterCategory.value) {
    if (filterCategory.value === 'uncategorized') {
      params.push('uncategorized=1')
    } else {
      params.push(`category=${filterCategory.value}`)
    }
  }
  if (searchQuery.value) {
    params.push(`search=${encodeURIComponent(searchQuery.value)}`)
  }
  if (params.length > 0) {
    url += '?' + params.join('&')
  }
  window.location.href = url
}

const clearFilters = () => {
  filterCategory.value = null
  searchQuery.value = ''
  window.location.href = `/member/listings/${props.listing.id}/menu-products`
}

const deleteProduct = (product) => {
  if (!confirm(`Eliminar el producto "${product.title}"?`)) return

  router.delete(`/member/listings/${props.listing.id}/menu-products/${product.id}`, {
    preserveScroll: true,
  })
}

const cloneProduct = (product) => {
  if (!confirm(`Clonar el producto "${product.title}"?`)) return

  router.post(`/member/listings/${props.listing.id}/menu-products/${product.id}/clone`, {
    preserveScroll: true,
  })
}

const deleteSelected = () => {
  if (selectedIds.value.length === 0) return

  const count = selectedIds.value.length
  if (confirm(`Eliminar ${count} producto${count > 1 ? 's' : ''} seleccionado${count > 1 ? 's' : ''}?`)) {
    deleting.value = true
    router.post(`/member/listings/${props.listing.id}/menu-products/bulk-delete`, {
      ids: selectedIds.value,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        selectedIds.value = []
      },
      onFinish: () => {
        deleting.value = false
      },
    })
  }
}
</script>

<style scoped>
.sortable-cards__checkbox {
  position: absolute;
  top: 8px;
  left: 8px;
  z-index: 20;
}

.form-check-input {
  cursor: pointer;
  width: 1.2em;
  height: 1.2em;
}

.sortable-cards__item {
  position: relative;
}
</style>

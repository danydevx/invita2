<template>
  <AdminLayout>
    <Head :title="`Productos del Menú - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin/businesses" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Negocios
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - Productos del Menú</h1>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><Link :href="`/admin/businesses/${business.id}/menu-categories`">Categorías</Link></li>
            <li v-if="selectedCategoryName" class="breadcrumb-item active">{{ selectedCategoryName }}</li>
          </ol>
        </nav>
      </div>
      <button @click="showCreateModal = true" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nuevo Producto
      </button>
    </div>

    <div class="row mb-4">
      <div class="col-md-4">
        <select v-model="filterCategory" class="form-select" @change="filterProducts">
          <option :value="null">Todas las categorías</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.title }}</option>
        </select>
      </div>
    </div>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div v-if="products.data.length === 0" class="alert alert-info">
      No hay productos{{ selectedCategoryName ? ' en esta categoría' : '' }}.
    </div>

    <div class="row">
      <div v-for="product in products.data" :key="product.id" class="col-md-4 mb-4">
        <div class="card h-100">
          <img v-if="product.image" :src="product.image" class="card-img-top" :alt="product.title" style="height: 150px; object-fit: cover;">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <h5 class="card-title">{{ product.title }}</h5>
              <span v-if="product.featured" class="badge bg-warning">Destacado</span>
            </div>
            <p class="card-text text-muted small">{{ product.category?.title }}</p>
            <p v-if="product.description" class="card-text small">{{ product.description.substring(0, 80) }}...</p>
            <div class="d-flex justify-content-between align-items-center">
              <span v-if="product.show_price && product.display_price" class="fw-bold">${{ product.display_price }}</span>
              <span v-else-if="product.show_price && product.variants?.length" class="fw-bold">Desde ${{ product.variants[0].price }}</span>
              <span v-else class="text-muted">Precio no visible</span>
              <div class="btn-group btn-group-sm">
                <button @click="editProduct(product)" class="btn btn-outline-primary">
                  <i class="bi bi-pencil"></i>
                </button>
                <button @click="deleteProduct(product)" class="btn btn-outline-danger">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="card-footer text-muted">
            <small>{{ product.active ? 'Activo' : 'Inactivo' }} | {{ product.variants?.length || 0 }} variantes</small>
          </div>
        </div>
      </div>
    </div>

    <div v-if="products.total > products.per_page" class="d-flex justify-content-center mt-4">
      <pagination :data="products" />
    </div>

    <div v-if="showCreateModal || editingProduct" class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingProduct ? 'Editar Producto' : 'Nuevo Producto' }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <form @submit.prevent="submitForm">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="mb-3">
                    <label class="form-label">Nombre del producto</label>
                    <input v-model="form.title" type="text" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <select v-model="form.category_id" class="form-select" required>
                      <option value="">Seleccionar categoría</option>
                      <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.title }}</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea v-model="form.description" class="form-control" rows="3"></textarea>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Precio base</label>
                        <input v-model.number="form.base_price" type="number" step="0.01" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label class="form-label">Orden</label>
                        <input v-model.number="form.sort_order" type="number" class="form-control">
                      </div>
                    </div>
                  </div>
                  <div class="mb-3 form-check">
                    <input v-model="form.show_price" type="checkbox" class="form-check-input" id="showPriceCheck">
                    <label class="form-check-label" for="showPriceCheck">Mostrar precio</label>
                  </div>
                  <div class="mb-3 form-check">
                    <input v-model="form.featured" type="checkbox" class="form-check-input" id="featuredCheck">
                    <label class="form-check-label" for="featuredCheck">Producto destacado</label>
                  </div>
                  <div class="mb-3 form-check">
                    <input v-model="form.active" type="checkbox" class="form-check-input" id="activeCheck">
                    <label class="form-check-label" for="activeCheck">Activo</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="mb-3">
                    <label class="form-label">URL de imagen</label>
                    <input v-model="form.image" type="text" class="form-control" placeholder="https://...">
                    <div v-if="form.image" class="mt-2">
                      <img :src="form.image" class="img-thumbnail w-100" alt="Preview">
                    </div>
                  </div>
                </div>
              </div>

              <hr>

              <h6>Variantes</h6>
              <div v-for="(variant, index) in form.variants" :key="index" class="border rounded p-3 mb-2">
                <div class="row">
                  <div class="col-md-4">
                    <input v-model="variant.title" type="text" class="form-control" placeholder="Nombre (ej: Chica)">
                  </div>
                  <div class="col-md-3">
                    <input v-model.number="variant.price" type="number" step="0.01" class="form-control" placeholder="Precio">
                  </div>
                  <div class="col-md-3">
                    <input v-model.number="variant.sort_order" type="number" class="form-control" placeholder="Orden">
                  </div>
                  <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger w-100" @click="removeVariant(index)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" @click="addVariant">
              <i class="bi bi-plus"></i> Agregar variante
            </button>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Guardando...' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  business: Object,
  products: Object,
  categories: Array,
  selectedCategory: [Number, String],
})

const showCreateModal = ref(false)
const editingProduct = ref(null)
const sending = ref(false)
const filterCategory = ref(props.selectedCategory)

const selectedCategoryName = computed(() => {
  if (!filterCategory.value) return null
  if (filterCategory.value === 'uncategorized') return 'Sin categoría'
  const cat = props.categories.find(c => c.id === filterCategory.value)
  return cat?.title
})

const form = ref({
  title: '',
  category_id: '',
  description: '',
  image: '',
  base_price: null,
  show_price: true,
  featured: false,
  active: true,
  sort_order: 0,
  variants: [],
})

const filterProducts = () => {
  let url = `/admin/businesses/${props.business.id}/menu-products`
  if (filterCategory.value) {
    if (filterCategory.value === 'uncategorized') {
      url += '?uncategorized=1'
    } else {
      url += `?category=${filterCategory.value}`
    }
  }
  window.location.href = url
}

const editProduct = (product) => {
  editingProduct.value = product
  form.value = {
    title: product.title,
    category_id: product.category_id,
    description: product.description || '',
    image: product.image || '',
    base_price: product.base_price,
    show_price: product.show_price,
    featured: product.featured,
    active: product.active,
    sort_order: product.sort_order || 0,
    variants: product.variants?.map(v => ({
      id: v.id,
      title: v.title,
      price: v.price,
      sort_order: v.sort_order || 0,
    })) || [],
  }
}

const deleteProduct = (product) => {
  if (!confirm(`¿Eliminar el producto "${product.title}"?`)) return
  router.delete(`/admin/businesses/${props.business.id}/menu-products/${product.id}`)
}

const closeModal = () => {
  showCreateModal.value = false
  editingProduct.value = null
  form.value = {
    title: '',
    category_id: '',
    description: '',
    image: '',
    base_price: null,
    show_price: true,
    featured: false,
    active: true,
    sort_order: 0,
    variants: [],
  }
}

const addVariant = () => {
  form.value.variants.push({
    title: '',
    price: null,
    sort_order: 0,
  })
}

const removeVariant = (index) => {
  form.value.variants.splice(index, 1)
}

const submitForm = () => {
  sending.value = true

  if (editingProduct.value) {
    router.put(
      `/admin/businesses/${props.business.id}/menu-products/${editingProduct.value.id}`,
      form.value,
      {
        onFinish: () => {
          sending.value = false
          closeModal()
        },
      }
    )
  } else {
    router.post(
      `/admin/businesses/${props.business.id}/menu-products`,
      form.value,
      {
        onFinish: () => {
          sending.value = false
          closeModal()
        },
      }
    )
  }
}
</script>

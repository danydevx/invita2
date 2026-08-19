<template>
  <MemberLayout>
    <Head title="Categorias del Menu" />

    <PageHeader
      title="Categorias del Menu"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
    >
      <template #actions>
        <button @click="openCreateModal" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nueva Categoria
        </button>
      </template>
    </PageHeader>

    <div class="container-fluid py-4">
      <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $page.props.flash.error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>

      <div v-if="categories.length === 0" class="alert alert-info">
        No hay categorias creadas. Crea tu primera categoria para empezar.
      </div>

      <div class="mb-3">
        <Link :href="`/member/businesses/${business.id}/menu-products?uncategorized=1`" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-dash-circle me-1"></i>Productos sin categoria
        </Link>
      </div>

      <div class="row g-3">
        <div v-for="category in categories" :key="category.id" class="col-12 col-md-6 col-lg-4">
          <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
              <div>
                <span :class="{ 'text-muted': !category.active }">
                  <strong>{{ category.title }}</strong>
                  <span v-if="!category.active" class="badge bg-secondary ms-2">Inactiva</span>
                </span>
                <small class="text-muted d-block">{{ category.children?.length || 0 }} subcategorias, {{ category.products?.length || 0 }} productos</small>
                <Link :href="`/member/businesses/${business.id}/menu-products?category=${category.id}`" class="text-decoration-none small">
                  <i class="bi bi-box-seam me-1"></i>Ver productos
                </Link>
              </div>
              <div class="btn-group">
                <button @click="editCategory(category)" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-pencil"></i>
                </button>
                <button @click="deleteCategory(category)" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
              <p v-if="category.description" class="text-muted mb-2">{{ category.description }}</p>

              <div v-if="category.children && category.children.length > 0" class="mt-3">
                <div v-for="child in category.children" :key="child.id" class="border-start border-3 ps-3 mb-2">
                  <div class="d-flex justify-content-between align-items-center">
                    <span :class="{ 'text-muted': !child.active }">
                      {{ child.title }}
                      <span v-if="!child.active" class="badge bg-secondary ms-2">Inactiva</span>
                      <Link :href="`/member/businesses/${business.id}/menu-products?category=${child.id}`" class="text-decoration-none small ms-2">
                        <i class="bi bi-box-seam"></i> {{ child.products?.length || 0 }}
                      </Link>
                    </span>
                    <div class="btn-group btn-group-sm">
                      <button @click="editCategory(child)" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button @click="deleteCategory(child)" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingCategory ? 'Editar Categoria' : 'Nueva Categoria' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitForm">
            <div class="modal-body">
              <div class="mb-3">
                <FieldText
                  id="category-title"
                  label="Nombre de la categoria"
                  v-model="form.title"
                  required
                />
              </div>
              <div class="mb-3">
                <FieldTextarea
                  id="category-description"
                  label="Descripcion"
                  v-model="form.description"
                  :rows="3"
                />
              </div>
              <div class="mb-3">
                <FieldSelect
                  id="category-parent"
                  label="Categoria padre"
                  v-model="form.parent_id"
                >
                  <option :value="null">Ninguna (categoria principal)</option>
                  <option v-for="cat in flatCategories" :key="cat.id" :value="cat.id">
                    {{ cat.nested_title }}
                  </option>
                </FieldSelect>
              </div>
              <div class="mb-3">
                <label class="form-label">Imagen</label>
                <input
                  ref="imageInput"
                  type="file"
                  class="form-control"
                  accept="image/jpeg,image/png,image/webp,image/gif"
                  @change="handleImageChange"
                />
                <div v-if="imagePreview" class="mt-2">
                  <img :src="imagePreview" class="img-thumbnail" style="max-height: 150px;" alt="Preview" />
                </div>
                <small class="text-muted d-block">JPG, PNG o WebP, max 5MB.</small>
                <button
                  v-if="editingCategory && editingCategory.images && editingCategory.images.length > 0"
                  type="button"
                  class="btn btn-outline-danger btn-sm mt-2"
                  @click="removeImage"
                >
                  <i class="bi bi-trash me-1"></i>Eliminar imagen
                </button>
              </div>
              <div class="mb-3">
                <FieldNumber
                  id="category-sort"
                  label="Orden"
                  v-model="form.sort_order"
                />
              </div>
              <div class="mb-3">
                <FieldSwitch
                  id="category-active"
                  label="Activa"
                  v-model="form.active"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { Head, usePage, Link, router } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const props = defineProps({
  business: Object,
  categories: Array,
})

const page = usePage()
const business = computed(() => page.props.business)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Categorias del Menu', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Categorias del Menu', active: true },
  ]
})

const modalElement = ref(null)
let categoryModal = null

const editingCategory = ref(null)
const sending = ref(false)
const imagePreview = ref(null)
const imageInput = ref(null)

const form = ref({
  title: '',
  description: '',
  parent_id: null,
  image: null,
  remove_image: false,
  sort_order: 0,
  active: true,
})

const flatCategories = computed(() => {
  const flat = []
  const flatten = (cats, prefix = '') => {
    cats.forEach(cat => {
      flat.push({
        id: cat.id,
        title: cat.title,
        nested_title: prefix + cat.title,
      })
      if (cat.children && cat.children.length) {
        flatten(cat.children, prefix + cat.title + ' > ')
      }
    })
  }
  flatten(props.categories || [])
  return flat
})

const openCreateModal = () => {
  editingCategory.value = null
  imagePreview.value = null
  if (imageInput.value) {
    imageInput.value.value = ''
  }
  form.value = {
    title: '',
    description: '',
    parent_id: null,
    image: null,
    remove_image: false,
    sort_order: 0,
    active: true,
  }
  nextTick(() => categoryModal.show())
}

const editCategory = (category) => {
  editingCategory.value = category
  imagePreview.value = null
  if (imageInput.value) {
    imageInput.value.value = ''
  }
  form.value = {
    title: category.title,
    description: category.description || '',
    parent_id: category.parent_id,
    image: null,
    remove_image: false,
    sort_order: category.sort_order || 0,
    active: category.active,
  }
  if (category.images && category.images.length > 0) {
    imagePreview.value = category.images[0].path
  }
  nextTick(() => categoryModal.show())
}

const handleImageChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  if (file.size > 5 * 1024 * 1024) {
    alert('El archivo supera el tamano maximo de 5MB.')
    return
  }

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
  if (!allowedTypes.includes(file.type)) {
    alert('Solo se permiten imagenes (JPEG, PNG, WebP, GIF).')
    return
  }

  form.value.image = file
  imagePreview.value = URL.createObjectURL(file)
}

const deleteCategory = (category) => {
  if (!confirm(`Eliminar la categoria "${category.title}"?`)) return

  router.delete(`/member/businesses/${props.business.id}/menu-categories/${category.id}`, {
    preserveScroll: true,
  })
}

const removeImage = () => {
  form.value.remove_image = true
  imagePreview.value = null
  if (imageInput.value) {
    imageInput.value.value = ''
  }
}

const closeModal = () => {
  categoryModal.hide()
}

const submitForm = () => {
  sending.value = true

  const data = new FormData()
  data.append('title', form.value.title)
  data.append('description', form.value.description || '')
  data.append('parent_id', form.value.parent_id || '')
  data.append('active', form.value.active ? '1' : '0')
  data.append('sort_order', form.value.sort_order || '0')

  if (form.value.image) {
    data.append('image', form.value.image)
  }
  if (form.value.remove_image) {
    data.append('remove_image', '1')
  }

  if (editingCategory.value) {
    data.append('_method', 'PUT')
    router.post(`/member/businesses/${props.business.id}/menu-categories/${editingCategory.value.id}`, data, {
      preserveScroll: true,
      onFinish: () => {
        sending.value = false
        closeModal()
      },
    })
  } else {
    router.post(`/member/businesses/${props.business.id}/menu-categories`, data, {
      preserveScroll: true,
      onFinish: () => {
        sending.value = false
        closeModal()
      },
    })
  }
}

onMounted(() => {
  categoryModal = new Modal(modalElement.value)
})
</script>

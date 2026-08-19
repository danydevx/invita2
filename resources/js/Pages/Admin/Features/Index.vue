<template>
  <AdminLayout>
    <Head title="Configuracion - Caracteristicas" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Caracteristicas</h1>
          <p class="text-muted mb-0">Configuracion global de caracteristicas y categorias</p>
        </div>
        <button class="btn btn-primary" @click="openCreateModal">
          <i class="bi bi-plus me-1"></i>Nueva caracteristica
        </button>
      </div>

      <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
          <button
            class="nav-link"
            :class="{ active: activeTab === 'features' }"
            @click="activeTab = 'features'"
          >
            <i class="bi bi-check-circle me-1"></i>Caracteristicas
          </button>
        </li>
        <li class="nav-item">
          <button
            class="nav-link"
            :class="{ active: activeTab === 'categories' }"
            @click="activeTab = 'categories'"
          >
            <i class="bi bi-folder me-1"></i>Categorias
          </button>
        </li>
      </ul>

      <div v-if="activeTab === 'features'">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div v-if="features.data.length === 0" class="text-center py-5">
              <i class="bi bi-check-circle display-1 text-muted"></i>
              <p class="text-muted mt-3">No hay caracteristicas creadas aun.</p>
              <button class="btn btn-primary" @click="openCreateModal">Crear primera caracteristica</button>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Orden</th>
                    <th>Nombre</th>
                    <th>Categoria</th>
                    <th>Icono</th>
                    <th>Negocios</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="feature in features.data" :key="feature.id">
                    <td>{{ feature.sort_order }}</td>
                    <td>
                      <strong>{{ feature.title }}</strong>
                      <p v-if="feature.description" class="text-muted small mb-0">{{ feature.description.substring(0, 60) }}...</p>
                    </td>
                    <td>
                      <span v-if="feature.category">
                        <i :class="feature.category.icon"></i> {{ feature.category.name }}
                      </span>
                      <span v-else class="text-muted">—</span>
                    </td>
                    <td><i :class="feature.icon || 'bi bi-check'"></i></td>
                    <td>{{ feature.businesses_count || 0 }}</td>
                    <td>
                      <span v-if="feature.is_active" class="badge bg-success">Activo</span>
                      <span v-else class="badge bg-secondary">Inactivo</span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary me-1" @click="openEditModal(feature)">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger" @click="deleteFeature(feature)" :disabled="(feature.businesses_count || 0) > 0">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div v-if="activeTab === 'categories'">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">Categorias</h5>
              <button class="btn btn-sm btn-primary" @click="openCategoryModal()">
                <i class="bi bi-plus me-1"></i>Nueva categoria
              </button>
            </div>

            <div v-if="categories.length === 0" class="text-center py-4">
              <i class="bi bi-folder display-1 text-muted"></i>
              <p class="text-muted mt-3">No hay categorias creadas.</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Orden</th>
                    <th>Nombre</th>
                    <th>Icono</th>
                    <th>Caracteristicas</th>
                    <th>Activo</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="cat in categories" :key="cat.id">
                    <td>{{ cat.sort_order }}</td>
                    <td>
                      <strong>{{ cat.name }}</strong>
                    </td>
                    <td><i :class="cat.icon || 'bi bi-folder'"></i></td>
                    <td>{{ cat.features_count || 0 }}</td>
                    <td>
                      <span v-if="cat.is_active" class="badge bg-success">Activo</span>
                      <span v-else class="badge bg-secondary">Inactivo</span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary me-1" @click="openCategoryModal(cat)">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger" @click="deleteCategory(cat)" :disabled="(cat.features_count || 0) > 0">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingFeature ? 'Editar caracteristica' : 'Nueva caracteristica' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitFeature">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label">Titulo</label>
                  <input v-model="form.title" type="text" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Categoria</label>
                  <select v-model="form.category_id" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                      {{ cat.name }}
                    </option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Descripcion</label>
                  <textarea v-model="form.description" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Icono (Bootstrap Icons)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i :class="form.icon || 'bi bi-check'"></i></span>
                    <input v-model="form.icon" type="text" class="form-control" placeholder="bi bi-check">
                  </div>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Orden</label>
                  <input v-model.number="form.sort_order" type="number" min="0" class="form-control">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                  <div class="form-check form-switch">
                    <input v-model="form.is_active" class="form-check-input" type="checkbox" id="isActive">
                    <label class="form-check-label" for="isActive">Activo</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                <span v-if="sending">Guardando...</span>
                <span v-else>{{ editingFeature ? 'Actualizar' : 'Crear' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div ref="categoryModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingCategory ? 'Editar categoria' : 'Nueva categoria' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitCategory">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label">Nombre</label>
                  <input v-model="categoryForm.name" type="text" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Orden</label>
                  <input v-model.number="categoryForm.sort_order" type="number" min="0" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Icono (Bootstrap Icons)</label>
                  <div class="input-group">
                    <span class="input-group-text"><i :class="categoryForm.icon || 'bi bi-folder'"></i></span>
                    <input v-model="categoryForm.icon" type="text" class="form-control" placeholder="bi bi-folder">
                  </div>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                  <div class="form-check form-switch">
                    <input v-model="categoryForm.is_active" class="form-check-input" type="checkbox" id="catIsActive">
                    <label class="form-check-label" for="catIsActive">Activo</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="categorySending">
                <span v-if="categorySending">Guardando...</span>
                <span v-else>{{ editingCategory ? 'Actualizar' : 'Crear' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref, onMounted, nextTick } from 'vue'
import { Modal } from 'bootstrap'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  features: Object,
  categories: Array,
})

const activeTab = ref('features')

const modalElement = ref(null)
const categoryModalElement = ref(null)
let featureModal = null
let categoryModal = null
let editingFeature = ref(null)
let editingCategory = ref(null)
let sending = false
let categorySending = false

const form = useForm({
  title: '',
  description: '',
  category_id: '',
  icon: 'bi bi-check',
  sort_order: 0,
  is_active: true,
})

const categoryForm = useForm({
  name: '',
  icon: 'bi bi-folder',
  sort_order: 0,
  is_active: true,
})

const openCreateModal = () => {
  editingFeature.value = null
  form.reset()
  form.icon = 'bi bi-check'
  form.sort_order = 0
  form.is_active = true
  nextTick(() => {
    featureModal.show()
  })
}

const openEditModal = (feature) => {
  editingFeature.value = feature
  form.title = feature.title
  form.description = feature.description || ''
  form.category_id = feature.category_id
  form.icon = feature.icon || 'bi bi-check'
  form.sort_order = feature.sort_order || 0
  form.is_active = feature.is_active
  nextTick(() => {
    featureModal.show()
  })
}

const submitFeature = () => {
  sending = true
  if (editingFeature.value) {
    form.put(`/admin/features/${editingFeature.value.id}`, {
      onFinish: () => {
        sending = false
        featureModal.hide()
      },
    })
  } else {
    form.post('/admin/features', {
      onFinish: () => {
        sending = false
        featureModal.hide()
      },
    })
  }
}

const deleteFeature = (feature) => {
  if (!confirm(`Estas seguro de eliminar "${feature.title}"?`)) {
    return
  }
  if ((feature.businesses_count || 0) > 0) {
    alert('No puedes eliminar una caracteristica que esta siendo usada por negocios.')
    return
  }
  router.delete(`/admin/features/${feature.id}`, {
    preserveScroll: true,
  })
}

const openCategoryModal = (category = null) => {
  editingCategory.value = category
  if (category) {
    categoryForm.name = category.name
    categoryForm.icon = category.icon || 'bi bi-folder'
    categoryForm.sort_order = category.sort_order || 0
    categoryForm.is_active = category.is_active
  } else {
    categoryForm.reset()
    categoryForm.icon = 'bi bi-folder'
    categoryForm.sort_order = 0
    categoryForm.is_active = true
  }
  nextTick(() => {
    categoryModal.show()
  })
}

const submitCategory = () => {
  categorySending = true
  if (editingCategory.value) {
    categoryForm.put(`/admin/feature-categories/${editingCategory.value.id}`, {
      onFinish: () => {
        categorySending = false
        categoryModal.hide()
      },
    })
  } else {
    categoryForm.post('/admin/feature-categories', {
      onFinish: () => {
        categorySending = false
        categoryModal.hide()
      },
    })
  }
}

const deleteCategory = (category) => {
  if (!confirm(`Estas seguro de eliminar "${category.name}"?`)) {
    return
  }
  if ((category.features_count || 0) > 0) {
    alert('No puedes eliminar una categoria que tiene caracteristicas.')
    return
  }
  router.delete(`/admin/feature-categories/${category.id}`, {
    preserveScroll: true,
  })
}

onMounted(() => {
  featureModal = new Modal(modalElement.value)
  categoryModal = new Modal(categoryModalElement.value)
})
</script>

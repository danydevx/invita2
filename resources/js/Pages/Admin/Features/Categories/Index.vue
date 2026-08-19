<template>
  <AdminLayout>
    <Head title="Categorias de caracteristicas" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Categorias de caracteristicas</h1>
          <p class="text-muted mb-0">Administracion de categorias para caracteristicas</p>
        </div>
        <button class="btn btn-primary" @click="openCreateModal">
          <i class="bi bi-plus me-1"></i>Nueva categoria
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div v-if="categories.length === 0" class="text-center py-5">
            <i class="bi bi-folder display-1 text-muted"></i>
            <p class="text-muted mt-3">No hay categorias creadas aun.</p>
            <button class="btn btn-primary" @click="openCreateModal">Crear primera categoria</button>
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
                <tr v-for="category in categories" :key="category.id">
                  <td>{{ category.sort_order }}</td>
                  <td>
                    <strong>{{ category.name }}</strong>
                    <p class="text-muted small mb-0">/{{ category.slug }}</p>
                  </td>
                  <td><i :class="category.icon"></i></td>
                  <td>{{ category.features_count || 0 }}</td>
                  <td>
                    <span v-if="category.is_active" class="badge bg-success">Activo</span>
                    <span v-else class="badge bg-secondary">Inactivo</span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary me-1" @click="openEditModal(category)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" @click="deleteCategory(category)" :disabled="(category.features_count || 0) > 0">
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

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingCategory ? 'Editar categoria' : 'Nueva categoria' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitCategory">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input v-model="form.name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Icono (Bootstrap Icons)</label>
                <div class="input-group">
                  <span class="input-group-text"><i :class="form.icon || 'bi bi-folder'"></i></span>
                  <input v-model="form.icon" type="text" class="form-control" placeholder="bi bi-folder">
                </div>
                <small class="text-muted">Usa clases de Bootstrap Icons como bi bi-wifi</small>
              </div>
              <div class="mb-3">
                <label class="form-label">Orden</label>
                <input v-model.number="form.sort_order" type="number" min="0" class="form-control">
              </div>
              <div class="form-check form-switch">
                <input v-model="form.is_active" class="form-check-input" type="checkbox" id="isActive">
                <label class="form-check-label" for="isActive">Categoria activa</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                <span v-if="sending">Guardando...</span>
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
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, onMounted, nextTick } from 'vue'
import { Modal } from 'bootstrap'

const props = defineProps({
  categories: Array,
})

const modalElement = ref(null)
let categoryModal = null
let editingCategory = ref(null)
let sending = false

const form = useForm({
  name: '',
  icon: 'bi bi-folder',
  sort_order: 0,
  is_active: true,
})

const openCreateModal = () => {
  editingCategory.value = null
  form.reset()
  form.icon = 'bi bi-folder'
  form.sort_order = 0
  form.is_active = true
  nextTick(() => {
    categoryModal.show()
  })
}

const openEditModal = (category) => {
  editingCategory.value = category
  form.name = category.name
  form.icon = category.icon || 'bi bi-folder'
  form.sort_order = category.sort_order || 0
  form.is_active = category.is_active
  nextTick(() => {
    categoryModal.show()
  })
}

const submitCategory = () => {
  sending = true
  if (editingCategory.value) {
    form.put(`/admin/feature-categories/${editingCategory.value.id}`, {
      onFinish: () => {
        sending = false
        categoryModal.hide()
      },
    })
  } else {
    form.post('/admin/feature-categories', {
      onFinish: () => {
        sending = false
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
    alert('No puedes eliminar una categoria que tiene caracteristicas asignadas.')
    return
  }
  router.delete(`/admin/feature-categories/${category.id}`, {
    preserveScroll: true,
  })
}

onMounted(() => {
  categoryModal = new Modal(modalElement.value)
})
</script>

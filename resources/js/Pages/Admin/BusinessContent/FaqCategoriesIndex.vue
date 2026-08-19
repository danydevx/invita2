<template>
  <AdminLayout>
    <Head :title="`Categorias FAQs - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/admin/businesses/${business.id}/faqs`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Volver a Preguntas
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - Categorias de FAQs</h1>
      </div>
      <button class="btn btn-primary btn-sm" @click="openCreateModal">
        <i class="bi bi-plus-lg me-1"></i>Nueva Categoria
      </button>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Descripcion</th>
                <th scope="col">Preguntas</th>
                <th scope="col">Activo</th>
                <th scope="col" class="text-end">Accion</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="categories.length === 0">
                <td colspan="5" class="text-center text-muted py-4">
                  No hay categorias registradas.
                </td>
              </tr>
              <tr v-for="cat in categories" :key="cat.id">
                <td class="fw-semibold">{{ cat.name }}</td>
                <td class="text-muted small">{{ cat.description || '-' }}</td>
                <td>
                  <span class="badge bg-light text-dark">{{ cat.faqs?.length || 0 }}</span>
                </td>
                <td>
                  <span v-if="cat.is_active" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary" @click="openEditModal(cat)">
                    Editar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
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
          <form @submit.prevent="editingCategory ? updateCategory() : createCategory()">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre *</label>
                <input type="text" v-model="form.name" class="form-control" required />
                <div v-if="errors.name" class="text-danger small">{{ errors.name }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <textarea v-model="form.description" class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-3">
                <div class="form-check">
                  <input type="checkbox" v-model="form.is_active" class="form-check-input" id="modal-is_active" />
                  <label class="form-check-label" for="modal-is_active">Activo</label>
                </div>
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
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const business = computed(() => page.props.business)
const categories = computed(() => page.props.categories || [])
const errors = computed(() => page.props.errors || {})

const modalElement = ref(null)
let categoryModal = null

const sending = ref(false)
const editingCategory = ref(null)

const form = reactive({
  name: '',
  description: '',
  is_active: true,
})

const openCreateModal = () => {
  editingCategory.value = null
  form.name = ''
  form.description = ''
  form.is_active = true
  nextTick(() => categoryModal.show())
}

const openEditModal = (category) => {
  editingCategory.value = category
  form.name = category.name
  form.description = category.description || ''
  form.is_active = category.is_active
  nextTick(() => categoryModal.show())
}

const closeModal = () => {
  categoryModal.hide()
}

const createCategory = () => {
  sending.value = true
  router.post(`/admin/businesses/${business.value.id}/faq-categories`, form, {
    onFinish: () => {
      sending.value = false
      closeModal()
    },
  })
}

const updateCategory = () => {
  sending.value = true
  router.put(`/admin/businesses/${business.value.id}/faq-categories/${editingCategory.value.id}`, form, {
    onFinish: () => {
      sending.value = false
      closeModal()
    },
  })
}

onMounted(() => {
  categoryModal = new Modal(modalElement.value)
})
</script>

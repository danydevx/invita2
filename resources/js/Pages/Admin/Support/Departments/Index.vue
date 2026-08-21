<template>
  <AdminLayout>
    <Head title="Departamentos de Soporte" />

    <PageHeader title="Departamentos de Soporte" :breadcrumbs="breadcrumbs">
      <template #actions>
        <button class="btn btn-primary btn-sm" @click="showCreateModal = true">
          <i class="bi bi-plus-lg me-1"></i>Nuevo departamento
        </button>
      </template>
    </PageHeader>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Orden</th>
              <th scope="col">Nombre</th>
              <th scope="col">Email</th>
              <th scope="col">Estado</th>
              <th scope="col">Tickets</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="departments.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay departamentos registrados.</td>
            </tr>
            <tr v-for="dept in departments" :key="dept.id">
              <td class="text-muted">{{ dept.sort_order }}</td>
              <td class="fw-semibold">{{ dept.name }}</td>
              <td class="text-muted">{{ dept.email || '-' }}</td>
              <td>
                <span :class="dept.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                  {{ dept.is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td class="text-muted">{{ dept.tickets_count }}</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary me-1" @click="openEditModal(dept)">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" @click="deleteDept(dept.id)" :disabled="dept.tickets_count > 0">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div ref="createModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo Departamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitCreate">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre *</label>
                <input v-model="createForm.name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="createForm.email" type="email" class="form-control" />
              </div>
              <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <textarea v-model="createForm.description" class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Orden</label>
                <input v-model.number="createForm.sort_order" type="number" class="form-control" min="0" />
              </div>
              <div class="form-check">
                <input v-model="createForm.is_active" class="form-check-input" type="checkbox" id="createIsActive" />
                <label class="form-check-label" for="createIsActive">Activo</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                <span v-if="sending">Guardando...</span>
                <span v-else>Crear</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div ref="editModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Departamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitEdit">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre *</label>
                <input v-model="editForm.name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="editForm.email" type="email" class="form-control" />
              </div>
              <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <textarea v-model="editForm.description" class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Orden</label>
                <input v-model.number="editForm.sort_order" type="number" class="form-control" min="0" />
              </div>
              <div class="form-check">
                <input v-model="editForm.is_active" class="form-check-input" type="checkbox" id="editIsActive" />
                <label class="form-check-label" for="editIsActive">Activo</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                <span v-if="sending">Guardando...</span>
                <span v-else>Actualizar</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import { Modal } from 'bootstrap'

const props = defineProps({
  departments: {
    type: Array,
    default: () => [],
  },
})

const breadcrumbs = [
  { label: 'Soporte', href: '/admin/support' },
  { label: 'Departamentos', active: true },
]

const createModalElement = ref(null)
const editModalElement = ref(null)
let createModal = null
let editModal = null
const sending = ref(false)

const createForm = ref({
  name: '',
  email: '',
  description: '',
  sort_order: 0,
  is_active: true,
})

const editForm = ref({
  id: null,
  name: '',
  email: '',
  description: '',
  sort_order: 0,
  is_active: true,
})

const openEditModal = (dept) => {
  editForm.value = {
    id: dept.id,
    name: dept.name,
    email: dept.email || '',
    description: dept.description || '',
    sort_order: dept.sort_order,
    is_active: dept.is_active,
  }
  editModal.show()
}

const submitCreate = () => {
  sending.value = true
  router.post('/admin/support/departments', createForm.value, {
    onFinish: () => {
      sending.value = false
      createModal.hide()
      createForm.value = {
        name: '',
        email: '',
        description: '',
        sort_order: 0,
        is_active: true,
      }
    },
  })
}

const submitEdit = () => {
  sending.value = true
  router.put(`/admin/support/departments/${editForm.value.id}`, editForm.value, {
    onFinish: () => {
      sending.value = false
      editModal.hide()
    },
  })
}

const deleteDept = (id) => {
  if (confirm('¿Estás seguro de eliminar este departamento?')) {
    router.delete(`/admin/support/departments/${id}`, {
      preserveScroll: true,
    })
  }
}

onMounted(() => {
  createModal = new Modal(createModalElement.value)
  editModal = new Modal(editModalElement.value)
})
</script>

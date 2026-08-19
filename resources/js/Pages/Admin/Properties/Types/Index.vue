<template>
  <AdminLayout>
    <Head title="Tipos de Propiedad" />

    <PageHeader title="Tipos de Propiedad" :breadcrumbs="breadcrumbs">
      <template #actions>
        <Link href="/admin/modules/properties/general-sections" class="btn btn-outline-secondary btn-sm me-2">
          <i class="bi bi-folder-plus me-1"></i>
          Campos Generales
        </Link>
        <button class="btn btn-primary btn-sm" @click="showCreateModal = true">
          <i class="bi bi-plus-lg me-1"></i>
          Nuevo Tipo
        </button>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Orden</th>
              <th>Icono</th>
              <th>Nombre</th>
              <th>Clave</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="type in types" :key="type.id">
              <td>{{ type.sort_order }}</td>
              <td><i :class="type.icon || 'bi bi-building'"></i></td>
              <td>{{ type.name }}</td>
              <td><code>{{ type.key }}</code></td>
              <td>
                <span :class="type.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                  {{ type.is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td>
                <div class="actions">
                  <Link
                    :href="`/admin/modules/properties/types/${type.id}/sections`"
                    class="btn btn-sm btn-outline-secondary"
                    title="Configurar secciones"
                  >
                    <i class="bi bi-list-check"></i>
                  </Link>
                  <button
                    class="btn btn-sm btn-outline-primary"
                    @click="editType(type)"
                    title="Editar"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger"
                    @click="deleteType(type)"
                    :disabled="deleting === type.id"
                    title="Eliminar"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="types.length === 0">
              <td colspan="6" class="text-center text-muted py-4">
                No hay tipos de propiedad. Crea el primero.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Modal :show="showCreateModal || showEditModal" @close="closeModals" size="lg">
      <template #header>
        <h5 class="modal-title">{{ showEditModal ? 'Editar Tipo' : 'Nuevo Tipo de Propiedad' }}</h5>
      </template>
      <form @submit.prevent="saveType">
        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text" class="form-control" v-model="typeForm.name" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Clave</label>
          <input type="text" class="form-control" v-model="typeForm.key" required />
          <small class="text-muted">使用snake_case，如：house, apartment</small>
        </div>
        <div class="mb-3">
          <label class="form-label">Icono (clase Bootstrap Icons)</label>
          <input type="text" class="form-control" v-model="typeForm.icon" placeholder="bi bi-house" />
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea class="form-control" v-model="typeForm.description" rows="2"></textarea>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" v-model="typeForm.is_active" id="typeActive" />
              <label class="form-check-label" for="typeActive">Activo</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" v-model="typeForm.is_public" id="typePublic" />
              <label class="form-check-label" for="typePublic">Público</label>
            </div>
          </div>
        </div>
      </form>
      <template #footer>
        <button type="button" class="btn btn-secondary" @click="closeModals">Cancelar</button>
        <button type="button" class="btn btn-primary" @click="saveType" :disabled="saving">
          {{ saving ? 'Guardando...' : 'Guardar' }}
        </button>
      </template>
    </Modal>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  types: Array,
})

const breadcrumbs = [
  { label: 'Admin', href: '/admin' },
  { label: 'Propiedades', href: '/admin/modules/properties/types' },
  { label: 'Tipos', active: true },
]

const showCreateModal = ref(false)
const showEditModal = ref(false)
const saving = ref(false)
const deleting = ref(null)
const editingType = ref(null)

const typeForm = reactive({
  name: '',
  key: '',
  icon: '',
  description: '',
  is_active: true,
  is_public: true,
})

const editType = (type) => {
  editingType.value = type
  typeForm.name = type.name
  typeForm.key = type.key
  typeForm.icon = type.icon || ''
  typeForm.description = type.description || ''
  typeForm.is_active = type.is_active
  typeForm.is_public = type.is_public
  showEditModal.value = true
}

const closeModals = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingType.value = null
  typeForm.name = ''
  typeForm.key = ''
  typeForm.icon = ''
  typeForm.description = ''
  typeForm.is_active = true
  typeForm.is_public = true
}

const saveType = () => {
  saving.value = true

  if (editingType.value) {
    router.put(`/admin/modules/properties/types/${editingType.value.id}`, typeForm, {
      onFinish: () => {
        saving.value = false
        closeModals()
      },
    })
  } else {
    router.post('/admin/modules/properties/types', typeForm, {
      onFinish: () => {
        saving.value = false
        closeModals()
      },
    })
  }
}

const deleteType = (type) => {
  if (confirm(`Eliminar el tipo "${type.name}"? Esta acción no se puede deshacer.`)) {
    deleting.value = type.id
    router.delete(`/admin/modules/properties/types/${type.id}`, {
      onFinish: () => {
        deleting.value = null
      },
    })
  }
}
</script>

<style scoped>
.actions {
  display: flex;
  gap: 0.25rem;
}
</style>

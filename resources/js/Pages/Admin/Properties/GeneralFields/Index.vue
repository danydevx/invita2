<template>
  <AdminLayout>
    <Head title="Secciones Generales" />

    <PageHeader
      title="Secciones Generales"
      :breadcrumbs="breadcrumbs"
    >
      <template #actions>
        <button @click="openSectionModal()" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Sección
        </button>
      </template>
    </PageHeader>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ $page.props.flash.error }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="sections.length === 0" class="text-center py-5">
          <i class="bi bi-grid-3x3-gap text-muted" style="font-size: 3rem;"></i>
          <p class="text-muted mt-3">No hay secciones definidas.</p>
        </div>

        <draggable
          v-else
          v-model="localSections"
          item-key="id"
          handle=".drag-handle"
          @end="onReorder"
          class="row g-3"
        >
          <template #item="{ element: section }">
            <div class="col-12">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center gap-2">
                    <span class="drag-handle" style="cursor: grab;">
                      <i class="bi bi-grip-vertical text-muted"></i>
                    </span>
                    <i :class="section.icon || 'bi bi-folder'"></i>
                    <strong>{{ section.name }}</strong>
                    <span v-if="!section.is_active" class="badge bg-secondary">Inactivo</span>
                    <span v-if="section.is_locked" class="badge bg-warning text-dark">
                      <i class="bi bi-lock-fill me-1"></i>Bloqueada
                    </span>
                  </div>
                  <div class="btn-group btn-group-sm">
                    <button
                      @click="toggleLock(section)"
                      class="btn"
                      :class="section.is_locked ? 'btn-outline-warning' : 'btn-outline-secondary'"
                      :title="section.is_locked ? 'Desbloquear sección' : 'Bloquear sección'"
                    >
                      <i :class="section.is_locked ? 'bi bi-unlock' : 'bi bi-lock'"></i>
                    </button>
                    <button @click="editSection(section)" class="btn btn-outline-secondary">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="deleteSection(section)" class="btn btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body p-0">
                  <ul class="list-group list-group-flush">
                    <li
                      v-for="field in section.fields || []"
                      :key="field.id"
                      class="list-group-item d-flex justify-content-between align-items-center"
                    >
                      <div>
                        <i :class="getFieldIcon(field.field_type)"></i>
                        <span class="ms-2" :class="{ 'text-muted': !field.is_active }">{{ field.label }}</span>
                        <small class="text-muted">({{ field.field_type }})</small>
                        <span v-if="!field.is_active" class="badge bg-secondary ms-1">Inactivo</span>
                      </div>
                      <div class="btn-group btn-group-sm">
                        <button @click="editField(section, field)" class="btn btn-outline-secondary">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button @click="deleteField(section, field)" class="btn btn-outline-danger">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </li>
                  </ul>
                  <div class="p-3 text-center border-top">
                    <button @click="openFieldModal(section)" class="btn btn-outline-primary btn-sm">
                      <i class="bi bi-plus-lg me-1"></i>
                      Agregar Campo
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </draggable>
      </div>
    </div>

    <!-- Section Modal -->
    <div class="modal fade" id="sectionModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingSection ? 'Editar Sección' : 'Nueva Sección' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="saveSection">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input v-model="sectionForm.name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Slug</label>
                <input v-model="sectionForm.slug" type="text" class="form-control">
                <small class="text-muted">Dejar vacío para generar automáticamente.</small>
              </div>
              <div class="mb-3">
                <label class="form-label">Icono (Bootstrap Icons)</label>
                <input v-model="sectionForm.icon" type="text" class="form-control" placeholder="bi bi-folder">
              </div>
              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea v-model="sectionForm.description" class="form-control" rows="2"></textarea>
              </div>
              <div class="mb-3 form-check">
                <input v-model="sectionForm.is_active" type="checkbox" class="form-check-input" id="sectionActive">
                <label class="form-check-label" for="sectionActive">Activo</label>
              </div>
              <div class="mb-3 form-check">
                <input v-model="sectionForm.is_locked" type="checkbox" class="form-check-input" id="sectionLocked">
                <label class="form-check-label" for="sectionLocked">Bloqueada (previene eliminar campos)</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Field Modal -->
    <div class="modal fade" id="fieldModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingField ? 'Editar Campo' : 'Agregar Campo' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="editingField ? updateField() : saveField()">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Label</label>
                  <input v-model="fieldForm.label" type="text" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Tipo de Campo</label>
                  <select v-model="fieldForm.field_type" class="form-select" required>
                    <option value="text">Texto</option>
                    <option value="textarea">Textarea</option>
                    <option value="number">Número</option>
                    <option value="decimal">Decimal</option>
                    <option value="price">Precio</option>
                    <option value="select">Selección</option>
                    <option value="multiselect">Multiselección</option>
                    <option value="radio">Radio</option>
                    <option value="checkbox">Checkbox</option>
                    <option value="date">Fecha</option>
                    <option value="url">URL</option>
                    <option value="email">Email</option>
                    <option value="phone">Teléfono</option>
                    <option value="image">Imagen</option>
                    <option value="boolean">Sí/No</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Key (para guardar)</label>
                  <input v-model="fieldForm.field_key" type="text" class="form-control">
                  <small class="text-muted">Dejar vacío para generar del label.</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Placeholder</label>
                  <input v-model="fieldForm.placeholder" type="text" class="form-control">
                </div>
                <div class="col-12">
                  <label class="form-label">Help Text</label>
                  <input v-model="fieldForm.help_text" type="text" class="form-control">
                </div>
                <div class="col-12">
                  <label class="form-check">
                    <input v-model="fieldForm.is_required" type="checkbox" class="form-check-input">
                    <span class="form-check-label">Requerido</span>
                  </label>
                </div>
                <div class="col-12" v-if="editingField">
                  <label class="form-check">
                    <input v-model="fieldForm.is_active" type="checkbox" class="form-check-input">
                    <span class="form-check-label">Activo</span>
                  </label>
                </div>

                <div v-if="['select', 'multiselect', 'radio', 'checkbox'].includes(fieldForm.field_type)" class="col-12">
                  <label class="form-label">Opciones</label>
                  <div class="border rounded p-3">
                    <div v-for="(opt, idx) in fieldForm.options" :key="idx" class="row g-2 mb-2">
                      <div class="col-5">
                        <input v-model="opt.value" type="text" class="form-control form-control-sm" placeholder="Valor">
                      </div>
                      <div class="col-5">
                        <input v-model="opt.label" type="text" class="form-control form-control-sm" placeholder="Label">
                      </div>
                      <div class="col-2">
                        <button type="button" @click="removeOption(idx)" class="btn btn-outline-danger btn-sm w-100">
                          <i class="bi bi-x"></i>
                        </button>
                      </div>
                    </div>
                    <button type="button" @click="addOption" class="btn btn-outline-primary btn-sm">
                      <i class="bi bi-plus-lg me-1"></i>Agregar Opción
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Guardando...' : (editingField ? 'Actualizar' : 'Guardar') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import draggable from 'vuedraggable'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const sections = ref(page.props.sections || [])

const breadcrumbs = [
  { label: 'Propiedades', href: '/admin/modules/properties' },
  { label: 'Tipos', href: '/admin/modules/properties/types' },
  { label: 'Secciones Generales' },
]

const showCreateModal = ref(false)
const editingSection = ref(null)
const saving = ref(false)
const currentSection = ref(null)
const editingField = ref(null)

const sectionForm = reactive({
  name: '',
  slug: '',
  icon: '',
  description: '',
  is_active: true,
  is_locked: false,
})

const fieldForm = reactive({
  label: '',
  field_key: '',
  field_type: 'text',
  placeholder: '',
  help_text: '',
  is_required: false,
  is_active: true,
  options: [],
})

const localSections = ref([...sections.value])

watch(
  () => page.props.sections,
  (newSections) => {
    sections.value = newSections || []
    localSections.value = Array.isArray(newSections) ? [...newSections] : []
  },
  { deep: true }
)

const openSectionModal = () => {
  editingSection.value = null
  sectionForm.name = ''
  sectionForm.slug = ''
  sectionForm.icon = ''
  sectionForm.description = ''
  sectionForm.is_active = true
  sectionForm.is_locked = false
  const modal = new Modal(document.getElementById('sectionModal'))
  modal.show()
}

const getFieldIcon = (type) => {
  const icons = {
    text: 'bi bi-type',
    textarea: 'bi bi-text-left',
    number: 'bi bi-hash',
    decimal: 'bi bi-calculator',
    price: 'bi bi-currency-dollar',
    select: 'bi bi-caret-down',
    multiselect: 'bi bi-check-all',
    radio: 'bi bi.ui-radios',
    checkbox: 'bi bi-check-square',
    date: 'bi bi-calendar',
    url: 'bi bi-link',
    email: 'bi bi-envelope',
    phone: 'bi bi-telephone',
    image: 'bi bi-image',
    boolean: 'bi bi-toggle-on',
  }
  return icons[type] || 'bi bi-input-cursor'
}

const onReorder = () => {
  const ids = localSections.value.map(s => s.id)
  router.post('/admin/modules/properties/general-sections/reorder', { sections: ids }, {
    preserveScroll: true,
  })
}

const editSection = (section) => {
  editingSection.value = section
  sectionForm.name = section.name
  sectionForm.slug = section.slug || ''
  sectionForm.icon = section.icon || ''
  sectionForm.description = section.description || ''
  sectionForm.is_active = !!section.is_active
  sectionForm.is_locked = !!section.is_locked

  const modal = new Modal(document.getElementById('sectionModal'))
  modal.show()
}

const saveSection = () => {
  saving.value = true
  const url = editingSection.value
    ? `/admin/modules/properties/general-sections/${editingSection.value.id}`
    : '/admin/modules/properties/general-sections'
  const method = editingSection.value ? 'put' : 'post'

  router[method](url, sectionForm, {
    onSuccess: () => {
      router.reload({ only: ['sections'], preserveScroll: true })
    },
    onFinish: () => {
      saving.value = false
      Modal.getInstance(document.getElementById('sectionModal'))?.hide()
    },
  })
}

const deleteSection = (section) => {
  if (!confirm(`¿Eliminar la sección "${section.name}" y todos sus campos?`)) return

  router.delete(`/admin/modules/properties/general-sections/${section.id}`, {
    onSuccess: () => {
      router.reload({ only: ['sections'], preserveScroll: true })
    },
    preserveScroll: true,
  })
}

const toggleLock = (section) => {
  const newLocked = !section.is_locked
  const action = newLocked ? 'bloquear' : 'desbloquear'
  if (!confirm(`¿${action.charAt(0).toUpperCase() + action.slice(1)} la sección "${section.name}"?`)) return

  router.put(`/admin/modules/properties/general-sections/${section.id}`, {
    is_locked: newLocked,
  }, {
    onSuccess: () => {
      router.reload({ only: ['sections'], preserveScroll: true })
    },
    preserveScroll: true,
  })
}

const openFieldModal = (section) => {
  editingField.value = null
  currentSection.value = section
  fieldForm.label = ''
  fieldForm.field_key = ''
  fieldForm.field_type = 'text'
  fieldForm.placeholder = ''
  fieldForm.help_text = ''
  fieldForm.is_required = false
  fieldForm.is_active = true
  fieldForm.options = []

  const modal = new Modal(document.getElementById('fieldModal'))
  modal.show()
}

const editField = (section, field) => {
  editingField.value = field
  currentSection.value = section
  fieldForm.label = field.label || ''
  fieldForm.field_key = field.field_key || ''
  fieldForm.field_type = field.field_type || 'text'
  fieldForm.placeholder = field.placeholder || ''
  fieldForm.help_text = field.help_text || ''
  fieldForm.is_required = !!field.is_required
  fieldForm.is_active = !!field.is_active
  fieldForm.options = field.fieldOptions ? field.fieldOptions.map(opt => ({ value: opt.value, label: opt.label })) : []

  const modal = new Modal(document.getElementById('fieldModal'))
  modal.show()
}

const addOption = () => {
  fieldForm.options.push({ value: '', label: '' })
}

const removeOption = (idx) => {
  fieldForm.options.splice(idx, 1)
}

const saveField = () => {
  saving.value = true

  router.post(`/admin/modules/properties/general-sections/${currentSection.value.id}/fields`, fieldForm, {
    onError: (errors) => {
      console.error('Error creating field:', errors)
      saving.value = false
    },
    onFinish: () => {
      saving.value = false
      Modal.getInstance(document.getElementById('fieldModal'))?.hide()
    },
  })
}

const updateField = () => {
  saving.value = true

  router.put(`/admin/modules/properties/general-sections/${currentSection.value.id}/fields/${editingField.value.id}`, fieldForm, {
    onFinish: () => {
      saving.value = false
      Modal.getInstance(document.getElementById('fieldModal'))?.hide()
    },
  })
}

const deleteField = (section, field) => {
  if (!confirm(`¿Eliminar el campo "${field.label}"?`)) return

  router.delete(`/admin/modules/properties/general-sections/${section.id}/fields/${field.id}`, {
    preserveScroll: true,
  })
}
</script>

<style scoped>
.drag-handle {
  cursor: grab;
  padding: 4px;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.drag-handle:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.drag-handle:active {
  cursor: grabbing;
}
</style>

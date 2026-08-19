<template>
  <AdminLayout>
    <Head :title="`Configurar Secciones - ${propertyType.name}`" />

    <PageHeader
      :title="`Configurar Secciones: ${propertyType.name}`"
      :breadcrumbs="breadcrumbs"
      backHref="/admin/modules/properties/types"
    />

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="row g-4">
      <!-- Available General Sections -->
      <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="bi bi-folder-plus me-2"></i>
              Secciones Disponibles
            </h5>
            <small class="text-muted">Arrastra al tipo de propiedad</small>
          </div>
          <div class="card-body p-2">
            <draggable
              v-if="availableGeneralSections.length > 0"
              :list="availableGeneralSections"
              item-key="id"
              :group="{ name: 'sections', pull: 'clone', put: false }"
              :clone="cloneSection"
              class="drag-area"
              :sort="false"
            >
              <template #item="{ element: section }">
                <div class="card mb-2">
                  <div class="card-body py-2 d-flex align-items-center gap-2">
                    <i :class="section.icon || 'bi bi-folder'"></i>
                    <div class="flex-grow-1">
                      <strong>{{ section.name }}</strong>
                      <small class="text-muted d-block">{{ section.fields?.length || 0 }} campos</small>
                    </div>
                    <i class="bi bi-arrows-move text-muted"></i>
                  </div>
                </div>
              </template>
            </draggable>
            <div v-else class="text-center py-4 text-muted">
              <i class="bi bi-folder-x" style="font-size: 2rem;"></i>
              <p class="mt-2 mb-0">No hay secciones generales creadas.</p>
              <Link href="/admin/modules/properties/general-sections" class="btn btn-outline-primary btn-sm mt-2">
                Crear Secciones
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Assigned Sections -->
      <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="bi bi-folder-check me-2"></i>
              Secciones Asignadas
            </h5>
          </div>
          <div class="card-body p-2">
            <!-- Locked Sections (always at top, not draggable) -->
            <div v-for="section in lockedSections" :key="section.id" class="card mb-2 border-secondary">
              <div class="card-header py-2 d-flex align-items-center gap-2 bg-light">
                <i :class="section.icon || 'bi bi-folder'" class="text-secondary"></i>
                <strong class="flex-grow-1 text-secondary">{{ section.name }}</strong>
                <span class="badge bg-secondary">Fijo</span>
              </div>
              <div class="card-body py-1">
                <ul class="list-unstyled mb-0 small">
                  <li
                    v-for="field in section.fields || []"
                    :key="field.id"
                    class="d-flex justify-content-between align-items-center py-1"
                  >
                    <span>
                      <i :class="getFieldIcon(field.field_type)"></i>
                      <span class="ms-2">{{ field.label }}</span>
                    </span>
                  </li>
                </ul>
              </div>
            </div>

            <draggable
              v-if="displayedSections.length > 0"
              v-model="displayedSections"
              item-key="id"
              group="sections"
              handle=".drag-handle"
              class="drag-area"
              @start="onDragStart"
              @end="onDragEnd"
              @add="onSectionAdded"
            >
              <template #item="{ element: section }">
                <div :class="['card', 'mb-2', section.isAmenities ? 'border-success' : (section.is_general ? 'border-primary' : '')]">
                  <template v-if="section.isAmenities">
                    <div class="card-header py-2 d-flex align-items-center gap-2 bg-success bg-opacity-10">
                      <i class="bi bi-grip-vertical drag-handle text-muted" style="cursor: grab;"></i>
                      <i class="bi bi-star text-success"></i>
                      <strong class="flex-grow-1 text-success">Amenidades</strong>
                      <span class="badge bg-success">{{ assignedAmenityIds.length }}</span>
                      <button
                        @click="openAmenitiesModal"
                        class="btn btn-outline-success btn-sm"
                        title="Configurar amenidades"
                      >
                        <i class="bi bi-pencil-square"></i>
                      </button>
                    </div>
                    <div class="card-body py-1">
                      <div v-if="allAmenities.length === 0" class="small text-muted">
                        No hay amenidades creadas.
                      </div>
                      <div v-else-if="assignedAmenityIds.length === 0" class="small text-muted">
                        No hay amenidades asignadas.
                      </div>
                      <div v-else class="d-flex flex-wrap gap-1">
                        <span
                          v-for="amenity in assignedAmenityObjects"
                          :key="amenity.id"
                          class="badge bg-success bg-opacity-25 text-success d-flex align-items-center gap-1"
                        >
                          <i :class="amenity.icon || 'bi bi-star'"></i>
                          {{ amenity.name }}
                        </span>
                      </div>
                    </div>
                  </template>

                  <template v-else>
                    <div class="card-header py-2 d-flex align-items-center gap-2">
                      <i v-if="!section.is_locked" class="bi bi-grip-vertical drag-handle text-muted" style="cursor: grab;"></i>
                      <i :class="section.icon || 'bi bi-folder'"></i>
                      <strong class="flex-grow-1">{{ section.name }}</strong>
                      <span v-if="section.is_general && !section.is_locked" class="badge bg-primary">Heredado</span>
                      <div class="btn-group btn-group-sm">
                        <button
                          v-if="section.is_general && !section.is_locked"
                          @click="openCustomizeModal(section)"
                          class="btn btn-outline-secondary"
                          title="Personalizar"
                        >
                          <i class="bi bi-pencil-square"></i>
                        </button>
                        <button
                          v-if="section.is_general && !section.is_locked"
                          @click="unassignSection(section)"
                          class="btn btn-outline-danger"
                          title="Desasignar"
                        >
                          <i class="bi bi-x-lg"></i>
                        </button>
                        <button
                          v-if="!section.is_general && !section.is_locked"
                          @click="deleteExclusiveSection(section)"
                          class="btn btn-outline-danger"
                          title="Eliminar"
                        >
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </div>
                    <div class="card-body py-1">
                      <ul class="list-unstyled mb-0 small">
                        <li
                          v-for="field in section.fields || []"
                          :key="field.id"
                          class="d-flex justify-content-between align-items-center py-1"
                        >
                          <span>
                            <i :class="getFieldIcon(field.field_type)"></i>
                            <span class="ms-2">{{ field.label }}</span>
                          </span>
                          <div class="btn-group btn-group-sm">
                            <button
                              v-if="!section.is_general"
                              @click="deleteField(section, field)"
                              class="btn btn-outline-danger btn-sm"
                              title="Eliminar campo"
                            >
                              <i class="bi bi-trash"></i>
                            </button>
                          </div>
                        </li>
                      </ul>
                      <div class="text-center mt-2" v-if="!section.is_general">
                        <button @click="openFieldModal(section)" class="btn btn-outline-primary btn-sm">
                          <i class="bi bi-plus-lg me-1"></i>
                          Agregar Campo
                        </button>
                      </div>
                    </div>
                  </template>
                </div>
              </template>
            </draggable>
            <div v-else class="text-center py-5 text-muted">
              <i class="bi bi-folder-plus" style="font-size: 2rem;"></i>
              <p class="mt-2">Arrastra secciones desde la izquierda</p>
            </div>
          </div>

          <!-- Exclusive Sections Header -->
          <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="mb-0">Secciones Exclusivas</h6>
              <button @click="openExclusiveSectionModal" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>
                Nueva Sección Exclusiva
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Customize Modal -->
    <div class="modal fade" id="customizeModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Personalizar: {{ customizingSection?.name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="saveCustomizations">
            <div class="modal-body">
              <p class="text-muted small">
                Personaliza los labels de los campos heredados para este tipo de propiedad.
              </p>
              <div v-if="customizingSection" class="border rounded p-3">
                <div v-for="field in customizingSection.fields" :key="field.id" class="mb-3">
                  <label class="form-label small text-muted">{{ field.label }} ({{ field.field_key }})</label>
                  <input
                    v-model="customizations[field.id]"
                    type="text"
                    class="form-control form-control-sm"
                    placeholder="Label personalizado"
                  >
                </div>
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

    <!-- Exclusive Section Modal -->
    <div class="modal fade" id="exclusiveSectionModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva Sección Exclusiva</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="createExclusiveSection">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input v-model="exclusiveSectionForm.name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea v-model="exclusiveSectionForm.description" class="form-control" rows="2"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Creando...' : 'Crear' }}
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
            <h5 class="modal-title">Agregar Campo a "{{ currentSection?.name }}"</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="saveField">
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
                {{ saving ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Amenities Modal -->
    <div class="modal fade" id="amenitiesModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Amenidades para {{ propertyType.name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="saveAmenities">
            <div class="modal-body">
              <p class="text-muted small mb-3">
                Selecciona las amenidades disponibles para este tipo de propiedad.
              </p>
              <div class="row g-3">
                <div v-for="amenity in allAmenities" :key="amenity.id" class="col-md-4 col-lg-3">
                  <div class="form-check">
                    <input
                      :id="'amenity-' + amenity.id"
                      v-model="localAmenityIds"
                      type="checkbox"
                      :value="amenity.id"
                      class="form-check-input"
                    >
                    <label :for="'amenity-' + amenity.id" class="form-check-label d-flex align-items-center gap-2">
                      <i :class="amenity.icon || 'bi bi-star'" style="font-size: 1rem;"></i>
                      {{ amenity.name }}
                    </label>
                  </div>
                </div>
              </div>
              <div v-if="allAmenities.length === 0" class="text-center py-4 text-muted">
                <p>No hay amenidades creadas.</p>
                <Link href="/admin/modules/properties/amenities" class="btn btn-outline-primary btn-sm">
                  Crear Amenidades
                </Link>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="savingAmenities">
                {{ savingAmenities ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import draggable from 'vuedraggable'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const propertyType = page.props.propertyType
const generalSections = ref(page.props.generalSections || [])
const localAssignedSections = ref(page.props.assignedSections || [])
const exclusiveSections = ref(page.props.exclusiveSections || [])
const allAmenities = ref(page.props.allAmenities || [])
const assignedAmenityIds = ref(page.props.assignedAmenityIds || [])
const localAmenityIds = ref([...assignedAmenityIds.value])
const savingAmenities = ref(false)

const breadcrumbs = [
  { label: 'Propiedades', href: '/admin/modules/properties' },
  { label: 'Tipos', href: '/admin/modules/properties/types' },
  { label: propertyType.name },
]

const assignedAmenityObjects = computed(() => {
  return allAmenities.value.filter(a => assignedAmenityIds.value.includes(a.id))
})

const availableGeneralSections = computed(() => {
  if (!generalSections.value || !localAssignedSections.value) return []
  try {
    const assignedIds = localAssignedSections.value.map(s => s?.id).filter(id => id != null)
    return generalSections.value.filter(s => !assignedIds.includes(s.id) && !s.is_locked)
  } catch (e) {
    console.error('Error computing available sections:', e)
    return []
  }
})

const saving = ref(false)
const customizingSection = ref(null)
const customizations = reactive({})

const exclusiveSectionForm = reactive({
  name: '',
  description: '',
})

const currentSection = ref(null)
const fieldForm = reactive({
  label: '',
  field_key: '',
  field_type: 'text',
  placeholder: '',
  help_text: '',
  is_required: false,
  options: [],
})

const rebuildDisplayedSections = () => {
  const nonLockedAssigned = localAssignedSections.value.filter(s => !s.is_locked)
  const sections = [...nonLockedAssigned, ...exclusiveSections.value]
  const sortedSections = sections.sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))

  const amenitiesItem = { isAmenities: true, id: 'amenities', name: 'Amenidades' }
  const amenitiesIndex = propertyType?.settings?.amenity_section_sort_order
  const maxIndex = sortedSections.length

  if (typeof amenitiesIndex === 'number' && amenitiesIndex >= 0 && amenitiesIndex <= maxIndex) {
    sortedSections.splice(amenitiesIndex, 0, amenitiesItem)
  } else {
    sortedSections.push(amenitiesItem)
  }

  displayedSections.value = sortedSections
}

const lockedSections = computed(() => {
  return localAssignedSections.value
    .filter(s => s.is_locked)
    .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
})

const displayedSections = ref([])
rebuildDisplayedSections()

watch(
  () => [localAssignedSections.value.length, exclusiveSections.value.length],
  () => {
    rebuildDisplayedSections()
  }
)

let dragUpdateTimeout = null
let previousDisplayedSectionIds = []

const onDragStart = () => {
  previousDisplayedSectionIds = displayedSections.value.map(s => s.isAmenities ? 'amenities' : s.id)
}

const onSectionAdded = (evt) => {
  console.log('onSectionAdded called', evt)
  const addedSection = displayedSections.value[evt.newIndex]
  console.log('Added section:', addedSection)
  if (!addedSection || addedSection.isAmenities) return

  // Check if this is actually a new assignment (not a reorder)
  const wasInAssigned = localAssignedSections.value.some(s => s.id === addedSection.id)
  const wasInExclusive = exclusiveSections.value.some(s => s.id === addedSection.id)
  console.log('wasInAssigned:', wasInAssigned, 'wasInExclusive:', wasInExclusive)

  if (!wasInAssigned && !wasInExclusive && addedSection.is_general) {
    console.log('Detected new section assignment, calling assign endpoint')
    // This is a new section being assigned
    if (!localAssignedSections.value.find(s => s.id === addedSection.id)) {
      localAssignedSections.value.push(addedSection)
    }

    router.post(`/admin/modules/properties/types/${propertyType.id}/assign-sections`, {
      section_ids: [addedSection.id],
    }, {
      preserveScroll: true,
      onSuccess: () => {
        console.log('Assign success, reloading')
        setTimeout(() => router.reload({ preserveScroll: true }), 300)
      },
      onError: (err) => {
        console.error('Assign error:', err)
      },
    })
  }
}

const onDragEnd = () => {
  if (dragUpdateTimeout) clearTimeout(dragUpdateTimeout)

  dragUpdateTimeout = setTimeout(() => {
    const sectionIds = displayedSections.value.map(s => s.isAmenities ? 'amenities' : s.id)
    router.post(`/admin/modules/properties/types/${propertyType.id}/reorder-sections`, {
      section_ids: sectionIds,
    }, {
      preserveScroll: true,
    })
  }, 100)
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

const cloneSection = (section) => {
  return { ...section, is_general: true }
}

const openCustomizeModal = (section) => {
  customizingSection.value = section
  Object.keys(customizations).forEach(key => delete customizations[key])

  if (section.custom_settings?.fields) {
    Object.assign(customizations, section.custom_settings.fields)
  }

  section.fields.forEach(field => {
    if (!customizations[field.id]) {
      customizations[field.id] = ''
    }
  })

  const modal = new Modal(document.getElementById('customizeModal'))
  modal.show()
}

const saveCustomizations = () => {
  saving.value = true

  const fieldsCustomizations = {}
  Object.entries(customizations).forEach(([fieldId, label]) => {
    if (label) {
      fieldsCustomizations[fieldId] = { label }
    }
  })

  router.post(`/admin/modules/properties/types/${propertyType.id}/customizations`, {
    section_id: customizingSection.value.general_field_section_id || customizingSection.value.id,
    custom_settings: { fields: fieldsCustomizations },
  }, {
    onFinish: () => {
      saving.value = false
      Modal.getInstance(document.getElementById('customizeModal'))?.hide()
    },
  })
}

const unassignSection = (section) => {
  if (!confirm(`¿Desasignar la sección "${section.name}"?`)) return

  router.delete(`/admin/modules/properties/types/${propertyType.id}/unassign-section/${section.general_field_section_id || section.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      localAssignedSections.value = localAssignedSections.value.filter(s => s.id !== section.id)
    },
  })
}

const deleteExclusiveSection = (section) => {
  if (!confirm(`¿Eliminar la sección "${section.name}" y todos sus campos?`)) return

  router.delete(`/admin/modules/properties/types/${propertyType.id}/sections/${section.id}`, {
    preserveScroll: true,
  })
}

const openExclusiveSectionModal = () => {
  exclusiveSectionForm.name = ''
  exclusiveSectionForm.description = ''
  const modal = new Modal(document.getElementById('exclusiveSectionModal'))
  modal.show()
}

const createExclusiveSection = () => {
  saving.value = true

  router.post(`/admin/modules/properties/types/${propertyType.id}/sections`, exclusiveSectionForm, {
    onSuccess: () => {
      saving.value = false
      Modal.getInstance(document.getElementById('exclusiveSectionModal'))?.hide()
      router.reload({ preserveScroll: true })
    },
    onFinish: () => {
      saving.value = false
    },
  })
}

const openFieldModal = (section) => {
  currentSection.value = section
  fieldForm.label = ''
  fieldForm.field_key = ''
  fieldForm.field_type = 'text'
  fieldForm.placeholder = ''
  fieldForm.help_text = ''
  fieldForm.is_required = false
  fieldForm.options = []
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

  const data = {
    section_id: currentSection.value.id,
    label: fieldForm.label,
    field_key: fieldForm.field_key,
    field_type: fieldForm.field_type,
    placeholder: fieldForm.placeholder,
    help_text: fieldForm.help_text,
    is_required: fieldForm.is_required,
    options_list: fieldForm.options,
  }

  router.post(`/admin/modules/properties/types/${propertyType.id}/fields`, data, {
    onSuccess: () => {
      saving.value = false
      Modal.getInstance(document.getElementById('fieldModal'))?.hide()
      router.reload({ preserveScroll: true })
    },
    onFinish: () => {
      saving.value = false
    },
  })
}

const deleteField = (section, field) => {
  if (!confirm(`¿Eliminar el campo "${field.label}"?`)) return

  router.delete(`/admin/modules/properties/types/${propertyType.id}/fields/${field.id}`, {
    preserveScroll: true,
  })
}

const openAmenitiesModal = () => {
  localAmenityIds.value = [...assignedAmenityIds.value]
  const modal = new Modal(document.getElementById('amenitiesModal'))
  modal.show()
}

const saveAmenities = () => {
  savingAmenities.value = true

  router.post(`/admin/modules/properties/types/${propertyType.id}/amenities`, {
    amenity_ids: localAmenityIds.value,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      assignedAmenityIds.value = [...localAmenityIds.value]
      Modal.getInstance(document.getElementById('amenitiesModal'))?.hide()
    },
    onFinish: () => {
      savingAmenities.value = false
    },
  })
}
</script>

<style scoped>
.drag-area {
  min-height: 100px;
}
</style>

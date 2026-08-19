<template>
  <MemberLayout>
    <Head :title="`Form Builder - ${form?.name || 'Formulario'} - ${business?.name || ''}`" />

    <PageHeader
      title="Constructor de Formulario"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/contact-forms`"
    >
      <template #actions>
        <Link
          :href="`/member/businesses/${business?.id}/contact-forms/${form?.id}/preview`"
          class="btn btn-outline-secondary"
          target="_blank"
        >
          <i class="bi bi-eye me-1"></i>
          Vista Previa
        </Link>
        <button class="btn btn-primary" @click="showFieldModal = true">
          <i class="bi bi-plus me-1"></i>
          Agregar Campo
        </button>
      </template>
    </PageHeader>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="row">
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h5 class="mb-0">{{ form?.name }}</h5>
                <small class="text-muted">
                  Código: <code>{{ form?.shortcode }}</code>
                  <button class="btn btn-sm p-0 ms-1" @click="copyShortcode" title="Copiar">
                    <i class="bi bi-clipboard"></i>
                  </button>
                </small>
              </div>
              <div class="form-check form-switch">
                <input
                  v-model="isActive"
                  type="checkbox"
                  class="form-check-input"
                  id="isActive"
                  @change="toggleActive"
                />
                <label class="form-check-label" for="isActive">
                  <span v-if="isActive" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </label>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div v-if="fields.length === 0" class="text-center py-5">
              <i class="bi bi-input-cursor text-muted" style="font-size: 3rem;"></i>
              <p class="text-muted mt-3">No hay campos configurados.</p>
              <p class="text-muted small">Agrega campos para construir tu formulario de contacto.</p>
              <button class="btn btn-primary" @click="showFieldModal = true">
                <i class="bi bi-plus me-1"></i>Agregar Primer Campo
              </button>
            </div>

            <draggable
              v-else
              v-model="localFields"
              item-key="id"
              handle=".field-drag-handle"
              ghost-class="sortable-ghost"
              @end="onDragEnd"
              class="field-list"
            >
              <template #item="{ element: field }">
                <div
                  class="field-item border rounded p-3 mb-2"
                  :class="{ 'bg-light': !field.is_active }"
                >
                  <div class="d-flex align-items-start justify-content-between">
                    <div class="d-flex align-items-start gap-3">
                      <div class="field-drag-handle text-muted">
                        <i class="bi bi-grip-vertical"></i>
                      </div>
                      <div>
                        <div class="d-flex align-items-center gap-2">
                          <span class="badge bg-secondary">{{ field.field_config?.type || field.type }}</span>
                          <strong>{{ field.field_config?.label || field.label }}</strong>
                          <span v-if="field.row" class="badge bg-info">Fila {{ field.row }}</span>
                          <span v-if="field.width && field.width !== 'full'" class="badge bg-dark">{{ field.width }}</span>
                          <span v-if="field.field_config?.required || field.is_required" class="badge bg-danger">Requerido</span>
                          <span v-if="!field.is_active" class="badge bg-warning">Inactivo</span>
                        </div>
                        <p class="text-muted small mb-0 mt-1">
                          <span v-if="field.field_config?.description">{{ field.field_config.description }}</span>
                          <span v-if="field.field_config?.placeholder">
                            <br>{{ field.field_config.placeholder }}
                          </span>
                        </p>
                        <p class="text-muted small mb-0">
                          <code>{{ field.field_config?.name || field.name }}</code>
                        </p>
                      </div>
                    </div>
                    <div class="btn-group">
                      <button class="btn btn-sm btn-outline-secondary" @click="editField(field)">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger" @click="deleteField(field)">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </template>
            </draggable>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm sticky-top" style="top: 1rem;">
          <div class="card-header bg-white">
            <h6 class="mb-0"><i class="bi bi-eye me-1"></i>Vista Previa</h6>
          </div>
          <div class="card-body">
            <div v-if="localFields.length === 0" class="text-center py-4">
              <p class="text-muted small mb-0">Agrega campos para ver la vista previa</p>
            </div>

            <div v-else class="preview-layout">
              <div v-for="(rowFields, rowIndex) in previewFieldsByRow" :key="rowIndex" class="row g-2 mb-2">
                <div
                  v-for="field in rowFields"
                  :key="field.id"
                  class="preview-field"
                  :class="getFieldWidthClass(field)"
                >
                  <div class="preview-field-inner border rounded p-2 bg-light">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="badge bg-secondary">{{ field.field_config?.type || field.type }}</span>
                      <small class="text-muted">{{ getWidthPercent(field) }}</small>
                    </div>
                    <div class="fw-medium small">
                      {{ field.field_config?.label || field.label }}
                      <span v-if="field.field_config?.required || field.is_required" class="text-danger">*</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.65rem;">
                      {{ field.field_config?.name || field.name }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <FieldModal
      v-if="showFieldModal"
      :field="editingField"
      @close="closeFieldModal"
      @save="saveField"
    />
  </MemberLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import draggable from 'vuedraggable'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldModal from '@/Components/ContactForm/FieldModal.vue'

const props = defineProps({
  business: Object,
  form: Object,
  fields: {
    type: Array,
    default: () => [],
  },
})

const page = usePage()
const business = computed(() => page.props.business)
const showFieldModal = ref(false)
const editingField = ref(null)
const isActive = ref(props.form?.is_active || false)
const localFields = ref([...props.fields])
const businessMenu = computed(() => page.props.businessMenu || [])

watch(() => props.fields, (newFields) => {
  localFields.value = [...newFields]
}, { deep: true })

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
        { label: 'Formularios', href: `/member/businesses/${biz.id}/contact-forms` },
        { label: 'Editar Formulario', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Formularios', active: true },
  ]
})

const copyShortcode = () => {
  if (props.form?.shortcode) {
    navigator.clipboard.writeText(props.form.shortcode)
  }
}

const toggleActive = () => {
  router.put(`/member/businesses/${business.value.id}/contact-forms/${props.form.id}`, {
    is_active: isActive.value,
  }, {
    preserveScroll: true,
  })
}

const editField = (field) => {
  editingField.value = { ...field }
  showFieldModal.value = true
}

const closeFieldModal = () => {
  showFieldModal.value = false
  editingField.value = null
}

const saveField = (fieldData) => {
  if (editingField.value?.id) {
    router.put(
      `/member/businesses/${business.value.id}/contact-forms/${props.form.id}/fields/${editingField.value.id}`,
      fieldData,
      {
        onSuccess: () => closeFieldModal(),
      }
    )
  } else {
    router.post(
      `/member/businesses/${business.value.id}/contact-forms/${props.form.id}/fields`,
      fieldData,
      {
        onSuccess: () => closeFieldModal(),
      }
    )
  }
}

const deleteField = (field) => {
  if (confirm(`¿Eliminar el campo "${field.field_config?.label || field.label}"?`)) {
    router.delete(
      `/member/businesses/${business.value.id}/contact-forms/${props.form.id}/fields/${field.id}`,
      {
        preserveScroll: true,
      }
    )
  }
}

const onDragEnd = () => {
  const orders = localFields.value.map((field, index) => ({
    id: field.id,
    order: index + 1,
  }))

  router.post(
    `/member/businesses/${business.value.id}/contact-forms/${props.form.id}/reorder`,
    { fields: orders },
    { preserveScroll: true }
  )
}

const previewFieldsByRow = computed(() => {
  const rows = {}
  localFields.value
    .filter(f => f.is_active)
    .forEach(field => {
      const row = field.row || 1
      if (!rows[row]) {
        rows[row] = []
      }
      rows[row].push(field)
    })
  return Object.values(rows)
})

const getWidthPercent = (field) => {
  const width = field.width || field.field_config?.width || 'full'
  const widthMap = {
    full: '100%',
    half: '50%',
    third: '33%',
    quarter: '25%',
  }
  return widthMap[width] || '100%'
}

const getFieldWidthClass = (field) => {
  const width = field.width || field.field_config?.width || 'full'
  const widthMap = {
    full: 'col-12',
    half: 'col-12 col-md-6',
    third: 'col-12 col-md-4',
    quarter: 'col-12 col-md-3',
  }
  return widthMap[width] || 'col-12'
}
</script>

<style scoped>
.field-item {
  transition: all 0.2s;
}

.field-item:hover {
  background-color: #f8f9fa;
}

.field-drag-handle {
  cursor: grab;
}

.field-drag-handle:active {
  cursor: grabbing;
}

.sortable-ghost {
  opacity: 0.5;
  background-color: #e9ecef;
}

:deep(.sortable-chosen) {
  background-color: #f8f9fa;
}

.preview-field {
  min-height: 60px;
}

.preview-field-inner {
  height: 100%;
  min-height: 60px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
</style>

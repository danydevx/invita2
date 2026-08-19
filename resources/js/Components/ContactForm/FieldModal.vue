<template>
  <dialog
    ref="dialogRef"
    class="border-0 rounded-3 p-0 shadow-lg"
    style="width: 900px; max-width: 95vw; max-height: 90vh;"
    @click="onBackdropClick"
    @cancel.prevent="close"
  >
    <div class="d-flex flex-column h-100">
      <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
        <h5 class="mb-0">{{ field?.id ? 'Editar Campo' : 'Agregar Campo' }}</h5>
        <button type="button" class="btn-close" aria-label="Cerrar" @click="close"></button>
      </div>

      <form @submit.prevent="save" class="flex-grow-1 overflow-auto">
        <div class="p-4">
          <div class="row g-4">
            <div class="col-12 col-md-4">
              <FieldSelect
                id="field-type"
                label="Tipo"
                v-model="config.type"
                :options="typeOptions"
                required
                @change="onTypeChange"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="field-label"
                label="Etiqueta"
                v-model="config.label"
                placeholder="Ej: Nombre completo"
                required
                @input="autoGenerateName"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldTextGenerator
                id="field-name"
                label="Nombre"
                v-model="config.name"
                placeholder="nombre_campo"
                generator-label="Generar"
                required
                @generate="generateName"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="field-placeholder"
                label="Marcador de posición"
                v-model="config.placeholder"
                placeholder="Texto dentro del campo"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="field-description"
                label="Texto de ayuda"
                v-model="config.description"
                placeholder="Texto debajo del campo"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                v-if="config.type !== 'select' && config.type !== 'checkbox' && config.type !== 'radio'"
                id="field-value"
                label="Valor por defecto"
                v-model="config.value"
                placeholder="Valor predeterminado"
              />
              <FieldSelect
                v-else-if="config.type === 'checkbox'"
                id="field-value"
                label="Valor por defecto"
                v-model="config.default_value"
                :options="booleanOptions"
              />
              <FieldSelect
                v-else
                id="field-value"
                label="Valor por defecto"
                v-model="config.default_value"
                :options="defaultValueOptions"
              />
            </div>

            <div class="col-12 col-md-4" v-if="config.type === 'text' || config.type === 'textarea' || config.type === 'email' || config.type === 'phone' || config.type === 'number' || config.type === 'url'">
              <FieldNumber
                id="field-maxlength"
                label="Longitud máxima"
                v-model="config.maxlength"
                placeholder="255"
                :min="1"
              />
            </div>

            <div class="col-12 col-md-4" v-if="config.type === 'date'">
              <FieldSelect
                id="field-min-date-type"
                label="Fecha mínima"
                v-model="config.min_date_type"
                :options="dateTypeOptions"
              />
            </div>

            <div class="col-12 col-md-4" v-if="config.type === 'date' && config.min_date_type === 'date'">
              <FieldDate
                id="field-min-date"
                label="Fecha mínima"
                v-model="config.min_date"
              />
            </div>

            <div class="col-12 col-md-4" v-if="config.type === 'date'">
              <FieldSelect
                id="field-max-date-type"
                label="Fecha máxima"
                v-model="config.max_date_type"
                :options="dateTypeOptions"
              />
            </div>

            <div class="col-12 col-md-4" v-if="config.type === 'date' && config.max_date_type === 'date'">
              <FieldDate
                id="field-max-date"
                label="Fecha máxima"
                v-model="config.max_date"
              />
            </div>

            <div class="col-12 col-md-4" v-if="config.type === 'file'">
              <FieldMultiSelect
                id="field-file-types"
                label="Tipos de archivo"
                v-model="config.file_types"
                :options="fileTypeOptions"
                :height="80"
                hint="Seleccione los tipos permitidos"
              />
            </div>

            <div class="col-12 col-md-4" v-if="config.type === 'image'">
              <FieldMultiSelect
                id="field-image-types"
                label="Tipos de imagen"
                v-model="config.file_types"
                :options="imageTypeOptions"
                :height="60"
                hint="Por defecto: JPG, PNG, WebP"
              />
            </div>

            <div class="col-12 col-md-4" v-if="config.type === 'file' || config.type === 'image'">
              <FieldSelect
                id="field-max-file-size"
                label="Tamaño máximo"
                v-model="config.max_file_size"
                :options="fileSizeOptions"
              />
            </div>

            <div class="col-12 col-md-4" v-if="config.type === 'select'">
              <FieldSwitch
                id="field-multiple"
                label="Selección múltiple"
                v-model="config.multiple"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldSelect
                id="field-width"
                label="Ancho en formulario"
                v-model="config.width"
                :options="widthOptions"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldNumber
                id="field-row"
                label="Fila"
                v-model="config.row"
                :min="1"
                placeholder="1"
              />
            </div>

            <div class="col-12 col-md-4">
              <FieldText
                id="field-class"
                label="Clase CSS"
                v-model="config.className"
                placeholder="form-control"
              />
            </div>

            <div class="col-12 col-md-4 d-flex align-items-center">
              <FieldSwitch
                id="field-required"
                label="Campo requerido"
                v-model="config.required"
              />
            </div>
          </div>

          <div v-if="config.type === 'select' || config.type === 'radio' || config.type === 'checkbox'" class="mt-4">
            <label class="form-label fw-semibold">Opciones</label>
            <div class="row g-2 mb-2">
              <div class="col-6">
                <small class="text-muted">Valor</small>
              </div>
              <div class="col-5">
                <small class="text-muted">Etiqueta</small>
              </div>
              <div class="col-1"></div>
            </div>
            <div
              v-for="(opt, index) in optionsArray"
              :key="index"
              class="row g-2 mb-2 align-items-center"
            >
              <div class="col-6">
                <FieldText
                  v-model="opt.value"
                  placeholder="valor"
                />
              </div>
              <div class="col-5">
                <FieldText
                  v-model="opt.label"
                  placeholder="Etiqueta"
                />
              </div>
              <div class="col-1">
                <button
                  type="button"
                  class="btn btn-outline-danger btn-sm w-100"
                  @click="removeOption(index)"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
            <div v-if="optionsArray.length === 0" class="text-muted small text-center py-2 border rounded">
              Sin opciones. Agrega una abajo.
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" @click="addOption">
              <i class="bi bi-plus me-1"></i>Agregar opción
            </button>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 px-4 py-3 border-top bg-light">
          <button type="button" class="btn btn-outline-secondary" @click="close">
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary">
            {{ field?.id ? 'Actualizar' : 'Agregar' }}
          </button>
        </div>
      </form>
    </div>
  </dialog>
</template>

<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldTextGenerator from '@/Components/Fields/FieldTextGenerator.vue'
import FieldMultiSelect from '@/Components/Fields/FieldMultiSelect.vue'

const props = defineProps({
  field: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close', 'save'])

const dialogRef = ref(null)

const typeOptions = [
  { value: 'text', label: 'Texto' },
  { value: 'email', label: 'Correo' },
  { value: 'phone', label: 'Teléfono' },
  { value: 'number', label: 'Número' },
  { value: 'password', label: 'Contraseña' },
  { value: 'url', label: 'URL' },
  { value: 'textarea', label: 'Área texto' },
  { value: 'select', label: 'Selección' },
  { value: 'checkbox', label: 'Casilla' },
  { value: 'radio', label: 'Opción' },
  { value: 'date', label: 'Fecha' },
  { value: 'file', label: 'Archivo' },
  { value: 'image', label: 'Imagen' },
]

const booleanOptions = [
  { value: true, label: 'Marcado' },
  { value: false, label: 'Sin marcar' },
]

const dateTypeOptions = [
  { value: 'none', label: 'Ninguna' },
  { value: 'now', label: 'Actual' },
  { value: 'date', label: 'Específica' },
]

const fileTypeOptions = [
  { value: 'pdf', label: 'PDF' },
  { value: 'xlsx', label: 'Excel' },
  { value: 'docx', label: 'Word' },
]

const imageTypeOptions = [
  { value: 'jpg', label: 'JPG' },
  { value: 'png', label: 'PNG' },
  { value: 'webp', label: 'WebP' },
]

const fileSizeOptions = [
  { value: 1, label: '1 MB' },
  { value: 2, label: '2 MB' },
  { value: 5, label: '5 MB' },
  { value: 10, label: '10 MB' },
]

const widthOptions = [
  { value: 'full', label: 'Completo (100%)' },
  { value: 'half', label: 'Mitad (50%)' },
  { value: 'third', label: 'Tercio (33%)' },
  { value: 'quarter', label: 'Cuarto (25%)' },
]

const defaultValueOptions = computed(() => {
  return [
    { value: '', label: '-- Ninguno --' },
    ...optionsArray.value.map(opt => ({ value: opt.value, label: opt.label || opt.value }))
  ]
})

onMounted(() => {
  if (dialogRef.value) {
    dialogRef.value.showModal()
    if (!props.field?.id) {
      generateName()
    }
  }
})

const config = ref({
  type: 'text',
  required: false,
  label: '',
  description: '',
  placeholder: '',
  className: 'form-control',
  name: '',
  subtype: '',
  maxlength: 255,
  value: '',
  options: [],
  access: null,
  role: null,
  row: 1,
  width: 'full',
  multiple: false,
  default_value: '',
  min_date_type: 'none',
  min_date: '',
  max_date_type: 'none',
  max_date: '',
  file_types: ['pdf', 'xlsx', 'docx'],
  max_file_size: 20,
})

const optionsArray = ref([])

const generateId = () => {
  return Math.random().toString(36).substring(2, 10)
}

const addOption = () => {
  const lastOpt = optionsArray.value[optionsArray.value.length - 1]
  const nextNum = lastOpt ? parseInt(lastOpt.value.replace(/\D/g, '')) + 1 || optionsArray.value.length + 1 : 1
  optionsArray.value.push({ value: `option_${nextNum}`, label: `Option ${nextNum}` })
}

const removeOption = (index) => {
  optionsArray.value.splice(index, 1)
}

const generateName = () => {
  const label = config.value.label || 'field'
  const slug = label.toLowerCase()
    .replace(/[^a-z0-9]+/g, '_')
    .replace(/^_|_$/g, '')
  config.value.name = `field_${slug}_${generateId()}`
}

const autoGenerateName = () => {
  if (!config.value.name || config.value.name.startsWith('field_')) {
    generateName()
  }
}

const onTypeChange = () => {
  if (config.value.type === 'select' || config.value.type === 'radio' || config.value.type === 'checkbox') {
    if (optionsArray.value.length === 0) {
      optionsArray.value = [
        { value: 'option_1', label: 'Option 1' },
        { value: 'option_2', label: 'Option 2' },
      ]
    }
  }
  if (config.value.type === 'image') {
    config.value.file_types = ['jpg', 'png', 'webp']
    config.value.max_file_size = 5
  } else if (config.value.type === 'file') {
    config.value.file_types = ['pdf', 'xlsx', 'docx']
    config.value.max_file_size = 20
  }
}

watch(
  () => props.field,
  (field) => {
    if (field) {
      const fieldConfig = field.field_config || {}
      const rawOptions = fieldConfig.options || field.options || []

      let parsedOptions = []
      if (rawOptions.length > 0) {
        if (typeof rawOptions[0] === 'object' && rawOptions[0] !== null) {
          parsedOptions = rawOptions
        } else {
          parsedOptions = rawOptions.map((opt, idx) => ({
            value: `option_${idx + 1}`,
            label: opt,
          }))
        }
      }

      config.value = {
        type: fieldConfig.type || field.type || 'text',
        required: fieldConfig.required ?? field.is_required ?? false,
        label: fieldConfig.label || field.label || '',
        description: fieldConfig.description || '',
        placeholder: fieldConfig.placeholder || field.placeholder || '',
        className: fieldConfig.className || 'form-control',
        name: fieldConfig.name || field.name || '',
        subtype: fieldConfig.subtype || '',
        maxlength: fieldConfig.maxlength || 255,
        value: fieldConfig.value || '',
        options: parsedOptions,
        access: fieldConfig.access || null,
        role: fieldConfig.role || null,
        row: fieldConfig.row || field.row || 1,
        width: fieldConfig.width || field.width || 'full',
        multiple: fieldConfig.multiple ?? field.multiple ?? false,
        default_value: fieldConfig.default_value ?? field.default_value ?? '',
        min_date_type: fieldConfig.min_date_type || 'none',
        min_date: fieldConfig.min_date || '',
        max_date_type: fieldConfig.max_date_type || 'none',
        max_date: fieldConfig.max_date || '',
        file_types: fieldConfig.file_types || ['pdf', 'xlsx', 'docx'],
        max_file_size: fieldConfig.max_file_size || 20,
      }

      optionsArray.value = parsedOptions
    } else {
      config.value = {
        type: 'text',
        required: false,
        label: '',
        description: '',
        placeholder: '',
        className: 'form-control',
        name: '',
        subtype: '',
        maxlength: 255,
        value: '',
        options: [],
        access: null,
        role: null,
        row: 1,
        width: 'full',
        multiple: false,
        default_value: '',
        min_date_type: 'none',
        min_date: '',
        max_date_type: 'none',
        max_date: '',
        file_types: ['pdf', 'xlsx', 'docx'],
        max_file_size: 20,
      }
      optionsArray.value = []
    }
  },
  { immediate: true }
)

const openDialog = () => {
  if (!dialogRef.value || dialogRef.value.open) return
  dialogRef.value.showModal()

  if (!props.field?.id) {
    generateName()
  }
}

const closeDialog = () => {
  if (!dialogRef.value || !dialogRef.value.open) return
  dialogRef.value.close()
}

watch(
  () => props.field,
  (value) => {
    if (value !== null && dialogRef.value) {
      dialogRef.value.showModal()
    }
  }
)

const close = () => {
  emit('close')
}

const save = () => {
  let defaultValue = config.value.default_value

  if (config.value.type === 'select' && config.value.multiple) {
    if (!Array.isArray(defaultValue)) {
      defaultValue = defaultValue ? [defaultValue] : []
    }
  }

  const needsOptions = ['select', 'radio', 'checkbox'].includes(config.value.type)

  const data = {
    ...config.value,
    default_value: defaultValue,
    options: needsOptions
      ? optionsArray.value.map(o => ({ value: o.value, label: o.label })).filter(o => o.value.trim())
      : [],
  }

  if (data.type === 'text') {
    data.subtype = 'text'
  } else if (data.type === 'email') {
    data.subtype = 'email'
  } else if (data.type === 'phone') {
    data.subtype = 'tel'
  } else if (data.type === 'number') {
    data.subtype = 'number'
  } else if (data.type === 'password') {
    data.subtype = 'password'
  } else if (data.type === 'url') {
    data.subtype = 'url'
  }

  emit('save', data)
  closeDialog()
}

const onBackdropClick = (event) => {
  if (event.target === dialogRef.value) {
    close()
  }
}
</script>

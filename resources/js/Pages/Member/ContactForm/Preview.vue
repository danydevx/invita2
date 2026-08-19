<template>
  <MemberLayout>
    <Head title="Vista Previa" />

    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <Link :href="`/member/businesses/${business?.id}/contact-forms/${form?.id}/edit`" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>
          Volver al editor
        </Link>
        <h4 class="mb-0 mt-1">Vista Previa: {{ form?.name }}</h4>
      </div>
      <a
        v-if="business?.slug && form?.shortcode"
        :href="`/b/${business.slug}/form/${form.shortcode}`"
        target="_blank"
        class="btn btn-outline-primary"
      >
        <i class="bi bi-box-arrow-up-right me-1"></i>
        Ver en minisitio
      </a>
    </div>

    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h5 class="mb-4">{{ form?.name }}</h5>

            <div v-if="fields.length === 0" class="text-center py-4">
              <i class="bi bi-exclamation-circle text-muted" style="font-size: 2rem;"></i>
              <p class="text-muted mt-2">No hay campos activos en este formulario.</p>
            </div>

            <form v-else @submit.prevent="submitForm">
              <template v-for="(rowFields, rowIndex) in fieldsByRow" :key="rowIndex">
                <div class="row">
                  <div
                    v-for="field in rowFields"
                    :key="field.name"
                    class="mb-3"
                    :class="getFieldWidthClass(field)"
                  >
                    <label class="form-label">
                      {{ field.label }}
                      <span v-if="field.required" class="text-danger">*</span>
                    </label>

                    <input
                      v-if="field.type === 'text' || field.type === 'email' || field.type === 'phone' || field.type === 'number' || field.type === 'password' || field.type === 'url'"
                      :type="field.type === 'password' ? 'password' : field.subtype || field.type"
                      v-model="formData[field.name]"
                      class="form-control"
                      :class="field.className"
                      :placeholder="field.placeholder"
                      :maxlength="field.maxlength"
                      :required="field.required"
                    />

                    <textarea
                      v-else-if="field.type === 'textarea'"
                      v-model="formData[field.name]"
                      class="form-control"
                      :class="field.className"
                      :placeholder="field.placeholder"
                      :maxlength="field.maxlength"
                      :required="field.required"
                      rows="4"
                    ></textarea>

                    <select
                      v-else-if="field.type === 'select'"
                      v-model="formData[field.name]"
                      class="form-select"
                      :class="field.className"
                      :required="field.required && !field.multiple"
                      :multiple="field.multiple"
                    >
                      <option v-if="!field.multiple" value="">{{ field.placeholder || 'Seleccionar...' }}</option>
                      <option v-for="(opt, oi) in field.options" :key="oi" :value="typeof opt === 'object' ? opt.value : opt">
                        {{ typeof opt === 'object' ? opt.label : opt }}
                      </option>
                    </select>

                    <div v-else-if="field.type === 'checkbox'" class="form-check-group">
                      <div v-if="field.options && field.options.length > 0">
                        <div v-for="(opt, oi) in field.options" :key="oi" class="form-check">
                          <input
                            type="checkbox"
                            :value="typeof opt === 'object' ? opt.value : opt"
                            v-model="formData[field.name]"
                            class="form-check-input"
                            :class="field.className"
                            :id="'field-' + field.name + '-' + oi"
                          />
                          <label class="form-check-label" :for="'field-' + field.name + '-' + oi">
                            {{ typeof opt === 'object' ? opt.label : opt }}
                          </label>
                        </div>
                      </div>
                      <div v-else class="form-check">
                        <input
                          type="checkbox"
                          v-model="formData[field.name]"
                          class="form-check-input"
                          :class="field.className"
                          :id="'field-' + field.name"
                          :required="field.required"
                        />
                        <label class="form-check-label" :for="'field-' + field.name">
                          {{ field.placeholder || field.label }}
                        </label>
                      </div>
                    </div>

                    <div v-else-if="field.type === 'radio'" class="form-check-group">
                      <div v-for="(opt, oi) in field.options" :key="oi" class="form-check">
                        <input
                          type="radio"
                          :name="field.name"
                          :id="'field-' + field.name + '-' + oi"
                          :value="typeof opt === 'object' ? opt.value : opt"
                          v-model="formData[field.name]"
                          class="form-check-input"
                          :class="field.className"
                          :required="field.required && oi === 0"
                        />
                        <label class="form-check-label" :for="'field-' + field.name + '-' + oi">
                          {{ typeof opt === 'object' ? opt.label : opt }}
                        </label>
                      </div>
                    </div>

                    <input
                      v-else-if="field.type === 'date'"
                      type="date"
                      v-model="formData[field.name]"
                      class="form-control"
                      :class="field.className"
                      :required="field.required"
                      :min="getMinDate(field)"
                      :max="getMaxDate(field)"
                    />

                    <input
                      v-else-if="field.type === 'file'"
                      type="file"
                      @change="handleFileChange($event, field.name)"
                      class="form-control"
                      :class="field.className"
                      :required="field.required"
                      :accept="getFileAccept(field)"
                      :max="field.max_file_size * 1024 * 1024"
                    />

                    <input
                      v-else-if="field.type === 'image'"
                      type="file"
                      @change="handleFileChange($event, field.name)"
                      class="form-control"
                      :class="field.className"
                      :required="field.required"
                      :accept="getImageAccept(field)"
                      :max="(field.max_file_size || 5) * 1024 * 1024"
                      capture="environment"
                    />

                    <small v-if="field.description" class="form-text text-muted">
                      {{ field.description }}
                    </small>
                  </div>
                </div>
              </template>

              <button type="submit" class="btn btn-primary">
                Enviar
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white">
            <h6 class="mb-0">Informacion</h6>
          </div>
          <div class="card-body">
            <p class="small text-muted mb-2">
              <strong>Shortcode:</strong>
              <code class="ms-1">{{ form?.shortcode }}</code>
            </p>
            <p class="small text-muted mb-2">
              <strong>Campos:</strong> {{ fields.length }}
            </p>
            <hr>
            <p class="small text-muted mb-0">
              Esta es una vista previa de como se vera el formulario en el minisitio.
              Los datos enviados no seran guardados.
            </p>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  business: Object,
  form: Object,
  fields: {
    type: Array,
    default: () => [],
  },
})

const business = computed(() => props.business)
const form = computed(() => props.form)

const formData = reactive({})

props.fields.forEach(field => {
  if (field.type === 'checkbox') {
    if (field.options && field.options.length > 0) {
      formData[field.name] = Array.isArray(field.default_value) ? field.default_value : (field.default_value ? [field.default_value] : [])
    } else {
      formData[field.name] = field.default_value === true || field.default_value === 'true' || false
    }
  } else if (field.type === 'radio') {
    formData[field.name] = field.default_value || ''
  } else if (field.type === 'select') {
    if (field.multiple) {
      formData[field.name] = Array.isArray(field.default_value) ? field.default_value : (field.default_value ? [field.default_value] : [])
    } else {
      formData[field.name] = field.default_value || ''
    }
  } else {
    formData[field.name] = field.value || ''
  }
})

const handleFileChange = (event, fieldName) => {
  const file = event.target.files[0]
  if (file) {
    formData[fieldName] = file.name
  }
}

const submitForm = () => {
  alert('Vista previa - Los datos no se guardan. Verifica el formulario y vuelve al editor.')
}

const getMinDate = (field) => {
  if (field.min_date_type === 'now') {
    return new Date().toISOString().split('T')[0]
  }
  return field.min_date || ''
}

const getMaxDate = (field) => {
  if (field.max_date_type === 'now') {
    return new Date().toISOString().split('T')[0]
  }
  return field.max_date || ''
}

const fieldsByRow = computed(() => {
  const rows = {}
  props.fields.forEach(field => {
    const row = field.row || 1
    if (!rows[row]) {
      rows[row] = []
    }
    rows[row].push(field)
  })
  return Object.values(rows)
})

const getFieldWidthClass = (field) => {
  const width = field.width || 'full'
  const widthMap = {
    full: 'col-12',
    half: 'col-12 col-md-6',
    third: 'col-12 col-md-4',
    quarter: 'col-12 col-md-3',
  }
  return widthMap[width] || 'col-12'
}

const getFileAccept = (field) => {
  const types = field.file_types || ['pdf', 'xlsx', 'docx']
  const mimeTypes = {
    pdf: '.pdf',
    xlsx: '.xlsx',
    docx: '.docx',
  }
  return types.map(t => mimeTypes[t]).join(',')
}

const getImageAccept = (field) => {
  return '.jpg,.jpeg,.png,.webp'
}
</script>

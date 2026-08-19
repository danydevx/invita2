<template>
  <div class="form-group" :class="classObject">
    <label :for="id">{{ label }} <strong v-if="required">*</strong></label>

    <input
      :id="id"
      type="file"
      :accept="accept"
      class="form-control"
      :class="{ 'is-invalid': hasError }"
      @change="onFileChange"
      @blur="onBlur"
      :disabled="readonly"
    />

    <div v-if="hasError" class="invalid-feedback">
      {{ formError || validationMessage || localError }}
    </div>

    <!-- Selección nueva: detalles + acciones -->
    <div v-if="fileName" class="mt-2">
      <div class="d-flex align-items-center flex-wrap gap-2">
        <span class="fw-semibold">{{ fileName }}</span>
        <span class="text-muted small" v-if="fileSize">· {{ formattedSize }}</span>
        <span class="text-muted small" v-if="fileType">· {{ fileType }}</span>
        <span class="text-muted small" v-if="lastModifiedHuman">· {{ lastModifiedHuman }}</span>

        <button
          v-if="!readonly"
          type="button"
          class="btn btn-sm btn-outline-danger ms-auto"
          @click="clearSelectedFile"
        >
          <i class="bi bi-x-circle"></i> Quitar archivo
        </button>

        <button
          v-if="showPreviewToggle && selectedUrl"
          type="button"
          class="btn btn-sm btn-outline-secondary"
          @click="previewOpen = !previewOpen"
        >
          <i class="bi" :class="previewOpen ? 'bi-eye-slash' : 'bi-eye'"></i>
          {{ previewOpen ? 'Ocultar vista previa' : 'Vista previa' }}
        </button>
      </div>

      <div v-if="previewOpen && selectedUrl" class="mt-2">
        <iframe :src="selectedUrl" :style="{ width:'100%', height: previewHeight }" />
      </div>
    </div>

    <!-- Archivo actual (modo edición, sin selección nueva) -->
    <div v-else-if="initialUrl" class="mt-2">
      <div class="border rounded p-3 d-flex flex-column gap-2">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="mb-0">Archivo actual</h6>
          <div class="d-flex gap-2">
            <!-- Descargar -->
            <a
              v-if="showDownloadButton && initialUrl && !remove"
              :href="initialUrl"
              class="btn btn-sm btn-outline-secondary"
              target="_blank"
              :download="downloadName || null"
            >
              <i class="bi bi-download"></i> Descargar
            </a>

            <!-- Ver -->
            <a
              v-if="!remove"
              :href="initialUrl"
              target="_blank"
              rel="noopener"
              class="btn btn-sm btn-outline-secondary"
            >
              Ver archivo
            </a>

            <!-- Eliminar / Cancelar -->
            <button
              v-if="!readonly"
              type="button"
              class="btn btn-sm"
              :class="remove ? 'btn-secondary' : 'btn-outline-danger'"
              @click="toggleRemove"
            >
              <i class="bi" :class="remove ? 'bi-arrow-counterclockwise me-1' : 'bi-trash me-1'"></i>
              {{ remove ? 'Cancelar eliminación' : 'Eliminar archivo' }}
            </button>
          </div>
        </div>

        <div>
          <template v-if="!remove && showPreviewToggle">
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary"
              @click="previewOpen = !previewOpen"
            >
              <i class="bi" :class="previewOpen ? 'bi-eye-slash' : 'bi-eye'"></i>
              {{ previewOpen ? 'Ocultar vista previa' : 'Vista previa' }}
            </button>
          </template>

          <template v-if="previewOpen && !remove">
            <div class="mt-2">
              <iframe :src="initialUrl" :style="{ width:'100%', height: previewHeight }" />
            </div>
          </template>

          <template v-if="remove">
            <span class="text-danger small">Se eliminará el archivo al guardar.</span>
          </template>
        </div>
      </div>
    </div>

    <!-- Ayuda -->
    <div v-if="help" class="form-text mt-1">{{ help }}</div>
  </div>
</template>

<script>
export default {
  emits: ['update:modelValue', 'update:remove', 'blur'],
  props: {
    id: { type: String, required: true },
    label: { type: String, required: true },
    required: { type: Boolean, default: false },
    showValidation: { type: Boolean, default: false },
    formError: { type: String, default: '' },
    validateFunction: { type: Function, default: null },
    classObject: { type: String, default: '' },
    accept: { type: String, default: 'application/pdf' },
    readonly: { type: Boolean, default: false },

    // v-model archivo nuevo
    modelValue: { type: [File, Object, String, null], default: null },

    // v-model:remove flag de eliminación del archivo actual
    remove: { type: Boolean, default: false },

    // URL pública del archivo actual
    initialUrl: { type: String, default: '' },
    // Nombre de descarga sugerido
    downloadName: { type: String, default: '' },

    // Reglas y UI
    onlyPdf: { type: Boolean, default: true },
    maxSizeMB: { type: Number, default: 20 },
    showPreviewToggle: { type: Boolean, default: false },
    previewHeight: { type: String, default: '360px' },
    showDownloadButton: { type: Boolean, default: true }, // <- nuevo
    help: { type: String, default: '' }
  },
  data() {
    return {
      fileName: null,
      fileSize: null,
      fileType: null,
      lastModifiedHuman: null,
      localError: '',
      selectedUrl: null,
      previewOpen: false
    }
  },
  computed: {
    validationMessage() {
      return this.validateFunction ? this.validateFunction() : ''
    },
    hasError() {
      return (!!this.formError) || (this.showValidation && !!this.validationMessage) || (!!this.localError)
    },
    formattedSize() {
      if (!this.fileSize && this.fileSize !== 0) return ''
      const units = ['B','KB','MB','GB']
      let size = this.fileSize
      let i = 0
      while (size >= 1024 && i < units.length - 1) {
        size /= 1024
        i++
      }
      return `${size.toFixed(i === 0 ? 0 : 1)} ${units[i]}`
    }
  },
  watch: {
    modelValue: {
      immediate: true,
      handler(val) {
        // Genera o libera URL de Blob para vista previa
        if (this.selectedUrl) {
          URL.revokeObjectURL(this.selectedUrl)
          this.selectedUrl = null
        }
        if (val instanceof File) {
          this.selectedUrl = URL.createObjectURL(val)
        }
        // Metadatos
        if (val && typeof val === 'object' && 'name' in val) {
          this.fileName = val.name
          this.fileSize = val.size
          this.fileType = val.type || ''
          this.lastModifiedHuman = val.lastModified
            ? new Date(val.lastModified).toLocaleString()
            : ''
        } else {
          this.fileName = null
          this.fileSize = null
          this.fileType = null
          this.lastModifiedHuman = null
        }
      }
    },
    remove(newVal) {
      // Si marcan eliminar, limpia cualquier archivo seleccionado
      if (newVal) this.clearSelectedFile(false)
    }
  },
  beforeUnmount() {
    if (this.selectedUrl) URL.revokeObjectURL(this.selectedUrl)
  },
  methods: {
    onFileChange(event) {
      if (this.readonly) return 
      this.localError = ''
      const file = event.target.files?.[0]
      if (!file) {
        this.clearSelectedFile()
        return
      }

      // Validación cliente: tipo
      const isPdfType = file.type === 'application/pdf' || /\.pdf$/i.test(file.name)
      if (this.onlyPdf && !isPdfType) {
        this.localError = 'El archivo debe ser un PDF'
        this.clearSelectedFile()
        return
      }

      // Validación cliente: tamaño
      const maxBytes = this.maxSizeMB * 1024 * 1024
      if (file.size > maxBytes) {
        this.localError = `El archivo excede ${this.maxSizeMB} MB`
        this.clearSelectedFile()
        return
      }

      // Estado
      this.fileName = file.name
      this.fileSize = file.size
      this.fileType = file.type || 'application/pdf'
      this.lastModifiedHuman = file.lastModified ? new Date(file.lastModified).toLocaleString() : ''

      this.$emit('update:modelValue', file)

      // Si habían marcado eliminar archivo actual, lo cancelamos
      if (this.remove) this.$emit('update:remove', false)
    },
    onBlur() {
      this.$emit('blur')
    },
    clearSelectedFile(resetInput = true) {
      if (this.readonly) return
      if (this.selectedUrl) {
        URL.revokeObjectURL(this.selectedUrl)
        this.selectedUrl = null
      }
      this.fileName = null
      this.fileSize = null
      this.fileType = null
      this.lastModifiedHuman = null
      this.$emit('update:modelValue', null)
      if (resetInput) {
        const input = document.getElementById(this.id)
        if (input) input.value = ''
      }
    },
    toggleRemove() {
      if (this.readonly) return
      const next = !this.remove
      this.$emit('update:remove', next)
      if (next) this.clearSelectedFile()
    }
  }
}
</script>

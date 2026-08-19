<template>
  <div class="product-image-gallery">
    <label class="form-label">{{ label }}</label>

    <div v-if="loading" class="text-muted">Cargando...</div>

    <div v-else>
      <div class="d-flex flex-wrap gap-2">
        <div
          v-for="(file, index) in localFiles"
          :key="index"
          class="position-relative"
          style="width: 80px; height: 80px;"
        >
          <img
            :src="getFilePreview(file)"
            class="img-thumbnail w-100 h-100"
            style="object-fit: cover;"
          />
          <button
            type="button"
            class="btn btn-sm btn-danger position-absolute top-0 start-100 translate-middle"
            style="margin-left: -20px; margin-top: -5px;"
            @click="removeFile(index)"
          >
            ×
          </button>
        </div>

        <div
          v-if="localFiles.length < maxFiles"
          class="border border-dashed rounded d-flex align-items-center justify-content-center"
          style="width: 80px; height: 80px; cursor: pointer;"
          @click="triggerFileInput"
          @dragover.prevent
          @drop.prevent="handleDrop"
        >
          <span class="text-muted">+</span>
        </div>
      </div>

      <input
        ref="fileInput"
        type="file"
        class="d-none"
        :accept="accept"
        :multiple="maxFiles > 1"
        @change="handleFileSelect"
      />

      <div v-if="error" class="text-danger small mt-1">{{ error }}</div>
      <small v-if="hint" class="text-muted d-block mt-1">{{ hint }}</small>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [],
  },
  existingFiles: {
    type: Array,
    default: () => [],
  },
  maxFiles: {
    type: Number,
    default: 6,
  },
  maxSizeMb: {
    type: Number,
    default: 2,
  },
  accept: {
    type: String,
    default: 'image/jpeg',
  },
  label: {
    type: String,
    default: 'Galería de imágenes',
  },
  hint: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])

const fileInput = ref(null)
const localFiles = ref([])
const error = ref('')
const loading = ref(false)

const getFilePreview = (file) => {
  if (typeof file === 'string') {
    return file
  }
  return URL.createObjectURL(file)
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files)
  validateAndAddFiles(files)
  event.target.value = ''
}

const handleDrop = (event) => {
  const files = Array.from(event.dataTransfer.files)
  validateAndAddFiles(files)
}

const validateAndAddFiles = (files) => {
  error.value = ''
  const maxSize = props.maxSizeMb * 1024 * 1024
  const allowedTypes = props.accept.split(',').map(t => t.trim())

  const validFiles = files
    .filter(file => {
      const isValidType = allowedTypes.some(type => {
        if (type === 'image/jpeg') {
          return file.type === 'image/jpeg' || file.type === 'image/jpg'
        }
        return file.type === type
      })
      const isValidSize = file.size <= maxSize
      if (!isValidType) {
        error.value = 'Solo se permiten archivos JPG'
      } else if (!isValidSize) {
        error.value = `El archivo excede el tamaño máximo de ${props.maxSizeMb}MB`
      }
      return isValidType && isValidSize
    })
    .slice(0, props.maxFiles - localFiles.value.length)

  localFiles.value = [...localFiles.value, ...validFiles]
  emit('update:modelValue', localFiles.value)
}

const removeFile = (index) => {
  localFiles.value.splice(index, 1)
  emit('update:modelValue', localFiles.value)
}

watch(() => props.modelValue, (newVal) => {
  localFiles.value = newVal || []
}, { immediate: true, deep: true })
</script>

<style scoped>
.product-image-gallery .form-label {
  font-weight: 500;
  margin-bottom: 0.25rem;
}
</style>

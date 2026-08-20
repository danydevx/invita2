<template>
  <div ref="uploadModalElement" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Subir imágenes</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" :disabled="uploading"></button>
        </div>
        <div class="modal-body p-4">
          <div
            v-if="!uploading && files.length === 0"
            class="upload-dropzone"
            :class="{ 'dragover': isDragOver }"
            @dragover.prevent="isDragOver = true"
            @dragleave.prevent="isDragOver = false"
            @drop.prevent="onDrop"
            @click="$refs.fileInput.click()"
          >
            <div class="upload-dropzone__content">
              <div class="upload-dropzone__icon">
                <i class="bi bi-cloud-arrow-up"></i>
              </div>
              <h5 class="upload-dropzone__title">Arrastra las imágenes aquí</h5>
              <p class="upload-dropzone__text">o haz clic para seleccionar</p>
              <p class="upload-dropzone__hint">
                Máximo 10 imágenes, cada una menor de 10MB<br>
                JPEG, PNG, WebP o GIF
              </p>
            </div>
            <input
              ref="fileInput"
              type="file"
              class="d-none"
              accept="image/jpeg,image/png,image/webp,image/gif"
              multiple
              @change="onFileSelect"
            />
          </div>

          <div v-else-if="uploading" class="upload-uploading">
            <div class="text-center py-5">
              <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Cargando...</span>
              </div>
              <h5>Subiendo {{ files.length }} imagen(es)...</h5>
              <p class="text-muted">Por favor espera mientras se completes la subida.</p>
            </div>
          </div>

          <div v-else class="upload-preview">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">{{ files.length }} imagen(es) seleccionada(s)</h5>
              <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                @click="clearFiles"
              >
                <i class="bi bi-trash me-1"></i> Limpiar todo
              </button>
            </div>

            <div class="upload-preview__grid">
              <div
                v-for="(file, index) in files"
                :key="index"
                class="upload-preview__item"
              >
                <img :src="file.preview" :alt="file.name" />
                <button
                  type="button"
                  class="btn btn-sm btn-danger upload-preview__remove"
                  @click="removeFile(index)"
                >
                  <i class="bi bi-x"></i>
                </button>
              </div>

              <div
                class="upload-preview__add"
                @click="$refs.fileInput.click()"
              >
                <i class="bi bi-plus-lg"></i>
                <span>Agregar más</span>
              </div>
            </div>

            <input
              ref="fileInput"
              type="file"
              class="d-none"
              accept="image/jpeg,image/png,image/webp,image/gif"
              multiple
              @change="onFileSelect"
            />
          </div>

          <div v-if="formError" class="alert alert-danger mt-3 mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ formError }}
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            data-bs-dismiss="modal"
            :disabled="uploading"
          >
            {{ uploading ? 'Subiendo...' : 'Cancelar' }}
          </button>
          <button
            v-if="!uploading && files.length > 0"
            type="button"
            class="btn btn-primary"
            @click="startUpload"
          >
            <i class="bi bi-cloud-arrow-up me-1"></i>
            Subir {{ files.length }} imagen(es)
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import { toast } from 'vue3-toastify'

const props = defineProps({
  businessId: {
    type: [Number, String],
    required: true,
  },
  galleryId: {
    type: [Number, String],
    required: true,
  },
  locations: {
    type: Array,
    default: () => [],
  },
  maxFiles: {
    type: Number,
    default: 10,
  },
  maxSizeKb: {
    type: Number,
    default: 10240,
  },
  allowedTypes: {
    type: Array,
    default: () => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
  },
})

const emit = defineEmits(['uploaded'])

const uploadModalElement = ref(null)
const fileInput = ref(null)
const isDragOver = ref(false)
const uploading = ref(false)
const formError = ref('')
const files = ref([])
let modalInstance = null

const formatFileSize = (bytes) => {
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
  return (bytes / (1024 * 1024)).toFixed(2) + ' MB'
}

const open = () => {
  clearFiles()
  uploadModalElement.value.removeEventListener('hidden.bs.modal', onModalHidden)
  uploadModalElement.value.addEventListener('hidden.bs.modal', onModalHidden)

  if (modalInstance) {
    modalInstance.dispose()
    modalInstance = null
  }

  modalInstance = new Modal(uploadModalElement.value)
  modalInstance.show()
}

const onModalHidden = () => {
  clearFiles()
}

const close = () => {
  modalInstance?.hide()
  clearFiles()
}

const clearFiles = () => {
  files.value = []
  formError.value = ''
  if (fileInput.value) fileInput.value.value = ''
}

const removeFile = (index) => {
  files.value.splice(index, 1)
  if (fileInput.value) fileInput.value.value = ''
}

const addFiles = (newFiles) => {
  const fileArray = Array.from(newFiles)

  if (files.value.length + fileArray.length > props.maxFiles) {
    formError.value = `Solo puedes subir hasta ${props.maxFiles} imágenes por vez.`
    return
  }

  const invalidType = fileArray.find((file) => !props.allowedTypes.includes(file.type))
  if (invalidType) {
    formError.value = `${invalidType.name}: formato no permitido.`
    return
  }

  const oversized = fileArray.find((file) => file.size > props.maxSizeKb * 1024)
  if (oversized) {
    formError.value = `${oversized.name}: cada imagen debe pesar menos de 10MB.`
    return
  }

  formError.value = ''

  fileArray.forEach((file) => {
    const reader = new FileReader()
    reader.onload = (e) => {
      files.value.push({
        file,
        name: file.name,
        size: file.size,
        preview: e.target.result,
      })
    }
    reader.readAsDataURL(file)
  })
}

const onDrop = (event) => {
  isDragOver.value = false
  const droppedFiles = event.dataTransfer?.files
  if (droppedFiles?.length) {
    addFiles(droppedFiles)
  }
}

const onFileSelect = (event) => {
  const selectedFiles = event.target.files
  if (selectedFiles?.length) {
    addFiles(selectedFiles)
  }
  event.target.value = ''
}

const startUpload = () => {
  if (files.value.length === 0) return

  uploading.value = true
  formError.value = ''

  const formData = new FormData()
  files.value.forEach((fileItem) => {
    formData.append('files[]', fileItem.file)
  })
  formData.append('business_gallery_id', props.galleryId)

  router.post(`/member/listings/${props.businessId}/gallery`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      const uploadedCount = files.value.length
      toast.success(`${uploadedCount} imagen(es) subida(s) correctamente`)
      files.value = []
      uploading.value = false
      formError.value = ''
      emit('uploaded')
      modalInstance?.hide()
    },
    onError: (errors) => {
      uploading.value = false
      formError.value = errors.files || errors['files.0'] || errors.file || Object.values(errors)[0] || 'No se pudieron subir las imágenes.'
    },
  })
}

defineExpose({ open, close })
</script>

<style scoped>
.upload-dropzone {
  border: 2px dashed #dee2e6;
  border-radius: 12px;
  padding: 60px 40px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  background: #f8f9fa;
}

.upload-dropzone:hover,
.upload-dropzone.dragover {
  border-color: #0d6efd;
  background: #e7f1ff;
}

.upload-dropzone__icon {
  font-size: 64px;
  color: #adb5bd;
  margin-bottom: 16px;
}

.upload-dropzone.dragover .upload-dropzone__icon {
  color: #0d6efd;
}

.upload-dropzone__title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #212529;
  margin-bottom: 8px;
}

.upload-dropzone__text {
  color: #6c757d;
  margin-bottom: 12px;
}

.upload-dropzone__hint {
  font-size: 0.875rem;
  color: #adb5bd;
  margin-bottom: 0;
}

.upload-preview__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: 12px;
}

.upload-preview__item {
  position: relative;
  aspect-ratio: 1;
  border-radius: 8px;
  overflow: hidden;
  background: #f8f9fa;
}

.upload-preview__item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.upload-preview__remove {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 28px;
  height: 28px;
  padding: 0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.upload-preview__add {
  aspect-ratio: 1;
  border: 2px dashed #dee2e6;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #6c757d;
  transition: all 0.2s ease;
}

.upload-preview__add:hover {
  border-color: #0d6efd;
  color: #0d6efd;
}

.upload-preview__add i {
  font-size: 24px;
  margin-bottom: 4px;
}

.upload-preview__add span {
  font-size: 0.75rem;
}
</style>

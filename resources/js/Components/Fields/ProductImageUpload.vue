<template>
  <div class="product-gallery-upload">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <label class="form-label mb-0">{{ label }}</label>
      <button type="button" class="btn btn-sm btn-primary" @click="openModal">
        <i class="bi bi-plus-lg me-1"></i> Agregar imágenes
      </button>
    </div>

    <div v-if="images.length === 0" class="text-muted small">
      No hay imágenes en la galería.
    </div>

    <div v-else class="table-responsive">
      <table class="table table-sm table-hover align-middle">
        <thead>
          <tr>
            <th style="width: 60px;">Imagen</th>
            <th>Nombre</th>
            <th style="width: 80px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="img in images" :key="img.id">
            <td>
              <img :src="img.url" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;" />
            </td>
            <td class="small">{{ img.filename }}</td>
            <td>
              <button
                type="button"
                class="btn btn-sm btn-outline-danger"
                @click="deleteImage(img.id)"
                :disabled="deletingId === img.id"
              >
                <i v-if="deletingId === img.id" class="bi bi-hourglass-split"></i>
                <i v-else class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <Teleport to="body">
      <div ref="modalElement" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
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
                @click="triggerFileInput"
              >
                <div class="upload-dropzone__content">
                  <div class="upload-dropzone__icon">
                    <i class="bi bi-cloud-arrow-up"></i>
                  </div>
                  <h5 class="upload-dropzone__title">Arrastra las imágenes aquí</h5>
                  <p class="upload-dropzone__text">o haz clic para seleccionar</p>
                  <p class="upload-dropzone__hint">
                    Máximo {{ maxFiles }} imágenes, cada una menor de {{ maxSizeMb }}MB<br>
                    JPEG o PNG
                  </p>
                </div>
                <input
                  ref="fileInput"
                  type="file"
                  class="d-none"
                  :accept="accept"
                  multiple
                  @change="onFileSelect"
                />
              </div>

              <div v-else-if="uploading" class="text-center py-5">
                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;">
                  <span class="visually-hidden">Cargando...</span>
                </div>
                <h5>Subiendo {{ files.length }} imagen(es)...</h5>
              </div>

              <div v-else class="upload-preview">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="mb-0">{{ files.length }} imagen(es) seleccionada(s)</h5>
                  <button type="button" class="btn btn-sm btn-outline-secondary" @click="clearFiles">
                    <i class="bi bi-trash me-1"></i> Limpiar todo
                  </button>
                </div>

                <div class="upload-preview__grid">
                  <div v-for="(file, index) in files" :key="index" class="upload-preview__item">
                    <img :src="file.preview" :alt="file.name" />
                    <button type="button" class="btn btn-sm btn-danger upload-preview__remove" @click="removeFile(index)">
                      <i class="bi bi-x"></i>
                    </button>
                  </div>

                  <div class="upload-preview__add" @click="triggerFileInput">
                    <i class="bi bi-plus-lg"></i>
                    <span>Agregar más</span>
                  </div>
                </div>

                <input
                  ref="fileInput"
                  type="file"
                  class="d-none"
                  :accept="accept"
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
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" :disabled="uploading">
                Cancelar
              </button>
              <button v-if="!uploading && files.length > 0" type="button" class="btn btn-primary" @click="startUpload">
                <i class="bi bi-cloud-arrow-up me-1"></i>
                Subir {{ files.length }} imagen(es)
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'

const props = defineProps({
  businessId: {
    type: [Number, String],
    required: true,
  },
  productId: {
    type: [Number, String],
    required: true,
  },
  images: {
    type: Array,
    default: () => [],
  },
  maxFiles: {
    type: Number,
    default: 10,
  },
  maxSizeMb: {
    type: Number,
    default: 2,
  },
  accept: {
    type: String,
    default: 'image/jpeg,image/png',
  },
  label: {
    type: String,
    default: 'Galería de imágenes',
  },
})

const emit = defineEmits(['updated'])

const modalElement = ref(null)
const fileInput = ref(null)
const isDragOver = ref(false)
const uploading = ref(false)
const deletingId = ref(null)
const formError = ref('')
const files = ref([])
let modalInstance = null

const openModal = () => {
  clearFiles()
  if (!modalInstance) {
    modalInstance = new Modal(modalElement.value)
  }
  modalInstance.show()
}

const closeModal = () => {
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
}

const triggerFileInput = () => {
  fileInput.value?.click()
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

const addFiles = (newFiles) => {
  formError.value = ''
  const fileArray = Array.from(newFiles)
  const maxSize = props.maxSizeMb * 1024 * 1024
  const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg']

  const validFiles = fileArray
    .filter(file => {
      const isValidType = allowedTypes.includes(file.type)
      const isValidSize = file.size <= maxSize
      if (!isValidType) {
        formError.value = 'Solo se permiten archivos JPG o PNG'
      } else if (!isValidSize) {
        formError.value = `El archivo excede el tamaño máximo de ${props.maxSizeMb}MB`
      }
      return isValidType && isValidSize
    })
    .slice(0, props.maxFiles - files.value.length)

  validFiles.forEach(file => {
    const reader = new FileReader()
    reader.onload = (e) => {
      files.value.push({
        file,
        name: file.name,
        preview: e.target.result,
      })
    }
    reader.readAsDataURL(file)
  })
}

const startUpload = () => {
  if (files.value.length === 0) return

  uploading.value = true
  formError.value = ''

  const formData = new FormData()
  files.value.forEach((item, index) => {
    formData.append(`images[${index}]`, item.file)
  })

  router.post(`/member/listings/${props.businessId}/products/${props.productId}/images`, formData, {
    preserveScroll: true,
    onSuccess: () => {
      files.value = []
      closeModal()
      emit('updated')
    },
    onError: (errs) => {
      formError.value = Object.values(errs)[0] || 'Error al subir imágenes'
    },
    onFinish: () => {
      uploading.value = false
    },
  })
}

const deleteImage = (imageId) => {
  if (!confirm('¿Eliminar esta imagen?')) return

  deletingId.value = imageId
  router.delete(`/member/listings/${props.businessId}/products/${props.productId}/images/${imageId}`, {
    preserveScroll: true,
    onSuccess: () => {
      emit('updated')
    },
    onFinish: () => {
      deletingId.value = null
    },
  })
}

defineExpose({ openModal })
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
  grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
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

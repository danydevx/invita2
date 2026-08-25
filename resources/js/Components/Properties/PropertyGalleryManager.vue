<template>
  <div class="property-gallery-manager">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 class="mb-0">
        <i class="bi bi-images me-2"></i>
        Galería de imágenes
      </h6>
      <span class="text-muted small">{{ localImages.length }} / {{ maxImages }}</span>
    </div>

    <div v-if="localImages.length > 0" class="table-responsive mb-3">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th style="width: 80px;">Imagen</th>
            <th>Nombre</th>
            <th style="width: 100px;">Principal</th>
            <th style="width: 120px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="image in localImages" :key="image.id">
            <td>
              <img
                :src="image.url"
                :alt="image.filename"
                class="img-thumbnail"
                style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                @click="openLightbox(image.id)"
              />
            </td>
            <td>
              <span class="text-break">{{ image.filename }}</span>
            </td>
            <td>
              <span v-if="image.is_main" class="badge bg-primary">
                <i class="bi bi-star-fill me-1"></i>Principal
              </span>
              <button
                v-else
                type="button"
                class="btn btn-outline-secondary btn-sm"
                @click="setMain(image)"
              >
                <i class="bi bi-star"></i>
              </button>
            </td>
            <td>
              <div class="btn-group btn-group-sm">
                <button
                  type="button"
                  class="btn btn-outline-danger"
                  title="Eliminar"
                  @click="removeImage(image)"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="text-center py-4 border rounded bg-light mb-3">
      <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
      <p class="text-muted mt-2 mb-0">No hay imágenes en la galería</p>
    </div>

    <div v-if="localImages.length < maxImages" class="mt-3">
      <div
        class="border border-dashed rounded p-4 text-center"
        :class="{ 'border-primary bg-light': isDragging }"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
      >
        <i class="bi bi-cloud-arrow-up text-muted" style="font-size: 1.5rem;"></i>
        <p class="text-muted mt-2 mb-2">Arrastra imágenes aquí o</p>
        <button
          type="button"
          class="btn btn-outline-primary btn-sm"
          @click="$refs.fileInput.click()"
        >
          <i class="bi bi-plus-lg me-1"></i>
          Seleccionar archivos
        </button>
        <input
          ref="fileInput"
          type="file"
          accept="image/jpeg,image/png,image/webp"
          :multiple="true"
          class="d-none"
          @change="handleFileSelect"
        />
        <p class="text-muted small mt-2 mb-0">JPG, PNG o WebP. Máximo {{ maxImages }} imágenes.</p>
      </div>
    </div>

    <div v-if="uploading" class="mt-3">
      <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%">
          Subiendo imagenes...
        </div>
      </div>
    </div>

    <div v-if="uploadError" class="alert alert-danger mt-3 py-2">
      {{ uploadError }}
    </div>

    <div v-if="uploadSuccess" class="alert alert-success mt-3 py-2">
      {{ uploadSuccess }}
    </div>

    <div v-if="successMessage" class="alert alert-success mt-3 py-2">
      {{ successMessage }}
    </div>

    <!-- Lightbox -->
    <Teleport to="body">
      <transition name="fade">
        <div
          v-if="lightboxOpen"
          class="lightbox"
          @click="closeLightbox"
          @keydown.esc="closeLightbox"
        >
          <div class="lightbox-content" @click.stop>
            <button
              type="button"
              class="btn btn-close btn-close-white position-absolute top-0 end-0 m-3"
              @click="closeLightbox"
            ></button>
            <button
              v-if="localImages.length > 1"
              type="button"
              class="btn btn-link text-white position-absolute top-50 start-0 translate-middle-y ms-3"
              @click="prevImage"
            >
              <i class="bi bi-chevron-left" style="font-size: 2rem;"></i>
            </button>
            <img
              :src="localImages[lightboxIndex]?.url"
              class="img-fluid"
              alt="Imagen"
            />
            <button
              v-if="localImages.length > 1"
              type="button"
              class="btn btn-link text-white position-absolute top-50 end-0 translate-middle-y me-3"
              @click="nextImage"
            >
              <i class="bi bi-chevron-right" style="font-size: 2rem;"></i>
            </button>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  listingId: {
    type: [Number, String],
    required: true
  },
  propertyId: {
    type: [Number, String],
    required: true
  },
  images: {
    type: Array,
    default: () => []
  },
  maxImages: {
    type: Number,
    default: 10
  }
})

const localImages = ref([...props.images])
const isDragging = ref(false)
const uploading = ref(false)
const uploadError = ref('')
const uploadSuccess = ref('')
const successMessage = ref('')
const lightboxOpen = ref(false)
const lightboxIndex = ref(0)
const fileInput = ref(null)

watch(() => props.images, (newImages) => {
  localImages.value = [...newImages]
}, { immediate: true })

const emit = defineEmits(['updated'])

const openLightbox = (imageId) => {
  const index = localImages.value.findIndex(img => img.id === imageId)
  lightboxIndex.value = index >= 0 ? index : 0
  lightboxOpen.value = true
  document.documentElement.classList.add('no-scroll')
}

const closeLightbox = () => {
  lightboxOpen.value = false
  document.documentElement.classList.remove('no-scroll')
}

const prevImage = () => {
  lightboxIndex.value = (lightboxIndex.value - 1 + localImages.value.length) % localImages.value.length
}

const nextImage = () => {
  lightboxIndex.value = (lightboxIndex.value + 1) % localImages.value.length
}

const handleDrop = (e) => {
  isDragging.value = false
  const files = e.dataTransfer.files
  if (files.length > 0) {
    uploadImages(files)
  }
}

const handleFileSelect = (e) => {
  const files = e.target.files
  if (files.length > 0) {
    uploadImages(files)
  }
  e.target.value = ''
}

const uploadImages = (files) => {
  uploadError.value = ''
  uploadSuccess.value = ''

  const allowedFiles = Array.from(files).filter(file => {
    const allowedMimes = ['image/jpeg', 'image/png', 'image/webp']
    return allowedMimes.includes(file.type)
  })

  if (allowedFiles.length === 0) {
    uploadError.value = 'Solo se permiten archivos JPG, PNG o WebP.'
    return
  }

  const availableSlots = props.maxImages - localImages.value.length
  if (allowedFiles.length > availableSlots) {
    uploadError.value = `Solo puedes subir ${availableSlots} imagen(es) más. El límite es ${props.maxImages}.`
    return
  }

  uploading.value = true

  const formData = new FormData()
  allowedFiles.forEach(file => {
    formData.append('images[]', file)
  })

  router.post(`/member/listings/${props.listingId}/properties/${props.propertyId}/images`, formData, {
    preserveScroll: true,
    onSuccess: (page) => {
      uploading.value = false
      uploadSuccess.value = 'Imagen(es) subida(s) correctamente.'

      if (page.props.propertyImages) {
        localImages.value = [...page.props.propertyImages]
        emit('updated', localImages.value)
      }

      setTimeout(() => {
        uploadSuccess.value = ''
      }, 3000)
    },
    onError: (errors) => {
      uploading.value = false
      uploadError.value = errors.error || 'Error al subir la imagen.'
    }
  })
}

const removeImage = (image) => {
  if (!confirm('¿Eliminar esta imagen?')) return

  successMessage.value = ''

  router.delete(`/member/listings/${props.listingId}/properties/${props.propertyId}/images/${image.id}`, {
    preserveScroll: true,
    onSuccess: (page) => {
      successMessage.value = 'Imagen eliminada correctamente.'
      localImages.value = localImages.value.filter(img => img.id !== image.id)
      emit('updated', localImages.value)

      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    },
    onError: (errors) => {
      uploadError.value = errors.error || 'Error al eliminar la imagen.'
    }
  })
}

const setMain = (image) => {
  successMessage.value = ''

  router.put(`/member/listings/${props.listingId}/properties/${props.propertyId}/images/${image.id}/set-main`, {}, {
    preserveScroll: true,
    onSuccess: (page) => {
      successMessage.value = 'Imagen establecida como principal.'
      localImages.value = localImages.value.map(img => ({
        ...img,
        is_main: img.id === image.id
      }))
      emit('updated', localImages.value)

      setTimeout(() => {
        successMessage.value = ''
      }, 3000)
    },
    onError: (errors) => {
      uploadError.value = errors.error || 'Error al establecer imagen principal.'
    }
  })
}
</script>

<style scoped>
.lightbox {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.9);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}

.lightbox-content {
  max-width: 90vw;
  max-height: 90vh;
  position: relative;
}

.lightbox-content img {
  max-height: 85vh;
  object-fit: contain;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.no-scroll {
  overflow: hidden;
}

.object-fit-cover {
  object-fit: cover;
}
</style>

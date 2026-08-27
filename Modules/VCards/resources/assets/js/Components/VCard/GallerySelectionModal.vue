<template>
  <div v-if="show" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="$emit('close')">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Seleccionar Galeria</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="text-center py-4">
            <span class="spinner-border spinner-border-sm"></span>
            Cargando...
          </div>
          <div v-else-if="availableGalleries.length === 0" class="text-center py-4 text-muted">
            No hay galerias disponibles. Crea galerias en la seccion de galerias del negocio.
          </div>
          <div v-else>
            <p class="small text-muted mb-3">Selecciona una galeria para mostrar en tu vCard.</p>
            <div class="gallery-list">
              <div
                v-for="gallery in availableGalleries"
                :key="gallery.id"
                class="gallery-item"
                :class="{ 'gallery-item--selected': selectedGalleryId === gallery.id }"
                @click="selectGallery(gallery)"
              >
                <div class="gallery-item__images">
                  <img
                    v-for="(img, idx) in gallery.images.slice(0, 4)"
                    :key="img.id"
                    :src="getImageUrl(img.path)"
                    :alt="img.title || gallery.name"
                    class="gallery-item__image"
                  >
                  <div v-if="gallery.images.length === 0" class="gallery-item__empty">
                    <i class="bi bi-image"></i>
                  </div>
                </div>
                <div class="gallery-item__info">
                  <h6 class="gallery-item__name">{{ gallery.name }}</h6>
                  <p class="gallery-item__count text-muted small">{{ gallery.images.length }} imagenes</p>
                </div>
                <div class="gallery-item__check">
                  <i :class="selectedGalleryId === gallery.id ? 'bi bi-check-circle-fill' : 'bi bi-circle'"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-danger me-auto" @click="clearSelection" :disabled="!selectedGalleryId">
            Quitar galeria
          </button>
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancelar</button>
          <button type="button" class="btn btn-primary" @click="save" :disabled="saving">
            {{ saving ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  listingId: { type: [Number, String], required: true },
  vcardId: { type: [Number, String], required: true },
  selectedGallery: { type: Object, default: null },
})

const emit = defineEmits(['close', 'update'])

const availableGalleries = ref([])
const selectedGalleryId = ref(null)
const loading = ref(false)
const saving = ref(false)

watch(() => props.show, async (val) => {
  if (val) {
    selectedGalleryId.value = props.selectedGallery?.id || null
    await fetchGalleries()
  }
})

async function fetchGalleries() {
  loading.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcard-galleries`)
    const data = await res.json()
    availableGalleries.value = data.galleries || []
  } catch (e) {
    availableGalleries.value = []
  } finally {
    loading.value = false
  }
}

function getImageUrl(path) {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/storage/')) {
    return path
  }
  return '/storage/' + path
}

function selectGallery(gallery) {
  selectedGalleryId.value = gallery.id
}

function clearSelection() {
  selectedGalleryId.value = null
}

async function save() {
  if (!props.vcardId || !props.listingId) {
    alert('Error: falta informacion del vCard')
    return
  }
  saving.value = true
  try {
    const res = await fetch(`/member/listings/${props.listingId}/vcards/${props.vcardId}/selected-gallery`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
      body: JSON.stringify({ gallery_id: selectedGalleryId.value }),
    })
    if (!res.ok) throw new Error('Error saving gallery')

    const selectedGalleryObj = selectedGalleryId.value
      ? availableGalleries.value.find(g => g.id === selectedGalleryId.value)
      : null

    emit('update', selectedGalleryObj)
    emit('close')
  } catch (e) {
    alert('Error al guardar galeria')
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.gallery-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.gallery-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s;
}

.gallery-item:hover {
  border-color: #d1d5db;
  background: #f9fafb;
}

.gallery-item--selected {
  border-color: #3b82f6;
  background: #eff6ff;
}

.gallery-item__images {
  display: flex;
  gap: 0.25rem;
  flex-shrink: 0;
}

.gallery-item__image {
  width: 48px;
  height: 48px;
  object-fit: cover;
  border-radius: 0.25rem;
}

.gallery-item__empty {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  border-radius: 0.25rem;
  color: #9ca3af;
  font-size: 1.25rem;
}

.gallery-item__info {
  flex: 1;
}

.gallery-item__name {
  margin: 0;
  font-size: 0.9375rem;
  font-weight: 600;
}

.gallery-item__count {
  margin: 0.25rem 0 0;
}

.gallery-item__check {
  font-size: 1.5rem;
  color: #3b82f6;
}
</style>

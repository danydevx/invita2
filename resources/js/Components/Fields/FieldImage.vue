<script setup>
import { ref, watch, computed, onMounted, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({
  id: { type: String, required: true },
  label: { type: String, required: true },
  required: { type: Boolean, default: false },
  showValidation: { type: Boolean, default: false },
  formError: { type: String, default: '' },
  validateFunction: { type: Function, default: null },
  classObject: { type: String, default: '' },
  initialPreview: { type: [String, Array], default: null },
  multiple: { type: Boolean, default: false },
  maxSizeMb: { type: Number, default: 5 },
  readonly: { type: Boolean, default: false }
});

const emit = defineEmits(['update:modelValue','update:keep','blur','image-removed']);

const fileInput = ref(null);
const previews = ref(
  Array.isArray(props.initialPreview)
    ? props.initialPreview
    : props.initialPreview
      ? [props.initialPreview]
      : []
);
const files = ref([]);

const validationMessage = computed(() =>
  props.validateFunction ? props.validateFunction() : ''
);

// Lightbox state
const lightboxOpen = ref(false);
const lightboxIndex = ref(0);
const lightboxImgRef = ref(null);
const closeBtnRef = ref(null);

function openLightboxAt(index) {
  lightboxIndex.value = index;
  lightboxOpen.value = true;
  document.documentElement.classList.add('no-scroll');
  // Enfocar botón cerrar al abrir
  nextTick(() => closeBtnRef.value?.focus());
}

function closeLightbox() {
  lightboxOpen.value = false;
  document.documentElement.classList.remove('no-scroll');
}

function nextImage() {
  if (!hasMultiple.value) return;
  lightboxIndex.value = (lightboxIndex.value + 1) % previews.value.length;
}

function prevImage() {
  if (!hasMultiple.value) return;
  lightboxIndex.value = (lightboxIndex.value - 1 + previews.value.length) % previews.value.length;
}

const hasMultiple = computed(() => previews.value.length > 1);

function onKeyDown(e) {
  if (!lightboxOpen.value) return;
  if (e.key === 'Escape') {
    e.preventDefault();
    closeLightbox();
  } else if (e.key === 'ArrowRight') {
    e.preventDefault();
    nextImage();
  } else if (e.key === 'ArrowLeft') {
    e.preventDefault();
    prevImage();
  }
}

// Limpieza de Object URLs
function revokeIfBlob(url) {
  if (typeof url === 'string' && url.startsWith('blob:')) {
    try { URL.revokeObjectURL(url); } catch {}
  }
}

watch(
  () => props.initialPreview,
  (newVal, oldVal) => {
    const oldList = Array.isArray(oldVal) ? oldVal : oldVal ? [oldVal] : [];
    oldList.forEach(revokeIfBlob);

    previews.value = Array.isArray(newVal) ? newVal : newVal ? [newVal] : [];
    emit('update:keep', previews.value.length > 0);
    files.value = [];
    if (fileInput.value) fileInput.value.value = null;
  },
  { immediate: true }
);

function onFileChange(event) {
  if (props.readonly) return;
  const selectedFiles = Array.from(event.target.files);
  const maxSizeBytes = (props.maxSizeMb || 5) * 1024 * 1024;
  const maxImages = props.multiple ? 6 : 1;

  const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

  const validFiles = selectedFiles
    .filter(file => {
      const isValidType = validTypes.includes(file.type);
      const isValidSize = file.size <= maxSizeBytes;
      return isValidType && isValidSize;
    })
    .slice(0, maxImages);

  files.value = props.multiple ? validFiles : validFiles.slice(0, 1);

  // Revoca blobs previos antes de reemplazar
  previews.value.forEach(revokeIfBlob);

  previews.value = files.value.map(file => URL.createObjectURL(file));
  emit('update:modelValue', props.multiple ? files.value : files.value[0] || null);
  emit('update:keep', false);
}

function onBlur() {
  emit('blur');
}

function removeImage(index) {
  if (props.readonly) return;

  revokeIfBlob(previews.value[index]);

  files.value.splice(index, 1);
  previews.value.splice(index, 1);

  if (fileInput.value) fileInput.value.value = null;

  if (files.value.length === 0 && previews.value.length === 0) {
    emit('update:modelValue', null);
    emit('update:keep', false);
    emit('image-removed');
  } else {
    emit('update:modelValue', props.multiple ? files.value : files.value[0] || null);
  }
}

function backdropClick(e) {
  // Cierra si se hace click en el overlay (no en el contenido)
  if (e.target.dataset.backdrop === 'true') closeLightbox();
}

onMounted(() => window.addEventListener('keydown', onKeyDown));
onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKeyDown);
  document.documentElement.classList.remove('no-scroll');
  // Revoca cualquier blob vivo
  previews.value.forEach(revokeIfBlob);
});
</script>

<template>
  <div class="form-group" :class="classObject">
    <label :for="id">
      {{ label }} <strong v-if="required">*</strong>
    </label>

    <input
      :id="id"
      type="file"
      accept="image/*"
      class="form-control"
      :multiple="multiple"
      :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
      @change="onFileChange"
      @blur="onBlur"
      ref="fileInput"
      :disabled="readonly"
    />

    <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback">
      {{ formError || validationMessage }}
    </div>

    <!-- Thumbnails -->
    <div v-if="previews.length" class="mt-3 image-list">
      <div
        v-for="(src, index) in previews"
        :key="index"
        class="image-item"
      >
        <img
          :src="src"
          alt="Vista previa"
          class="img-thumbnail img-fluid mb-1 thumb"
          loading="lazy"
          decoding="async"
          @click="openLightboxAt(index)"
        />
        <button
          v-if="!readonly"
          type="button"
          class="btn btn-sm btn-outline-danger w-100"
          @click="removeImage(index)"
        >
          <i class="bi bi-x-circle"></i> Quitar
        </button>
      </div>
    </div>

    <!-- LIGHTBOX -->
    <Teleport to="body">
      <transition name="fade">
        <div
          v-if="lightboxOpen"
          class="lightbox"
          data-backdrop="true"
          @click="backdropClick"
          role="dialog"
          aria-modal="true"
          aria-label="Visor de imagen"
        >
          <div class="lightbox-content" role="document">
            <button
              ref="closeBtnRef"
              type="button"
              class="btn btn-light btn-close-lightbox"
              @click="closeLightbox"
              aria-label="Cerrar"
            >
              <i class="bi bi-x-lg"></i>
            </button>

            <button
              v-if="hasMultiple"
              type="button"
              class="btn btn-light btn-nav btn-prev"
              @click.stop="prevImage"
              aria-label="Anterior"
              title="Anterior"
            >
              <i class="bi bi-chevron-left"></i>
            </button>

            <div class="lightbox-image-wrapper">
              <img
                :src="previews[lightboxIndex]"
                alt="Imagen ampliada"
                class="img-fluid lightbox-img"
                ref="lightboxImgRef"
                draggable="false"
              />
            </div>

            <button
              v-if="hasMultiple"
              type="button"
              class="btn btn-light btn-nav btn-next"
              @click.stop="nextImage"
              aria-label="Siguiente"
              title="Siguiente"
            >
              <i class="bi bi-chevron-right"></i>
            </button>

            <div v-if="hasMultiple" class="lightbox-counter">
              {{ lightboxIndex + 1 }} / {{ previews.length }}
            </div>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<style scoped>
/* Grid de miniaturas */
.image-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: .75rem;
}
.image-item {
  display: flex;
  flex-direction: column;
  align-items: stretch;
}
.thumb {
  cursor: zoom-in;
  object-fit: cover;
  aspect-ratio: 4/3;
}

/* Overlay */
.lightbox {
  position: fixed;
  inset: 0;
  background: rgba(15, 15, 20, 0.85);
  display: grid;
  place-items: center;
  z-index: 1080;
  padding: 2rem;
}
.lightbox-content {
  position: relative;
  max-width: 92vw;
  max-height: 88vh;
  width: 100%;
  height: 100%;
  display: grid;
  place-items: center;
}
.lightbox-image-wrapper {
  max-width: 100%;
  max-height: 100%;
  display: grid;
  place-items: center;
}
.lightbox-img {
  max-width: 100%;
  max-height: 100%;
  user-select: none;
  cursor: zoom-out;
  border-radius: .5rem;
  box-shadow: 0 10px 40px rgba(0,0,0,.4);
}

/* Botones */
.btn-close-lightbox {
  position: absolute;
  top: .75rem;
  right: .75rem;
  border-radius: 999px;
  box-shadow: 0 4px 16px rgba(0,0,0,.25);
}
.btn-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  border-radius: 999px;
  padding: .5rem .7rem;
  box-shadow: 0 4px 16px rgba(0,0,0,.25);
}
.btn-prev { left: .75rem; }
.btn-next { right: .75rem; }
.lightbox-counter {
  position: absolute;
  bottom: .75rem;
  left: 50%;
  transform: translateX(-50%);
  color: #fff;
  background: rgba(0,0,0,.35);
  padding: .25rem .5rem;
  border-radius: .5rem;
  font-size: .875rem;
}

/* Transición */
.fade-enter-active, .fade-leave-active { transition: opacity .18s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Bloqueo de scroll cuando el lightbox está abierto */
:global(html.no-scroll), :global(body.no-scroll) {
  overflow: hidden !important;
}
</style>

<template>
  <section class="vcard-section vcard-gallery" v-if="galleryImages.length > 0">
    <h2 class="vcard-section__title">{{ gallery?.name || 'Galeria' }}</h2>

    <div class="vcard-gallery__carousel" ref="carouselRef">
      <div class="vcard-gallery__track">
        <div
          v-for="(item, index) in displayItems"
          :key="item.id || index"
          class="vcard-gallery__slide"
        >
          <div class="vcard-gallery__item">
            <img
              :src="getImageUrl(item.path)"
              :alt="item.title || `Imagen ${index + 1}`"
              class="vcard-gallery__image"
              loading="lazy"
            >
            <div v-if="item.title" class="vcard-gallery__caption">
              {{ item.title }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="vcard-gallery__nav" v-if="displayItems.length > 1">
      <button
        class="vcard-gallery__btn vcard-gallery__btn--prev"
        @click="prev"
        :disabled="currentIndex === 0"
      >
        <i class="bi bi-chevron-left"></i>
      </button>
      <div class="vcard-gallery__dots">
        <button
          v-for="(_, index) in displayItems"
          :key="index"
          class="vcard-gallery__dot"
          :class="{ active: index === currentIndex }"
          @click="goTo(index)"
        ></button>
      </div>
      <button
        class="vcard-gallery__btn vcard-gallery__btn--next"
        @click="next"
        :disabled="currentIndex >= displayItems.length - 1"
      >
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
  </section>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'

const props = defineProps({
  gallery: {
    type: Object,
    default: null,
  },
  limit: {
    type: Number,
    default: 10,
  },
})

const currentIndex = ref(0)
const carouselRef = ref(null)
const touchStartX = ref(0)
const touchEndX = ref(0)

const galleryImages = computed(() => {
  if (props.gallery?.images && props.gallery.images.length > 0) {
    return props.gallery.images
  }
  return []
})

function getImageUrl(path) {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/storage/')) {
    return path
  }
  return '/storage/' + path
}

const displayItems = computed(() => {
  return galleryImages.value.slice(0, props.limit)
})

function updateCarousel() {
  if (!carouselRef.value) return
  const track = carouselRef.value.querySelector('.vcard-gallery__track')
  if (!track) return
  const slideWidth = 100 / displayItems.value.length
  track.style.transform = `translateX(-${currentIndex.value * slideWidth}%)`
}

function next() {
  if (currentIndex.value < displayItems.value.length - 1) {
    currentIndex.value++
    updateCarousel()
  }
}

function prev() {
  if (currentIndex.value > 0) {
    currentIndex.value--
    updateCarousel()
  }
}

function goTo(index) {
  currentIndex.value = index
  updateCarousel()
}

function handleTouchStart(e) {
  touchStartX.value = e.touches[0].clientX
}

function handleTouchMove(e) {
  touchEndX.value = e.touches[0].clientX
}

function handleTouchEnd() {
  const diff = touchStartX.value - touchEndX.value
  const threshold = 50

  if (Math.abs(diff) > threshold) {
    if (diff > 0) {
      next()
    } else {
      prev()
    }
  }
}

onMounted(() => {
  if (carouselRef.value) {
    carouselRef.value.addEventListener('touchstart', handleTouchStart, { passive: true })
    carouselRef.value.addEventListener('touchmove', handleTouchMove, { passive: true })
    carouselRef.value.addEventListener('touchend', handleTouchEnd, { passive: true })
  }
})
</script>

<style scoped>
.vcard-gallery__carousel {
  position: relative;
  width: 100%;
  overflow: hidden;
  border-radius: 0.875rem;
  touch-action: pan-y;
}

.vcard-gallery__track {
  display: flex;
  transition: transform 0.3s ease-out;
}

.vcard-gallery__slide {
  flex: 0 0 100%;
  width: 100%;
  padding: 0 0.5rem;
  box-sizing: border-box;
}

.vcard-gallery__item {
  position: relative;
  width: 100%;
  aspect-ratio: 4 / 3;
  overflow: hidden;
  border-radius: 0.875rem;
  background: var(--vcard-surface-2);
}

.vcard-gallery__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.vcard-gallery__caption {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 0.75rem 1rem;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.6));
  color: #fff;
  font-size: 0.82rem;
  font-weight: 500;
}

.vcard-gallery__nav {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  margin-top: 1rem;
}

.vcard-gallery__btn {
  width: 2.25rem;
  height: 2.25rem;
  border-radius: 50%;
  background: var(--vcard-surface);
  color: var(--vcard-text);
  border: 1px solid var(--vcard-border);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.vcard-gallery__btn:hover:not(:disabled) {
  background: var(--vcard-primary);
  color: var(--vcard-surface);
  border-color: var(--vcard-primary);
}

.vcard-gallery__btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.vcard-gallery__btn i {
  font-size: 1rem;
}

.vcard-gallery__dots {
  display: flex;
  gap: 0.375rem;
}

.vcard-gallery__dot {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 50%;
  background: var(--vcard-muted);
  opacity: 0.4;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
  padding: 0;
}

.vcard-gallery__dot.active {
  background: var(--vcard-primary);
  opacity: 1;
  transform: scale(1.2);
}

.vcard-gallery__dot:hover:not(.active) {
  opacity: 0.7;
}
</style>

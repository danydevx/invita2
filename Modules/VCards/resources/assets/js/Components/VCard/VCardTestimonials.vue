<template>
  <section class="vcard-section vcard-testimonials" v-if="displayTestimonials.length > 0">
    <h2 class="vcard-section__title">Testimonios</h2>

    <div class="vcard-testimonials__carousel" ref="carouselRef">
      <div class="vcard-testimonials__track">
        <article
          v-for="(item, index) in displayTestimonials"
          :key="index"
          class="vcard-testimonial card border-0 shadow-sm"
        >
          <div class="card-body text-center">
            <div class="vcard-testimonial__avatar mx-auto mb-3">
              <img
                v-if="item.avatar"
                :src="item.avatar"
                :alt="item.name"
                class="vcard-testimonial__avatar-img"
              >
              <span v-else class="vcard-testimonial__avatar-initials">
                {{ getInitials(item.name) }}
              </span>
            </div>
            <div class="vcard-testimonial__stars" v-if="item.rating">
              <i v-for="star in 5" :key="star" class="bi" :class="star <= item.rating ? 'bi-star-fill' : 'bi-star'"></i>
            </div>
            <h3 class="vcard-testimonial__name">{{ item.name }}</h3>
            <p class="vcard-testimonial__role" v-if="item.role">{{ item.role }}</p>
            <p class="vcard-testimonial__text">{{ item.text }}</p>
          </div>
        </article>
      </div>
    </div>

    <div class="vcard-testimonials__nav" v-if="displayTestimonials.length > 1">
      <button
        class="vcard-testimonials__btn vcard-testimonials__btn--prev"
        @click="prev"
        :disabled="currentIndex === 0"
      >
        <i class="bi bi-chevron-left"></i>
      </button>
      <div class="vcard-testimonials__dots">
        <button
          v-for="(_, index) in displayTestimonials"
          :key="index"
          class="vcard-testimonials__dot"
          :class="{ active: index === currentIndex }"
          @click="goTo(index)"
        ></button>
      </div>
      <button
        class="vcard-testimonials__btn vcard-testimonials__btn--next"
        @click="next"
        :disabled="currentIndex >= displayTestimonials.length - 1"
      >
        <i class="bi bi-chevron-right"></i>
      </button>
    </div>
  </section>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'

const props = defineProps({
  testimonials: {
    type: Array,
    default: () => [],
  },
  limit: {
    type: Number,
    default: 6,
  },
})

const currentIndex = ref(0)
const carouselRef = ref(null)
const touchStartX = ref(0)
const touchEndX = ref(0)

const displayTestimonials = computed(() => {
  if (props.testimonials && props.testimonials.length > 0) {
    return props.testimonials.slice(0, props.limit).map(t => ({
      name: t.client_name || t.name || 'Cliente',
      role: t.company || t.role || '',
      text: t.comment || t.text || '',
      rating: t.rating || 0,
      avatar: t.avatar || null,
    }))
  }
  return []
})

function getInitials(name) {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

function updateCarousel() {
  if (!carouselRef.value) return
  const track = carouselRef.value.querySelector('.vcard-testimonials__track')
  if (!track) return
  track.style.transform = `translateX(-${currentIndex.value * 100}%)`
}

function next() {
  if (currentIndex.value < displayTestimonials.value.length - 1) {
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
.vcard-testimonials__carousel {
  position: relative;
  width: 100%;
  overflow: hidden;
  border-radius: 0.875rem;
  touch-action: pan-y;
}

.vcard-testimonials__track {
  display: flex;
  transition: transform 0.3s ease-out;
}

.vcard-testimonial {
  flex: 0 0 100%;
  width: 100%;
  box-sizing: border-box;
}

.vcard-testimonial .card-body {
  padding: 1.5rem;
}

.vcard-testimonial__avatar {
  width: 4rem;
  height: 4rem;
  border-radius: 50%;
  background: var(--vcard-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.vcard-testimonial__avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.vcard-testimonial__avatar-initials {
  color: var(--vcard-surface);
  font-size: 1.25rem;
  font-weight: 700;
}

.vcard-testimonials__stars {
  margin-bottom: 0.75rem;
  color: var(--vcard-primary);
  font-size: 0.875rem;
}

.vcard-testimonials__stars i {
  margin: 0 0.125rem;
}

.vcard-testimonial__name {
  margin: 0 0 0.25rem;
  font-size: 1rem;
  font-weight: 600;
  color: var(--vcard-text);
}

.vcard-testimonial__role {
  margin: 0 0 0.75rem;
  font-size: 0.8rem;
  color: var(--vcard-muted);
}

.vcard-testimonial__text {
  margin: 0;
  font-size: 0.875rem;
  color: var(--vcard-text);
  line-height: 1.5;
  font-style: italic;
}

.vcard-testimonials__nav {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  margin-top: 1rem;
}

.vcard-testimonials__btn {
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

.vcard-testimonials__btn:hover:not(:disabled) {
  background: var(--vcard-primary);
  color: var(--vcard-surface);
  border-color: var(--vcard-primary);
}

.vcard-testimonials__btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.vcard-testimonials__btn i {
  font-size: 1rem;
}

.vcard-testimonials__dots {
  display: flex;
  gap: 0.375rem;
}

.vcard-testimonials__dot {
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

.vcard-testimonials__dot.active {
  background: var(--vcard-primary);
  opacity: 1;
  transform: scale(1.2);
}

.vcard-testimonials__dot:hover:not(.active) {
  opacity: 0.7;
}
</style>

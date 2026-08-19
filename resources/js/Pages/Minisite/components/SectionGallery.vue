<template>
  <section class="section-gallery">
    <div class="section-gallery__inner">
      <h2 v-if="title" class="section-gallery__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-gallery__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-gallery__description-text">{{ description }}</p>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay imágenes en la galería.
      </div>

      <div v-else-if="viewMode === 'carousel'" class="section-gallery__carousel">
        <div
          v-for="item in items"
          :key="item.id"
          class="section-gallery__carousel-item"
        >
          <a
            :href="item.path"
            class="section-gallery__item glightbox"
            data-gallery="gallery"
            :data-title="item.title || 'Imagen'"
          >
            <img :src="item.path" :alt="item.title || 'Imagen'" class="section-gallery__image" loading="lazy" />
            <div v-if="showCaptions && item.title" class="section-gallery__overlay">
              <span>{{ item.title }}</span>
            </div>
          </a>
        </div>
      </div>

      <div v-else class="section-gallery__grid">
        <a
          v-for="item in items"
          :key="item.id"
          :href="item.path"
          class="section-gallery__item glightbox"
          data-gallery="gallery"
          :data-title="item.title || 'Imagen'"
        >
          <img :src="item.path" :alt="item.title || 'Imagen'" class="section-gallery__image" loading="lazy" />
          <div v-if="showCaptions && item.title" class="section-gallery__overlay">
            <span>{{ item.title }}</span>
          </div>
        </a>
      </div>

      <div v-if="buttons && buttons.length" class="section-gallery__buttons mt-4">
        <a
          v-for="(btn, idx) in buttons"
          :key="idx"
          :href="btn.url || '#'"
          class="btn btn-primary me-2 mb-2"
          :target="btn.open_in_new_tab ? '_blank' : '_self'"
        >
          {{ btn.text }}
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, nextTick } from 'vue'
import GLightbox from 'glightbox'
import 'glightbox/dist/css/glightbox.min.css'

const props = defineProps({
  title: String,
  subtitle: String,
  description: String,
  buttons: {
    type: Array,
    default: () => [],
  },
  items: {
    type: Array,
    default: () => [],
  },
  config: {
    type: Object,
    default: () => ({}),
  },
})

const viewMode = computed(() => props.config?.gallery_view_mode || 'grid')
const showCaptions = computed(() => props.config?.show_captions !== false)

onMounted(() => {
  nextTick(() => {
    const lightbox = GLightbox({
      touchNavigation: true,
      loop: true,
      autoplayVideos: true,
      selector: '.section-gallery .glightbox',
    })
  })
})
</script>

<style lang="less">
.section-gallery {
  padding: 48px 16px;
  background: #fff;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
  }

  &__title {
    font-weight: 700;
    margin: 0 0 8px;
    text-align: center;
    color: #212529;
  }

  &__subtitle {
    font-weight: 600;
    color: #495057;
    text-align: center;
    margin: 0 0 16px;
  }

  &__description-text {
    font-size: 1rem;
    color: #6c757d;
    text-align: center;
    margin: 0 0 16px;
  }

  &__buttons {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 8px;
  }

  &__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }

  &__carousel {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding-bottom: 16px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;

    &::-webkit-scrollbar {
      height: 4px;
    }

    &::-webkit-scrollbar-thumb {
      background: #dee2e6;
      border-radius: 2px;
    }
  }

  &__carousel-item {
    flex: 0 0 200px;
    scroll-snap-align: start;

    .section-gallery__item {
      height: 200px;
    }
  }

  &__item {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: 4px;
    cursor: pointer;
    display: block;

    &:hover .section-gallery__overlay {
      opacity: 1;
    }

    &:hover .section-gallery__image {
      transform: scale(1.05);
    }
  }

  &__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s ease;
  }

  &__overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s ease;

    span {
      color: #fff;
      font-size: 0.875rem;
      text-align: center;
      padding: 8px;
    }
  }
}
</style>

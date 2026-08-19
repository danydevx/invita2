<template>
  <section class="section-properties">
    <div class="section-properties__inner">
      <h2 v-if="title" class="section-properties__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-properties__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-properties__description-text">{{ description }}</p>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay propiedades disponibles.
      </div>

      <div v-else-if="viewMode === 'carousel'" class="section-properties__carousel">
        <div
          v-for="item in displayedItems"
          :key="item.id"
          class="section-properties__card"
          @click="openPropertyModal(item)"
        >
          <div class="section-properties__card-image-wrapper">
            <img
              v-if="showImage && item.main_image"
              :src="item.main_image"
              :alt="item.title"
              class="section-properties__card-image"
              loading="lazy"
            />
            <div v-else class="section-properties__card-image-placeholder">
              <i class="bi bi-house"></i>
            </div>
            <span v-if="item.operation_label" class="section-properties__operation-badge">
              {{ item.operation_label }}
            </span>
          </div>
          <div class="section-properties__card-body">
            <h3 class="section-properties__card-title">{{ item.title }}</h3>
            <p v-if="showLocation && (item.city || item.state)" class="section-properties__card-location">
              <i class="bi bi-geo-alt"></i>{{ item.city }}{{ item.state ? ', ' + item.state : '' }}
            </p>
            <p v-if="showDescription && item.description" class="section-properties__card-desc">
              {{ truncateText(item.description, 80) }}
            </p>
            <div v-if="showPrice && item.formatted_price" class="section-properties__card-price">
              {{ item.formatted_price }}
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="viewMode === 'grid'" class="section-properties__grid">
        <div
          v-for="item in displayedItems"
          :key="item.id"
          class="section-properties__grid-card"
          @click="openPropertyModal(item)"
        >
          <div class="section-properties__card-image-wrapper">
            <img
              v-if="showImage && item.main_image"
              :src="item.main_image"
              :alt="item.title"
              class="section-properties__card-image"
              loading="lazy"
            />
            <div v-else class="section-properties__card-image-placeholder">
              <i class="bi bi-house"></i>
            </div>
            <span v-if="item.operation_label" class="section-properties__operation-badge">
              {{ item.operation_label }}
            </span>
          </div>
          <div class="section-properties__card-body">
            <h3 class="section-properties__card-title">{{ item.title }}</h3>
            <p v-if="showLocation && (item.city || item.state)" class="section-properties__card-location">
              <i class="bi bi-geo-alt"></i>{{ item.city }}{{ item.state ? ', ' + item.state : '' }}
            </p>
            <p v-if="showDescription && item.description" class="section-properties__card-desc">
              {{ truncateText(item.description, 80) }}
            </p>
            <div v-if="showPrice && item.formatted_price" class="section-properties__card-price">
              {{ item.formatted_price }}
            </div>
          </div>
        </div>
      </div>

      <div v-else class="section-properties__list">
        <div
          v-for="item in displayedItems"
          :key="item.id"
          class="section-properties__list-item"
          @click="openPropertyModal(item)"
        >
          <div class="section-properties__list-image-wrapper">
            <img
              v-if="showImage && item.main_image"
              :src="item.main_image"
              :alt="item.title"
              class="section-properties__list-image"
              loading="lazy"
            />
            <div v-else class="section-properties__list-image-placeholder">
              <i class="bi bi-house"></i>
            </div>
          </div>
          <div class="section-properties__list-content">
            <span v-if="item.operation_label" class="section-properties__list-operation">
              {{ item.operation_label }}
            </span>
            <h3 class="section-properties__list-title">{{ item.title }}</h3>
            <p v-if="showLocation && (item.city || item.state)" class="section-properties__list-location">
              <i class="bi bi-geo-alt"></i>{{ item.city }}{{ item.state ? ', ' + item.state : '' }}
            </p>
            <p v-if="showDescription && item.description" class="section-properties__list-desc">
              {{ truncateText(item.description, 120) }}
            </p>
          </div>
          <div class="section-properties__list-price-wrapper">
            <div v-if="showPrice && item.formatted_price" class="section-properties__list-price">
              {{ item.formatted_price }}
            </div>
            <button class="section-properties__list-btn" @click.stop="openPropertyModal(item)">
              Ver detalles
            </button>
          </div>
        </div>
      </div>

      <div v-if="hasMoreItems && showAllButton" class="section-properties__show-all">
        <a :href="allPropertiesUrl" class="btn btn-outline-primary">
          Ver todas las propiedades ({{ items.length }})
        </a>
      </div>
    </div>

    <div v-if="selectedProperty" class="property-modal" @click="closePropertyModal">
      <div class="property-modal__content" @click.stop>
        <button class="property-modal__close" @click="closePropertyModal">
          <i class="bi bi-x-lg"></i>
        </button>
        <div v-if="selectedProperty.main_image" class="property-modal__image">
          <img :src="selectedProperty.main_image" :alt="selectedProperty.title" />
        </div>
        <div class="property-modal__info">
          <div class="property-modal__header">
            <span v-if="selectedProperty.operation_label" class="property-modal__operation">
              {{ selectedProperty.operation_label }}
            </span>
            <h2 class="property-modal__name">{{ selectedProperty.title }}</h2>
          </div>
          <p v-if="showLocation && (selectedProperty.city || selectedProperty.state)" class="property-modal__location">
            <i class="bi bi-geo-alt"></i>{{ selectedProperty.city }}{{ selectedProperty.state ? ', ' + selectedProperty.state : '' }}
          </p>
          <div v-if="showPrice && selectedProperty.formatted_price" class="property-modal__price">
            {{ selectedProperty.formatted_price }}
          </div>
          <p v-if="showDescription && selectedProperty.description" class="property-modal__description">
            {{ selectedProperty.description }}
          </p>

          <div v-if="selectedProperty.gallery && selectedProperty.gallery.length > 0" class="property-modal__gallery">
            <h4 class="property-modal__gallery-title">Galería</h4>
            <div class="property-modal__gallery-grid">
              <img
                v-for="img in selectedProperty.gallery"
                :key="img.id"
                :src="img.path"
                :alt="img.title || selectedProperty.title"
                class="property-modal__gallery-image"
                loading="lazy"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  title: String,
  subtitle: String,
  description: String,
  items: {
    type: Array,
    default: () => [],
  },
  config: {
    type: Object,
    default: () => ({}),
  },
  buttons: {
    type: Array,
    default: () => [],
  },
  businessSlug: {
    type: String,
    default: '',
  },
  showAllButton: {
    type: Boolean,
    default: true,
  },
})

const selectedProperty = ref(null)

const viewMode = computed(() => props.config?.view_mode || 'grid')
const showImage = computed(() => props.config?.show_image !== false)
const showPrice = computed(() => props.config?.show_price !== false)
const showLocation = computed(() => props.config?.show_location !== false)
const showDescription = computed(() => props.config?.show_description !== false)

const maxItems = computed(() => {
  if (props.config?.show_all) return props.items.length
  return props.config?.max_items || 12
})

const displayedItems = computed(() => {
  return props.items.slice(0, maxItems.value)
})

const hasMoreItems = computed(() => {
  return props.items.length > 0
})

const allPropertiesUrl = computed(() => {
  return `/m/${props.businessSlug}/propiedades`
})

const truncateText = (text, length) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

const openPropertyModal = (property) => {
  router.get(`/m/${props.businessSlug}/propiedades/${property.slug}`)
}

const closePropertyModal = () => {
  selectedProperty.value = null
}
</script>

<script>
import { defineComponent } from 'vue'
export default defineComponent({ name: 'SectionProperties' })
</script>

<style lang="less">
.section-properties {
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

  &__carousel {
    display: flex;
    gap: 16px;
    overflow-x: auto;
    padding-bottom: 16px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;

    &::-webkit-scrollbar {
      height: 4px;
    }

    &::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 2px;
    }

    &::-webkit-scrollbar-thumb {
      background: #dee2e6;
      border-radius: 2px;
    }
  }

  &__carousel &__card {
    flex: 0 0 280px;
    scroll-snap-align: start;
  }

  &__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
  }

  &__card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;

    &:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    &-image-wrapper {
      position: relative;
    }

    &-body {
      padding: 16px;
    }

    &-title {
      font-size: 1rem;
      font-weight: 600;
      color: #212529;
      margin: 0 0 8px;
    }

    &-location {
      font-size: 0.875rem;
      color: #6c757d;
      margin: 0 0 8px;

      i {
        margin-right: 4px;
      }
    }

    &-desc {
      font-size: 0.875rem;
      color: #6c757d;
      margin: 0 0 12px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    &-price {
      font-size: 1.125rem;
      font-weight: 700;
      color: #198754;
    }
  }

  &__grid-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;

    &:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .section-properties__card-image-wrapper {
      position: relative;
    }

    .section-properties__card-body {
      padding: 16px;
    }

    .section-properties__card-title {
      font-size: 1rem;
      font-weight: 600;
      color: #212529;
      margin: 0 0 8px;
    }

    .section-properties__card-location {
      font-size: 0.875rem;
      color: #6c757d;
      margin: 0 0 8px;

      i {
        margin-right: 4px;
      }
    }

    .section-properties__card-desc {
      font-size: 0.875rem;
      color: #6c757d;
      margin: 0 0 12px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .section-properties__card-price {
      font-size: 1.125rem;
      font-weight: 700;
      color: #198754;
    }
  }

  &__card-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
  }

  &__card-image-placeholder {
    width: 100%;
    height: 180px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 3rem;
  }

  &__operation-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #0d6efd;
    color: #fff;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
  }

  &__list {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  &__list-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.2s;

    &:hover {
      background: #e9ecef;
    }
  }

  &__list-image-wrapper {
    flex: 0 0 120px;
    width: 120px;
    height: 90px;
    border-radius: 8px;
    overflow: hidden;
  }

  &__list-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  &__list-image-placeholder {
    width: 100%;
    height: 100%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 2rem;
  }

  &__list-content {
    flex: 1;
    min-width: 0;
  }

  &__list-operation {
    display: inline-block;
    background: #e7f1ff;
    color: #0d6efd;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  &__list-title {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
    margin: 0 0 4px;
  }

  &__list-location {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0 0 8px;

    i {
      margin-right: 4px;
    }
  }

  &__list-desc {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  &__list-price-wrapper {
    flex: 0 0 auto;
    text-align: right;
  }

  &__list-price {
    font-size: 1.125rem;
    font-weight: 700;
    color: #198754;
    margin-bottom: 8px;
  }

  &__list-btn {
    padding: 8px 16px;
    background: #0d6efd;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background 0.2s;

    &:hover {
      background: #0b5ed7;
    }
  }

  &__show-all {
    display: flex;
    justify-content: center;
    margin-top: 24px;
  }
}

.property-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  overflow-y: auto;

  &__content {
    background: #fff;
    border-radius: 16px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
  }

  &__close {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    color: #495057;

    &:hover {
      background: #fff;
      color: #dc3545;
    }
  }

  &__image {
    width: 100%;
    height: 300px;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  &__info {
    padding: 24px;
  }

  &__header {
    margin-bottom: 16px;
  }

  &__operation {
    display: inline-block;
    background: #e7f1ff;
    color: #0d6efd;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 8px;
  }

  &__name {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    color: #212529;
  }

  &__location {
    font-size: 1rem;
    color: #6c757d;
    margin: 0 0 16px;

    i {
      margin-right: 4px;
    }
  }

  &__price {
    font-size: 1.5rem;
    font-weight: 700;
    color: #198754;
    margin-bottom: 16px;
  }

  &__description {
    font-size: 0.9375rem;
    color: #495057;
    line-height: 1.6;
    margin: 0 0 24px;
  }

  &__gallery {
    border-top: 1px solid #e9ecef;
    padding-top: 16px;
  }

  &__gallery-title {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
    margin: 0 0 12px;
  }

  &__gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 8px;
  }

  &__gallery-image {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: opacity 0.2s;

    &:hover {
      opacity: 0.8;
    }
  }
}
</style>

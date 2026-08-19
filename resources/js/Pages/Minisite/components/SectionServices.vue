<template>
  <section class="section-services">
    <div class="section-services__inner">
      <h2 v-if="title" class="section-services__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-services__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-services__description-text">{{ description }}</p>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay servicios disponibles.
      </div>

      <div v-else-if="viewMode === 'grid'" class="section-services__grid">
        <div
          v-for="item in displayedItems"
          :key="item.id"
          class="section-services__grid-card"
          @click="openServiceModal(item)"
        >
          <div class="section-services__card-image-wrapper">
            <img
              v-if="showImage && item.image"
              :src="item.image"
              :alt="item.name"
              class="section-services__card-image"
            />
            <div v-else class="section-services__card-image-placeholder">
              <i class="bi bi-briefcase"></i>
            </div>
            <span v-if="item.duration_minutes" class="section-services__duration-badge">
              <i class="bi bi-clock me-1"></i>{{ item.duration_minutes }} min
            </span>
          </div>
          <div class="section-services__card-body">
            <h3 class="section-services__card-title">{{ item.name }}</h3>
            <p v-if="showDescription && item.description" class="section-services__card-desc">
              {{ truncateText(item.description, 60) }}
            </p>
            <div class="section-services__card-footer">
              <p v-if="showPrice && item.price" class="section-services__card-price">
                {{ formatCurrency(item.price) }}
              </p>
              <button class="section-services__book-btn" @click.stop="openServiceModal(item)">
                Ver detalles
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="viewMode === 'carousel'" class="section-services__carousel">
        <div
          v-for="item in displayedItems"
          :key="item.id"
          class="section-services__card"
          @click="openServiceModal(item)"
        >
          <div class="section-services__card-image-wrapper">
            <img
              v-if="showImage && item.image"
              :src="item.image"
              :alt="item.name"
              class="section-services__card-image"
            />
            <div v-else class="section-services__card-image-placeholder">
              <i class="bi bi-briefcase"></i>
            </div>
            <span v-if="item.duration_minutes" class="section-services__duration-badge">
              <i class="bi bi-clock me-1"></i>{{ item.duration_minutes }} min
            </span>
          </div>
          <div class="section-services__card-body">
            <h3 class="section-services__card-title">{{ item.name }}</h3>
            <p v-if="showDescription && item.description" class="section-services__card-desc">
              {{ truncateText(item.description, 60) }}
            </p>
            <div class="section-services__card-footer">
              <p v-if="showPrice && item.price" class="section-services__card-price">
                {{ formatCurrency(item.price) }}
              </p>
              <button class="section-services__book-btn" @click.stop="openServiceModal(item)">
                Ver detalles
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="section-services__list">
        <div
          v-for="item in displayedItems"
          :key="item.id"
          class="section-services__list-item"
          @click="openServiceModal(item)"
        >
          <div class="section-services__list-image-wrapper">
            <img
              v-if="showImage && item.image"
              :src="item.image"
              :alt="item.name"
              class="section-services__list-image"
            />
            <div v-else class="section-services__list-image-placeholder">
              <i class="bi bi-briefcase"></i>
            </div>
          </div>
          <div class="section-services__list-content">
            <h3 class="section-services__list-title">{{ item.name }}</h3>
            <p v-if="showDescription && item.description" class="section-services__list-desc">
              {{ truncateText(item.description, 80) }}
            </p>
            <div class="section-services__list-meta">
              <span v-if="item.duration_minutes" class="section-services__meta-item">
                <i class="bi bi-clock"></i>
                {{ item.duration_minutes }} min
              </span>
              <span v-if="item.deposit_required && item.deposit_amount" class="section-services__meta-item">
                <i class="bi bi-currency-dollar"></i>
                Anticipo: ${{ item.deposit_amount }}
              </span>
            </div>
          </div>
          <div class="section-services__list-actions">
            <div v-if="showPrice && item.price" class="section-services__list-price">
              {{ formatCurrency(item.price) }}
            </div>
            <button class="section-services__action-btn" @click.stop="openServiceModal(item)">
              <i class="bi bi-arrow-right-circle"></i>
            </button>
          </div>
        </div>
      </div>

      <div v-if="hasMoreItems" class="section-services__show-all">
        <a :href="showAllUrl" class="btn btn-outline-primary">
          <i class="bi bi-grid me-2"></i>Ver todos los servicios ({{ items.length }})
        </a>
      </div>

      <div v-if="buttons && buttons.length" class="section-services__buttons mt-4">
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

    <div v-if="selectedService" class="service-modal" @click="closeServiceModal">
      <div class="service-modal__content" @click.stop>
        <button class="service-modal__close" @click="closeServiceModal">
          <i class="bi bi-x-lg"></i>
        </button>
        <div class="service-modal__gallery">
          <div v-if="selectedService.image" class="service-modal__main-image">
            <img :src="selectedService.image" :alt="selectedService.name" />
          </div>
          <div v-if="selectedService.gallery && selectedService.gallery.length > 1" class="service-modal__thumbs">
            <a
              v-for="(img, idx) in selectedService.gallery"
              :key="img.id"
              :href="img.path"
              class="service-modal__thumb glightbox"
              data-gallery="service-gallery"
              :data-title="selectedService.name"
            >
              <img :src="img.path" :alt="img.title || selectedService.name" />
            </a>
          </div>
        </div>
        <div class="service-modal__info">
          <h2 class="service-modal__name">{{ selectedService.name }}</h2>
          <div class="service-modal__prices">
            <span v-if="selectedService.price" class="service-modal__price">
              {{ formatCurrency(selectedService.price) }}
            </span>
          </div>
          <div class="service-modal__meta">
            <div v-if="selectedService.duration_minutes" class="service-modal__meta-item">
              <i class="bi bi-clock"></i>
              <span>Duracion: <strong>{{ selectedService.duration_minutes }} minutos</strong></span>
            </div>
            <div v-if="selectedService.deposit_required" class="service-modal__meta-item">
              <i class="bi bi-currency-dollar"></i>
              <span>
                Requiere anticipo:
                <strong v-if="selectedService.deposit_amount">{{ formatCurrency(selectedService.deposit_amount) }}</strong>
                <strong v-else>Si</strong>
              </span>
            </div>
            <div v-if="selectedService.allows_online_booking" class="service-modal__meta-item service-modal__meta-item--success">
              <i class="bi bi-check-circle"></i>
              <span>Reservas online disponibles</span>
            </div>
          </div>
          <p v-if="selectedService.description" class="service-modal__description">
            {{ selectedService.description }}
          </p>
          <div class="service-modal__actions">
            <a
              v-if="selectedService.whatsapp_contact"
              :href="`https://wa.me/${selectedService.whatsapp_contact}?text=Hola, me interesa el servicio: ${selectedService.name}`"
              target="_blank"
              class="btn btn-success"
            >
              <i class="bi bi-whatsapp me-2"></i>Contactar por WhatsApp
            </a>
            <a
              v-if="selectedService.allows_online_booking"
              :href="`/m/${businessSlug}/citas`"
              class="btn btn-primary"
            >
              <i class="bi bi-calendar-check me-2"></i>Reservar ahora
            </a>
            <a
              :href="`/m/${businessSlug}/servicios/${selectedService.slug}`"
              class="btn btn-outline-primary"
            >
              <i class="bi bi-eye me-2"></i>Ver detalles completos
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, nextTick, ref } from 'vue'
import GLightbox from 'glightbox'
import { usePriceFormatter } from '@/Composables/usePriceFormatter'
import 'glightbox/dist/css/glightbox.min.css'

const props = defineProps({
  title: String,
  subtitle: String,
  items: {
    type: Array,
    default: () => [],
  },
  config: {
    type: Object,
    default: () => ({}),
  },
  description: String,
  buttons: {
    type: Array,
    default: () => [],
  },
  businessSlug: {
    type: String,
    default: '',
  },
})

const selectedService = ref(null)
let lightbox = null

const { formatPrice } = usePriceFormatter({
  locale: 'es-MX',
  currency: '$',
  decimals: 2,
})

onMounted(() => {
  initLightbox()
})

const initLightbox = () => {
  nextTick(() => {
    if (lightbox) {
      lightbox.destroy()
    }
    lightbox = GLightbox({
      touchNavigation: true,
      loop: true,
      autoplayVideos: false,
      selector: '.glightbox',
    })
  })
}

const viewMode = computed(() => props.config?.view_mode || 'carousel')
const showImage = computed(() => props.config?.show_image !== false)
const showPrice = computed(() => props.config?.show_price !== false)
const showDescription = computed(() => props.config?.show_description !== true)
const maxItems = computed(() => props.config?.max_items || 12)
const minItems = computed(() => props.config?.min_items || 3)
const displayedItems = computed(() => {
  const max = maxItems.value
  const items = props.items || []
  if (items.length <= max) {
    return items
  }
  return items.slice(0, max)
})
const hasMoreItems = computed(() => (props.items || []).length > maxItems.value)
const showAllUrl = computed(() => `/m/${props.businessSlug}/servicios`)

const truncateText = (text, length) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

const formatCurrency = (value) => {
  if (value === null || value === undefined) return ''
  return formatPrice(value) || ''
}

const openServiceModal = (item) => {
  selectedService.value = item
  initLightbox()
}

const closeServiceModal = () => {
  selectedService.value = null
}
</script>

<script>
import { computed, defineComponent } from 'vue'
export default defineComponent({ name: 'SectionServices' })
</script>

<style lang="less">
.section-services {
  padding: 48px 16px;

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

  &__show-all {
    display: flex;
    justify-content: center;
    margin-top: 24px;
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

    &::-webkit-scrollbar-thumb {
      background: #dee2e6;
      border-radius: 2px;
    }
  }

  &__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 16px;
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

    .section-services__card-image-wrapper {
      position: relative;
    }

    .section-services__card-body {
      padding: 16px;
    }

    .section-services__card-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 12px;
    }
  }

  &__card {
    flex: 0 0 220px;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    scroll-snap-align: start;
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

    &-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 12px;
    }
  }

  &__card-image {
    width: 100%;
    height: 140px;
    object-fit: cover;
  }

  &__card-image-placeholder {
    width: 100%;
    height: 140px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 2.5rem;
  }

  &__duration-badge {
    position: absolute;
    bottom: 8px;
    left: 8px;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 4px;
    display: flex;
    align-items: center;
  }

  &__card-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 8px;
    color: #212529;
  }

  &__card-desc {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0 0 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
  }

  &__card-price {
    font-size: 1.125rem;
    font-weight: 700;
    color: #198754;
    margin: 0;
  }

  &__book-btn {
    background: #0d6efd;
    color: #fff;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;

    &:hover {
      background: #0b5ed7;
    }
  }

  &__list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  &__list-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;

    &:hover {
      transform: translateX(4px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }
  }

  &__list-image-wrapper {
    flex-shrink: 0;
  }

  &__list-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
  }

  &__list-image-placeholder {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 1.75rem;
    border-radius: 8px;
  }

  &__list-content {
    flex: 1;
    min-width: 0;
  }

  &__list-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 4px;
    color: #212529;
  }

  &__list-desc {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0 0 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  &__list-meta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
  }

  &__meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8125rem;
    color: #6c757d;

    i {
      color: #adb5bd;
    }
  }

  &__list-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
  }

  &__list-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #198754;
    white-space: nowrap;
  }

  &__action-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fa;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #495057;
    transition: all 0.2s;

    i {
      font-size: 1.25rem;
    }

    &:hover {
      background: #0d6efd;
      color: #fff;
    }
  }
}

.service-modal {
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
    max-width: 800px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;

    @media (max-width: 768px) {
      grid-template-columns: 1fr;
    }
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
    transition: all 0.2s;

    &:hover {
      background: #fff;
      color: #dc3545;
    }
  }

  &__gallery {
    padding: 24px;
    background: #f8f9fa;
  }

  &__main-image {
    width: 100%;
    height: 280px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 12px;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  &__thumbs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 8px;
  }

  &__thumb {
    flex: 0 0 60px;
    height: 60px;
    border-radius: 4px;
    overflow: hidden;
    display: block;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    &:hover {
      opacity: 0.8;
    }
  }

  &__info {
    padding: 24px;
  }

  &__name {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 16px;
    color: #212529;
  }

  &__prices {
    margin-bottom: 16px;
  }

  &__price {
    font-size: 1.75rem;
    font-weight: 700;
    color: #198754;
  }

  &__meta {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
  }

  &__meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9375rem;
    color: #495057;
    margin-bottom: 8px;

    &:last-child {
      margin-bottom: 0;
    }

    i {
      color: #6c757d;
      font-size: 1rem;
    }

    strong {
      color: #212529;
    }

    &--success {
      i {
        color: #198754;
      }
      color: #198754;
    }
  }

  &__description {
    font-size: 0.9375rem;
    color: #495057;
    line-height: 1.6;
    margin-bottom: 24px;
  }

  &__actions {
    display: flex;
    flex-direction: column;
    gap: 10px;

    .btn {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 12px 24px;
      font-size: 1rem;
      font-weight: 600;
    }
  }
}
</style>

<template>
  <section class="section-promotions">
    <div class="section-promotions__inner">
      <h2 v-if="title" class="section-promotions__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-promotions__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-promotions__description-text">{{ description }}</p>

      <div v-if="buttons && buttons.length" class="section-promotions__buttons">
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

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay promociones disponibles.
      </div>

      <div v-else-if="viewMode === 'carousel'" class="section-promotions__carousel">
        <div
          v-for="item in itemsWithDiscount"
          :key="item.id"
          class="section-promotions__carousel-item"
          @click="goToPromotion(item.slug)"
        >
          <div v-if="showImage && item.image" class="section-promotions__item-image">
            <img :src="item.image" :alt="item.name" />
          </div>
          <div class="section-promotions__item-content">
            <div class="section-promotions__item-header">
              <h3 class="section-promotions__item-title">{{ item.name }}</h3>
              <span v-if="item.discountPercent" class="section-promotions__item-discount">
                -{{ item.discountPercent }}%
              </span>
            </div>
            <p v-if="showDescription && item.description" class="section-promotions__item-desc">
              {{ truncateText(item.description, 80) }}
            </p>
            <div v-if="showPrice" class="section-promotions__item-prices">
              <span v-if="item.regular_price" class="section-promotions__price-original">
                ${{ item.regular_price }}
              </span>
              <span v-if="item.promotion_price" class="section-promotions__price-promotion">
                ${{ item.promotion_price }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="section-promotions__list">
        <div
          v-for="item in itemsWithDiscount"
          :key="item.id"
          class="section-promotions__list-item"
          @click="openPromotionModal(item)"
        >
          <div v-if="showImage && item.image" class="section-promotions__list-image-wrapper">
            <img :src="item.image" :alt="item.name" class="section-promotions__list-image" />
          </div>
          <div v-else class="section-promotions__list-image-placeholder">
            <i class="bi bi-tag"></i>
          </div>
          <div class="section-promotions__list-content">
            <div class="section-promotions__list-header">
              <h3 class="section-promotions__list-title">{{ item.name }}</h3>
              <span v-if="item.discountPercent" class="section-promotions__list-discount">
                -{{ item.discountPercent }}%
              </span>
            </div>
            <p v-if="showDescription && item.description" class="section-promotions__list-desc">
              {{ truncateText(item.description, 80) }}
            </p>
            <div class="section-promotions__list-meta">
              <span v-if="showPrice && item.promotion_price" class="section-promotions__list-price">
                {{ formatCurrency(item.promotion_price) }}
              </span>
              <span v-if="item.regular_price && item.promotion_price" class="section-promotions__list-price-original">
                {{ formatCurrency(item.regular_price) }}
              </span>
              <span v-if="item.expires_at" class="section-promotions__list-valid">
                <i class="bi bi-calendar3"></i>
                Valido hasta: {{ formatDate(item.expires_at) }}
              </span>
              <span v-if="item.coupon_code" class="section-promotions__list-coupon">
                <i class="bi bi-ticket"></i>
                {{ item.coupon_code }}
              </span>
            </div>
          </div>
          <div class="section-promotions__list-actions">
            <button class="section-promotions__action-btn" @click.stop="openPromotionModal(item)">
              <i class="bi bi-arrow-right-circle"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="selectedPromotion" class="promotion-modal" @click="closePromotionModal">
      <div class="promotion-modal__content" @click.stop>
        <button class="promotion-modal__close" @click="closePromotionModal">
          <i class="bi bi-x-lg"></i>
        </button>
        <div class="promotion-modal__image">
          <img v-if="selectedPromotion.image" :src="selectedPromotion.image" :alt="selectedPromotion.name" />
          <span v-if="selectedPromotion.discountPercent" class="promotion-modal__discount">
            -{{ selectedPromotion.discountPercent }}% OFF
          </span>
        </div>
        <div class="promotion-modal__info">
          <h2 class="promotion-modal__name">{{ selectedPromotion.name }}</h2>
          <div class="promotion-modal__prices">
            <span v-if="selectedPromotion.regular_price" class="promotion-modal__price-original">
              {{ formatCurrency(selectedPromotion.regular_price) }}
            </span>
            <span v-if="selectedPromotion.promotion_price" class="promotion-modal__price-promotion">
              {{ formatCurrency(selectedPromotion.promotion_price) }}
            </span>
          </div>
          <div class="promotion-modal__meta">
            <div v-if="selectedPromotion.expires_at" class="promotion-modal__meta-item">
              <i class="bi bi-calendar3"></i>
              <span>Valido hasta: <strong>{{ formatDate(selectedPromotion.expires_at) }}</strong></span>
            </div>
            <div v-if="selectedPromotion.coupon_code" class="promotion-modal__meta-item promotion-modal__meta-item--coupon">
              <i class="bi bi-ticket"></i>
              <span>Codigo: <strong>{{ selectedPromotion.coupon_code }}</strong></span>
            </div>
          </div>
          <p v-if="selectedPromotion.description" class="promotion-modal__description">
            {{ selectedPromotion.description }}
          </p>
          <div class="promotion-modal__actions">
            <button class="btn btn-outline-primary" @click="closePromotionModal">
              <i class="bi bi-x me-2"></i>Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue'
import { usePriceFormatter } from '@/Composables/usePriceFormatter'

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
  businessSlug: {
    type: String,
    default: '',
  },
})

const selectedPromotion = ref(null)

const { formatPrice } = usePriceFormatter({
  locale: 'es-MX',
  currency: '$',
  decimals: 2,
})

const viewMode = computed(() => props.config?.view_mode || 'list')
const showImage = computed(() => props.config?.show_image !== false)
const showPrice = computed(() => props.config?.show_price !== false)
const showDescription = computed(() => props.config?.show_description !== true)

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}

const formatCurrency = (value) => {
  if (value === null || value === undefined) return ''
  return formatPrice(value) || ''
}

const truncateText = (text, length) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

const itemsWithDiscount = computed(() => {
  return props.items.map(item => {
    let discountPercent = null
    if (item.regular_price && item.promotion_price && item.regular_price > item.promotion_price) {
      discountPercent = Math.round((1 - item.promotion_price / item.regular_price) * 100)
    }
    return { ...item, discountPercent }
  })
})

const goToPromotion = (slug) => {
  window.location.href = `/m/${props.businessSlug}/promociones/${slug}`
}

const openPromotionModal = (item) => {
  selectedPromotion.value = item
  document.body.style.overflow = 'hidden'
}

const closePromotionModal = () => {
  selectedPromotion.value = null
  document.body.style.overflow = ''
}
</script>

<style lang="less">
.section-promotions {
  padding: 48px 16px;
  background: #f8f9fa;

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
    margin-bottom: 24px;
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

  &__carousel-item {
    flex: 0 0 280px;
    scroll-snap-align: start;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;

    &:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }
  }

  &__item-image {
    width: 100%;
    height: 160px;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  &__item-content {
    padding: 16px;
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

  &__list-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 4px;
  }

  &__list-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0;
    color: #212529;
  }

  &__list-discount {
    background: #dc3545;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
    flex-shrink: 0;
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
    align-items: center;
  }

  &__list-price {
    font-size: 1.125rem;
    font-weight: 700;
    color: #dc3545;
    white-space: nowrap;
  }

  &__list-price-original {
    font-size: 0.875rem;
    color: #6c757d;
    text-decoration: line-through;
    white-space: nowrap;
  }

  &__list-valid {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8125rem;
    color: #6c757d;

    i {
      color: #adb5bd;
    }
  }

  &__list-coupon {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8125rem;
    color: #0d6efd;
    font-weight: 600;

    i {
      color: #adb5bd;
    }
  }

  &__list-actions {
    display: flex;
    align-items: center;
    flex-shrink: 0;
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

.promotion-modal {
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
    transition: all 0.2s;

    &:hover {
      background: #fff;
      color: #dc3545;
    }
  }

  &__image {
    width: 100%;
    height: 240px;
    overflow: hidden;
    position: relative;
    background: #f8f9fa;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  &__discount {
    position: absolute;
    top: 16px;
    right: 16px;
    background: #dc3545;
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 8px;
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
    display: flex;
    gap: 16px;
    align-items: center;
    margin-bottom: 16px;
  }

  &__price-original {
    font-size: 1rem;
    color: #6c757d;
    text-decoration: line-through;
  }

  &__price-promotion {
    font-size: 1.75rem;
    font-weight: 700;
    color: #dc3545;
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

    &--coupon {
      i {
        color: #0d6efd;
      }
      strong {
        color: #0d6efd;
        font-size: 1.125rem;
      }
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

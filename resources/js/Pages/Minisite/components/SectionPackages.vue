<template>
  <section class="section-packages">
    <div class="section-packages__inner">
      <h2 v-if="title" class="section-packages__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-packages__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-packages__description-text">{{ description }}</p>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay paquetes disponibles.
      </div>

      <div v-else class="section-packages__grid">
        <div
          v-for="item in displayedItems"
          :key="item.id"
          class="section-packages__card"
        >
          <div class="section-packages__card-image-wrapper">
            <img
              v-if="showImage && item.image"
              :src="item.image"
              :alt="item.title"
              class="section-packages__card-image"
              loading="lazy"
            />
            <div v-else class="section-packages__card-image-placeholder">
              <i class="bi bi-box-seam"></i>
            </div>
            <span v-if="item.promo_price" class="section-packages__discount-badge">
              -{{ discountPercent(item) }}%
            </span>
          </div>
          <div class="section-packages__card-body">
            <h3 class="section-packages__card-title">{{ item.title }}</h3>
            <p v-if="item.short_description" class="section-packages__card-desc">
              {{ truncateText(item.short_description, 80) }}
            </p>
            <ul v-if="item.features && item.features.length" class="section-packages__features">
              <li v-for="(feature, index) in item.features.slice(0, 4)" :key="index">
                <i class="bi bi-check-circle"></i> {{ feature }}
              </li>
            </ul>
            <div class="section-packages__card-footer">
              <div class="section-packages__card-prices">
                <span v-if="showPrice && item.promo_price" class="section-packages__card-price">
                  {{ formatCurrency(item.promo_price) }}
                </span>
                <span v-if="showPrice && item.promo_price" class="section-packages__card-price-compare">
                  {{ formatCurrency(item.price) }}
                </span>
                <span v-else-if="showPrice && item.price" class="section-packages__card-price">
                  {{ formatCurrency(item.price) }}
                </span>
              </div>
              <a
                v-if="item.whatsapp"
                :href="`https://wa.me/${item.whatsapp}?text=${encodeURIComponent(item.whatsapp_message || 'Hola, me interesa este paquete')}`"
                target="_blank"
                class="section-packages__card-btn"
              >
                <i class="bi bi-whatsapp"></i> Contactar
              </a>
            </div>
          </div>
        </div>
      </div>

      <div v-if="buttons && buttons.length" class="section-packages__buttons mt-4">
        <a
          v-for="(btn, index) in buttons"
          :key="index"
          :href="btn.url"
          class="btn btn-primary me-2 mb-2"
        >
          {{ btn.text }}
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
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
})

const showImage = computed(() => props.config?.show_image !== false)
const showPrice = computed(() => props.config?.show_price !== false)
const viewMode = computed(() => props.config?.view_mode || 'grid')
const maxItems = computed(() => props.config?.max_items || 12)
const displayedItems = computed(() => {
  const max = maxItems.value
  const items = props.items || []
  if (items.length <= max) {
    return items
  }
  return items.slice(0, max)
})

const formatCurrency = (value) => {
  if (value === null || value === undefined) return ''
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
  }).format(value)
}

const discountPercent = (item) => {
  if (!item.promo_price || !item.price) return 0
  return Math.round((1 - parseFloat(item.promo_price) / parseFloat(item.price)) * 100)
}

const truncateText = (text, length) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}
</script>

<script>
import { defineComponent } from 'vue'
import { computed } from 'vue'
export default defineComponent({ name: 'SectionPackages' })
</script>

<style lang="less">
.section-packages {
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

  &__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;

    @media (max-width: 768px) {
      grid-template-columns: repeat(2, 1fr);
    }

    @media (max-width: 480px) {
      grid-template-columns: 1fr;
    }
  }

  &__card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;

    &:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    &-image-wrapper {
      position: relative;
    }

    &-body {
      padding: 16px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    &-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: auto;
      padding-top: 12px;
    }

    &-prices {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    &-btn {
      background: #25d366;
      color: #fff;
      border: none;
      padding: 8px 12px;
      border-radius: 6px;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 4px;

      &:hover {
        background: #128c7e;
        color: #fff;
      }
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

  &__discount-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #dc3545;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 4px;
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

  &__features {
    list-style: none;
    padding: 0;
    margin: 0 0 12px;
    font-size: 0.8125rem;
    color: #495057;

    li {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 4px;

      i {
        color: #198754;
      }
    }
  }

  &__card-price {
    font-size: 1.125rem;
    font-weight: 700;
    color: #198754;
    margin: 0;
  }

  &__card-price-compare {
    font-size: 0.8125rem;
    color: #6c757d;
    text-decoration: line-through;
    margin: 0;
  }
}
</style>

<template>
  <section class="section-restaurant-menu">
    <div class="section-restaurant-menu__inner">
      <h2 v-if="title" class="section-restaurant-menu__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-restaurant-menu__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-restaurant-menu__description-text">{{ description }}</p>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay opciones disponibles en el menú.
      </div>

      <div v-else>
        <div v-if="hasCategories && items.length > 1" class="section-restaurant-menu__tabs">
          <button
            v-for="category in items"
            :key="category.id"
            class="section-restaurant-menu__tab"
            :class="{ 'active': activeCategory === category.id }"
            @click="activeCategory = category.id"
          >
            {{ category.title }}
          </button>
        </div>

        <div class="section-restaurant-menu__category-content">
          <template v-for="category in items" :key="category.id">
            <div
              v-show="!hasCategories || activeCategory === category.id || items.length === 1"
              class="section-restaurant-menu__category"
            >
              <div v-if="category.title && items.length > 1" class="section-restaurant-menu__category-header">
                <h3 class="section-restaurant-menu__category-title">{{ category.title }}</h3>
                <p v-if="category.description" class="section-restaurant-menu__category-desc">
                  {{ category.description }}
                </p>
              </div>

              <template v-if="viewMode === 'full'">
                <div class="section-restaurant-menu__full-section">
                  <h4 class="section-restaurant-menu__full-title">
                    <i class="bi bi-collection me-2"></i>Carrusel
                  </h4>
                  <div class="section-restaurant-menu__carousel">
                    <div
                      v-for="product in getDisplayedProducts(category)"
                      :key="'carousel-' + product.id"
                      class="section-restaurant-menu__carousel-card"
                      @click="openProductModal(product)"
                    >
                      <template v-if="showImages">
                        <div v-if="product.image" class="section-restaurant-menu__card-image">
                          <img :src="product.image" :alt="product.title" loading="lazy" />
                        </div>
                        <div v-else class="section-restaurant-menu__card-image-placeholder">
                          <i class="bi bi-basket"></i>
                        </div>
                      </template>
                      <div class="section-restaurant-menu__card-body">
                        <h4 class="section-restaurant-menu__product-name">{{ product.title }}</h4>
                        <p v-if="product.description" class="section-restaurant-menu__product-desc">
                          {{ truncateText(product.description, 60) }}
                        </p>
                        <div v-if="showPrices && product.price !== null && product.price !== undefined" class="section-restaurant-menu__product-price">
                          {{ formatCurrency(product.price) }}
                        </div>
                        <div v-if="product.has_variants" class="section-restaurant-menu__product-variants">
                          <span class="badge bg-secondary">
                            <i class="bi bi-list-ul me-1"></i>Con variantes
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="section-restaurant-menu__full-section">
                  <h4 class="section-restaurant-menu__full-title">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Cuadrícula
                  </h4>
                  <div class="section-restaurant-menu__grid">
                    <div
                      v-for="product in getDisplayedProducts(category)"
                      :key="'grid-' + product.id"
                      class="section-restaurant-menu__grid-card"
                      @click="openProductModal(product)"
                    >
                      <template v-if="showImages">
                        <div v-if="product.image" class="section-restaurant-menu__card-image">
                          <img :src="product.image" :alt="product.title" loading="lazy" />
                        </div>
                        <div v-else class="section-restaurant-menu__card-image-placeholder">
                          <i class="bi bi-basket"></i>
                        </div>
                      </template>
                      <div class="section-restaurant-menu__card-body">
                        <h4 class="section-restaurant-menu__product-name">{{ product.title }}</h4>
                        <p v-if="product.description" class="section-restaurant-menu__product-desc">
                          {{ truncateText(product.description, 60) }}
                        </p>
                        <div v-if="showPrices && product.price !== null && product.price !== undefined" class="section-restaurant-menu__product-price">
                          {{ formatCurrency(product.price) }}
                        </div>
                        <div v-if="product.has_variants" class="section-restaurant-menu__product-variants">
                          <span class="badge bg-secondary">
                            <i class="bi bi-list-ul me-1"></i>Con variantes
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="section-restaurant-menu__full-section">
                  <h4 class="section-restaurant-menu__full-title">
                    <i class="bi bi-list-ul me-2"></i>Lista
                  </h4>
                  <div class="section-restaurant-menu__list">
                    <div
                      v-for="product in getDisplayedProducts(category)"
                      :key="'list-' + product.id"
                      class="section-restaurant-menu__product"
                      @click="openProductModal(product)"
                    >
                      <div v-if="showImages && product.image" class="section-restaurant-menu__product-image">
                        <img :src="product.image" :alt="product.title" loading="lazy" />
                      </div>
                      <div class="section-restaurant-menu__product-info">
                        <h4 class="section-restaurant-menu__product-name">{{ product.title }}</h4>
                        <p v-if="product.description" class="section-restaurant-menu__product-desc">
                          {{ truncateText(product.description, 80) }}
                        </p>
                        <div v-if="showPrices && product.price !== null && product.price !== undefined" class="section-restaurant-menu__product-price">
                          {{ formatCurrency(product.price) }}
                        </div>
                        <div v-if="product.has_variants" class="section-restaurant-menu__product-variants">
                          <span class="badge bg-secondary">
                            <i class="bi bi-list-ul me-1"></i>Con variantes
                          </span>
                        </div>
                      </div>
                      <button class="section-restaurant-menu__product-btn">
                        <i class="bi bi-plus-lg"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </template>

              <template v-else>
                <div v-if="viewMode === 'carousel'" class="section-restaurant-menu__carousel">
                  <div
                    v-for="product in getDisplayedProducts(category)"
                    :key="product.id"
                    class="section-restaurant-menu__carousel-card"
                    @click="openProductModal(product)"
                  >
                    <template v-if="showImages">
                      <div v-if="product.image" class="section-restaurant-menu__card-image">
                        <img :src="product.image" :alt="product.title" loading="lazy" />
                      </div>
                      <div v-else class="section-restaurant-menu__card-image-placeholder">
                        <i class="bi bi-basket"></i>
                      </div>
                    </template>
                    <div class="section-restaurant-menu__card-body">
                      <h4 class="section-restaurant-menu__product-name">{{ product.title }}</h4>
                      <p v-if="product.description" class="section-restaurant-menu__product-desc">
                        {{ truncateText(product.description, 60) }}
                      </p>
                      <div v-if="showPrices && product.price !== null && product.price !== undefined" class="section-restaurant-menu__product-price">
                        {{ formatCurrency(product.price) }}
                      </div>
                      <div v-if="product.has_variants" class="section-restaurant-menu__product-variants">
                        <span class="badge bg-secondary">
                          <i class="bi bi-list-ul me-1"></i>Con variantes
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-else-if="viewMode === 'grid'" class="section-restaurant-menu__grid">
                  <div
                    v-for="product in getDisplayedProducts(category)"
                    :key="product.id"
                    class="section-restaurant-menu__grid-card"
                    @click="openProductModal(product)"
                  >
                    <template v-if="showImages">
                      <div v-if="product.image" class="section-restaurant-menu__card-image">
                        <img :src="product.image" :alt="product.title" loading="lazy" />
                      </div>
                      <div v-else class="section-restaurant-menu__card-image-placeholder">
                        <i class="bi bi-basket"></i>
                      </div>
                    </template>
                    <div class="section-restaurant-menu__card-body">
                      <h4 class="section-restaurant-menu__product-name">{{ product.title }}</h4>
                      <p v-if="product.description" class="section-restaurant-menu__product-desc">
                        {{ truncateText(product.description, 60) }}
                      </p>
                      <div v-if="showPrices && product.price !== null && product.price !== undefined" class="section-restaurant-menu__product-price">
                        {{ formatCurrency(product.price) }}
                      </div>
                      <div v-if="product.has_variants" class="section-restaurant-menu__product-variants">
                        <span class="badge bg-secondary">
                          <i class="bi bi-list-ul me-1"></i>Con variantes
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <div v-else class="section-restaurant-menu__list">
                  <div
                    v-for="product in getDisplayedProducts(category)"
                    :key="product.id"
                    class="section-restaurant-menu__product"
                    @click="openProductModal(product)"
                  >
                    <div v-if="showImages && product.image" class="section-restaurant-menu__product-image">
                      <img :src="product.image" :alt="product.title" loading="lazy" />
                    </div>
                    <div class="section-restaurant-menu__product-info">
                      <h4 class="section-restaurant-menu__product-name">{{ product.title }}</h4>
                      <p v-if="product.description" class="section-restaurant-menu__product-desc">
                        {{ truncateText(product.description, 80) }}
                      </p>
                      <div v-if="showPrices && product.price !== null && product.price !== undefined" class="section-restaurant-menu__product-price">
                        {{ formatCurrency(product.price) }}
                      </div>
                      <div v-if="product.has_variants" class="section-restaurant-menu__product-variants">
                        <span class="badge bg-secondary">
                          <i class="bi bi-list-ul me-1"></i>Con variantes
                        </span>
                      </div>
                    </div>
                    <button class="section-restaurant-menu__product-btn">
                      <i class="bi bi-plus-lg"></i>
                    </button>
                  </div>
                </div>
              </template>

              <div v-if="hasMoreItems(category) && viewMode !== 'full'" class="section-restaurant-menu__show-all">
                <button class="btn btn-outline-primary" @click="showAllCategory(category.id)">
                  Ver todos ({{ category.products.length }})
                </button>
              </div>
            </div>
          </template>
        </div>
      </div>

      <div class="section-restaurant-menu__buttons">
        <a
          v-if="buttons && buttons.length"
          v-for="(btn, index) in buttons"
          :key="index"
          :href="btn.url"
          class="btn"
          :class="'btn-' + (btn.style || 'primary')"
        >
          {{ btn.text }}
        </a>
        <a
          v-if="businessSlug"
          :href="`/m/${businessSlug}/menu`"
          class="btn btn-outline-primary"
        >
          <i class="bi bi-cup-hot me-2"></i>Ver menú completo
        </a>
      </div>
    </div>

    <div v-if="selectedProduct" class="product-modal" @click="closeProductModal">
      <div class="product-modal__content" @click.stop>
        <button class="product-modal__close" @click="closeProductModal">
          <i class="bi bi-x-lg"></i>
        </button>
        <div v-if="selectedProduct.image" class="product-modal__image">
          <img :src="selectedProduct.image" :alt="selectedProduct.title" />
        </div>
        <div class="product-modal__info">
          <h2 class="product-modal__name">{{ selectedProduct.title }}</h2>
          <div v-if="selectedProduct.price && !selectedProduct.has_variants" class="product-modal__price">
            {{ formatCurrency(selectedProduct.price) }}
          </div>
          <p v-if="selectedProduct.description" class="product-modal__description">
            {{ selectedProduct.description }}
          </p>

          <div v-if="selectedProduct.has_variants && selectedProduct.variants" class="product-modal__variants">
            <h4 class="product-modal__variants-title">Opciones:</h4>
            <div
              v-for="variant in selectedProduct.variants"
              :key="variant.id"
              class="product-modal__variant"
            >
              <div class="product-modal__variant-info">
                <span class="product-modal__variant-name">{{ variant.title }}</span>
                <span v-if="variant.description" class="product-modal__variant-desc">
                  {{ variant.description }}
                </span>
              </div>
              <span class="product-modal__variant-price">{{ formatCurrency(variant.price) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue'

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

const selectedProduct = ref(null)
const expandedCategories = ref(new Set())

const viewMode = computed(() => props.config?.view_mode || 'list')
const showImages = computed(() => props.config?.show_images !== false)
const showPrices = computed(() => props.config?.show_prices !== false)
const maxItems = computed(() => props.config?.max_items || 12)
const showAll = computed(() => props.config?.show_all === true)

const hasCategories = computed(() => {
  return props.items.some(cat => cat.products && cat.products.length > 0)
})

const activeCategory = ref(null)

const getDisplayedProducts = (category) => {
  const products = category.products || []
  if (showAll.value || expandedCategories.value.has(category.id)) {
    return products
  }
  return products.slice(0, maxItems.value)
}

const hasMoreItems = (category) => {
  const products = category.products || []
  return !showAll.value && !expandedCategories.value.has(category.id) && products.length > maxItems.value
}

const showAllCategory = (categoryId) => {
  expandedCategories.value.add(categoryId)
}

const truncateText = (text, length) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

const formatCurrency = (value) => {
  if (value === null || value === undefined) return ''
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
  }).format(value)
}

const openProductModal = (product) => {
  selectedProduct.value = product
}

const closeProductModal = () => {
  selectedProduct.value = null
}
</script>

<script>
import { defineComponent } from 'vue'
export default defineComponent({ name: 'SectionRestaurantMenu' })
</script>

<style lang="less">
.section-restaurant-menu {
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

  &__tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e9ecef;
  }

  &__tab {
    padding: 8px 16px;
    border: 1px solid #dee2e6;
    border-radius: 20px;
    background: #fff;
    color: #495057;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;

    &:hover {
      background: #f8f9fa;
      border-color: #adb5bd;
    }

    &.active {
      background: #0d6efd;
      border-color: #0d6efd;
      color: #fff;
    }
  }

  &__category-content {
    margin-bottom: 24px;
  }

  &__category {
    margin-bottom: 32px;

    &:last-child {
      margin-bottom: 0;
    }
  }

  &__category-header {
    margin-bottom: 16px;
  }

  &__category-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #212529;
    margin: 0 0 4px;
  }

  &__category-desc {
    font-size: 0.875rem;
    color: #6c757d;
    margin: 0;
  }

  &__list {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  &__product {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: 8px;
    background: #f8f9fa;
    cursor: pointer;
    transition: background 0.2s;

    &:hover {
      background: #e9ecef;
    }
  }

  &__product-image {
    flex: 0 0 64px;
    width: 64px;
    height: 64px;
    border-radius: 8px;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  }

  &__product-image-placeholder {
    width: 64px;
    height: 64px;
    border-radius: 8px;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 1.5rem;
  }

  &__product-info {
    flex: 1;
    min-width: 0;
  }

  &__product-name {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
    margin: 0 0 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  &__product-desc {
    font-size: 0.8125rem;
    color: #6c757d;
    margin: 0 0 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  &__product-price {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #198754;
  }

  &__product-variants {
    margin-top: 4px;
  }

  &__product-btn {
    flex: 0 0 32px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #0d6efd;
    color: #fff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;

    &:hover {
      background: #0b5ed7;
    }
  }

  &__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
  }

  &__grid-card {
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;

    &:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .section-restaurant-menu__card-image {
      width: 100%;
      height: 140px;
      overflow: hidden;

      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }

    .section-restaurant-menu__card-image-placeholder {
      width: 100%;
      height: 140px;
      background: #e9ecef;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #adb5bd;
      font-size: 2.5rem;
    }

    .section-restaurant-menu__card-body {
      padding: 16px;
    }

    .section-restaurant-menu__product-name {
      font-size: 0.9375rem;
      font-weight: 600;
      color: #212529;
      margin: 0 0 8px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .section-restaurant-menu__product-desc {
      font-size: 0.8125rem;
      color: #6c757d;
      margin: 0 0 8px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .section-restaurant-menu__product-price {
      font-size: 0.9375rem;
      font-weight: 600;
      color: #198754;
    }
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

  &__carousel-card {
    flex: 0 0 220px;
    background: #f8f9fa;
    border-radius: 12px;
    overflow: hidden;
    scroll-snap-align: start;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;

    &:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .section-restaurant-menu__card-image {
      width: 100%;
      height: 140px;
      overflow: hidden;

      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
    }

    .section-restaurant-menu__card-image-placeholder {
      width: 100%;
      height: 140px;
      background: #e9ecef;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #adb5bd;
      font-size: 2.5rem;
    }

    .section-restaurant-menu__card-body {
      padding: 16px;
    }

    .section-restaurant-menu__product-name {
      font-size: 0.9375rem;
      font-weight: 600;
      color: #212529;
      margin: 0 0 8px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .section-restaurant-menu__product-desc {
      font-size: 0.8125rem;
      color: #6c757d;
      margin: 0 0 8px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .section-restaurant-menu__product-price {
      font-size: 0.9375rem;
      font-weight: 600;
      color: #198754;
    }
  }

  &__show-all {
    display: flex;
    justify-content: center;
    margin-top: 16px;
  }

  &__full-section {
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 1px dashed #dee2e6;

    &:last-of-type {
      border-bottom: none;
      margin-bottom: 0;
    }
  }

  &__full-title {
    font-size: 1rem;
    font-weight: 600;
    color: #495057;
    margin: 0 0 16px;
    display: flex;
    align-items: center;

    i {
      color: #0d6efd;
    }
  }

  &__buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
  }
}

.product-modal {
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

  &__content {
    background: #fff;
    border-radius: 16px;
    max-width: 500px;
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
    height: 250px;
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

  &__name {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0 0 12px;
    color: #212529;
  }

  &__price {
    font-size: 1.25rem;
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

  &__variants {
    border-top: 1px solid #e9ecef;
    padding-top: 16px;
  }

  &__variants-title {
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
    margin: 0 0 12px;
  }

  &__variant {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 8px;

    &:last-child {
      margin-bottom: 0;
    }
  }

  &__variant-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  &__variant-name {
    font-weight: 500;
    color: #212529;
  }

  &__variant-desc {
    font-size: 0.8125rem;
    color: #6c757d;
  }

  &__variant-price {
    font-weight: 600;
    color: #198754;
  }
}
</style>

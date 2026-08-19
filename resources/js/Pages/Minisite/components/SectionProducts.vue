<template>
  <section class="section-products">
    <div class="section-products__inner">
      <h2 v-if="title" class="section-products__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-products__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-products__description-text">{{ description }}</p>

      <div v-if="items.length === 0" class="text-muted text-center py-4">
        No hay productos disponibles.
      </div>

      <div v-else-if="viewMode === 'carousel'" class="section-products__carousel">
        <div
          v-for="item in displayedItems"
          :key="item.id"
          class="section-products__card"
          @click="openProductModal(item)"
        >
          <div class="section-products__card-image-wrapper">
            <img
              v-if="showImage && item.image"
              :src="item.image"
              :alt="item.name"
              class="section-products__card-image"
              loading="lazy"
            />
            <div v-else class="section-products__card-image-placeholder">
              <i class="bi bi-image"></i>
            </div>
            <span v-if="item.compare_at_price && showComparePrice" class="section-products__discount-badge">
              -{{ discountPercent(item) }}%
            </span>
          </div>
          <div class="section-products__card-body">
            <h3 class="section-products__card-title">{{ item.name }}</h3>
            <p v-if="item.description" class="section-products__card-desc">
              {{ truncateText(item.description, 60) }}
            </p>
            <div class="section-products__card-footer">
              <div class="section-products__card-prices">
                <span v-if="showPrice && item.price" class="section-products__card-price">
                  {{ formatCurrency(item.price) }}
                </span>
                <span v-if="showComparePrice && item.compare_at_price" class="section-products__card-price-compare">
                  {{ formatCurrency(item.compare_at_price) }}
                </span>
              </div>
              <button class="section-products__card-btn" @click.stop="openProductModal(item)">
                Ver
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="viewMode === 'list'" class="section-products__list">
        <div
          v-for="item in displayedItems"
          :key="item.id"
          class="section-products__list-item"
          @click="openProductModal(item)"
        >
          <div class="section-products__list-image-wrapper">
            <img
              v-if="showImage && item.image"
              :src="item.image"
              :alt="item.name"
              class="section-products__list-image"
              loading="lazy"
            />
            <div v-else class="section-products__list-image-placeholder">
              <i class="bi bi-image"></i>
            </div>
          </div>
          <div class="section-products__list-content">
            <span v-if="item.category_name" class="section-products__list-category">
              <i class="bi bi-tag"></i>{{ item.category_name }}
            </span>
            <h3 class="section-products__list-title">{{ item.name }}</h3>
            <p v-if="item.description" class="section-products__list-desc">
              {{ truncateText(item.description, 100) }}
            </p>
            <div class="section-products__list-meta">
              <span v-if="item.location_name" class="section-products__meta-item">
                <i class="bi bi-geo-alt"></i>{{ item.location_name }}
              </span>
              <span v-if="showStock && item.quantity !== null" class="section-products__meta-item">
                <i :class="item.quantity > 0 ? 'bi bi-check-circle text-success' : 'bi bi-x-circle text-danger'"></i>
                {{ item.quantity > 0 ? 'En stock' : 'Agotado' }}
              </span>
              <span v-if="item.sku" class="section-products__meta-item">
                <i class="bi bi-upc"></i>
                SKU: {{ item.sku }}
              </span>
            </div>
          </div>
          <div class="section-products__list-actions">
            <div class="section-products__list-prices">
              <span v-if="showPrice && item.price" class="section-products__list-price">
                {{ formatCurrency(item.price) }}
              </span>
              <span v-if="showComparePrice && item.compare_at_price" class="section-products__list-price-compare">
                {{ formatCurrency(item.compare_at_price) }}
              </span>
            </div>
            <button class="section-products__action-btn" @click.stop="openProductModal(item)">
              <i class="bi bi-arrow-right-circle"></i>
            </button>
          </div>
        </div>
      </div>

      <div v-else class="section-products__grid">
        <div
          v-for="item in displayedItems"
          :key="item.id"
          class="section-products__card"
          @click="openProductModal(item)"
        >
          <div class="section-products__card-image-wrapper">
            <img
              v-if="showImage && item.image"
              :src="item.image"
              :alt="item.name"
              class="section-products__card-image"
              loading="lazy"
            />
            <div v-else class="section-products__card-image-placeholder">
              <i class="bi bi-image"></i>
            </div>
            <span v-if="item.compare_at_price && showComparePrice" class="section-products__discount-badge">
              -{{ discountPercent(item) }}%
            </span>
          </div>
          <div class="section-products__card-body">
            <h3 class="section-products__card-title">{{ item.name }}</h3>
            <p v-if="item.description" class="section-products__card-desc">
              {{ truncateText(item.description, 60) }}
            </p>
            <div class="section-products__card-footer">
              <div class="section-products__card-prices">
                <span v-if="showPrice && item.price" class="section-products__card-price">
                  {{ formatCurrency(item.price) }}
                </span>
                <span v-if="showComparePrice && item.compare_at_price" class="section-products__card-price-compare">
                  {{ formatCurrency(item.compare_at_price) }}
                </span>
              </div>
              <button class="section-products__card-btn" @click.stop="openProductModal(item)">
                Ver
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="hasMoreItems" class="section-products__show-all">
        <a :href="showAllUrl" class="btn btn-outline-primary">
          <i class="bi bi-grid me-2"></i>Ver todos los productos ({{ items.length }})
        </a>
      </div>

      <div v-if="buttons && buttons.length" class="section-products__buttons mt-4">
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

    <div v-if="selectedProduct" class="product-modal" @click="closeProductModal">
      <div class="product-modal__content" @click.stop>
        <button class="product-modal__close" @click="closeProductModal">
          <i class="bi bi-x-lg"></i>
        </button>
        <div class="product-modal__gallery">
          <div v-if="selectedProduct.image" class="product-modal__main-image">
            <a :href="selectedProduct.image" class="glightbox" data-gallery="product-gallery" :data-title="selectedProduct.name">
              <img :src="selectedProduct.image" :alt="selectedProduct.name" />
            </a>
          </div>
          <div v-if="selectedProduct.gallery && selectedProduct.gallery.length > 1" class="product-modal__thumbs">
            <a
              v-for="(img, idx) in selectedProduct.gallery"
              :key="img.id"
              :href="img.path"
              class="product-modal__thumb glightbox"
              data-gallery="product-gallery"
              :data-title="selectedProduct.name"
            >
              <img :src="img.path" :alt="img.title || selectedProduct.name" />
            </a>
          </div>
        </div>
        <div class="product-modal__info">
          <div v-if="selectedProduct.category_name" class="product-modal__category">
            <i class="bi bi-tag me-1"></i>{{ selectedProduct.category_name }}
          </div>
          <h2 class="product-modal__name">{{ selectedProduct.name }}</h2>
          <div class="product-modal__prices">
            <span v-if="selectedProduct.price" class="product-modal__price">
              {{ formatCurrency(selectedProduct.price) }}
            </span>
            <span v-if="selectedProduct.compare_at_price" class="product-modal__price-compare">
              {{ formatCurrency(selectedProduct.compare_at_price) }}
            </span>
            <span v-if="selectedProduct.compare_at_price" class="product-modal__discount">
              -{{ discountPercent(selectedProduct) }}% OFF
            </span>
          </div>
          <div class="product-modal__meta">
            <div v-if="selectedProduct.location_name" class="product-modal__meta-item">
              <i class="bi bi-geo-alt"></i>
              <span>{{ selectedProduct.location_name }}</span>
            </div>
            <div v-if="selectedProduct.sku" class="product-modal__meta-item">
              <i class="bi bi-upc"></i>
              <span>SKU: {{ selectedProduct.sku }}</span>
            </div>
            <div v-if="selectedProduct.barcode" class="product-modal__meta-item">
              <i class="bi bi-barcode"></i>
              <span>EAN: {{ selectedProduct.barcode }}</span>
            </div>
          </div>
          <div v-if="selectedProduct.quantity !== null" class="product-modal__stock">
            <span v-if="selectedProduct.quantity > 0" class="badge bg-success">
              <i class="bi bi-check-circle me-1"></i>En stock ({{ selectedProduct.quantity }})
            </span>
            <span v-else class="badge bg-danger">
              <i class="bi bi-x-circle me-1"></i>Agotado
            </span>
          </div>
          <p v-if="selectedProduct.description" class="product-modal__description">
            {{ selectedProduct.description }}
          </p>
          <div class="product-modal__actions">
            <button
              v-if="orderSettings?.is_active && hasValidPrice"
              class="btn btn-primary btn-lg w-100 mb-2"
              @click="addToCart"
            >
              <i class="bi bi-cart-plus me-2"></i>Agregar al carrito
            </button>
            <a
              v-if="selectedProduct.whatsapp_contact"
              :href="`https://wa.me/${selectedProduct.whatsapp_contact}?text=Hola, me interesa el producto: ${selectedProduct.name}`"
              target="_blank"
              class="btn btn-success"
            >
              <i class="bi bi-whatsapp me-2"></i>Contactar por WhatsApp
            </a>
            <a
              :href="`/m/${businessSlug}/productos/${selectedProduct.slug}`"
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
import 'glightbox/dist/css/glightbox.min.css'
import { useCart } from '@/composables/useCart'

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
  orderSettings: {
    type: Object,
    default: null,
  },
})

const cart = useCart()

const selectedProduct = ref(null)
let lightbox = null

const formatCurrency = (value) => {
  if (value === null || value === undefined) return ''
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
  }).format(value)
}

const showImage = computed(() => props.config?.show_image !== false)
const showPrice = computed(() => props.config?.show_price !== false)
const showComparePrice = computed(() => props.config?.show_compare_price !== false)
const showStock = computed(() => props.config?.show_stock !== false)
const viewMode = computed(() => props.config?.view_mode || 'grid')
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
const showAllUrl = computed(() => `/m/${props.businessSlug}/productos`)

const discountPercent = (item) => {
  if (!item.compare_at_price || !item.price) return 0
  return Math.round((1 - item.price / item.compare_at_price) * 100)
}

const truncateText = (text, length) => {
  if (!text || text.length <= length) return text
  return text.substring(0, length) + '...'
}

const openProductModal = (item) => {
  selectedProduct.value = item
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

const closeProductModal = () => {
  selectedProduct.value = null
}

const hasValidPrice = computed(() => {
  if (!selectedProduct.value) return false
  const price = parseFloat(selectedProduct.value.price)
  return !isNaN(price) && price > 0
})

const addToCart = () => {
  if (!selectedProduct.value) return
  const product = selectedProduct.value
  cart.addItem({
    id: product.id,
    business_id: product.business_id,
    title: product.name,
    image: product.image,
    base_price: product.price,
  }, {
    productType: 'product',
    quantity: 1,
  })
  closeProductModal()
  cart.openCart()
}
</script>

<script>
import { defineComponent } from 'vue'
export default defineComponent({ name: 'SectionProducts' })
</script>

<style lang="less">
.section-products {
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

  // Carousel
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

  // Grid
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

  // Card (used by both carousel and grid)
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

    &-prices {
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    &-btn {
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

  // List View
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
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
  }

  &__list-image-placeholder {
    width: 100px;
    height: 100px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 2rem;
    border-radius: 8px;
  }

  &__list-content {
    flex: 1;
    min-width: 0;
  }

  &__list-category {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 600;
    color: #0d6efd;
    margin-bottom: 4px;

    i {
      margin-right: 4px;
    }
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

    .text-success {
      color: #198754 !important;
    }

    .text-danger {
      color: #dc3545 !important;
    }
  }

  &__list-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
  }

  &__list-prices {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
  }

  &__list-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #198754;
    white-space: nowrap;
  }

  &__list-price-compare {
    font-size: 0.875rem;
    color: #6c757d;
    text-decoration: line-through;
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
    transition: all 0.2s;
    color: #495057;

    &:hover {
      background: #0d6efd;
      color: #fff;
    }
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
  overflow-y: auto;

  &__content {
    background: #fff;
    border-radius: 16px;
    max-width: 1000px;
    width: 95%;
    max-height: 95vh;
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
    top: 16px;
    right: 16px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    color: #495057;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);

    &:hover {
      background: #fff;
      color: #dc3545;
    }
  }

  &__gallery {
    padding: 32px;
    background: #f8f9fa;
  }

  &__main-image {
    width: 100%;
    height: 400px;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 16px;

    a {
      display: block;
      width: 100%;
      height: 100%;
    }

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s;
    }

    &:hover img {
      transform: scale(1.02);
    }
  }

  &__thumbs {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding-bottom: 8px;
  }

  &__thumb {
    flex: 0 0 70px;
    height: 70px;
    border-radius: 8px;
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
    padding: 32px;
  }

  &__name {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 12px;
    color: #212529;
  }

  &__category {
    display: inline-block;
    background: #e7f1ff;
    color: #0d6efd;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
    margin-bottom: 12px;
  }

  &__prices {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  &__price {
    font-size: 2.25rem;
    font-weight: 700;
    color: #198754;
  }

  &__price-compare {
    font-size: 1.25rem;
    color: #6c757d;
    text-decoration: line-through;
  }

  &__discount {
    background: #dc3545;
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 6px;
  }

  &__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 16px;
  }

  &__meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.9375rem;
    color: #6c757d;

    i {
      color: #adb5bd;
    }
  }

  &__stock {
    margin-bottom: 20px;
  }

  &__description {
    font-size: 1.0625rem;
    color: #495057;
    line-height: 1.7;
    margin-bottom: 28px;
  }

  &__actions {
    display: flex;
    flex-direction: column;
    gap: 8px;

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

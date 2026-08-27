<template>
  <section class="vcard-section vcard-menu" v-if="displayCategories.length > 0">
    <h2 class="vcard-section__title">Menú</h2>

    <div v-if="displayCategories.length > 0" class="vcard-menu__categories">
      <button
        v-for="(cat, index) in displayCategories"
        :key="cat.id"
        class="vcard-menu__category-btn"
        :class="{ active: activeCategory === index }"
        @click="selectCategory(index)"
      >
        {{ cat.title }}
      </button>
    </div>

    <div v-if="displayCategories.length > 0 && currentCategoryProducts.length > 0" class="vcard-menu__products">
      <article
        v-for="product in visibleProducts"
        :key="product.id"
        class="vcard-menu__product card border-0 shadow-sm"
      >
        <div v-if="product.image" class="vcard-menu__product-image">
          <img :src="getImageUrl(product.image)" :alt="product.title">
        </div>
        <div class="vcard-menu__product-placeholder" v-else>
          <i class="bi bi-image"></i>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-1">
            <h3 class="vcard-menu__product-name">{{ product.title }}</h3>
            <span v-if="product.price" class="vcard-menu__product-price" :style="{ color: 'var(--vcard-primary)' }">
              {{ formatPrice(product.price) }}
            </span>
          </div>
          <p v-if="product.description" class="vcard-menu__product-desc text-muted small mb-0">
            {{ product.description }}
          </p>
        </div>
      </article>

      <div v-if="hasMoreProducts" class="vcard-menu__load-more text-center">
        <button class="btn btn-outline-primary btn-sm" @click="loadMore">
          Ver más ({{ remainingProducts }})
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
  maxCategories: {
    type: Number,
    default: 6,
  },
  initialProductsPerCategory: {
    type: Number,
    default: 5,
  },
})

const activeCategory = ref(0)
const visibleCount = ref(props.initialProductsPerCategory)

const displayCategories = computed(() => {
  if (props.categories && props.categories.length > 0) {
    return props.categories.slice(0, props.maxCategories)
  }
  return []
})

const currentCategoryProducts = computed(() => {
  const cat = displayCategories.value[activeCategory.value]
  return cat?.products || []
})

const visibleProducts = computed(() => {
  return currentCategoryProducts.value.slice(0, visibleCount.value)
})

const hasMoreProducts = computed(() => {
  return visibleCount.value < currentCategoryProducts.value.length
})

const remainingProducts = computed(() => {
  return currentCategoryProducts.value.length - visibleCount.value
})

function selectCategory(index) {
  activeCategory.value = index
  visibleCount.value = props.initialProductsPerCategory
}

function loadMore() {
  visibleCount.value += props.initialProductsPerCategory
}

function getImageUrl(path) {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/storage/')) {
    return path
  }
  return '/storage/' + path
}

function formatPrice(price) {
  if (!price && price !== 0) return ''
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'USD' }).format(price)
}
</script>

<style scoped>
.vcard-menu__categories {
  display: flex;
  gap: 0.375rem;
  overflow-x: auto;
  padding-bottom: 0.5rem;
  margin-bottom: 1rem;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}

.vcard-menu__categories::-webkit-scrollbar {
  display: none;
}

.vcard-menu__category-btn {
  flex-shrink: 0;
  padding: 0.375rem 0.875rem;
  background: var(--vcard-surface);
  border: 1px solid #e5e7eb;
  border-radius: 1.5rem;
  font-size: 0.8125rem;
  font-weight: 500;
  color: var(--vcard-text);
  cursor: pointer;
  transition: all 0.2s;
}

.vcard-menu__category-btn:hover {
  border-color: var(--vcard-primary);
  color: var(--vcard-primary);
}

.vcard-menu__category-btn.active {
  background: var(--vcard-primary);
  border-color: var(--vcard-primary);
  color: #fff;
}

.vcard-menu__products {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 0.875rem;
}

.vcard-menu__product {
  display: flex;
  flex-direction: row;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.vcard-menu__product:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.375rem 1rem rgba(0, 0, 0, 0.1);
}

.vcard-menu__product-image {
  width: 90px;
  height: 90px;
  flex-shrink: 0;
  overflow: hidden;
}

.vcard-menu__product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.vcard-menu__product-placeholder {
  width: 90px;
  height: 90px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: color-mix(in srgb, var(--vcard-primary) 10%, var(--vcard-surface));
  color: var(--vcard-muted);
  font-size: 1.75rem;
}

.vcard-menu__product .card-body {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 0.625rem 0.75rem;
}

.vcard-menu__product-name {
  margin: 0;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--vcard-text);
  line-height: 1.3;
}

.vcard-menu__product-price {
  font-size: 0.875rem;
  font-weight: 700;
  white-space: nowrap;
}

.vcard-menu__product-desc {
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.vcard-menu__load-more {
  grid-column: 1 / -1;
  margin-top: 0.5rem;
}

@media (max-width: 380px) {
  .vcard-menu__product {
    flex-direction: column;
  }

  .vcard-menu__product-image,
  .vcard-menu__product-placeholder {
    width: 100%;
    height: 100px;
  }
}
</style>

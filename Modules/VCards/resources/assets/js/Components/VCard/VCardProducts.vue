<template>
  <section class="vcard-section vcard-products" v-if="displayProducts.length > 0">
    <h2 class="vcard-section__title">Productos</h2>

    <div class="vcard-products__grid">
      <article
        v-for="product in displayProducts"
        :key="product.id"
        class="vcard-product card border-0 shadow-sm h-100"
      >
        <div class="vcard-product__image-wrap" v-if="product.image">
          <img
            :src="getImageUrl(product.image)"
            :alt="product.name"
            class="vcard-product__image"
            loading="lazy"
          >
        </div>
        <div class="card-body">
          <h3 class="vcard-product__title">{{ product.name }}</h3>
          <p v-if="product.description" class="vcard-product__text">{{ product.description }}</p>
          <strong v-if="product.price" class="vcard-product__price">{{ formatPrice(product.price) }}</strong>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  products: {
    type: Array,
    default: () => [],
  },
})

const displayProducts = computed(() => {
  if (props.products && props.products.length > 0) {
    return props.products
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

function formatPrice(price) {
  if (!price && price !== 0) return ''
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'USD' }).format(price)
}
</script>

<style scoped>
.vcard-products__grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
}

@media (max-width: 35.9375rem) {
  .vcard-products__grid {
    grid-template-columns: 1fr;
  }
}

.vcard-product {
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.vcard-product:hover {
  transform: translateY(-0.125rem);
  box-shadow: 0 0.5rem 1.25rem rgba(15, 23, 42, 0.12);
}

.vcard-product__image-wrap {
  width: 100%;
  aspect-ratio: 4 / 3;
  overflow: hidden;
  background: var(--vcard-surface-2);
}

.vcard-product__image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.3s;
}

.vcard-product:hover .vcard-product__image {
  transform: scale(1.05);
}

.vcard-product .card-body {
  padding: 1rem;
}

.vcard-product__title {
  margin: 0 0 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--vcard-text);
  line-height: 1.3;
}

.vcard-product__text {
  margin: 0 0 0.75rem;
  font-size: 0.8rem;
  color: var(--vcard-muted);
  line-height: 1.4;
}

.vcard-product__price {
  display: inline-block;
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--vcard-primary);
}
</style>

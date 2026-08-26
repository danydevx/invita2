<template>
  <section class="vcard-section vcard-products" v-if="displayProducts.length > 0">
    <h2 class="vcard-section__title">Productos</h2>

    <div class="vcard-products__grid">
      <article
        v-for="product in displayProducts"
        :key="product.title"
        class="vcard-product card border-0 shadow-sm h-100"
      >
        <div class="vcard-product__image-wrap" v-if="product.image || showPlaceholders">
          <img
            :src="product.image || `https://placehold.co/400x300/${getProductColor(product.title)}/ffffff?text=${encodeURIComponent(product.title)}`"
            :alt="product.title"
            class="vcard-product__image"
            loading="lazy"
          >
        </div>
        <div class="card-body">
          <h3 class="vcard-product__title">{{ product.title }}</h3>
          <p v-if="product.description" class="vcard-product__text">{{ product.description }}</p>
          <p v-else-if="product.text" class="vcard-product__text">{{ product.text }}</p>
          <strong v-if="product.price" class="vcard-product__price">{{ product.price }}</strong>
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
  limit: {
    type: Number,
    default: 10,
  },
  showPlaceholders: {
    type: Boolean,
    default: true,
  },
})

const displayProducts = computed(() => {
  if (props.products && props.products.length > 0) {
    return props.products.slice(0, props.limit)
  }
  return [
    { title: 'Servicio de Barbería', description: 'Corte clásico y moderno con tratamiento facial incluido.', price: '$250', image: '' },
    { title: 'Arreglo de Barba', description: 'Diseño y perfilado de barba con toalla caliente.', price: '$120', image: '' },
    { title: 'Tintura', description: 'Aplicación de tintura profesional con garantía de duración.', price: '$350', image: '' },
    { title: 'Paquete Completo', description: 'Incluye corte, barba, tintura y tratamiento capilar.', price: '$550', image: '' },
  ].slice(0, props.limit)
})

function getProductColor(title) {
  const colors = [
    '2563EB', '7C3AED', 'DB2777', '16A34A', 'EA580C',
    '0891B2', '4F46E5', 'DC2626', 'CA8A04', '059669'
  ]
  let hash = 0
  for (let i = 0; i < title.length; i++) {
    hash = title.charCodeAt(i) + ((hash << 5) - hash)
  }
  return colors[Math.abs(hash) % colors.length]
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

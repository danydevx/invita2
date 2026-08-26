<template>
  <section class="vcard-section vcard-packages" v-if="packages.length > 0">
    <h2 class="vcard-section__title">Paquetes</h2>

    <div class="vcard-packages__list">
      <article
        v-for="pkg in packages"
        :key="pkg.id"
        class="vcard-package card border-0 shadow-sm"
      >
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h3 class="vcard-package__title">{{ pkg.name }}</h3>
            <span v-if="pkg.price" class="vcard-package__price">
              {{ formatPrice(pkg.price, pkg.currency) }}
            </span>
          </div>
          <p v-if="pkg.description" class="vcard-package__description">{{ pkg.description }}</p>
          <p v-if="pkg.duration_days" class="vcard-package__duration">
            <i class="bi bi-calendar3 me-1"></i>
            {{ pkg.duration_days }} dias
          </p>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  packages: {
    type: Array,
    default: () => [],
  },
})

function formatPrice(price, currency) {
  return new Intl.NumberFormat('es-ES', {
    style: 'currency',
    currency: currency || 'USD',
  }).format(price)
}
</script>

<style scoped>
.vcard-packages__list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.vcard-package {
  transition: transform 0.2s, box-shadow 0.2s;
}

.vcard-package:hover {
  transform: translateX(0.25rem);
  box-shadow: 0 0.375rem 1rem rgba(15, 23, 42, 0.12);
}

.vcard-package__title {
  margin: 0;
  font-size: 0.9375rem;
  font-weight: 600;
  color: var(--vcard-text);
  line-height: 1.3;
}

.vcard-package__price {
  font-size: 1rem;
  font-weight: 700;
  color: var(--vcard-primary);
}

.vcard-package__description {
  margin: 0.5rem 0 0;
  font-size: 0.8125rem;
  color: var(--vcard-muted);
  line-height: 1.4;
}

.vcard-package__duration {
  margin: 0.5rem 0 0;
  font-size: 0.75rem;
  color: var(--vcard-muted);
}
</style>

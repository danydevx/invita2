<template>
  <section class="vcard-section vcard-services" v-if="services.length > 0">
    <h2 class="vcard-section__title">Servicios</h2>

    <div class="vcard-services__list">
      <article
        v-for="service in services"
        :key="service.id"
        class="vcard-service card border-0 shadow-sm"
      >
        <div class="card-body d-flex align-items-center gap-3">
          <div class="vcard-service__icon">
            <i class="bi bi-briefcase"></i>
          </div>
          <div class="vcard-service__content">
            <h3 class="vcard-service__title">{{ service.name }}</h3>
            <p v-if="service.description" class="vcard-service__text">{{ service.description }}</p>
            <p v-if="service.duration_minutes" class="vcard-service__duration">{{ service.duration_minutes }} min</p>
          </div>
          <div class="vcard-service__price" v-if="service.price">
            {{ formatPrice(service.price) }}
          </div>
        </div>
      </article>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  services: {
    type: Array,
    default: () => [],
  },
})

function formatPrice(price) {
  if (!price && price !== 0) return ''
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'USD' }).format(price)
}
</script>

<style scoped>
.vcard-services__list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.vcard-service {
  transition: transform 0.2s, box-shadow 0.2s;
}

.vcard-service:hover {
  transform: translateX(0.25rem);
  box-shadow: 0 0.375rem 1rem rgba(15, 23, 42, 0.12);
}

.vcard-service__icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.75rem;
  background: var(--vcard-primary);
  color: var(--vcard-surface);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.vcard-service__icon i {
  font-size: 1.25rem;
}

.vcard-service__content {
  flex: 1;
  min-width: 0;
}

.vcard-service__title {
  margin: 0 0 0.25rem;
  font-size: 0.9375rem;
  font-weight: 600;
  color: var(--vcard-text);
  line-height: 1.3;
}

.vcard-service__text {
  margin: 0;
  font-size: 0.8125rem;
  color: var(--vcard-muted);
  line-height: 1.4;
}

.vcard-service__duration {
  margin: 0.25rem 0 0;
  font-size: 0.75rem;
  color: var(--vcard-muted);
}

.vcard-service__price {
  font-size: 0.9375rem;
  font-weight: 700;
  color: var(--vcard-primary);
  flex-shrink: 0;
}
</style>

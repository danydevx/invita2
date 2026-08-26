<template>
  <section class="vcard-section vcard-services" v-if="allServices.length > 0">
    <h2 class="vcard-section__title">Servicios</h2>

    <div class="vcard-services__list">
      <article
        v-for="service in visibleServices"
        :key="service.title"
        class="vcard-service card border-0 shadow-sm"
      >
        <div class="card-body d-flex align-items-center gap-3">
          <div class="vcard-service__icon">
            <i :class="service.icon"></i>
          </div>
          <div class="vcard-service__content">
            <h3 class="vcard-service__title">{{ service.title }}</h3>
            <p v-if="service.description" class="vcard-service__text">{{ service.description }}</p>
            <p v-else-if="service.text" class="vcard-service__text">{{ service.text }}</p>
          </div>
          <i class="bi bi-chevron-right vcard-service__arrow"></i>
        </div>
      </article>
    </div>

    <div v-if="hasMore" class="vcard-services__load-more text-center mt-3">
      <button class="btn btn-outline-primary btn-sm" @click="loadMore">
        Ver más ({{ remaining }})
      </button>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  services: {
    type: Array,
    default: () => [],
  },
  initialLimit: {
    type: Number,
    default: 4,
  },
})

const visibleCount = ref(props.initialLimit)

const allServices = computed(() => {
  if (props.services && props.services.length > 0) {
    return props.services
  }
  return [
    { title: 'Consultoría', description: 'Asesoría profesional y acompañamiento.', icon: 'bi bi-chat-dots' },
    { title: 'Diseño', description: 'Soluciones visuales limpias y modernas.', icon: 'bi bi-palette2' },
    { title: 'Implementación', description: 'Ejecución cuidada de principio a fin.', icon: 'bi bi-gear' },
    { title: 'Soporte', description: 'Seguimiento y mantenimiento continuo.', icon: 'bi bi-life-preserver' },
    { title: 'Capacitación', description: 'Formación para tu equipo de trabajo.', icon: 'bi bi-graduation-cap' },
    { title: 'Auditoría', description: 'Evaluación y mejora de procesos.', icon: 'bi bi-clipboard-check' },
  ]
})

const visibleServices = computed(() => {
  return allServices.value.slice(0, visibleCount.value)
})

const hasMore = computed(() => {
  return visibleCount.value < allServices.value.length
})

const remaining = computed(() => {
  return allServices.value.length - visibleCount.value
})

function loadMore() {
  visibleCount.value += props.initialLimit
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

.vcard-service__arrow {
  color: var(--vcard-muted);
  font-size: 1rem;
  flex-shrink: 0;
}

.vcard-service:hover .vcard-service__arrow {
  color: var(--vcard-primary);
}
</style>

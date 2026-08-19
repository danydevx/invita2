<template>
  <section
    class="services-section py-5"
    :class="scheme ? 'section--' + scheme : ''"
    :style="sectionBgStyle"
  >
    <div class="container">
      <div v-if="showTitle" class="text-center mb-5">
        <h2 class="section-title" :style="scheme ? {} : { color: titleColor }">{{ title }}</h2>
        <p v-if="subtitle" class="section-subtitle lead" :style="scheme ? {} : { color: subtitleColor }">{{ subtitle }}</p>
      </div>

      <div v-if="variant === 'list'" class="services-list">
        <div
          v-for="(service, index) in services"
          :key="service.id"
          class="card service-list-item mb-3"
        >
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h3 class="service-name">{{ service.name }}</h3>
                <p v-if="service.description" class="service-description mb-0">{{ service.description }}</p>
              </div>
              <div class="text-end flex-shrink-0 ms-3">
                <div v-if="service.price" class="service-price">
                  <span class="price-amount">${{ service.price }}</span>
                  <span v-if="service.duration_minutes" class="price-duration d-block">{{ service.duration_minutes }} min</span>
                </div>
                <div class="service-list-actions mt-2">
                  <a
                    v-if="showBookingButton && service.allows_online_booking"
                    :href="`/b/${businessSlug}/book?service=${service.slug}`"
                    class="btn service-btn btn-scheme"
                  >
                    Reservar
                  </a>
                  <a
                    v-if="service.whatsapp_contact && showWhatsApp"
                    :href="`https://wa.me/${service.whatsapp_contact.replace(/[^0-9]/g, '')}`"
                    target="_blank"
                    class="btn service-btn btn-scheme"
                  >
                    <i class="bi bi-whatsapp"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="row g-4">
        <div
          v-for="(service, index) in services"
          :key="service.id"
          class="col-md-6 col-lg-4"
        >
          <div class="card h-100">
            <div class="card-body d-flex flex-column">
              <h3 class="service-name">{{ service.name }}</h3>
              <p v-if="service.description" class="service-description flex-grow-1">{{ service.description }}</p>
              <div v-if="service.price" class="service-price mt-auto mb-3">
                <span class="price-amount">${{ service.price }}</span>
                <span v-if="service.duration_minutes" class="price-duration">{{ service.duration_minutes }} min</span>
              </div>
              <div class="service-actions">
                <a
                  v-if="showBookingButton && service.allows_online_booking"
                  :href="`/b/${businessSlug}/book?service=${service.slug}`"
                  class="btn service-btn w-100 btn-scheme"
                >
                  Reservar
                </a>
                <a
                  v-if="service.whatsapp_contact && showWhatsApp"
                  :href="`https://wa.me/${service.whatsapp_contact.replace(/[^0-9]/g, '')}`"
                  target="_blank"
                  class="btn service-btn w-100 btn-scheme mt-2"
                >
                  <i class="bi bi-whatsapp me-1"></i>Consultar
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  services: {
    type: Array,
    default: () => []
  },
  businessSlug: {
    type: String,
    default: ''
  },
  title: {
    type: String,
    default: 'Nuestros Servicios'
  },
  subtitle: {
    type: String,
    default: ''
  },
  showTitle: {
    type: Boolean,
    default: true
  },
  showBookingButton: {
    type: Boolean,
    default: true
  },
  showWhatsApp: {
    type: Boolean,
    default: true
  },
  titleColor: {
    type: String,
    default: '#1a1a2e'
  },
  subtitleColor: {
    type: String,
    default: '#6B7280'
  },
  sectionBgColor: {
    type: String,
    default: '#ffffff'
  },
  variant: {
    type: String,
    default: 'cards'
  },
  scheme: {
    type: String,
    default: ''
  },
})

const sectionBgStyle = computed(() => {
  if (props.scheme) return {}
  return { backgroundColor: props.sectionBgColor }
})
</script>

<style scoped>
.services-section {
  background: transparent;
}

.section-title {
  font-family: var(--heading-font);
  font-size: var(--font-size-h2, 2.5rem);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-family: 'Roboto Condensed', sans-serif;
  max-width: 600px;
  margin: 0 auto;
}

.service-name {
  font-family: var(--heading-font);
  font-size: var(--font-size-h6, 1.25rem);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin: 0 0 0.5rem 0;
}

.service-price {
  white-space: nowrap;
}

.price-amount {
  font-family: var(--heading-font);
  font-size: var(--font-size-h5, 1.5rem);
  font-weight: 700;
  color: currentColor;
}

.price-duration {
  font-size: var(--font-size-xs, 0.8rem);
  opacity: 0.7;
}

.service-description {
  font-family: var(--body-font);
  font-size: var(--font-size-sm, 0.95rem);
  line-height: 1.6;
  margin-bottom: 0;
}

.service-actions {
  margin-top: auto;
}

.service-btn {
  font-family: 'Oswald', sans-serif;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 500;
  padding: 0.6rem 1.5rem;
  border-radius: 0;
  transition: all 0.3s ease;
}

@media (max-width: 768px) {
  .section-title {
    font-size: var(--font-size-h3, 2rem);
  }

  .service-list-item .d-flex {
    flex-direction: column;
    align-items: flex-start !important;
  }

  .service-list-item .text-end {
    text-align: left !important;
    margin-left: 0 !important;
    margin-top: 1rem;
  }
}
</style>

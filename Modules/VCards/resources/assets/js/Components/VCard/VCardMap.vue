<template>
  <section class="vcard-section vcard-map">
    <h2 class="vcard-section__title">Ubicación</h2>

    <div v-if="hasLocation" class="card border-0 shadow-sm overflow-hidden">
      <div class="vcard-map__content">
        <div class="vcard-map__iframe-container">
          <iframe
            width="100%"
            height="250"
            style="border:0;"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            :src="mapEmbedUrl"
          ></iframe>
        </div>

        <div class="vcard-map__info p-3">
          <div v-if="displayAddress" class="d-flex align-items-start gap-2 mb-3">
            <i class="bi bi-geo-alt text-primary mt-1"></i>
            <div>
              <p class="mb-0 fw-medium">{{ displayAddress }}</p>
              <p v-if="fullAddressLine" class="text-muted small mb-0">{{ fullAddressLine }}</p>
            </div>
          </div>
          <a
            :href="directionsUrl"
            target="_blank"
            class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2"
          >
            <i class="bi bi-cursor-fill"></i>
            <span>Cómo llegar</span>
          </a>
        </div>
      </div>
    </div>

    <div v-else class="card border-0 shadow-sm p-4 text-center">
      <p class="text-muted mb-0">
        <i class="bi bi-geo-alt me-2"></i>
        Sin ubicación disponible
      </p>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  lat: {
    type: [Number, String],
    default: null,
  },
  lng: {
    type: [Number, String],
    default: null,
  },
  address: {
    type: String,
    default: '',
  },
  city: {
    type: String,
    default: '',
  },
  state: {
    type: String,
    default: '',
  },
  country: {
    type: String,
    default: '',
  },
  zip: {
    type: String,
    default: '',
  },
})

const latNum = computed(() => props.lat != null && props.lat !== '' ? Number(props.lat) : 19.4326)
const lngNum = computed(() => props.lng != null && props.lng !== '' ? Number(props.lng) : -99.1332)
const hasLocation = computed(() => true)

const displayAddress = computed(() => props.address || 'Av. Reforma 505, Juárez, 06600 Ciudad de México, CDMX')
const displayCity = computed(() => props.city || 'Ciudad de México')
const displayState = computed(() => props.state || 'CDMX')
const displayCountry = computed(() => props.country || 'México')
const displayZip = computed(() => props.zip || '06600')

const fullAddressLine = computed(() => {
  const parts = [displayCity.value, displayState.value, displayZip.value, displayCountry.value].filter(Boolean)
  return parts.join(', ')
})

const mapEmbedUrl = computed(() => {
  if (!hasLocation.value) return ''
  return `https://www.google.com/maps/embed/v1/place?key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8&q=${latNum.value},${lngNum.value}`
})

const directionsUrl = computed(() => {
  const query = encodeURIComponent(fullAddress.value)
  return `https://www.google.com/maps/dir/?api=1&destination=${query}`
})

const fullAddress = computed(() => {
  const parts = [displayAddress.value, displayCity.value, displayState.value, displayZip.value, displayCountry.value].filter(Boolean)
  return parts.join(', ')
})
</script>

<style scoped>
.vcard-map__content {
  display: flex;
  flex-direction: column;
}

.vcard-map__iframe-container {
  line-height: 0;
}

.vcard-map__info {
  border-top: 1px solid #e5e7eb;
}

.vcard-map__info .btn-primary {
  background-color: var(--vcard-primary);
  border-color: var(--vcard-primary);
}

.vcard-map__info .btn-primary:hover {
  opacity: 0.9;
}
</style>

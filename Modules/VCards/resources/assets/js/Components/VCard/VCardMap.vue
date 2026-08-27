<template>
  <section class="vcard-section vcard-map">
    <h2 class="vcard-section__title">Ubicación</h2>

    <div v-if="hasLocation" class="card border-0 shadow-sm overflow-hidden">
      <div class="vcard-map__content">
        <div class="vcard-map__map-container">
          <l-map
            ref="map"
            :zoom="14"
            :center="[latNum, lngNum]"
            :options="{ scrollWheelZoom: false, zoomControl: true }"
            style="height: 250px; width: 100%;"
          >
            <l-tile-layer
              url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
              layer-type="base"
              name="OpenStreetMap"
              attribution="&copy; OpenStreetMap contributors"
            />
            <l-marker :lat-lng="[latNum, lngNum]" />
          </l-map>
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
import { LMap, LTileLayer, LMarker } from '@vue-leaflet/vue-leaflet'
import 'leaflet/dist/leaflet.css'

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
  location: {
    type: Object,
    default: null,
  },
})

const loc = computed(() => props.location)

const latNum = computed(() => {
  if (loc.value?.latitude != null) return Number(loc.value.latitude)
  if (props.lat != null && props.lat !== '') return Number(props.lat)
  return null
})

const lngNum = computed(() => {
  if (loc.value?.longitude != null) return Number(loc.value.longitude)
  if (props.lng != null && props.lng !== '') return Number(props.lng)
  return null
})

const hasLocation = computed(() => latNum.value != null && lngNum.value != null)

const displayAddress = computed(() => {
  if (loc.value?.address_line_1) return loc.value.address_line_1
  return props.address
})

const displayCity = computed(() => {
  if (loc.value?.city) return loc.value.city
  return props.city
})

const displayState = computed(() => {
  if (loc.value?.state) return loc.value.state
  return props.state
})

const displayCountry = computed(() => {
  if (loc.value?.country) return loc.value.country
  return props.country
})

const displayZip = computed(() => {
  if (loc.value?.postal_code) return loc.value.postal_code
  return props.zip
})

const fullAddressLine = computed(() => {
  const parts = [displayCity.value, displayState.value, displayZip.value, displayCountry.value].filter(Boolean)
  return parts.join(', ')
})

const directionsUrl = computed(() => {
  const query = encodeURIComponent(fullAddress.value)
  return `https://www.openstreetmap.org/directions?from=&to=${query}`
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

.vcard-map__map-container {
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

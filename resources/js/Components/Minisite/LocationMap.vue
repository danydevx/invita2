<template>
  <div v-if="latNum && lngNum" class="location-map">
    <l-map
      ref="map"
      :zoom="14"
      :center="[latNum, lngNum]"
      :options="{ scrollWheelZoom: false, zoomControl: true }"
      style="height: 200px; width: 100%; border-radius: 8px;"
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
  <div v-else-if="address" class="location-map-placeholder">
    <a
      :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`"
      target="_blank"
      class="location-map-link"
    >
      <i class="bi bi-geo-alt"></i>
      <span>Ver en Google Maps</span>
    </a>
  </div>
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
})

const latNum = computed(() => props.lat != null ? Number(props.lat) : null)
const lngNum = computed(() => props.lng != null ? Number(props.lng) : null)
</script>

<style lang="less">
.location-map {
  border-radius: 8px;
  overflow: hidden;
  margin-top: 12px;
}

.location-map-placeholder {
  margin-top: 12px;
}

.location-map-link {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  background: #e7f1ff;
  border: 1px solid #0d6efd;
  border-radius: 8px;
  color: #0d6efd;
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s;

  &:hover {
    background: #0d6efd;
    color: #fff;
  }

  i {
    font-size: 1.25rem;
  }
}
</style>

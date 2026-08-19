<template>
  <div>
    <label class="form-label">{{ label }}</label>
    <div class="map-picker-container mb-2">
      <l-map
        ref="map"
        :zoom="zoom"
        :center="center"
        :options="{ scrollWheelZoom: false }"
        @click="onMapClick"
        style="height: 500px; width: 100%; border-radius: 0.375rem;"
      >
        <l-tile-layer
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
          layer-type="base"
          name="OpenStreetMap"
          attribution="&copy; OpenStreetMap contributors"
        />
        <l-marker v-if="markerPosition" :lat-lng="markerPosition" :draggable="true" @dragend="onMarkerDrag" />
      </l-map>
    </div>
    <div class="row g-2">
      <div class="col-6">
        <input
          type="text"
          class="form-control form-control-sm"
          :value="lat"
          @input="$emit('update:lat', $event.target.value)"
          placeholder="Latitud"
          readonly
        />
      </div>
      <div class="col-6">
        <input
          type="text"
          class="form-control form-control-sm"
          :value="lng"
          @input="$emit('update:lng', $event.target.value)"
          placeholder="Longitud"
          readonly
        />
      </div>
    </div>
    <div class="text-muted small mt-1">
      <button type="button" class="btn btn-link btn-sm p-0" @click="searchAddress">
        <i class="bi bi-search me-1"></i>Buscar direccion
      </button>
      <span v-if="lat && lng" class="ms-2">
        | <button type="button" class="btn btn-link btn-sm p-0" @click="clearMarker">Limpiar</button>
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue'
import { LMap, LTileLayer, LMarker } from '@vue-leaflet/vue-leaflet'
import L from 'leaflet'

delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
})

const props = defineProps({
  label: {
    type: String,
    default: 'Ubicacion en el mapa',
  },
  lat: {
    type: [String, Number],
    default: null,
  },
  lng: {
    type: [String, Number],
    default: null,
  },
  initialCenter: {
    type: Array,
    default: () => [19.4326, -99.1332],
  },
})

const emit = defineEmits(['update:lat', 'update:lng'])

const map = ref(null)
const zoom = ref(15)
const center = computed(() => {
  if (props.lat && props.lng) {
    return [parseFloat(props.lat), parseFloat(props.lng)]
  }
  return props.initialCenter
})

const markerPosition = computed(() => {
  if (props.lat && props.lng) {
    return [parseFloat(props.lat), parseFloat(props.lng)]
  }
  return null
})

const onMapClick = (e) => {
  const { lat, lng } = e.latlng
  emit('update:lat', lat.toFixed(7))
  emit('update:lng', lng.toFixed(7))
}

const onMarkerDrag = async (e) => {
  const { lat, lng } = e.target.getLatLng()
  const latFixed = lat.toFixed(7)
  const lngFixed = lng.toFixed(7)
  emit('update:lat', latFixed)
  emit('update:lng', lngFixed)

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latFixed}&lon=${lngFixed}&addressdetails=1&zoom=18`
    )
    const data = await response.json()

    if (data && data.address) {
      const address = data.address
      const locationData = {
        lat: latFixed,
        lng: lngFixed,
        address: address.road || address.pedestrian || address.path || address.footway || address.street || '',
        number: address.house_number || '',
        colony: address.neighbourhood || address.suburb || address.village || address.hamlet || address.locality || '',
        postal_code: address.postcode || '',
        city: address.city || address.town || address.village || address.municipality || '',
        municipality: address.municipality || address.city || address.town || '',
        state: address.state || '',
        state_code: '',
        country: address.country || '',
        country_code: address.country_code || '',
      }

      emit('reverse-geocoded', locationData)
    }
  } catch (error) {
    console.error('Error reverse geocoding:', error)
  }
}

const clearMarker = () => {
  emit('update:lat', '')
  emit('update:lng', '')
}

const searchAddress = async () => {
  const addressParts = []

  const form = document.querySelector('#location-address-1')?.value
  const city = document.querySelector('#location-city')?.value
  const state = document.querySelector('#location-state')?.value
  const country = document.querySelector('#location-country')?.value

  if (form) addressParts.push(form)
  if (city) addressParts.push(city)
  if (state) addressParts.push(state)
  if (country) addressParts.push(country)

  if (addressParts.length === 0) {
    alert('Completa la direccion primero')
    return
  }

  const query = addressParts.join(', ')

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`
    )
    const data = await response.json()
    if (data && data.length > 0) {
      emit('update:lat', parseFloat(data[0].lat).toFixed(7))
      emit('update:lng', parseFloat(data[0].lon).toFixed(7))

      if (map.value?.leafletObject) {
        map.value.leafletObject.setView([data[0].lat, data[0].lon], 16)
      }
    } else {
      alert('No se encontro la direccion')
    }
  } catch (error) {
    console.error('Error searching address:', error)
    alert('Error al buscar la direccion')
  }
}

watch(() => props.lat, (newVal) => {
  if (newVal && map.value?.leafletObject) {
    map.value.leafletObject.setView([parseFloat(newVal), parseFloat(props.lng || 0)], 16)
  }
})
</script>

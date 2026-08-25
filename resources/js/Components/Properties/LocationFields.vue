<template>
  <div class="location-fields">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <FieldSelect
          id="property-country"
          label="País"
          v-model="form.country"
          :formError="errors.country"
        >
          <option value="">Selecciona un país</option>
          <option v-for="c in countries" :key="c.value" :value="c.value">{{ c.label }}</option>
        </FieldSelect>
      </div>

      <div class="col-12 col-md-6">
        <FieldSelect
          id="property-state"
          label="Estado"
          v-model="form.state"
          :formError="errors.state"
          :disabled="!form.country"
        >
          <option value="">{{ form.country ? 'Selecciona un estado' : 'Selecciona un país primero' }}</option>
          <option v-for="s in states" :key="s.value" :value="s.value">{{ s.label }}</option>
        </FieldSelect>
      </div>

      <div class="col-12 col-md-6">
        <FieldText
          id="property-city"
          label="Ciudad"
          v-model="form.city"
          placeholder="Nombre de la ciudad"
          :formError="errors.city"
        />
      </div>

      <div class="col-12 col-md-6">
        <FieldSelect
          id="property-municipality"
          label="Municipio"
          v-model="form.municipality"
          :formError="errors.municipality"
          :disabled="!form.state"
        >
          <option value="">{{ form.state ? 'Selecciona un municipio' : 'Selecciona un estado primero' }}</option>
          <option v-for="m in municipalities" :key="m.value" :value="m.value">{{ m.label }}</option>
        </FieldSelect>
      </div>

      <div class="col-12 col-md-4">
        <FieldText
          id="property-colony"
          label="Colonia"
          v-model="form.colony"
          placeholder="Nombre de la colonia"
        />
      </div>

      <div class="col-12 col-md-4">
        <FieldText
          id="property-postal-code"
          label="Código Postal"
          v-model="form.postal_code"
          placeholder="00000"
        />
      </div>

      <div class="col-12 col-md-4">
        <FieldText
          id="property-street"
          label="Calle"
          v-model="form.street"
          placeholder="Nombre de la calle"
        />
      </div>

      <div class="col-12 col-md-6">
        <FieldText
          id="property-exterior-number"
          label="Número Exterior"
          v-model="form.exterior_number"
          placeholder="S/N"
        />
      </div>

      <div class="col-12 col-md-6">
        <FieldText
          id="property-interior-number"
          label="Número Interior"
          v-model="form.interior_number"
          placeholder="(Opcional)"
        />
      </div>

      <div class="col-12">
        <FieldTextarea
          id="property-references"
          label="Referencias"
          v-model="form.references"
          placeholder="Indicaciones para llegar, puntos de referencia..."
          :rows="2"
        />
      </div>

      <div class="col-12 col-md-4">
        <FieldText
          id="property-latitude"
          label="Latitud"
          v-model="form.latitude"
          placeholder="20.659698"
          @blur="onLatLngChange"
        />
      </div>

      <div class="col-12 col-md-4">
        <FieldText
          id="property-longitude"
          label="Longitud"
          v-model="form.longitude"
          placeholder="-103.349609"
          @blur="onLatLngChange"
        />
      </div>

      <div class="col-12 col-md-4">
        <FieldSwitch
          id="property-show-exact-location"
          label="Precisión del mapa"
          v-model="form.show_exact_location"
        />
        <small class="text-muted d-block">
          {{ form.show_exact_location ? 'Se mostrará la ubicación exacta en el mapa público' : 'La ubicación se mostrará con aproximación' }}
        </small>
      </div>

      <div class="col-12">
        <MapPicker
          v-if="form.latitude && form.longitude"
          label="Ubicación en el mapa"
          :lat="form.latitude"
          :lng="form.longitude"
          @update:lat="form.latitude = $event"
          @update:lng="form.longitude = $event"
          @reverse-geocoded="onReverseGeocoded"
        />
        <div v-else class="alert alert-info">
          <i class="bi bi-info-circle me-2"></i>
          Ingresa una dirección o coordenadas para ver el mapa
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, watch } from 'vue'
import MapPicker from '@/Components/MapPicker.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import axios from 'axios'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({}),
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['update:modelValue'])

const countries = ref([
  { value: 'MX', label: 'México' },
])

const states = ref([])
const municipalities = ref([])
let geocodeTimeout = null

const form = reactive({
  country: props.modelValue?.country || 'MX',
  state: props.modelValue?.state || '',
  city: props.modelValue?.city || '',
  municipality: props.modelValue?.municipality || '',
  colony: props.modelValue?.colony || '',
  postal_code: props.modelValue?.postal_code || '',
  street: props.modelValue?.street || '',
  exterior_number: props.modelValue?.exterior_number || '',
  interior_number: props.modelValue?.interior_number || '',
  references: props.modelValue?.references || '',
  latitude: props.modelValue?.latitude || '',
  longitude: props.modelValue?.longitude || '',
  show_exact_location: props.modelValue?.show_exact_location ?? false,
})

function onReverseGeocoded(data) {
  if (data.address) {
    const address = data.address
    const numberMatch = address.match(/^([^\d]*)(\d+[A-Za-z]?)\s*(.*)$/)
    if (numberMatch && !data.number) {
      form.exterior_number = numberMatch[2]
      form.street = numberMatch[1].replace(/[,#\s]+$/, '').trim() + (numberMatch[3] ? ' ' + numberMatch[3] : '')
      form.street = form.street.trim()
    } else {
      form.street = address
      if (data.number) form.exterior_number = data.number
    }
  } else if (data.number) {
    form.exterior_number = data.number
  }
  if (data.colony) form.colony = data.colony
  if (data.postal_code) form.postal_code = data.postal_code
  if (data.city) form.city = data.city
  if (data.municipality && !form.municipality) form.municipality = data.municipality
  if (data.state) {
    const stateOption = states.value.find(s => s.label === data.state || s.value === data.state)
    if (stateOption) {
      form.state = stateOption.value
      loadMunicipalities(stateOption.value)
    }
  }
  if (data.country) {
    const countryOption = countries.value.find(c => c.label === data.country || c.value === data.country)
    if (countryOption) form.country = countryOption.value
  }
}

watch(
  () => form,
  (newVal) => {
    emit('update:modelValue', { ...newVal })
  },
  { deep: true, immediate: true }
)

watch(
  () => props.modelValue,
  (newVal) => {
    if (newVal && typeof newVal === 'object') {
      const hadState = !!form.state
      Object.keys(newVal).forEach(key => {
        if (form.hasOwnProperty(key) && newVal[key] !== undefined && newVal[key] !== null) {
          form[key] = newVal[key]
        }
      })
      if (newVal.state && !hadState) {
        loadMunicipalities(newVal.state)
      }
    }
  },
  { deep: true }
)

watch(
  () => form.state,
  async (newState, oldState) => {
    if (newState) {
      await loadMunicipalities(newState)
    } else {
      municipalities.value = []
      form.municipality = ''
    }
    if (oldState !== undefined) {
      triggerGeocode()
    }
  }
)

watch(
  () => form.city,
  () => { triggerGeocode() }
)

watch(
  () => form.municipality,
  () => { triggerGeocode() }
)

watch(
  () => form.colony,
  () => { triggerGeocode() }
)

watch(
  () => form.postal_code,
  () => { triggerGeocode() }
)

function triggerGeocode() {
  if (geocodeTimeout) clearTimeout(geocodeTimeout)
  geocodeTimeout = setTimeout(async () => {
    await geocodeFromAddress()
  }, 1000)
}

async function geocodeFromAddress() {
  const hasAddress = form.city || form.colony || form.postal_code || form.state
  if (!hasAddress && !form.municipality) {
    return
  }

  const addressParts = []
  if (form.colony) addressParts.push(form.colony)
  if (form.postal_code) addressParts.push(form.postal_code)
  if (form.city) addressParts.push(form.city)

  if (form.municipality) {
    const muni = municipalities.value.find(m => m.value === form.municipality)
    addressParts.push(muni?.label || form.municipality)
  }

  if (form.state) {
    const st = states.value.find(s => s.value === form.state)
    addressParts.push(st?.label || form.state)
  }

  addressParts.push('México')

  const query = addressParts.join(', ')

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=mx`
    )
    const data = await response.json()
    if (data && data.length > 0) {
      form.latitude = parseFloat(data[0].lat).toFixed(7)
      form.longitude = parseFloat(data[0].lon).toFixed(7)
    }
  } catch (error) {
    console.error('Error geocoding address:', error)
  }
}

async function loadStates() {
  try {
    const { data } = await axios.get('/api/v1/location-data/states')
    states.value = data.states.map(s => ({
      value: s.code,
      label: s.name,
    }))
    if (form.state && !municipalities.value.length) {
      await loadMunicipalities(form.state)
    }
  } catch (error) {
    console.error('Error loading states:', error)
    states.value = []
  }
}

async function loadMunicipalities(stateCode) {
  try {
    const { data } = await axios.get(`/api/v1/location-data/municipalities/${stateCode}`)
    municipalities.value = data.municipalities.map(m => ({
      value: m.code,
      label: m.name,
    }))
  } catch (error) {
    console.error('Error loading municipalities:', error)
    municipalities.value = []
  }
}

function onLatLngChange() {
  if (form.latitude && form.longitude) {
    form.latitude = parseFloat(form.latitude)
    form.longitude = parseFloat(form.longitude)
  }
}

loadStates()
</script>

<style scoped>
.location-fields {
  position: relative;
}
</style>

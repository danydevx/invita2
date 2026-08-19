<template>
  <div class="row g-3">
    <div class="col-12 col-md-6">
      <div class="form-group">
        <div class="form-floating">
          <select
            id="location-state"
            v-model="selectedState"
            class="form-control"
            :class="{ 'is-invalid': stateError }"
            @change="onStateChange"
            :disabled="loading"
          >
            <option value="">Seleccione un estado</option>
            <option v-for="state in states" :key="state.code" :value="state.code">
              {{ state.name }}
            </option>
          </select>
          <label for="location-state">Estado <strong v-if="required">*</strong></label>
          <div v-if="stateError" class="invalid-feedback">{{ stateError }}</div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-6">
      <div class="form-group">
        <div class="form-floating">
          <select
            id="location-municipality"
            v-model="selectedMunicipality"
            class="form-control"
            :class="{ 'is-invalid': municipalityError }"
            :disabled="!selectedState || loadingMunicipalities"
          >
            <option value="">
              {{ selectedState ? (loadingMunicipalities ? 'Cargando...' : 'Seleccione un municipio') : 'Primero seleccione un estado' }}
            </option>
            <option v-for="muni in municipalities" :key="muni.code" :value="muni.name">
              {{ muni.name }}
            </option>
          </select>
          <label for="location-municipality">Municipio <strong v-if="required">*</strong></label>
          <div v-if="municipalityError" class="invalid-feedback">{{ municipalityError }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({ state_code: '', municipality: '', lat: null, lng: null }),
  },
  stateError: {
    type: String,
    default: '',
  },
  municipalityError: {
    type: String,
    default: '',
  },
  required: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue', 'state-changed', 'municipality-changed', 'location-updated'])

const states = ref([])
const municipalities = ref([])
const selectedState = ref(props.modelValue.state_code || '')
const selectedMunicipality = ref(props.modelValue.municipality || '')
const loading = ref(false)
const loadingMunicipalities = ref(false)

const loadStates = async () => {
  loading.value = true
  try {
    const response = await fetch('/api/v1/location-data/states')
    const data = await response.json()
    states.value = data.states || []
  } catch (error) {
    console.error('Error loading states:', error)
    states.value = []
  } finally {
    loading.value = false
  }
}

const findStateByCoordinates = (lat, lng) => {
  if (!states.value.length) return null

  let closestState = null
  let minDistance = Infinity

  for (const state of states.value) {
    if (state.lat && state.lng) {
      const distance = Math.sqrt(
        Math.pow(parseFloat(state.lat) - parseFloat(lat), 2) +
        Math.pow(parseFloat(state.lng) - parseFloat(lng), 2)
      )
      if (distance < minDistance) {
        minDistance = distance
        closestState = state
      }
    }
  }

  return closestState
}

const findMunicipalityByCoordinates = async (stateCode, lat, lng) => {
  if (!stateCode || !municipalities.value.length) return null

  let closestMuni = null
  let minDistance = Infinity

  for (const muni of municipalities.value) {
    if (muni.lat && muni.lng) {
      const distance = Math.sqrt(
        Math.pow(parseFloat(muni.lat) - parseFloat(lat), 2) +
        Math.pow(parseFloat(muni.lng) - parseFloat(lng), 2)
      )
      if (distance < minDistance) {
        minDistance = distance
        closestMuni = muni
      }
    }
  }

  return closestMuni
}

const handleReverseGeocoded = async (locationData) => {
  if (locationData.lat && locationData.lng) {
    const lat = locationData.lat
    const lng = locationData.lng

    const closestState = findStateByCoordinates(lat, lng)
    if (closestState) {
      selectedState.value = closestState.code
      await loadMunicipalities(closestState.code)

      await new Promise(resolve => setTimeout(resolve, 100))

      const closestMuni = await findMunicipalityByCoordinates(closestState.code, lat, lng)
      if (closestMuni) {
        selectedMunicipality.value = closestMuni.name
      }

      const coords = { lat: closestState.lat, lng: closestState.lng }
      emit('update:modelValue', {
        state_code: closestState.code,
        municipality: selectedMunicipality.value,
        ...coords,
      })
      emit('state-changed', coords)
      emit('location-updated', locationData)
    }
  }
}

defineExpose({ handleReverseGeocoded })

const loadMunicipalities = async (stateCode) => {
  if (!stateCode) {
    municipalities.value = []
    return
  }

  loadingMunicipalities.value = true
  try {
    const response = await fetch(`/api/v1/location-data/municipalities/${stateCode}`)
    const data = await response.json()
    municipalities.value = data.municipalities || []
  } catch (error) {
    console.error('Error loading municipalities:', error)
    municipalities.value = []
  } finally {
    loadingMunicipalities.value = false
  }
}

const onStateChange = () => {
  selectedMunicipality.value = ''

  const state = states.value.find(s => s.code === selectedState.value)
  const coords = state ? { lat: state.lat, lng: state.lng } : { lat: null, lng: null }

  emit('update:modelValue', {
    state_code: selectedState.value,
    municipality: '',
    ...coords,
  })

  emit('state-changed', coords)

  if (selectedState.value) {
    loadMunicipalities(selectedState.value)
  } else {
    municipalities.value = []
  }
}

watch(selectedMunicipality, (val) => {
  const municipality = municipalities.value.find(m => m.name === val)
  const coords = municipality && municipality.lat ? { lat: municipality.lat, lng: municipality.lng } : null

  emit('update:modelValue', {
    state_code: selectedState.value,
    municipality: val,
    ...(coords || {}),
  })

  if (coords) {
    emit('municipality-changed', coords)
  }
})

watch(() => props.modelValue, (newVal) => {
  if (newVal.state_code && newVal.state_code !== selectedState.value) {
    selectedState.value = newVal.state_code
    loadMunicipalities(newVal.state_code)
  }
  if (newVal.municipality) {
    selectedMunicipality.value = newVal.municipality
  }
}, { immediate: true })

onMounted(() => {
  loadStates()
  if (props.modelValue.state_code) {
    loadMunicipalities(props.modelValue.state_code)
  }
})
</script>

<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  modelValueCountry: { type: String, default: '' },
  modelValueState:   { type: String, default: '' },
  required: { type: Boolean, default: false },
  errorCountry: { type: String, default: '' },
  errorState: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValueCountry','update:modelValueState'])

const countries = ref([])
const states = ref([])

const country = ref(props.modelValueCountry)
const state = ref(props.modelValueState)

onMounted(async () => {
  const { data } = await axios.get('/api/locations/countries')
  countries.value = data
  if (country.value) await loadStates(country.value)
})

watch(country, async (c) => {
  emit('update:modelValueCountry', c)
  state.value = ''
  emit('update:modelValueState', '')
  if (c) await loadStates(c)
})

watch(state, (s) => emit('update:modelValueState', s))

async function loadStates(code) {
  const { data } = await axios.get(`/api/locations/states/${code}`)
  states.value = data
}
</script>

<template>
  <div class="row g-3">
    <div class="col-12 col-md-6">
      <label class="form-label">País <span v-if="required" class="text-danger">*</span></label>
      <select class="form-select" v-model="country">
        <option value="">Selecciona un país</option>
        <option v-for="c in countries" :key="c.value" :value="c.value">{{ c.label }}</option>
      </select>
      <div v-if="errorCountry" class="invalid-feedback d-block">{{ errorCountry }}</div>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label">Estado / Provincia <span v-if="required" class="text-danger">*</span></label>
      <select class="form-select" v-model="state" :disabled="!country">
        <option value="">{{ country ? 'Selecciona un estado' : 'Selecciona un país primero' }}</option>
        <option v-for="s in states" :key="s.value" :value="s.value">{{ s.label }}</option>
      </select>
      <div v-if="errorState" class="invalid-feedback d-block">{{ errorState }}</div>
    </div>
  </div>
</template>

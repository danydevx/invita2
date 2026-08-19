<template>
  <div class="form-group" :class="classObject">
     <div class="form-floating">
   

    <input
      :id="id"
      type="search"
      class="form-control"
      v-model="search"
      :readonly="readonly"
      :placeholder="placeholder"
      autocomplete="off"
      :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
      @input="onInput"
      @blur="onBlur"
    />

         <label :for="id">
        {{ label }} <strong v-if="required">*</strong>
      </label>

    <!-- Dropdown resultados -->
    <div
      class="list-group position-absolute w-100 z-3"
      v-if="showDropdown && results.length && !readonly"
    >
      <button
        type="button"
        class="list-group-item list-group-item-action"
        v-for="user in results"
        :key="user.value"
        @mousedown.prevent="selectUser(user)"
      >
        {{ user.label }}
      </button>
    </div>

    <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback d-block">
      {{ formError || validationMessage }}
    </div>
  </div>
</div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  id: String,
  label: String,
  modelValue: [String, Number],
  placeholder: {
    type: String,
    default: 'Buscar por nombre o correo'
  },
  fetchUrl: {
    type: String,
    default: '/admin/students/search'
  },
  required: Boolean,
  formError: String,
  showValidation: Boolean,
  validateFunction: Function,
  classObject: String,
  readonly: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'blur'])

const search = ref('')
const results = ref([])
const showDropdown = ref(false)

const validationMessage = computed(() => {
  return props.validateFunction ? props.validateFunction() : ''
})

// Busca usuarios mientras se escribe
const onInput = async () => {
  if (props.readonly || search.value.length < 2) {
    results.value = []
    showDropdown.value = false
    return
  }

  try {
    const response = await axios.get(props.fetchUrl, {
      params: { search: search.value }
    })
    results.value = response.data
    showDropdown.value = true
  } catch (err) {
    console.error('Error buscando usuarios', err)
  }
}

// Selecciona un usuario del dropdown
const selectUser = (user) => {
  emit('update:modelValue', user.value)
  search.value = user.label
  showDropdown.value = false
}

// Cierra dropdown y emite blur
const onBlur = () => {
  setTimeout(() => {
    showDropdown.value = false
  }, 100) // Delay para permitir selección
  emit('blur')
}

// Sincroniza label del usuario cuando viene por props
watch(
  () => props.modelValue,
  async (newVal) => {
    if (!newVal || props.readonly) return

    try {
      const response = await axios.get(`${props.fetchUrl}/${newVal}`)
      if (response.data?.label) {
        search.value = response.data.label
      }
    } catch (e) {
      console.error('Error cargando usuario seleccionado', e)
    }
  },
  { immediate: true }
)
</script>

<template>
  <div class="form-group" :class="classObject">
    <div class="form-floating">
      <select
        :id="id"
        v-model="inputValue"
        class="form-control"
        :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
        :disabled="readonly"
        @blur="onBlur"
      >
        <option value="">Seleccione una opción</option>
        <option
          v-for="option in options"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
      <label :for="id">{{ label }} <strong v-if="required">*</strong></label>
      <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback">
        {{ formError || validationMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  id: String,
  label: String,
  modelValue: [String, Number],
  required: Boolean,
  placeholder: String,
  showValidation: Boolean,
  formError: String,
  validateFunction: Function,
  apiUrl: String,
  classObject: String,
  readonly: {
    type: Boolean,
    default: false,
  }
})

const emit = defineEmits(['update:modelValue', 'blur'])

const options = ref([])

const inputValue = computed({
  get: () => props.modelValue,
  set: (val) => {
    if (!props.readonly) emit('update:modelValue', val)
  }
})

const validationMessage = computed(() =>
  props.validateFunction ? props.validateFunction() : ''
)

const onBlur = () => emit('blur')

onMounted(async () => {
  try {
    const response = await axios.get(props.apiUrl)
    options.value = response.data
  } catch (error) {
    console.error('Error al cargar opciones desde API:', error)
  }
})
</script>

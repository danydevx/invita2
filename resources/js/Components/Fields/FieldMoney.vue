<template>
  <div class="form-group" :class="classObject">
    <div class="form-floating">
      <input
        :id="id"
        type="text"
        class="form-control"
        v-model="displayValue"
        :placeholder="placeholder"
        :readonly="readonly"
        :disabled="readonly"     
        :class="{ 'is-invalid': showValidation && formError }"
        @input="onInput"
        @blur="onBlur"
      />
      <label :for="id">
        {{ label }} <strong v-if="required">*</strong>
      </label>
      <div v-if="showValidation && formError" class="invalid-feedback">
        {{ formError }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  id: String,
  label: String,
  modelValue: [String, Number],
  placeholder: String,
  required: Boolean,
  formError: String,
  showValidation: Boolean,
  classObject: String,
  readonly: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue', 'blur'])
const displayValue = ref('')

// Reflejar modelValue cuando cambia externamente
watch(
  () => props.modelValue,
  (val) => {
    if (document.activeElement !== document.getElementById(props.id)) {
      displayValue.value = val !== null && val !== undefined && !isNaN(val) ? String(val) : ''
    }
  },
  { immediate: true }
)

const formatCurrency = (val) => {
  const number = parseFloat(val)
  if (isNaN(number)) return ''
  return number.toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

// Solo permitir input si no es readonly
const onInput = () => {
  if (props.readonly) return
  const raw = displayValue.value.replace(/[^0-9.,-]+/g, '').replace(',', '.')
  displayValue.value = raw
  const parsed = parseFloat(raw)
  emit('update:modelValue', isNaN(parsed) ? null : parsed)
}

const onBlur = () => {
  if (!isNaN(props.modelValue)) {
    displayValue.value = formatCurrency(props.modelValue)
  }
  emit('blur')
}
</script>

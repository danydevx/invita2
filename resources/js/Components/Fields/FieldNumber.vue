<template>
  <div class="form-group" :class="classObject">
    <div class="form-floating">
      <input
        :id="id"
        type="number"
        class="form-control"
        inputmode="numeric"
        step="1"
        :min="min"
        :max="max"
        :placeholder="placeholder"
        :readonly="readonly"
        :disabled="readonly"
        :value="displayValue"
        :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
        @keydown="onKeydown"
        @input="onInput"
        @blur="onBlur"
        autocomplete="off"
      />
      <label :for="id">
        {{ label }} <strong v-if="required">*</strong>
      </label>

      <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback">
        {{ formError || validationMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
  id: { type: String, required: true },
  label: { type: String, default: '' },
  modelValue: { type: [Number, String], default: '' },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  showValidation: { type: Boolean, default: false },
  formError: { type: String, default: '' },
  validateFunction: { type: Function, default: null },
  classObject: { type: [String, Object, Array], default: '' },
  readonly: { type: Boolean, default: false },
  min: { type: Number, default: Number.NEGATIVE_INFINITY },
  max: { type: Number, default: Number.POSITIVE_INFINITY },
  default: { type: Number, default: null },
})

const emit = defineEmits(['update:modelValue', 'blur'])

const toIntOrNull = (value) => {
  if (value === '' || value === null || typeof value === 'undefined') return null
  const n = parseInt(value, 10)
  return Number.isNaN(n) ? null : n
}

const clamp = (n) => {
  if (n < props.min) return props.min
  if (n > props.max) return props.max
  return n
}

const displayValue = computed(() => {
  return props.modelValue === '' || props.modelValue === null || typeof props.modelValue === 'undefined'
    ? ''
    : String(props.modelValue)
})

const validationMessage = computed(() => {
  if (props.validateFunction) return props.validateFunction()
  const v = toIntOrNull(props.modelValue)
  if (v === null && props.required) return 'Este campo es requerido'
  if (v !== null) {
    if (v < props.min) return `El valor debe ser mayor o igual a ${props.min}`
    if (v > props.max) return `El valor debe ser menor o igual a ${props.max}`
  }
  return ''
})

watch(
  () => props.modelValue,
  (val) => {
    if ((val === '' || val === null || typeof val === 'undefined') && props.default !== null) {
      const init = clamp(props.default)
      emit('update:modelValue', init)
    }
  },
  { immediate: true }
)

const onKeydown = (e) => {
  const allowedControl = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End']
  if (allowedControl.includes(e.key)) return

  if (e.key === '-') {
    if (props.min < 0) {
      const el = e.target
      const value = el.value
      const selectionStart = el.selectionStart
      const selectionEnd = el.selectionEnd
      const willInsertAtStart = selectionStart === 0 && selectionEnd === 0
      const alreadyHasMinus = value.startsWith('-')
      if (willInsertAtStart && !alreadyHasMinus) return
    }
    e.preventDefault()
    return
  }

  if (!/^\d$/.test(e.key)) {
    e.preventDefault()
  }
}

const onInput = (e) => {
  if (props.readonly) return

  let raw = e.target.value

  if (props.min < 0) {
    raw = raw.replace(/[^\d-]/g, '')
    raw = raw.replace(/(?!^)-/g, '')
  } else {
    raw = raw.replace(/\D+/g, '')
  }

  if (raw === '' || raw === '-') {
    emit('update:modelValue', '')
    return
  }

  const num = parseInt(raw, 10)
  if (Number.isNaN(num)) {
    emit('update:modelValue', '')
    return
  }

  const clamped = clamp(num)
  if (String(clamped) !== raw) {
    e.target.value = String(clamped)
  }
  emit('update:modelValue', clamped)
}

const onBlur = () => {
  if (props.readonly) {
    emit('blur')
    return
  }

  let v = toIntOrNull(props.modelValue)

  if (v === null) {
    if (props.default !== null) {
      v = clamp(props.default)
      emit('update:modelValue', v)
    } else {
      emit('update:modelValue', '')
    }
  } else {
    const clamped = clamp(v)
    if (clamped !== v) emit('update:modelValue', clamped)
  }

  emit('blur')
}
</script>

<template>
  <div class="form-group">
    <div class="form-floating">
      <input
        :id="id"
        type="number"
        class="form-control"
        inputmode="decimal"
        step="0.01"
        :min="min"
        :max="max"
        :placeholder="placeholder"
        :readonly="readonly"
        :disabled="readonly"
        :value="modelValue"
        :class="{ 'is-invalid': formError }"
        @input="onInput"
        @blur="onBlur"
        autocomplete="off"
      />
      <label :for="id">
        {{ label }} <strong v-if="required">*</strong>
      </label>
      <div v-if="formError" class="invalid-feedback">{{ formError }}</div>
    </div>
    <small v-if="helpText" class="form-text text-muted">{{ helpText }}</small>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  id: { type: String, required: true },
  label: { type: String, default: '' },
  modelValue: { type: [Number, String], default: '' },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  formError: { type: String, default: '' },
  helpText: { type: String, default: '' },
  readonly: { type: Boolean, default: false },
  min: { type: Number, default: 0 },
  max: { type: Number, default: 999999999.99 },
  currencyLabel: { type: String, default: '$' },
})

const emit = defineEmits(['update:modelValue', 'blur'])

const displayValue = computed(() => {
  return props.modelValue === '' || props.modelValue === null || typeof props.modelValue === 'undefined'
    ? ''
    : String(props.modelValue)
})

const onInput = (e) => {
  if (props.readonly) return

  let raw = e.target.value

  raw = raw.replace(/[^\d.]/g, '')

  const parts = raw.split('.')
  if (parts.length > 2) {
    raw = parts[0] + '.' + parts.slice(1).join('')
  }

  if (parts.length === 2 && parts[1].length > 2) {
    raw = parts[0] + '.' + parts[1].substring(0, 2)
  }

  if (raw === '' || raw === '.') {
    emit('update:modelValue', '')
    return
  }

  const num = parseFloat(raw)
  if (Number.isNaN(num)) {
    emit('update:modelValue', '')
    return
  }

  const clamped = Math.min(Math.max(num, props.min), props.max)
  emit('update:modelValue', clamped)
}

const onBlur = () => {
  if (props.readonly) {
    emit('blur')
    return
  }

  let v = parseFloat(props.modelValue)

  if (isNaN(v)) {
    emit('update:modelValue', '')
  } else {
    const clamped = Math.min(Math.max(v, props.min), props.max)
    emit('update:modelValue', clamped)
  }

  emit('blur')
}
</script>

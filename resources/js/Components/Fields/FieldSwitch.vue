<template>
  <div class="form-group" :class="classObject">
    <div class="form-check form-switch">
      <input
        class="form-check-input"
        type="checkbox"
        role="switch"
        :id="id"
        :checked="modelValue"
        :disabled="disabled || readonly"
        @change="onChange"
      >
      <label class="form-check-label" :for="id">
        {{ label }} <strong v-if="required">*</strong>
      </label>
    </div>

    <div v-if="(showValidation && validationMessage) || formError" class="text-danger mt-2">
      {{ formError || validationMessage }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  id: { type: String, required: true },
  label: { type: String, required: true },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  readonly: { type: Boolean, default: false },
  showValidation: { type: Boolean, default: false },
  formError: { type: String, default: '' },
  validateFunction: { type: Function, default: null },
  classObject: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const validationMessage = computed(() => {
  return props.validateFunction ? props.validateFunction() : ''
})

const onChange = (event) => {
  if (props.readonly) return
  emit('update:modelValue', event.target.checked)
}
</script>

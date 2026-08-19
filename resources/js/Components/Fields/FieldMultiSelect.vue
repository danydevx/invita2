<template>
  <div class="form-group" :class="classObject">
    <div class="form-floating">
      <select
        :id="id"
        v-model="inputValue"
        class="form-control"
        :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
        multiple
        :disabled="readonly"
        :style="{ height: height + 'px' }"
        @blur="onBlur"
      >
        <option value="">{{ placeholder || 'Seleccione opciones' }}</option>
        <option v-for="option in options" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>
      <label :for="id">{{ label }} <strong v-if="required">*</strong></label>
      <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback">
        {{ formError || validationMessage }}
      </div>
    </div>
    <small v-if="hint" class="text-muted d-block mt-1">{{ hint }}</small>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  id: { type: String, required: true },
  label: { type: String, required: true },
  modelValue: { type: Array, default: () => [] },
  placeholder: { type: String, default: '' },
  required: { type: Boolean, default: false },
  showValidation: { type: Boolean, default: false },
  formError: { type: String, default: '' },
  validateFunction: Function,
  classObject: [String, Object, Array],
  readonly: { type: Boolean, default: false },
  options: { type: Array, default: () => [] },
  height: { type: Number, default: 80 },
  hint: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue', 'blur'])

const inputValue = computed({
  get() {
    return props.modelValue ?? []
  },
  set(val) {
    if (props.readonly) return
    emit('update:modelValue', val ?? [])
  },
})

const validationMessage = computed(() => {
  return props.validateFunction ? props.validateFunction() : ''
})

const onBlur = () => {
  emit('blur')
}
</script>

<style scoped>
.form-floating > .form-control[multiple] {
  height: auto;
  padding-top: 1.625rem;
  padding-bottom: 0.625rem;
}

.form-floating > label {
  padding-top: 0.625rem;
}
</style>

<template>
  <div class="form-group" :class="classObject">
    <div class="form-floating">
      <input
        :id="id"
        type="email"
        v-model="inputValue"
        class="form-control"
        :placeholder="placeholder"
        :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
        @blur="onBlur"
        :readonly="readonly"
        :disabled="readonly"
      />
      <label :for="id">{{ label }} <strong v-if="required">*</strong></label>
      <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback">
        {{ formError || validationMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  id: String,
  label: String,
  modelValue: String,
  placeholder: String,
  required: Boolean,
  showValidation: Boolean,
  formError: String,
  validateFunction: Function,
  classObject: String,
  readonly: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue', 'blur'])

const inputValue = computed({
  get() {
    return props.modelValue
  },
  set(val) {
    emit('update:modelValue', val)
  },
})

const validationMessage = computed(() => {
  return props.validateFunction ? props.validateFunction() : ''
})

const onBlur = () => {
  emit('blur')
}
</script>

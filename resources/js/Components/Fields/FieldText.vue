<template>
  <div class="form-group" :class="classObject">
    <div class="form-floating">
      <input
        :id="id"
        type="text"
        v-model="inputValue"
        class="form-control"
        :readonly="readonly"
        autocomplete="off"
        :placeholder="placeholder"
        :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
        @blur="onBlur"
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
import { computed } from 'vue'

const props = defineProps({
  id: String,
  label: String,
  modelValue: { type: [String, Number], default: '' },
  placeholder: String,
  required: Boolean,
  showValidation: Boolean,
  formError: String,
  validateFunction: Function,
  classObject: [String, Object, Array],
  readonly: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue', 'blur'])

const inputValue = computed({
  get() {
    return props.modelValue ?? ''
  },
  set(val) {
    if (props.readonly) return
    const out = typeof val === 'number' ? String(val) : (val ?? '')
    emit('update:modelValue', out)
  },
})

const validationMessage = computed(() => {
  return props.validateFunction ? props.validateFunction() : ''
})

const onBlur = () => {
  emit('blur')
}
</script>

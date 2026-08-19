<template>
  <div class="form-group">
    <label v-if="label" :for="id" class="form-label">
      {{ label }} <strong v-if="required">*</strong>
    </label>
    <div class="d-flex align-items-center gap-2">
      <input
        :id="id + '-color'"
        type="color"
        :value="modelValue"
        class="form-control form-control-color"
        :style="{ width: '50px', height: '38px', padding: '4px' }"
        @input="onColorInput"
      />
      <input
        :id="id"
        type="text"
        :value="modelValue"
        class="form-control"
        :placeholder="placeholder || '#000000'"
        :class="{ 'is-invalid': formError }"
        @input="onTextInput"
        @blur="onBlur"
      />
    </div>
    <small v-if="hint" class="text-muted d-block mt-1">{{ hint }}</small>
    <div v-if="formError" class="invalid-feedback d-block">
      {{ formError }}
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  id: { type: String, required: true },
  label: String,
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: '#000000' },
  required: Boolean,
  formError: String,
  hint: String,
})

const emit = defineEmits(['update:modelValue', 'blur'])

const isValidHex = (value) => /^#([0-9A-Fa-f]{3}){1,2}$/.test(value)

const onColorInput = (e) => {
  emit('update:modelValue', e.target.value)
}

const onTextInput = (e) => {
  let value = e.target.value.trim()
  if (value && !value.startsWith('#')) {
    value = '#' + value
  }
  emit('update:modelValue', value)
}

const onBlur = () => {
  emit('blur')
}
</script>

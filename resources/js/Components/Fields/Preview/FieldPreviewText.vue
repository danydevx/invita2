<template>
  <div :class="`col-md-${config.fieldColumns}`" class="mb-3">

    <label class="form-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <input
      type="text"
      class="form-control"
      :id="id"
      :readonly="readonly"
      :disabled="readonly"
      :minlength="config.textMinLength || null"
      :maxlength="config.textMaxLength || null"
      :pattern="config.textPattern || null"
      :placeholder="config.textPlaceholder || ''"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
    />

    <small v-if="config.descripcion" class="text-muted d-block mt-1">
      {{ config.descripcion }}
    </small>

  </div>
</template>

<script setup>
defineProps({
  id: String,
  label: String,
  modelValue: String,
  required: Boolean,
  readonly: Boolean,
  config: {
    type: Object,
    default: () => ({
      fieldColumns: 12,
      descripcion: '',
      textMinLength: null,
      textMaxLength: null,
      textPattern: '',
      textPlaceholder: ''
    })
  }
})
defineEmits(['update:modelValue'])
</script>

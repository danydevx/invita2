<template>
  <div :class="`col-md-${config.fieldColumns}`" class="mb-3">

    <label class="form-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <textarea
      class="form-control"
      :id="id"
      :rows="config.textareaRows"
      :readonly="readonly"
      :disabled="readonly"
      :minlength="config.textareaMinLength || null"
      :maxlength="config.textareaMaxLength || null"
      :placeholder="config.textareaPlaceholder || ''"
      @input="$emit('update:modelValue', $event.target.value)"
    >{{ modelValue }}</textarea>

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
      textareaMinLength: null,
      textareaMaxLength: null,
      textareaRows: 4,
      textareaPlaceholder: ''
    })
  }
})
defineEmits(['update:modelValue'])
</script>

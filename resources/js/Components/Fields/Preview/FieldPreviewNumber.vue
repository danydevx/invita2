<template>
  <div :class="`col-md-${config.fieldColumns}`" class="mb-3">

    <label class="form-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <input
      type="number"
      class="form-control"
      :id="id"
      :readonly="readonly"
      :disabled="readonly"
      :min="config.numberMin ?? null"
      :max="config.numberMax ?? null"
      :step="config.numberStep ?? 1"
      :placeholder="config.numberPlaceholder || ''"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @wheel="handleWheel"
    />

    <small v-if="config.descripcion" class="text-muted d-block mt-1">
      {{ config.descripcion }}
    </small>

  </div>
</template>

<script setup>
const props = defineProps({
  id: String,
  label: String,
  modelValue: [String, Number],
  required: Boolean,
  readonly: Boolean,
  config: {
    type: Object,
    default: () => ({
      fieldColumns: 12,
      descripcion: '',

      numberMin: null,
      numberMax: null,
      numberStep: 1,
      numberPlaceholder: '',
      disableScroll: true
    })
  }
})

const emit = defineEmits(['update:modelValue'])

function handleWheel(e) {
  if (props.config.disableScroll) {
    e.target.blur()     // evita cambios en Safari/Chrome
    e.preventDefault()  // evita cambios en Firefox
  }
}
</script>

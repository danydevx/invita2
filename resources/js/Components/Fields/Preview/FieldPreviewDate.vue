<template>
  <div :class="`col-md-${config.fieldColumns}`" class="mb-3">

    <label class="form-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <input
      type="date"
      class="form-control"
      :id="id"
      :readonly="readonly"
      :disabled="readonly"
      :min="computedMinDate"
      :max="config.maxDate || null"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
    />

    <small v-if="config.descripcion" class="text-muted d-block mt-1">
      {{ config.descripcion }}
    </small>

  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
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
      minDate: '',
      maxDate: '',
      restrictToTodayAndForward: false
    })
  }
})

defineEmits(['update:modelValue'])

/**
 * Si restrictToTodayAndForward = true
 * la fecha mínima será HOY
 */
const computedMinDate = computed(() => {
  if (props.config.restrictToTodayAndForward) {
    return formatDate(new Date())
  }
  return props.config.minDate || null
})

/**
 * formatea fecha a YYYY-MM-DD
 */
function formatDate(d) {
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}
</script>

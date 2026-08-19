<template>
  <div :class="`col-md-${config.fieldColumns}`" class="mb-3">

    <label class="form-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <input
      type="time"
      class="form-control"
      :id="id"
      :readonly="readonly"
      :disabled="readonly"
      :min="computedMinTime"
      :max="computedMaxTime"
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

      minTime: '',
      maxTime: '',

      restrictToBusinessHours: false,
      businessHoursStart: '09:00',
      businessHoursEnd: '18:00'
    })
  }
})

defineEmits(['update:modelValue'])

/**
 * MinTime dinámico
 */
const computedMinTime = computed(() => {
  if (props.config.restrictToBusinessHours) {
    return props.config.businessHoursStart
  }
  return props.config.minTime || null
})

/**
 * MaxTime dinámico
 */
const computedMaxTime = computed(() => {
  if (props.config.restrictToBusinessHours) {
    return props.config.businessHoursEnd
  }
  return props.config.maxTime || null
})
</script>

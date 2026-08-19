<template>
  <div :class="`col-md-${config.fieldColumns}`" class="mb-3">

    <label class="form-label d-block">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <div class="row">
      <div
        v-for="opt in normalizedOptions"
        :key="opt.key"
        :class="`col-${12 / config.radioColumns}`"
      >
        <div class="form-check">
          <input
            class="form-check-input"
            type="radio"
            :name="id"
            :id="id + '_' + opt.key"
            :checked="isSelected(opt.value)"
            :disabled="readonly"
            @change="select(opt.value)"
          />

          <label class="form-check-label" :for="id + '_' + opt.key">
            {{ opt.label }}
          </label>
        </div>
      </div>
    </div>

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
  modelValue: null,
  required: Boolean,
  readonly: Boolean,
  options: Array,
  config: {
    type: Object,
    default: () => ({
      fieldColumns: 12,
      descripcion: '',
      radioColumns: 1
    })
  }
})

const emit = defineEmits(['update:modelValue'])

const normalizedOptions = computed(() => {
  const opts = Array.isArray(props.options) ? props.options : []

  return opts.map((opt, i) => ({
    label: opt?.label ?? opt?.value ?? `Opción ${i + 1}`,
    value: opt?.value ?? opt?.label ?? i,
    key: typeof opt?.value === 'object'
      ? JSON.stringify(opt.value)
      : String(opt?.value ?? i)
  }))
})

function isSelected(value) {
  if (typeof value === 'object') {
    return JSON.stringify(props.modelValue) === JSON.stringify(value)
  }
  return String(props.modelValue) === String(value)
}

function select(value) {
  if (props.readonly) return
  emit('update:modelValue', value)
}
</script>

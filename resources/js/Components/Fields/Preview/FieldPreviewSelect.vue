<template>
  <div :class="`col-md-${config.fieldColumns}`" class="mb-3">

    <label class="form-label d-block">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <select
      class="form-select"
      :id="id"
      :disabled="readonly"
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
    >
      <!-- Placeholder -->
      <option v-if="config.placeholder" disabled value="">
        {{ config.placeholder }}
      </option>

      <!-- Opciones -->
      <option
        v-for="opt in normalizedOptions"
        :key="opt.key"
        :value="opt.value"
      >
        {{ opt.label }}
      </option>
    </select>

    <!-- Botón clear -->
    <button
      v-if="config.clearable && modelValue"
      type="button"
      class="btn btn-sm btn-outline-secondary mt-2"
      @click="$emit('update:modelValue', '')"
    >
      Limpiar selección
    </button>

    <!-- Descripción -->
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
  modelValue: [String, Number, null],
  required: Boolean,
  readonly: Boolean,
  options: Array,
  config: {
    type: Object,
    default: () => ({
      fieldColumns: 12,
      descripcion: '',
      selectColumns: 1,
      placeholder: '',
      clearable: false
    })
  }
})

const normalizedOptions = computed(() => {
  const opts = Array.isArray(props.options) ? props.options : []

  return opts.map((opt, index) => ({
    label: opt?.label ?? opt?.value ?? `Opción ${index + 1}`,
    value: opt?.value ?? opt?.label ?? index,
    key: String(opt?.value ?? index)
  }))
})
</script>

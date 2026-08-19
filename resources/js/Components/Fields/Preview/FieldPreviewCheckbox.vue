<template>
  <div :class="`col-md-${config.fieldColumns}`">

    <!-- Label principal -->
    <label class="form-label d-block">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <!-- Opciones en columnas -->
    <div class="row">
      <div
        v-for="opt in normalizedOptions"
        :key="opt.key"
        :class="`col-md-${columnWidth}`"
        class="mb-2"
      >
        <div class="form-check">

          <input
            class="form-check-input"
            type="checkbox"
            :id="id + '_' + opt.key"
            :value="opt.value"
            :checked="modelValue.includes(opt.value)"
            :disabled="readonly"
            @change="toggle(opt.value)"
          />

          <label class="form-check-label" :for="id + '_' + opt.key">
            {{ opt.label }}
          </label>

        </div>
      </div>
    </div>

    <!-- Descripción -->
    <small v-if="config.descripcion" class="text-muted d-block mt-1">
      {{ config.descripcion }}
    </small>

    <!-- Validación min/max (solo visual) -->
    <div v-if="showMinError" class="text-danger small mt-1">
      Debes seleccionar al menos {{ config.selectItemsMin }} opción(es)
    </div>

    <div v-if="showMaxError" class="text-danger small mt-1">
      Solo puedes seleccionar hasta {{ config.selectItemsMax }} opción(es)
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  id: String,
  label: String,
  modelValue: {
    type: Array,
    default: () => []
  },
  required: Boolean,
  readonly: Boolean,
  options: {
    type: Array,
    default: () => []
  },
  config: {
    type: Object,
    default: () => ({
      fieldColumns: 12,
      descripcion: '',
      checkboxColumns: 1,
      selectItemsMin: null,
      selectItemsMax: null
    })
  }
})

const emit = defineEmits(['update:modelValue'])

/**
 * Normalizar opciones
 */
const normalizedOptions = computed(() =>
  props.options.map((opt, i) => ({
    label: opt.label ?? opt.value ?? `Opción ${i + 1}`,
    value: opt.value ?? opt.label ?? i,
    key:
      typeof opt.value === 'object'
        ? JSON.stringify(opt.value)
        : String(opt.value ?? i)
  }))
)

/**
 * Columnas para dividir los checkbox
 */
const columnWidth = computed(() => {
  const cols = Number(props.config.checkboxColumns || 1)
  const width = Math.floor(12 / cols)
  return width >= 1 ? width : 12
})

/**
 * Validación visual de min/max
 */
const showMinError = computed(() => {
  const min = props.config.selectItemsMin
  if (min == null) return false
  return props.modelValue.length < min
})

const showMaxError = computed(() => {
  const max = props.config.selectItemsMax
  if (max == null) return false
  return props.modelValue.length > max
})

/**
 * Toggle del checkbox
 */
function toggle(val) {
  if (props.readonly) return

  const arr = [...props.modelValue]
  const i = arr.indexOf(val)

  if (i === -1) {
    arr.push(val)
  } else {
    arr.splice(i, 1)
  }

  emit('update:modelValue', arr)
}
</script>

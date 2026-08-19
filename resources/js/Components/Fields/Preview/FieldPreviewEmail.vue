<template>
  <div :class="`col-md-${config.fieldColumns}`" class="mb-3">

    <label class="form-label">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
    </label>

    <input
      type="email"
      class="form-control"
      :id="id"
      :placeholder="config.emailPlaceholder"
      :readonly="readonly"
      :disabled="readonly"
      :value="modelValue"
      :maxlength="config.emailMaxLength || null"
      :minlength="config.emailMinLength || null"
      autocomplete="email"
      @input="handleInput"
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
      emailPlaceholder: '',
      emailPattern: '',
      emailRestrictDomain: false,
      emailDomain: '',
      emailAllowedDomains: [],
      emailBlockDisposable: false,
      emailMinLength: null,
      emailMaxLength: null
    })
  }
})

const emit = defineEmits(['update:modelValue'])

const disposableDomains = [
  'mailinator.com','trashmail.com','10minutemail.com',
  'tempmail.com','guerrillamail.com','yopmail.com'
]

function handleInput(e) {
  const val = e.target.value

  if (!val) {
    emit('update:modelValue', val)
    return
  }

  // Restricción de dominio único
  if (props.config.emailRestrictDomain) {
    const domain = val.split('@')[1] || ''
    if (domain !== props.config.emailDomain) return
  }

  // Lista blanca de dominios
  if (props.config.emailAllowedDomains.length > 0) {
    const domain = val.split('@')[1] || ''
    if (!props.config.emailAllowedDomains.includes(domain)) return
  }

  // Bloquear temporales
  if (props.config.emailBlockDisposable) {
    const domain = val.split('@')[1] || ''
    if (disposableDomains.includes(domain)) return
  }

  // Regex personalizado
  if (props.config.emailPattern) {
    const regex = new RegExp(props.config.emailPattern)
    if (!regex.test(val)) return
  }

  emit('update:modelValue', val)
}
</script>

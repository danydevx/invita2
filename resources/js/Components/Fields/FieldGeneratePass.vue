<template>
  <div class="field-generate-pass">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <div class="form-floating position-relative">
          <input
            :id="id"
            v-model="passwordValue"
            :type="showPassword ? 'text' : 'password'"
            class="form-control"
            :class="{ 'is-invalid': formError }"
            :placeholder="placeholder"
            autocomplete="new-password"
            :required="required"
          />
          <label :for="id">{{ label }} <strong v-if="required">*</strong></label>
          <button
            type="button"
            class="btn btn-link password-visibility position-absolute"
            :title="showPassword ? 'Ocultar password' : 'Mostrar password'"
            :aria-label="showPassword ? 'Ocultar password' : 'Mostrar password'"
            @click="showPassword = !showPassword"
          >
            <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
          </button>
          <div v-if="formError" class="invalid-feedback">{{ formError }}</div>
        </div>
      </div>

      <div class="col-12 col-md-6">
        <div class="form-floating">
          <input
            :id="confirmId"
            v-model="confirmationValue"
            :type="showPassword ? 'text' : 'password'"
            class="form-control"
            :class="{ 'is-invalid': confirmFormError }"
            placeholder="********"
            autocomplete="new-password"
            :required="required"
          />
          <label :for="confirmId">{{ confirmLabel }}</label>
          <div v-if="confirmFormError" class="invalid-feedback">{{ confirmFormError }}</div>
        </div>
      </div>
    </div>

    <div class="border rounded-3 p-3 mt-3">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-md-3">
          <label :for="`${id}-length`" class="form-label">Longitud</label>
          <input
            :id="`${id}-length`"
            v-model.number="length"
            type="number"
            class="form-control"
            :min="minLength"
            :max="maxLength"
            @change="normalizeLength"
          />
          <div class="form-text">Entre {{ minLength }} y {{ maxLength }} caracteres.</div>
        </div>

        <div class="col-12 col-md-6">
          <div class="row g-2">
            <div v-for="option in options" :key="option.key" class="col-6">
              <div class="form-check">
                <input
                  :id="`${id}-${option.key}`"
                  v-model="option.value"
                  type="checkbox"
                  class="form-check-input"
                />
                <label class="form-check-label" :for="`${id}-${option.key}`">{{ option.label }}</label>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-3">
          <div class="d-grid gap-2">
            <button type="button" class="btn btn-outline-primary" @click="generate">
              <i class="bi bi-shuffle me-1"></i>Generar password
            </button>
            <button
              type="button"
              class="btn btn-outline-secondary"
              :disabled="!passwordValue"
              @click="copy"
            >
              <i :class="copied ? 'bi bi-clipboard-check' : 'bi bi-clipboard'"></i>
              <span class="ms-1">{{ copied ? 'Copiado' : 'Copiar' }}</span>
            </button>
          </div>
        </div>
      </div>

      <div v-if="generatorError" class="text-danger small mt-2">{{ generatorError }}</div>
      <div v-else class="form-text mt-2">
        El password generado se coloca también en la confirmación.
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'
import { useAppToast } from '@/Composables/useToast'

const props = defineProps({
  id: { type: String, required: true },
  confirmId: { type: String, required: true },
  label: { type: String, default: 'Password' },
  confirmLabel: { type: String, default: 'Confirmar password' },
  modelValue: { type: String, default: '' },
  confirmation: { type: String, default: '' },
  placeholder: { type: String, default: '********' },
  required: { type: Boolean, default: false },
  formError: { type: String, default: '' },
  confirmFormError: { type: String, default: '' },
  minLength: { type: Number, default: 8 },
  maxLength: { type: Number, default: 30 },
  defaultLength: { type: Number, default: 12 },
})

const emit = defineEmits(['update:modelValue', 'update:confirmation', 'generated', 'copied'])
const toast = useAppToast()
const length = ref(Math.min(props.maxLength, Math.max(props.minLength, props.defaultLength)))
const showPassword = ref(false)
const copied = ref(false)
const generatorError = ref('')
const copyTimer = ref(null)
const options = ref([
  { key: 'uppercase', label: 'Uppercase', characters: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', value: true },
  { key: 'lowercase', label: 'Lowercase', characters: 'abcdefghijklmnopqrstuvwxyz', value: true },
  { key: 'numbers', label: 'Números', characters: '0123456789', value: true },
  { key: 'symbols', label: 'Símbolos', characters: '!@#$%^&*()-_=+[]{};:,.?', value: true },
])

const passwordValue = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const confirmationValue = computed({
  get: () => props.confirmation,
  set: (value) => emit('update:confirmation', value),
})

const normalizeLength = () => {
  const parsed = Number.parseInt(length.value, 10)
  length.value = Math.min(props.maxLength, Math.max(props.minLength, Number.isNaN(parsed) ? props.minLength : parsed))
}

const randomIndex = (maximum) => {
  if (!globalThis.crypto?.getRandomValues) {
    throw new Error('El navegador no permite generar passwords seguros.')
  }

  const limit = Math.floor(0x100000000 / maximum) * maximum
  const values = new Uint32Array(1)
  do {
    globalThis.crypto.getRandomValues(values)
  } while (values[0] >= limit)

  return values[0] % maximum
}

const pick = (characters) => characters[randomIndex(characters.length)]

const shuffle = (characters) => {
  for (let index = characters.length - 1; index > 0; index -= 1) {
    const random = randomIndex(index + 1)
    ;[characters[index], characters[random]] = [characters[random], characters[index]]
  }

  return characters
}

const generate = () => {
  generatorError.value = ''
  normalizeLength()
  const enabled = options.value.filter((option) => option.value)

  if (enabled.length === 0) {
    generatorError.value = 'Selecciona al menos un tipo de carácter.'
    return
  }

  const hasLetters = enabled.some((option) => ['uppercase', 'lowercase'].includes(option.key))
  const hasNumbers = enabled.some((option) => option.key === 'numbers')
  if (!hasLetters || !hasNumbers) {
    generatorError.value = 'Selecciona letras y números para cumplir la validación del password.'
    return
  }

  try {
    const characters = enabled.map((option) => pick(option.characters))
    const available = enabled.map((option) => option.characters).join('')

    while (characters.length < length.value) {
      characters.push(pick(available))
    }

    const password = shuffle(characters).join('')
    passwordValue.value = password
    confirmationValue.value = password
    showPassword.value = true
    emit('generated', password)
  } catch (error) {
    generatorError.value = error.message
  }
}

const markCopied = () => {
  copied.value = true
  emit('copied')
  clearTimeout(copyTimer.value)
  copyTimer.value = setTimeout(() => {
    copied.value = false
  }, 1500)
}

const fallbackCopy = () => {
  const textarea = document.createElement('textarea')
  textarea.value = passwordValue.value
  textarea.style.position = 'fixed'
  textarea.style.opacity = '0'
  document.body.appendChild(textarea)
  textarea.select()
  const successful = document.execCommand('copy')
  document.body.removeChild(textarea)

  if (!successful) {
    throw new Error('No se pudo copiar el password.')
  }
}

const copy = async () => {
  if (!passwordValue.value) return

  try {
    if (navigator.clipboard?.writeText) {
      await navigator.clipboard.writeText(passwordValue.value)
    } else {
      fallbackCopy()
    }
    markCopied()
  } catch (error) {
    toast.error('No se pudo copiar el password.')
  }
}

onBeforeUnmount(() => clearTimeout(copyTimer.value))
</script>

<style scoped>
.password-visibility {
  right: .5rem;
  top: 50%;
  transform: translateY(-50%);
  z-index: 5;
}

.form-floating .form-control {
  padding-right: 3rem;
}
</style>

<template>
  <div class="row">
    <!-- Password -->
    <div class="form-group mb-3 col-12 col-sm-6" :class="classObject">
      <div class="form-floating position-relative">
        <input
          :id="id"
          :type="showPassword ? 'text' : 'password'"
          v-model="passwordValue"
          class="form-control"
          :placeholder="placeholder"
          :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
          :style="{ paddingRight: '9.5rem' }"
          @blur="() => $emit('blur', 'password')"
        />
        <label :for="id">{{ label }} <strong v-if="required">*</strong></label>

        <!-- Botonera -->
        <div class="password-toolbar btn-group position-absolute end-0 top-50 translate-middle-y me-2">
          <!-- Generar -->
          <button
            type="button"
            class="btn btn-outline-secondary btn-sm"
            title="Generar contraseña segura"
            aria-label="Generar contraseña segura"
            @click="handleGenerate"
          >
            <i class="bi bi-shuffle"></i>
          </button>

          <!-- Copiar -->
          <button
            type="button"
            class="btn btn-outline-secondary btn-sm"
            :title="copied ? 'Copiado' : 'Copiar contraseña'"
            :aria-label="copied ? 'Copiado' : 'Copiar contraseña'"
            @click="handleCopy"
          >
            <i :class="copied ? 'bi bi-clipboard-check' : 'bi bi-clipboard'"></i>
          </button>

          <!-- Ver/Ocultar -->
          <button
            type="button"
            class="btn btn-outline-secondary btn-sm"
            :title="showPassword ? 'Ocultar contraseña' : 'Ver contraseña'"
            :aria-label="showPassword ? 'Ocultar contraseña' : 'Ver contraseña'"
            @click="toggleShow"
          >
            <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
          </button>
        </div>

        <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback">
          {{ formError || validationMessage }}
        </div>
      </div>
    </div>

    <!-- Confirmación -->
    <div class="form-group col-12 col-sm-6">
      <div class="form-floating">
        <input
          :id="confirmId"
          type="password"
          v-model="passwordConfirmationValue"
          class="form-control"
          :placeholder="'Confirme ' + label"
          :class="{ 'is-invalid': (showValidation && confirmValidationMessage) || confirmFormError }"
          @blur="() => $emit('blur', 'password_confirmation')"
        />
        <label :for="confirmId">Confirmar {{ label }}</label>
        <div v-if="(showValidation && confirmValidationMessage) || confirmFormError" class="invalid-feedback">
          {{ confirmFormError || confirmValidationMessage }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    id: String,
    confirmId: String,
    label: String,
    password: String,
    passwordConfirmation: String,
    placeholder: String,
    required: Boolean,
    showValidation: Boolean,
    formError: String,
    confirmFormError: String,
    validateFunction: Function,
    validateConfirmFunction: Function,
    classObject: String,
  },
  emits: ["update:password", "update:passwordConfirmation", "blur"],
  data() {
    return {
      showPassword: false,
      copied: false,
      copyTimer: null,
    }
  },
  computed: {
    passwordValue: {
      get() {
        return this.password
      },
      set(value) {
        this.$emit("update:password", value)
      }
    },
    passwordConfirmationValue: {
      get() {
        return this.passwordConfirmation
      },
      set(value) {
        this.$emit("update:passwordConfirmation", value)
      }
    },
    validationMessage() {
      return this.validateFunction ? this.validateFunction() : ""
    },
    confirmValidationMessage() {
      return this.validateConfirmFunction ? this.validateConfirmFunction() : ""
    }
  },
  methods: {
    toggleShow() {
      this.showPassword = !this.showPassword
    },
    handleGenerate() {
      const pwd = this.generatePassword(8) // 8 caracteres, letras, números y un símbolo
      this.passwordValue = pwd
      // Limpia la confirmación para obligar confirmarla de nuevo
      this.passwordConfirmationValue = pwd
      // Enfoca el campo de confirmación si existe
      this.$nextTick(() => {
        const el = document.getElementById(this.confirmId)
        if (el) el.focus()
      })
    },
    handleCopy() {
      const text = this.passwordValue || ""
      if (!text) return
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(this.onCopied, this.fallbackCopy)
      } else {
        this.fallbackCopy()
      }
    },
    onCopied() {
      this.copied = true
      clearTimeout(this.copyTimer)
      this.copyTimer = setTimeout(() => (this.copied = false), 1500)
    },
    fallbackCopy() {
      try {
        const temp = document.createElement('textarea')
        temp.value = this.passwordValue || ''
        document.body.appendChild(temp)
        temp.select()
        document.execCommand('copy')
        document.body.removeChild(temp)
        this.onCopied()
      } catch (e) {
        // Si falla, no hacemos nada especial
      }
    },
    generatePassword(length = 8) {
      // Debe incluir al menos: 1 minúscula, 1 mayúscula, 1 dígito, 1 símbolo
      const lowers = 'abcdefghijklmnopqrstuvwxyz'
      const uppers = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
      const digits = '0123456789'
      const symbols = '!@#$%^&*()-_=+[]{};:,.?'

      // Garantiza uno de cada tipo
      const pick = (set) => set[Math.floor(Math.random() * set.length)]
      let chars = [
        pick(lowers),
        pick(uppers),
        pick(digits),
        pick(symbols),
      ]

      // Rellena el resto con un conjunto combinado
      const all = lowers + uppers + digits + symbols
      while (chars.length < length) {
        chars.push(pick(all))
      }

      // Mezcla
      for (let i = chars.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1))
        ;[chars[i], chars[j]] = [chars[j], chars[i]]
      }

      return chars.join('')
    }
  },
  beforeUnmount() {
    clearTimeout(this.copyTimer)
  }
}
</script>

<style scoped>
/* Asegura que la botonera no tape el texto y se vea bien en alturas pequeñas */
.password-toolbar .btn {
  line-height: 1;
  padding-top: .35rem;
  padding-bottom: .35rem;
}
</style>

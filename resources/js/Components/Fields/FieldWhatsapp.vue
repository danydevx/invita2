<template>
  <div class="form-group">
    <label :for="id" class="form-label">{{ label }} <strong v-if="required">*</strong></label>
    <div class="input-group">
      <button
        class="btn btn-outline-secondary dropdown-toggle"
        type="button"
        data-bs-toggle="dropdown"
        :disabled="readonly"
      >
        <span v-if="selectedCountry">{{ getFlag(selectedCountry) }} {{ selectedCountry }}</span>
        <span v-else>+1</span>
      </button>
      <ul class="dropdown-menu dropdown-menu-start" style="max-height: 200px; overflow-y: auto;">
        <li v-for="country in countries" :key="country.code">
          <a
            class="dropdown-item d-flex align-items-center gap-2"
            href="#"
            @click.prevent="selectCountry(country.code)"
          >
            <span>{{ getFlag(country.code) }}</span>
            <span>{{ country.name }}</span>
            <span class="text-muted ms-auto">{{ country.code }}</span>
          </a>
        </li>
      </ul>
      <input
        :id="id"
        type="tel"
        v-model="phoneValue"
        class="form-control"
        :placeholder="placeholder"
        :readonly="readonly"
        :disabled="readonly"
        :class="{ 'is-invalid': formError }"
        @input="validateNumeric"
        @blur="onBlur"
      />
    </div>
    <div v-if="formError" class="invalid-feedback d-block">{{ formError }}</div>
  </div>
</template>

<script>
export default {
  props: {
    id: String,
    label: String,
    modelValue: String,
    countryValue: String,
    placeholder: String,
    required: Boolean,
    formError: String,
    readonly: { type: Boolean, default: false },
  },
  emits: ['update:modelValue', 'update:countryValue', 'blur'],
  data() {
    return {
      countries: [
        { code: '+1', name: 'Estados Unidos', flag: '🇺🇸' },
        { code: '+1', name: 'Canada', flag: '🇨🇦' },
        { code: '+52', name: 'Mexico', flag: '🇲🇽' },
        { code: '+53', name: 'Cuba', flag: '🇨🇺' },
        { code: '+54', name: 'Argentina', flag: '🇦🇷' },
        { code: '+55', name: 'Brasil', flag: '🇧🇷' },
        { code: '+56', name: 'Chile', flag: '🇨🇱' },
        { code: '+57', name: 'Colombia', flag: '🇨🇴' },
        { code: '+58', name: 'Venezuela', flag: '🇻🇪' },
        { code: '+591', name: 'Bolivia', flag: '🇧🇴' },
        { code: '+593', name: 'Ecuador', flag: '🇪🇨' },
        { code: '+595', name: 'Paraguay', flag: '🇵🇾' },
        { code: '+596', name: 'Guyana Francesa', flag: '🇬🇫' },
        { code: '+597', name: 'Surinam', flag: '🇸🇷' },
        { code: '+598', name: 'Uruguay', flag: '🇺🇾' },
        { code: '+501', name: 'Belice', flag: '🇧🇿' },
        { code: '+502', name: 'Guatemala', flag: '🇬🇹' },
        { code: '+503', name: 'El Salvador', flag: '🇸🇻' },
        { code: '+504', name: 'Honduras', flag: '🇭🇳' },
        { code: '+505', name: 'Nicaragua', flag: '🇳🇮' },
        { code: '+506', name: 'Costa Rica', flag: '🇨🇷' },
        { code: '+507', name: 'Panama', flag: '🇵🇦' },
        { code: '+509', name: 'Haiti', flag: '🇭🇹' },
        { code: '+596', name: 'Martinica', flag: '🇲🇶' },
        { code: '+590', name: 'Guadalupe', flag: '🇬🇵' },
        { code: '+1', name: 'Puerto Rico', flag: '🇵🇷' },
        { code: '+1', name: 'Republica Dominicana', flag: '🇩🇴' },
        { code: '+1', name: 'Jamaica', flag: '🇯🇲' },
        { code: '+1', name: 'Trinidad y Tobago', flag: '🇹🇹' },
        { code: '+1', name: 'Bahamas', flag: '🇧🇸' },
        { code: '+1', name: 'Barbados', flag: '🇧🇧' },
        { code: '+1', name: 'Guyana', flag: '🇬🇾' },
        { code: '+1', name: 'Surinam', flag: '🇸🇷' },
      ],
    }
  },
  computed: {
    selectedCountry: {
      get() {
        return this.countryValue || '+1'
      },
      set(val) {
        this.$emit('update:countryValue', val)
      },
    },
    phoneValue: {
      get() {
        return this.modelValue
      },
      set(val) {
        this.$emit('update:modelValue', val)
      },
    },
  },
  methods: {
    selectCountry(code) {
      this.selectedCountry = code
    },
    getFlag(code) {
      const country = this.countries.find((c) => c.code === code)
      return country ? country.flag : '🌐'
    },
    validateNumeric(event) {
      const value = event.target.value
      event.target.value = value.replace(/\D/g, '')
      this.phoneValue = event.target.value
    },
    onBlur() {
      this.$emit('blur')
    },
  },
}
</script>

<style scoped>
.dropdown-toggle::after {
  margin-left: 0.5rem;
}
</style>

<template>
  <div class="form-group" :class="classObject">
    <label :for="id" class="form-label">{{ label }} <strong v-if="required">*</strong></label>
    <div class="input-group">
      <span class="input-group-text">
        <span class="flag-icon">🇲🇽</span> +52
      </span>
      <input
        :id="id"
        type="tel"
        v-model="inputValue"
        class="form-control"
        :placeholder="placeholder || 'XX XXX XXXX'"
        :readonly="readonly"
        :disabled="readonly"
        :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
        @blur="onBlur"
        @input="validateNumeric"
      />
    </div>
    <small class="text-muted d-block mt-1">Numero de telefono sin el prefijo del pais</small>
    <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback d-block">
      {{ formError || validationMessage }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    id: String,
    label: String,
    modelValue: String,
    placeholder: String,
    required: Boolean,
    showValidation: Boolean,
    formError: String,
    validateFunction: Function,
    classObject: String,
    readonly: { type: Boolean, default: false },
  },
  emits: ["update:modelValue", "blur"],
  computed: {
    inputValue: {
      get() { return this.modelValue; },
      set(val) { this.$emit("update:modelValue", val); }
    },
    validationMessage() {
      return this.validateFunction ? this.validateFunction() : "";
    }
  },
  methods: {
    onBlur() { this.$emit("blur"); },
    validateNumeric(event) {
      const value = event.target.value
      event.target.value = value.replace(/\D/g, '')
      this.inputValue = event.target.value
    }
  }
};
</script>

<style scoped>
.flag-icon {
  font-size: 1.2em;
  line-height: 1;
}
</style>

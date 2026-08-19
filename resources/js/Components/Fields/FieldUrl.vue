<template>
  <div class="form-group" :class="classObject">
    <div class="form-floating">
      <input
        :id="id"
        type="url"
        v-model="inputValue"
        class="form-control"
        :placeholder="placeholder || 'https://...'"
        :readonly="readonly"
        :disabled="readonly"
        :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
        @blur="onBlur"
      />
      <label :for="id">{{ label }} <strong v-if="required">*</strong></label>
      <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback">
        {{ formError || validationMessage }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    id: { type: String, required: true },
    label: { type: String, required: true },
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    showValidation: { type: Boolean, default: false },
    formError: { type: String, default: '' },
    validateFunction: { type: Function, default: null },
    classObject: { type: String, default: '' },
    readonly: { type: Boolean, default: false }, // <-- añadido aquí
  },
  emits: ['update:modelValue', 'blur'],
  computed: {
    inputValue: {
      get() {
        return this.modelValue;
      },
      set(val) {
        if (!this.readonly) {
          this.$emit('update:modelValue', val);
        }
      }
    },
    validationMessage() {
      if (this.validateFunction) {
        return this.validateFunction();
      }
      // Validación por defecto de URL
      if (this.required && !this.inputValue.trim()) {
        return 'El campo es obligatorio';
      }
      if (this.inputValue && !/^https?:\/\/.+\..+/.test(this.inputValue)) {
        return 'Ingrese una URL válida (https://...)';
      }
      return '';
    }
  },
  methods: {
    onBlur() {
      this.$emit('blur');
    }
  }
};
</script>

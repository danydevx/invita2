<template>
  <div class="form-group" :class="classObject">
    <div class="form-floating">
      <input
        :id="id"
        type="date"
        v-model="inputValue"
        class="form-control"
        :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
        :min="minDate"
        @blur="onBlur"
        :readonly="readonly"
        :disabled="readonly"
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
    required: { type: Boolean, default: false },
    showValidation: { type: Boolean, default: false },
    formError: { type: String, default: '' },
    validateFunction: { type: Function, default: null },
    classObject: { type: String, default: '' },
    readonly: { type: Boolean, default: false },
    setDefaultToday: { type: Boolean, default: true },
  },
  emits: ['update:modelValue', 'blur'],
  computed: {
    minDate() {
      const today = new Date()
      return today.toISOString().split('T')[0]
    },
    inputValue: {
      get() {
        if (!this.modelValue && this.setDefaultToday) {
          const today = new Date()
          const dateStr = today.toISOString().split('T')[0]
          this.$emit('update:modelValue', dateStr)
          return dateStr
        }
        return this.modelValue
      },
      set(val) {
        this.$emit('update:modelValue', val)
      }
    },
    validationMessage() {
      return this.validateFunction ? this.validateFunction() : ''
    }
  },
  methods: {
    onBlur() {
      this.$emit('blur')
    }
  }
}
</script>

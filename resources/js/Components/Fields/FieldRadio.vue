<template>
  <div class="form-group" :class="classObject">
    <label class="form-label">{{ label }} <strong v-if="required">*</strong></label>

    <div class="d-flex flex-column gap-2">
      <div
        class="form-check"
        v-for="item in items"
        :key="item.value"
      >
        <input
          class="form-check-input"
          type="radio"
          :name="name"
          :id="`${id}_${item.value}`"
          :value="item.value"
          :checked="modelValue === item.value"
          :disabled="readonly"
          @change="onChange(item.value)"
        />

        <label
          class="form-check-label"
          :for="`${id}_${item.value}`"
        >
          {{ item.label }}
        </label>
      </div>
    </div>

    <div v-if="hasError" class="text-danger mt-2">
      {{ formError || validationMessage }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'FieldRadio',

  props: {
    id: { type: String, required: true },
    name: { type: String, default: '' },
    label: { type: String, required: true },
    items: { type: Array, required: true }, // [{label, value}]
    modelValue: { type: [String, Number], default: '' },
    required: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },

    showValidation: { type: Boolean, default: false },
    formError: { type: String, default: '' },
    validateFunction: { type: Function, default: null },

    classObject: { type: [String, Array, Object], default: '' },
  },

  emits: ['update:modelValue'],

  computed: {
    validationMessage() {
      if (this.validateFunction) return this.validateFunction();

      if (this.required && (!this.modelValue || this.modelValue === '')) {
        return 'Este campo es requerido';
      }
      return '';
    },
    hasError() {
      return (this.showValidation && !!this.validationMessage) || !!this.formError;
    }
  },

  methods: {
    onChange(value) {
      if (this.readonly) return;
      this.$emit('update:modelValue', value);
    }
  }
};
</script>

<style scoped>
.form-group {
  width: 100%;
}
</style>

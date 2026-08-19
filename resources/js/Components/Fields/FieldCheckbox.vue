<template>
  <div class="form-group" :class="classObject">
    <label class="form-label">
      {{ label }} <strong v-if="required">*</strong>
    </label>

    <div class="d-flex flex-column gap-2">
      <div
        class="form-check"
        v-for="item in items"
        :key="item.value"
      >
        <input
          class="form-check-input"
          type="checkbox"
          :id="`${id}_${item.value}`"
          :value="item.value"
          :checked="safeModel.includes(item.value)"
          :disabled="readonly"
          @change="onChange($event, item.value)"
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
  name: 'FieldCheckbox',

  props: {
    id: { type: String, required: true },
    label: { type: String, required: true },
    items: { type: Array, required: true }, // [{label, value}]
    modelValue: { type: [Array, String, Number, Object, null], default: () => [] },

    required: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },

    showValidation: { type: Boolean, default: false },
    formError: { type: String, default: '' },
    validateFunction: { type: Function, default: null },

    classObject: { type: [String, Array, Object], default: '' },
  },

  emits: ['update:modelValue'],

  computed: {

    /* ============================
       Siempre devuelve un array válido
    ============================ */
    safeModel() {
      if (Array.isArray(this.modelValue)) {
        return this.modelValue
      }
      return [] // fallback seguro
    },

    validationMessage() {
      if (this.validateFunction) return this.validateFunction();

      if (this.required && this.safeModel.length === 0) {
        return 'Seleccione al menos una opción';
      }
      return '';
    },

    hasError() {
      return (this.showValidation && !!this.validationMessage) || !!this.formError;
    }
  },

  methods: {
    onChange(event, value) {
      if (this.readonly) return;

      const newValue = [...this.safeModel];

      if (event.target.checked) {
        if (!newValue.includes(value)) newValue.push(value);
      } else {
        const i = newValue.indexOf(value);
        if (i !== -1) newValue.splice(i, 1);
      }

      this.$emit('update:modelValue', newValue);
    }
  }
};
</script>

<style scoped>
.form-group {
  width: 100%;
}
</style>

<template>
  <div class="form-group" :class="classObject">
    <label :for="id" class="form-label">
      {{ label }} <strong v-if="required">*</strong>
    </label>
    <div class="d-flex align-items-center gap-2">
      <input
        type="range"
        class="form-range"
        :id="id"
        :min="min"
        :max="max"
        :step="step"
        :disabled="disabled || readonly"  <!-- Aquí -->
        :value="modelValue"
        @input="onInput"
      >
      <output :for="id" :aria-hidden="true">{{ modelValue }}</output>
    </div>

    <div v-if="(showValidation && validationMessage) || formError" class="text-danger mt-2">
      {{ formError || validationMessage }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: { type: [Number, String], required: true }, // v-model
    id: { type: String, required: true },
    label: { type: String, required: true },
    min: { type: [Number, String], default: 0 },
    max: { type: [Number, String], default: 100 },
    step: { type: [Number, String], default: 1 },
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false }, // <-- Añadido
    showValidation: { type: Boolean, default: false },
    formError: { type: String, default: "" },
    validateFunction: { type: Function, default: null },
    classObject: { type: String, default: "" }
  },
  emits: ["update:modelValue"],
  computed: {
    validationMessage() {
      return this.validateFunction ? this.validateFunction() : "";
    }
  },
  methods: {
    onInput(event) {
      if (this.readonly) return; // Evita cambios si es readonly
      this.$emit("update:modelValue", event.target.value);
    }
  }
};
</script>

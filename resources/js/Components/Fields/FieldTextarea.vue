<template>
  <div class="form-group" :class="classObject">
    <div class="form-floating">
      <textarea
        :id="id"
        v-model="inputValue"
        class="form-control"
        :placeholder="placeholder"
        :class="{ 'is-invalid': hasError }"
        @blur="onBlur"
        :rows="rows"
        :readonly="readonly"
        :disabled="readonly"
        :style="computedStyle"
        :maxlength="maxLengthAttr"
        :aria-describedby="helpId"
      ></textarea>

      <label :for="id">
        {{ label }}
        <strong v-if="required">*</strong>
      </label>

      <!-- Texto ayuda + contador (solo si maxLength) -->
      <div v-if="showHelpOrCounter" :id="helpId" class="form-text d-flex justify-content-between">
        <span v-if="helpText">{{ helpText }}</span>
        <span v-if="maxLength" :class="{ 'text-danger': isOverLimit }">
          {{ currentLength }}/{{ maxLength }}
        </span>
      </div>

      <div v-if="hasError" class="invalid-feedback">
        {{ formError || validationMessage || maxLengthMessage }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    id: { type: String, required: true },
    label: { type: String, required: true },
    modelValue: { type: String, default: "" },
    placeholder: { type: String, default: "" },
    required: { type: Boolean, default: false },
    showValidation: { type: Boolean, default: false },
    formError: { type: String, default: "" },
    rows: { type: Number, default: 5 },
    height: { type: [Number, String], default: null },
    validateFunction: { type: Function, default: null },
    classObject: { type: String, default: "" },
    readonly: { type: Boolean, default: false },

    // ✅ NUEVAS (opcionales) — no rompen uso actual
    maxLength: { type: [Number, String], default: null },   // ej 255
    enforceMax: { type: Boolean, default: false },          // si true, corta el texto al max
    maxLengthError: { type: String, default: "" },          // mensaje custom
    showCounter: { type: Boolean, default: true },          // contador si maxLength
    helpText: { type: String, default: "" },                // texto ayuda opcional
  },
  emits: ["update:modelValue", "blur"],
  computed: {
    inputValue: {
      get() {
        return this.modelValue;
      },
      set(val) {
        let v = val ?? "";

        const max = this.maxLengthNumber;
        if (max && this.enforceMax && typeof v === "string" && v.length > max) {
          v = v.slice(0, max);
        }

        this.$emit("update:modelValue", v);
      }
    },

    validationMessage() {
      return this.validateFunction ? this.validateFunction() : "";
    },

    computedStyle() {
      const style = {};
      if (this.height) {
        style.height = typeof this.height === "number" ? `${this.height}px` : this.height;
      }
      return style;
    },

    maxLengthNumber() {
      const n = Number(this.maxLength);
      return Number.isFinite(n) && n > 0 ? Math.floor(n) : null;
    },

    maxLengthAttr() {
      // Si enforceMax, usamos maxlength nativo (evita escribir de más)
      return this.enforceMax && this.maxLengthNumber ? this.maxLengthNumber : null;
    },

    currentLength() {
      const v = this.modelValue ?? "";
      return typeof v === "string" ? v.length : String(v).length;
    },

    isOverLimit() {
      const max = this.maxLengthNumber;
      return !!(max && this.currentLength > max);
    },

    maxLengthMessage() {
      if (!this.maxLengthNumber || !this.isOverLimit) return "";
      return this.maxLengthError || `Máximo ${this.maxLengthNumber} caracteres.`;
    },

    helpId() {
      return `${this.id}-help`;
    },

    showHelpOrCounter() {
      return !!this.helpText || (this.showCounter && !!this.maxLengthNumber);
    },

    hasError() {
      // Mantiene la lógica anterior, pero agrega error por maxLength
      const base = (this.showValidation && !!this.validationMessage) || !!this.formError;
      const maxErr = this.showValidation && !!this.maxLengthMessage;
      return base || maxErr;
    }
  },
  methods: {
    onBlur() {
      this.$emit("blur");
    }
  }
};
</script>

<style>
textarea {
  height: auto;
}
</style>

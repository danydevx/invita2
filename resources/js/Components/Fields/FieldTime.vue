<template>
  <div class="form-group" :class="classObject">
    <div class="form-floating">
      <input
        :id="id"
        type="time"
        v-model="inputValue"
        class="form-control"
        :readonly="readonly"
        :disabled="readonly"
        step="60"
        :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
        @blur="onBlur"
        @change="onChange"
      />
      <label :for="id">
        {{ label }} <strong v-if="required">*</strong>
      </label>

      <div v-if="(showValidation && validationMessage) || formError" class="invalid-feedback">
        {{ formError || validationMessage }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "FieldTime",

  props: {
    id: { type: String, required: true },
    label: { type: String, required: true },
    modelValue: { type: String, default: "" },
    required: { type: Boolean, default: false },
    showValidation: { type: Boolean, default: false },
    formError: { type: String, default: "" },

    validateFunction: { type: Function, default: null },

    classObject: { type: [String, Object, Array], default: "" },

    readonly: { type: Boolean, default: false }
  },

  emits: ["update:modelValue", "blur", "change"],

  computed: {
    inputValue: {
      get() {
        return this.modelValue ?? "";
      },
      set(val) {
        if (this.readonly) return;
        const normalized = this.normalize(val);
        if (normalized !== this.modelValue) {
          this.$emit("update:modelValue", normalized);
        }
      }
    },

    validationMessage() {
      if (this.validateFunction) return this.validateFunction();

      if (this.required && (!this.modelValue || !this.modelValue.trim())) {
        return "Este campo es requerido";
      }

      if (this.modelValue) {
        const regex = /^([01]\d|2[0-3]):([0-5]\d)$/;
        if (!regex.test(this.modelValue)) return "Hora inválida";
      }

      return "";
    }
  },

  methods: {
    normalize(time) {
      if (!time) return "";
      const trimmed = time.toString().trim();
      const match = trimmed.match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?/);
      if (match) {
        const h = match[1].padStart(2, '0');
        const m = match[2];
        return `${h}:${m}`;
      }
      return trimmed;
    },

    onBlur() {
      this.$emit("blur");
    },

    onChange(e) {
      const normalized = this.normalize(e.target.value);
      this.$emit("change", normalized);
    }
  },

  watch: {
    modelValue(newVal) {
      const normalized = this.normalize(newVal);
      if (normalized !== newVal && normalized !== this.normalize(this.normalize(newVal))) {
      }
    }
  }
};
</script>

<style scoped>
.form-group {
  width: 100%;
}
</style>

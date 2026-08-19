<template>
  <div class="form-group" :class="classObject">
    <label class="form-label">{{ label }} <strong v-if="required">*</strong></label>
    <div class="d-flex flex-wrap gap-2">
      <template v-for="item in items" :key="item.id">
        <input
          class="btn-check"
          type="radio"
          :name="radioName"
          :id="idPrefix + item.id"
          :value="item.id"
          :checked="modelValue === item.id"
          :disabled="item.disabled || disabled || readonly"  <!-- agregado -->
          autocomplete="off"
          @change="onChange(item.id)"
        >
        <label
          class="btn btn-outline-primary"
          :for="idPrefix + item.id"
        >
          {{ item.label }}
        </label>
      </template>
    </div>

    <div v-if="(showValidation && validationMessage) || formError" class="text-danger mt-2">
      {{ formError || validationMessage }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: { type: [String, Number], required: true }, // v-model
    items: { type: Array, required: true }, // [{ id, label, disabled? }]
    label: { type: String, required: true },
    radioName: { type: String, default: "btn_radio" }, // grupo
    idPrefix: { type: String, default: "btnradio_" }, 
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false }, 
    readonly: { type: Boolean, default: false }, // <-- añadido aquí
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
    onChange(id) {
      if (this.readonly) return; // no permite cambio si es readonly
      this.$emit("update:modelValue", id);
    }
  }
};
</script>

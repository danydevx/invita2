<template>
  <div class="form-group" :class="classObject">
    <label class="form-label">{{ label }} <strong v-if="required">*</strong></label>
    <div class="d-flex flex-wrap gap-2">
      <template v-for="item in items" :key="item.id">
        <input
          class="btn-check"
          type="checkbox"
          :id="idPrefix + item.id"
          :value="item.id"
          :checked="modelValue.includes(item.id)"
          :disabled="item.disabled || disabled"
          autocomplete="off"
          @change="onChange(item.id, $event)"
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
    modelValue: { type: Array, required: true }, // v-model
    items: { type: Array, required: true }, // [{ id, label, disabled? }]
    label: { type: String, required: true },
    idPrefix: { type: String, default: "btncheck_" },
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
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
    onChange(id, event) {
      const newValue = [...this.modelValue];
      if (event.target.checked) {
        if (!newValue.includes(id)) {
          newValue.push(id);
        }
      } else {
        const index = newValue.indexOf(id);
        if (index !== -1) {
          newValue.splice(index, 1);
        }
      }
      this.$emit("update:modelValue", newValue);
    }
  }
};
</script>


<!-- 
<FieldCheckboxButtons
  v-model="form.options"
  label="Selecciona opciones"
  :items="[
    { id: 'opt1', label: 'Opción 1' },
    { id: 'opt2', label: 'Opción 2', disabled: true },
    { id: 'opt3', label: 'Opción 3' }
  ]"
  :required="true"
  :showValidation="touched.options"
  :formError="form.errors.options"
  :validateFunction="() => validateField('options')"
/>


-->
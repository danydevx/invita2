<template>
  <div class="form-group mb-3 col-12 col-sm-6" :class="classObject">
    <div class="form-floating">
      <input
        :id="id"
        type="password"
        v-model="passwordValue"
        class="form-control"
        :placeholder="placeholder"
        :class="{ 'is-invalid': (showValidation && validationMessage) || formError }"
        @blur="() => $emit('blur', 'password')"
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
    id: String,
    label: String,
    password: String,
    placeholder: String,
    required: Boolean,
    showValidation: Boolean,
    formError: String,
    validateFunction: Function,
    classObject: String,
  },
  emits: ["update:password", "blur"],
  computed: {
    passwordValue: {
      get() {
        return this.password;
      },
      set(value) {
        this.$emit("update:password", value);
      }
    },
    validationMessage() {
      return this.validateFunction ? this.validateFunction() : "";
    }
  }
};
</script>

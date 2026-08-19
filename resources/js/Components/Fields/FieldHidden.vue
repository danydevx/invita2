<template>
  <input
    type="hidden"
    :id="id"
    v-model="inputValue"
  />
</template>

<script>
export default {
  name: "FieldHidden",
  props: {
    id: { type: String, required: true },
    modelValue: { type: [String, Number], default: "" },
  },
  emits: ["update:modelValue"],
  computed: {
    inputValue: {
      get() {
        return this.modelValue;
      },
      set(val) {
        this.$emit("update:modelValue", val);
      }
    }
  }
};
</script>

<template>
  <input
    type="checkbox"
    :value="id"
    :checked="isSelected"
    @change="toggle"
    class="form-check-input"
  />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  id: {
    type: [Number, String],
    required: true,
  },
  selectedIds: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['update:selectedIds'])

const isSelected = computed(() => props.selectedIds.includes(props.id))

const toggle = () => {
  const newSelected = [...props.selectedIds]
  const index = newSelected.indexOf(props.id)
  if (index === -1) {
    newSelected.push(props.id)
  } else {
    newSelected.splice(index, 1)
  }
  emit('update:selectedIds', newSelected)
}
</script>

<style scoped>
.form-check-input {
  cursor: pointer;
}
</style>

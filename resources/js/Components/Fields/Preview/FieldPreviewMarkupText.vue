<template>
  <div class="markup-text-preview" v-html="content"></div>
</template>

<script setup>
defineProps({
  content: {
    type: String,
    default: ''
  }
})
</script>

<style scoped>
.markup-text-preview {
  padding: 0.75rem 1rem;
  background: #f8f9fa;
  border-left: 4px solid #0d6efd;
  border-radius: 4px;
  font-size: 0.95rem;
  line-height: 1.5;
}
</style>

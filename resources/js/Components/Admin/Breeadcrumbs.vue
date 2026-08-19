<template>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li
        v-for="(item, index) in items"
        :key="`${item.label}-${index}`"
        class="breadcrumb-item"
        :class="{ active: item.active }"
        :aria-current="item.active ? 'page' : null"
      >
        <Link
          v-if="item.href && !item.active"
          :href="item.href"
          class="text-decoration-none"
          prefetch="hover"
        >
          {{ item.label }}
        </Link>
        <span v-else>{{ item.label }}</span>
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  items: {
    type: Array,
    default: () => [],
  },
})
</script>

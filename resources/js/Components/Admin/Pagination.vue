<template>
  <nav v-if="show" :aria-label="ariaLabel">
    <ul class="pagination mb-0">
      <li
        v-for="(link, index) in links"
        :key="`${link.label}-${index}`"
        class="page-item"
        :class="{ active: link.active, disabled: !link.url }"
      >
        <Link v-if="link.url" class="page-link" :href="link.url" v-html="link.label" prefetch="hover" />
        <span v-else class="page-link" v-html="link.label"></span>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  links: {
    type: Array,
    default: () => [],
  },
  ariaLabel: {
    type: String,
    default: 'Paginacion',
  },
})

const show = computed(() => props.links.length > 1)
</script>

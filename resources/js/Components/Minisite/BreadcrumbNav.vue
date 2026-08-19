<template>
  <nav class="breadcrumb-nav" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <Link :href="`/m/${baseSlug}`">Inicio</Link>
      </li>
      <li v-for="(item, index) in items" :key="index" class="breadcrumb-item" :class="{ active: index === items.length - 1 }">
        <Link v-if="item.href" :href="item.href">{{ item.label }}</Link>
        <span v-else>{{ item.label }}</span>
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  baseSlug: {
    type: String,
    required: true,
  },
  items: {
    type: Array,
    default: () => [],
  },
})
</script>

<style lang="less">
.breadcrumb-nav {
  padding: 16px 0;
}

.breadcrumb {
  margin: 0;
  padding: 0;
  background: transparent;
  font-size: 0.875rem;

  &-item {
    a {
      color: #6c757d;
      text-decoration: none;

      &:hover {
        color: #0d6efd;
      }
    }

    &.active {
      color: #495057;
    }

    & + .breadcrumb-item::before {
      content: "/";
      color: #adb5bd;
      padding: 0 4px;
    }
  }
}
</style>

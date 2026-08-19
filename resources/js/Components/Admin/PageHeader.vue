<template>
  <div class="page-header">
    <div class="page-header__top">
      <div>
        <h1 class="page-header__title">{{ title }}</h1>
        <nav v-if="breadcrumbs && breadcrumbs.length" aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li v-for="(crumb, index) in breadcrumbs" :key="index" class="breadcrumb-item" :class="{ active: crumb.active }">
              <Link v-if="!crumb.active && crumb.href" :href="crumb.href">{{ crumb.label }}</Link>
              <span v-else>{{ crumb.label }}</span>
            </li>
          </ol>
        </nav>
      </div>

      <div v-if="backHref || $slots.actions" class="d-flex gap-2">
        <Link v-if="backHref" :href="backHref" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left me-1"></i>
          {{ backLabel }}
        </Link>
        <slot name="actions" />
      </div>
    </div>

    <div v-if="$slots.description" class="page-header__description">
      <slot name="description" />
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  title: {
    type: String,
    required: true,
  },
  breadcrumbs: {
    type: Array,
    default: () => [],
  },
  backHref: {
    type: String,
    default: '',
  },
  backLabel: {
    type: String,
    default: 'Regresar',
  },
})
</script>

<style scoped>
.page-header {
  margin-bottom: 1.5rem;
}

.page-header__top {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.page-header__title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.page-header__description {
  margin-top: 0.75rem;
  color: #6c757d;
}

.page-header__description p {
  margin-bottom: 0;
}

@media (max-width: 576px) {
  .page-header__top {
    flex-direction: column;
    align-items: stretch;
  }

  .page-header__top > div:last-child {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }
}
</style>

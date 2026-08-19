<template>
  <div class="sortable-cards">
    <div v-if="$slots.header" class="sortable-cards__header">
      <slot name="header"></slot>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Cargando...</span>
      </div>
    </div>

    <div v-else-if="!items || items.length === 0" class="sortable-cards__empty">
      <slot name="empty">
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            <strong>{{ emptyTitle }}</strong>
            <p v-if="emptyText" class="mb-0">{{ emptyText }}</p>
          </div>
        </div>
      </slot>
    </div>

    <div v-else ref="gridRef" class="row g-3">
      <div
        v-for="item in items"
        :key="item.id"
        :class="itemClass"
        class="sortable-cards__item-wrapper"
      >
        <div
          class="sortable-cards__item card border-0 shadow-sm h-100"
          :data-id="item.id"
        >
          <slot name="item" :item="item"></slot>
          <div v-if="reorderable" class="sortable-cards__drag-handle">
            <i class="bi bi-grip-vertical"></i>
          </div>
        </div>
      </div>
    </div>

    <div v-if="$slots.pagination && hasPagination">
      <slot name="pagination"></slot>
    </div>

    <Transition name="fade">
      <div v-if="showToast" class="sortable-cards__toast">
        <i class="bi bi-check-circle me-2"></i>
        {{ toastMessage }}
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import Sortable from 'sortablejs'

const props = defineProps({
  items: {
    type: Array,
    default: () => [],
  },
  itemClass: {
    type: String,
    default: 'col-6 col-md-4 col-lg-3',
  },
  reorderable: {
    type: Boolean,
    default: true,
  },
  reorderEndpoint: {
    type: String,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
  emptyTitle: {
    type: String,
    default: 'No hay elementos',
  },
  emptyText: {
    type: String,
    default: '',
  },
  toastMessage: {
    type: String,
    default: 'Orden actualizado',
  },
  hasPagination: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['reordered'])

const gridRef = ref(null)
let sortableInstance = null
const showToast = ref(false)
let toastTimeout = null

const displayToast = () => {
  showToast.value = true
  if (toastTimeout) {
    clearTimeout(toastTimeout)
  }
  toastTimeout = setTimeout(() => {
    showToast.value = false
  }, 3000)
}

const initSortable = () => {
  if (!gridRef.value || !props.reorderable) return

  if (sortableInstance) {
    sortableInstance.destroy()
  }

  sortableInstance = Sortable.create(gridRef.value, {
    handle: '.sortable-cards__drag-handle',
    animation: 150,
    ghostClass: 'sortable-cards__ghost',
    dragClass: 'sortable-cards__drag',
    onEnd: (evt) => {
      const ids = Array.from(gridRef.value.querySelectorAll('.sortable-cards__item'))
        .map(el => parseInt(el.dataset.id))

      if (ids.length > 0) {
        import('@inertiajs/vue3').then(({ router }) => {
          router.post(props.reorderEndpoint, {
            ids: ids,
          }, {
            preserveScroll: true,
            onSuccess: () => {
              displayToast()
              emit('reordered', ids)
            },
          })
        })
      }
    },
  })
}

const destroySortable = () => {
  if (sortableInstance) {
    sortableInstance.destroy()
    sortableInstance = null
  }
}

watch(() => props.items, () => {
  nextTick(() => initSortable())
}, { deep: true })

watch(() => props.reorderable, (val) => {
  if (val) {
    nextTick(() => initSortable())
  } else {
    destroySortable()
  }
})

onMounted(() => {
  nextTick(() => initSortable())
})

onUnmounted(() => {
  destroySortable()
  if (toastTimeout) {
    clearTimeout(toastTimeout)
  }
})

defineExpose({
  initSortable,
  destroySortable,
})
</script>

<style scoped>
.sortable-cards__item {
  position: relative;
}

.sortable-cards__item-wrapper {
  transition: transform 0.2s;
}

.sortable-cards__drag-handle {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(0, 0, 0, 0.5);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  cursor: grab;
  opacity: 0;
  transition: opacity 0.2s;
  z-index: 10;
}

.sortable-cards__item:hover .sortable-cards__drag-handle {
  opacity: 1;
}

.sortable-cards__drag-handle:active {
  cursor: grabbing;
}

.sortable-cards__ghost {
  opacity: 0.4;
}

.sortable-cards__drag {
  opacity: 0.8;
  background: white;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.sortable-cards__toast {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #198754;
  color: white;
  padding: 12px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 9999;
  display: flex;
  align-items: center;
  font-weight: 500;
}

.sortable-cards__toast i {
  font-size: 1.2rem;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>

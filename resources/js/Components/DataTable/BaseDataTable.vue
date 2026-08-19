<template>
  <div class="app-datatable">
    <div class="app-datatable__header">
      <div class="app-datatable__search-wrapper">
        <div class="app-datatable__search">
          <i class="bi bi-search search-icon"></i>
          <input
            type="text"
            v-model="search"
            :placeholder="searchPlaceholder"
            @input="onSearchInput"
          />
        </div>
      </div>
      <div class="app-datatable__controls">
        <slot name="header-actions"></slot>
      </div>
    </div>

    <div class="app-datatable__table-wrapper" ref="tableWrapperElement">
      <table class="app-datatable__table" :class="{ 'app-datatable__table--reorderable': reorderable }">
        <thead>
          <tr>
            <th v-if="reorderable" class="app-datatable__drag-column" style="width: 40px;"></th>
            <th
              v-for="column in columns"
              :key="column.key"
              :class="{
                sortable: column.sortable !== false && !reorderable,
                sorted: sortKey === column.key,
              }"
              @click="!reorderable && column.sortable !== false && toggleSort(column.key)"
              :style="column.width ? { width: column.width } : {}"
            >
              {{ column.label }}
              <i
                v-if="column.sortable !== false && !reorderable"
                class="bi sort-icon"
                :class="{
                  'bi-chevron-up': sortKey === column.key && sortDirection === 'asc',
                  'bi-chevron-down': sortKey === column.key && sortDirection === 'desc',
                  active: sortKey === column.key,
                }"
              ></i>
            </th>
          </tr>
        </thead>
        <tbody>
          <template v-if="loading">
            <tr>
              <td :colspan="totalColumns" class="app-datatable__loading">
                <div class="spinner"></div>
                <div>{{ loadingText }}</div>
              </td>
            </tr>
          </template>
          <template v-else-if="!localData.data || localData.data.length === 0">
            <tr>
              <td :colspan="totalColumns" class="app-datatable__empty">
                <div class="empty-icon">
                  <i class="bi bi-inbox"></i>
                </div>
                <div class="empty-title">{{ emptyTitle }}</div>
                <div class="empty-text">{{ emptyText }}</div>
              </td>
            </tr>
          </template>
          <template v-else>
            <tr
              v-for="(row, index) in localData.data"
              :key="getRowKey(row, index)"
              :data-id="row.id"
            >
              <td v-if="reorderable" class="app-datatable__drag-cell">
                <i class="bi bi-grip-vertical app-datatable__drag-handle"></i>
              </td>
              <td
                v-for="column in columns"
                :key="column.key"
                :class="column.class"
              >
                <slot
                  :name="`cell-${column.key}`"
                  :row="row"
                  :value="getNestedValue(row, column.key)"
                >
                  <template v-if="column.format">
                    {{ column.format(getNestedValue(row, column.key), row) }}
                  </template>
                  <template v-else>
                    {{ getNestedValue(row, column.key) }}
                  </template>
                </slot>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <div class="app-datatable__footer" v-if="!reorderable || localData.last_page > 1">
      <div class="app-datatable__info">
        Mostrando {{ from }} a {{ to }} de {{ total }} registros
      </div>

      <div class="app-datatable__per-page">
        <span>Mostrar:</span>
        <select v-model="perPage" @change="onPerPageChange">
          <option v-for="option in perPageOptions" :key="option" :value="option">
            {{ option }}
          </option>
        </select>
      </div>

      <div class="app-datatable__pagination" v-if="localData.last_page > 1">
        <button
          type="button"
          class="btn btn-sm btn-outline-secondary"
          :disabled="currentPage <= 1"
          @click="goToPage(currentPage - 1)"
        >
          <i class="bi bi-chevron-left"></i>
        </button>

        <button
          type="button"
          v-for="page in visiblePages"
          :key="page"
          class="btn btn-sm"
          :class="page === currentPage ? 'btn-primary' : 'btn-outline-secondary'"
          @click="goToPage(page)"
        >
          {{ page }}
        </button>

        <button
          type="button"
          class="btn btn-sm btn-outline-secondary"
          :disabled="currentPage >= localData.last_page"
          @click="goToPage(currentPage + 1)"
        >
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import Sortable from 'sortablejs'
import { toast } from 'vue3-toastify'

const props = defineProps({
  endpoint: {
    type: String,
    required: true,
  },
  columns: {
    type: Array,
    required: true,
  },
  initialData: {
    type: Object,
    default: null,
  },
  searchPlaceholder: {
    type: String,
    default: 'Buscar...',
  },
  loadingText: {
    type: String,
    default: 'Cargando datos...',
  },
  emptyTitle: {
    type: String,
    default: 'No hay registros disponibles',
  },
  emptyText: {
    type: String,
    default: 'Comienza agregando nuevos registros.',
  },
  perPageOptions: {
    type: Array,
    default: () => [10, 25, 50, 100],
  },
  initialPerPage: {
    type: Number,
    default: 10,
  },
  rowKey: {
    type: String,
    default: 'id',
  },
  reorderable: {
    type: Boolean,
    default: false,
  },
  reorderEndpoint: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['updated', 'reordered'])

const search = ref('')
const sortKey = ref('')
const sortDirection = ref('asc')
const currentPage = ref(1)
const perPage = ref(props.initialPerPage)
const loading = ref(false)
const savingOrder = ref(false)
const tableWrapperElement = ref(null)
let sortableInstance = null

const totalColumns = computed(() => {
  return props.columns.length + (props.reorderable ? 1 : 0)
})

const localData = ref(props.initialData || {
  data: [],
  current_page: 1,
  last_page: 1,
  per_page: props.initialPerPage,
  total: 0,
  from: null,
  to: null,
})

let debounceTimer = null

const from = computed(() => localData.value.from || 0)
const to = computed(() => localData.value.to || 0)
const total = computed(() => localData.value.total || 0)

const visiblePages = computed(() => {
  const current = currentPage.value
  const last = localData.value.last_page
  const pages = []

  let start = Math.max(1, current - 2)
  let end = Math.min(last, current + 2)

  if (end - start < 4) {
    if (start === 1) {
      end = Math.min(last, start + 4)
    } else {
      start = Math.max(1, end - 4)
    }
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  return pages
})

const getNestedValue = (obj, path) => {
  return path.split('.').reduce((acc, part) => acc && acc[part], obj)
}

const getRowKey = (row, index) => {
  return row[props.rowKey] ?? index
}

const toggleSort = (key) => {
  if (sortKey.value === key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortDirection.value = 'asc'
  }
  currentPage.value = 1
  fetchData()
}

const onSearchInput = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    currentPage.value = 1
    fetchData()
  }, 300)
}

const onPerPageChange = () => {
  currentPage.value = 1
  fetchData()
}

const goToPage = (page) => {
  if (page < 1 || page > localData.value.last_page) return
  currentPage.value = page
  fetchData()
}

const initSortable = () => {
  if (!props.reorderable || !props.reorderEndpoint) return

  const tbody = tableWrapperElement.value?.querySelector('tbody')
  if (!tbody) return

  if (sortableInstance) {
    sortableInstance.destroy()
    sortableInstance = null
  }

  sortableInstance = Sortable.create(tbody, {
    handle: '.app-datatable__drag-handle',
    animation: 200,
    ghostClass: 'app-datatable__row-ghost',
    chosenClass: 'app-datatable__row-chosen',
    dragClass: 'app-datatable__row-drag',
    onEnd: () => {
      onDragEnd(tbody)
    },
  })
}

const onDragEnd = (tbody) => {
  if (!props.reorderable || !props.reorderEndpoint) return

  const rows = tbody.querySelectorAll('tr[data-id]')
  const ids = Array.from(rows).map(row => Number(row.getAttribute('data-id'))).filter(Boolean)

  if (ids.length === 0) return

  savingOrder.value = true

  router.post(props.reorderEndpoint, {
    ids,
    page: currentPage.value,
    perPage: perPage.value,
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      emit('reordered', ids)
      toast.success('Orden actualizado correctamente.')
    },
    onError: (errors) => {
      console.error('Error saving order:', errors)
      toast.error('Error al actualizar el orden.')
    },
    onFinish: () => {
      savingOrder.value = false
    },
  })
}

const fetchData = async () => {
  loading.value = true

  const params = {
    page: currentPage.value,
    per_page: perPage.value,
  }

  if (search.value) {
    params.search = search.value
  }

  if (sortKey.value) {
    params.sort = sortKey.value
    params.direction = sortDirection.value
  }

  try {
    await new Promise((resolve) => {
      router.get(
        props.endpoint,
        params,
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: (page) => {
            localData.value = page.props.dataTable
            emit('updated', localData.value)
            resolve()
          },
          onError: () => {
            resolve()
          },
        }
      )
    })
  } finally {
    loading.value = false
  }

  await nextTick()
  if (props.reorderable) {
    initSortable()
  }
}

const reload = () => {
  fetchData()
}

watch(() => props.initialData, async (newVal) => {
  if (newVal) {
    localData.value = newVal
    await nextTick()
    if (props.reorderable) {
      initSortable()
    }
  }
}, { deep: true })

watch(() => props.reorderable, async (val) => {
  if (val) {
    await nextTick()
    initSortable()
  } else if (sortableInstance) {
    sortableInstance.destroy()
    sortableInstance = null
  }
})

onMounted(async () => {
  await nextTick()
  if (props.reorderable) {
    initSortable()
  }
})

onBeforeUnmount(() => {
  if (sortableInstance) {
    sortableInstance.destroy()
    sortableInstance = null
  }
})

defineExpose({
  reload,
  search,
  sortKey,
  sortDirection,
  currentPage,
  perPage,
  savingOrder,
})
</script>

<template>
  <MemberLayout>
    <Head :title="`Propiedades - ${listing?.name || ''}`" />

    <PageHeader
      title="Propiedades"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/listings'"
    >
      <template #actions>
        <button
          v-if="selectedIds.length > 0"
          class="btn btn-danger btn-sm"
          @click="deleteSelected"
          :disabled="deleting"
        >
          <i class="bi bi-trash me-1"></i>
          Eliminar ({{ selectedIds.length }})
        </button>
        <Link :href="`/member/listings/${listing?.id}/properties/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Propiedad
        </Link>
      </template>
    </PageHeader>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body py-3">
        <div class="row g-3 align-items-end">
          <div class="col-12 col-md-3">
            <label class="form-label small text-muted mb-1">Buscar</label>
            <div class="input-group">
              <input
                type="text"
                v-model="searchQuery"
                class="form-control"
                placeholder="Titulo, descripcion..."
                @keyup.enter="filterProperties"
              />
              <button class="btn btn-outline-secondary" @click="filterProperties" type="button">
                <i class="bi bi-search"></i>
              </button>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label small text-muted mb-1">Tipo</label>
            <select v-model="filters.property_type_id" class="form-select form-select-sm" @change="filterProperties">
              <option :value="null">Todos</option>
              <option v-for="type in propertyTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label small text-muted mb-1">Operacion</label>
            <select v-model="filters.operation_type" class="form-select form-select-sm" @change="filterProperties">
              <option :value="null">Todas</option>
              <option v-for="op in operationOptions" :key="op" :value="op">{{ getOperationLabel(op) }}</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label small text-muted mb-1">Estado</label>
            <select v-model="filters.status" class="form-select form-select-sm" @change="filterProperties">
              <option :value="null">Todos</option>
              <option v-for="st in statusOptions" :key="st" :value="st">{{ getStatusLabel(st) }}</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label small text-muted mb-1">Ubicacion</label>
            <select v-model="filters.state" class="form-select form-select-sm" @change="onStateChange">
              <option :value="null">Estado</option>
              <option v-for="state in availableStates" :key="state" :value="state">{{ state }}</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <select v-model="filters.city" class="form-select form-select-sm" @change="filterProperties" :disabled="!filters.state">
              <option :value="null">Ciudad</option>
              <option v-for="city in availableCities" :key="city" :value="city">{{ city }}</option>
            </select>
          </div>
          <div class="col-6 col-md-2">
            <button v-if="hasActiveFilters" type="button" class="btn btn-outline-secondary btn-sm w-100" @click="clearFilters">
              <i class="bi bi-x-lg me-1"></i>Limpiar
            </button>
          </div>
        </div>

        <div v-if="hasActiveFilters" class="mt-3 d-flex flex-wrap gap-2">
          <span class="badge bg-light text-dark border" v-if="filters.property_type_id">
            Tipo: {{ getPropertyTypeName(filters.property_type_id) }}
            <button class="btn-close btn-close-sm ms-1" @click="filters.property_type_id = null; filterProperties()"></button>
          </span>
          <span class="badge bg-light text-dark border" v-if="filters.operation_type">
            {{ getOperationLabel(filters.operation_type) }}
            <button class="btn-close btn-close-sm ms-1" @click="filters.operation_type = null; filterProperties()"></button>
          </span>
          <span class="badge bg-light text-dark border" v-if="filters.status">
            {{ getStatusLabel(filters.status) }}
            <button class="btn-close btn-close-sm ms-1" @click="filters.status = null; filterProperties()"></button>
          </span>
          <span class="badge bg-light text-dark border" v-if="filters.state">
            {{ filters.state }}
            <button class="btn-close btn-close-sm ms-1" @click="filters.state = null; filters.city = null; filterProperties()"></button>
          </span>
          <span class="badge bg-light text-dark border" v-if="searchQuery">
            "{{ searchQuery }}"
            <button class="btn-close btn-close-sm ms-1" @click="searchQuery = ''; filterProperties()"></button>
          </span>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="text-muted small">
        {{ dataTable?.total || 0 }} propiedades
      </div>
      <BulkSelect
        v-model:selectedIds="selectedIds"
        :current-page-ids="currentPageIds"
        :delete-endpoint="`/member/listings/${listing?.id}/properties/bulk-delete`"
        item-name="propiedades"
        @deleted="onBulkDeleted"
      />
    </div>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/properties`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      :reorderable="true"
      :reorder-endpoint="`/member/listings/${listing?.id}/properties/reorder`"
      search-placeholder="Buscar propiedades..."
      empty-title="No hay propiedades"
      empty-text="Comienza creando tu primera propiedad."
      @updated="onDataTableUpdated"
    >
      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>

      <template #cell-image="{ row }">
        <img
          v-if="row.main_image_url"
          :src="row.main_image_url"
          class="rounded"
          style="width: 48px; height: 48px; object-fit: cover;"
        />
        <div v-else class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
          <i class="bi bi-house text-muted"></i>
        </div>
      </template>

      <template #cell-title="{ row }">
        <strong>{{ row.title }}</strong>
        <p v-if="row.description" class="text-muted small mb-0">{{ row.description.substring(0, 60) }}...</p>
      </template>

      <template #cell-property_type="{ row }">
        <span class="badge bg-light text-dark">{{ row.property_type_name || '-' }}</span>
      </template>

      <template #cell-is_featured="{ row }">
        <span v-if="row.is_featured" class="badge bg-warning text-dark">
          <i class="bi bi-star-fill"></i>
        </span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-created_at="{ row }">
        <small class="text-muted">{{ formatDate(row.created_at) }}</small>
      </template>

      <template #cell-location="{ row }">
        <small>{{ row.location || '-' }}</small>
      </template>

      <template #cell-operation_type="{ row }">
        <span class="badge bg-info">{{ row.operation_label }}</span>
      </template>

      <template #cell-price="{ row }">
        <span class="fw-semibold">{{ row.formatted_price }}</span>
      </template>

      <template #cell-status="{ row }">
        <span :class="getStatusBadgeClass(row.status)">
          {{ row.status_label }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link
            :href="`/member/listings/${listing?.id}/properties/${row.id}/edit`"
            class="btn btn-sm btn-outline-primary"
          >
            <i class="bi bi-pencil"></i>
          </Link>
          <button
            class="btn btn-sm btn-outline-secondary"
            @click="duplicateProperty(row)"
            :disabled="duplicating === row.id"
            title="Duplicar"
          >
            <i class="bi bi-copy"></i>
          </button>
          <div class="btn-group">
            <button
              class="btn btn-sm btn-outline-secondary dropdown-toggle"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="bi bi-chevron-down"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <button
                  v-if="row.status !== 'published'"
                  class="dropdown-item"
                  @click="changeStatus(row, 'published')"
                >
                  <i class="bi bi-eye me-2"></i>Publicar
                </button>
              </li>
              <li>
                <button
                  v-if="row.status !== 'paused'"
                  class="dropdown-item"
                  @click="changeStatus(row, 'paused')"
                >
                  <i class="bi bi-pause me-2"></i>Pausar
                </button>
              </li>
              <li>
                <button
                  v-if="row.status !== 'archived'"
                  class="dropdown-item"
                  @click="changeStatus(row, 'archived')"
                >
                  <i class="bi bi-archive me-2"></i>Archivar
                </button>
              </li>
              <li><hr class="dropdown-divider" /></li>
              <li>
                <button class="dropdown-item text-danger" @click="deleteProperty(row)">
                  <i class="bi bi-trash me-2"></i>Eliminar
                </button>
              </li>
            </ul>
          </div>
        </div>
      </template>
    </BaseDataTable>
  </MemberLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import { BulkSelect, BulkSelectRowCheckbox } from '@/Components/BulkSelect'

const props = defineProps({
  propertyTypes: Array,
  statusOptions: Array,
  operationOptions: Array,
  filters: Object,
  availableStates: Array,
  availableCities: Array,
})

const page = usePage()
const listing = computed(() => page.props.listing)
const dataTable = computed(() => page.props.dataTable)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/listings\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Inicio', href: `/member/listings/${biz.id}/modules` },
        { label: 'Propiedades', active: true },
      ]
    }
  }
  return [
    { label: 'Inicio', href: '/member/dashboard' },
    { label: 'Propiedades', active: true },
  ]
})

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'image', label: '', sortable: false, width: '60px' },
  { key: 'title', label: 'Título', sortable: true },
  { key: 'property_type', label: 'Tipo', sortable: true },
  { key: 'operation_type', label: 'Operación', sortable: true },
  { key: 'price', label: 'Precio', sortable: true },
  { key: 'location', label: 'Ubicación', sortable: false },
  { key: 'is_featured', label: '', sortable: false, width: '50px' },
  { key: 'created_at', label: 'Fecha', sortable: true },
  { key: 'status', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const deleting = ref(null)
const duplicating = ref(null)
const perPage = ref(10)
const selectedIds = ref([])
const searchQuery = ref(props.filters?.search || '')

const filters = ref({
  property_type_id: props.filters?.property_type_id || null,
  operation_type: props.filters?.operation_type || null,
  status: props.filters?.status || null,
})

const currentPageIds = computed(() => {
  if (!dataTable.value?.data) return []
  return dataTable.value.data.map(row => row.id)
})

const hasActiveFilters = computed(() => {
  return filters.value.property_type_id ||
    filters.value.operation_type ||
    filters.value.status ||
    searchQuery.value
})

const onDataTableUpdated = (data) => {
  perPage.value = data.per_page
  selectedIds.value = []
}

const onBulkDeleted = () => {
  if (dataTableRef.value) {
    dataTableRef.value.reload()
  }
}

const filterProperties = () => {
  let url = `/member/listings/${listing.value.id}/properties?`
  const params = []

  if (filters.value.property_type_id) {
    params.push(`property_type=${filters.value.property_type_id}`)
  }
  if (filters.value.operation_type) {
    params.push(`operation=${filters.value.operation_type}`)
  }
  if (filters.value.status) {
    params.push(`status=${filters.value.status}`)
  }
  if (filters.value.state) {
    params.push(`state=${filters.value.state}`)
  }
  if (filters.value.city) {
    params.push(`city=${filters.value.city}`)
  }
  if (searchQuery.value) {
    params.push(`search=${encodeURIComponent(searchQuery.value)}`)
  }

  url += params.join('&')
  window.location.href = url
}

const onStateChange = () => {
  filters.value.city = null
  filterProperties()
}

const searchProperties = () => {
  filterProperties()
}

const clearFilters = () => {
  filters.value = {
    property_type_id: null,
    operation_type: null,
    status: null,
    state: null,
    city: null,
  }
  searchQuery.value = ''
  window.location.href = `/member/listings/${listing.value.id}/properties`
}

const getOperationLabel = (op) => {
  const labels = { sale: 'Venta', rent: 'Renta', transfer: 'Traspaso' }
  return labels[op] || op
}

const getStatusLabel = (st) => {
  const labels = {
    draft: 'Borrador',
    published: 'Publicada',
    paused: 'Pausada',
    rented: 'Rentada',
    sold: 'Vendida',
    transferred: 'Traspasada',
    archived: 'Archivada',
  }
  return labels[st] || st
}

const getPropertyTypeName = (id) => {
  const type = props.propertyTypes.find(t => t.id === id)
  return type ? type.name : id
}

const formatDate = (date) => {
  if (!date) return '-'
  const d = new Date(date)
  return d.toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' })
}

const getStatusBadgeClass = (status) => {
  const classes = {
    draft: 'badge bg-secondary',
    published: 'badge bg-success',
    paused: 'badge bg-warning',
    rented: 'badge bg-info',
    sold: 'badge bg-primary',
    transferred: 'badge bg-dark',
    archived: 'badge bg-secondary',
  }
  return classes[status] || 'badge bg-secondary'
}

const changeStatus = (property, status) => {
  router.post(`/member/listings/${listing.value.id}/properties/${property.id}/change-status`, {
    status,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const duplicateProperty = (property) => {
  if (confirm(`Duplicar la propiedad "${property.title}"?`)) {
    duplicating.value = property.id
    router.post(`/member/listings/${listing.value.id}/properties/${property.id}/duplicate`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        duplicating.value = null
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
      onError: () => {
        duplicating.value = null
      },
    })
  }
}

const deleteProperty = (property) => {
  if (confirm(`Eliminar la propiedad "${property.title}"?`)) {
    deleting.value = property.id
    router.delete(`/member/listings/${listing.value.id}/properties/${property.id}`, {
      preserveScroll: true,
      onFinish: () => {
        deleting.value = null
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}

const deleteSelected = () => {
  if (selectedIds.value.length === 0) return

  const count = selectedIds.value.length
  if (confirm(`Eliminar ${count} propert${count > 1 ? 'es' : 'ad'} seleccionado${count > 1 ? 's' : ''}?`)) {
    deleting.value = true
    router.post(`/member/listings/${listing.value.id}/properties/bulk-delete`, {
      ids: selectedIds.value,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        selectedIds.value = []
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
      onFinish: () => {
        deleting.value = false
      },
    })
  }
}
</script>

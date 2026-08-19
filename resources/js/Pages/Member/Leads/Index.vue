<template>
  <MemberLayout>
    <Head :title="`Contactos - ${business?.name || ''}`" />

    <PageHeader
      title="Contactos"
      :breadcrumbs="breadcrumbs"
      :backHref="'/member/business-modules'"
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
        <a :href="`/member/businesses/${business?.id}/leads/export`" class="btn btn-success btn-sm">
          <i class="bi bi-download me-1"></i>Exportar
        </a>
        <Link :href="`/member/businesses/${business?.id}/leads/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nuevo Contacto
        </Link>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/businesses/${business?.id}/leads`"
      :columns="columns"
      :initial-data="dataTable"
      search-placeholder="Buscar contactos..."
      empty-title="No hay contactos"
      empty-text="Comienza creando tu primer contacto."
      @updated="onDataTableUpdated"
    >
      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>

      <template #cell-name="{ row }">
        <strong>{{ row.name }}</strong>
      </template>

      <template #cell-email="{ row }">
        <a :href="`mailto:${row.email}`">{{ row.email }}</a>
      </template>

      <template #cell-phone="{ row }">
        {{ row.phone || '-' }}
      </template>

      <template #cell-status="{ row }">
        <span :class="statusClass(row.status)" class="badge">
          {{ row.status_label }}
        </span>
      </template>

      <template #cell-source="{ row }">
        {{ row.source_label }}
      </template>

      <template #cell-created_at="{ row }">
        {{ formatDate(row.created_at) }}
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link :href="`/member/businesses/${business?.id}/leads/${row.id}`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-eye"></i>
          </Link>
          <Link :href="`/member/businesses/${business?.id}/leads/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button class="btn btn-sm btn-outline-danger" @click="deleteLead(row)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>

      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/businesses/${business?.id}/leads/bulk-delete`"
          item-name="contactos"
          @deleted="onBulkDeleted"
        />
      </template>
    </BaseDataTable>
  </MemberLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import { BulkSelect, BulkSelectRowCheckbox } from '@/Components/BulkSelect'

const page = usePage()
const business = computed(() => page.props.business)
const dataTable = computed(() => page.props.dataTable)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Leads', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Leads', active: true },
  ]
})

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'email', label: 'Email', sortable: false },
  { key: 'phone', label: 'Telefono', sortable: false },
  { key: 'status', label: 'Estado', sortable: true },
  { key: 'source', label: 'Fuente', sortable: false },
  { key: 'created_at', label: 'Fecha', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const deleting = ref(null)
const selectedIds = ref([])

const currentPageIds = computed(() => {
  if (!dataTable.value?.data) return []
  return dataTable.value.data.map(row => row.id)
})

const onDataTableUpdated = (data) => {
  selectedIds.value = []
}

const onBulkDeleted = () => {
  if (dataTableRef.value) {
    dataTableRef.value.reload()
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-AR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const statusClass = (status) => {
  const classes = {
    new: 'bg-info',
    contacted: 'bg-primary',
    qualified: 'bg-success',
    converted: 'bg-dark',
    lost: 'bg-secondary',
  }
  return classes[status] || 'bg-secondary'
}

const deleteLead = (lead) => {
  if (confirm('¿Estás seguro de eliminar este contacto?')) {
    deleting.value = lead.id
    router.delete(`/member/businesses/${business.value.id}/leads/${lead.id}`, {
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
  if (confirm(`Eliminar ${count} contacto${count > 1 ? 's' : ''} seleccionado${count > 1 ? 's' : ''}?`)) {
    deleting.value = true
    router.post(`/member/businesses/${business.value.id}/leads/bulk-delete`, {
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

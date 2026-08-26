<template>
  <MemberLayout>
    <Head :title="`Clientes - ${listing?.name || ''}`" />

    <PageHeader
      title="Clientes"
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
        <Link :href="`/member/listings/${listing?.id}/clients/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nuevo Cliente
        </Link>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/clients`"
      :columns="columns"
      :initial-data="dataTable"
      search-placeholder="Buscar clientes..."
      empty-title="No hay clientes"
      empty-text="Comienza registrando tu primer cliente."
      @updated="onDataTableUpdated"
    >
      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>

      <template #cell-company_name="{ row }">
        <div>{{ row.company_name || row.contact_person || '-' }}</div>
        <small class="text-muted">{{ row.contact_person || '' }}</small>
      </template>

      <template #cell-customer_email="{ row }">
        <div>{{ row.customer_name }}</div>
        <small class="text-muted">{{ row.customer_email || '' }}</small>
      </template>

      <template #cell-state="{ row }">
        {{ row.state_code ? `${row.state_code} / ${row.municipality || ''}` : '-' }}
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button class="btn btn-sm btn-outline-secondary" @click="cloneClient(row)" title="Clonar">
            <i class="bi bi-copy"></i>
          </button>
          <Link :href="`/member/listings/${listing?.id}/clients/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deleteClient(row)"
          >
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>

      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
           :delete-endpoint="`/member/listings/${listing?.id}/clients/bulk-delete`"
          item-name="clientes"
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
const listing = computed(() => page.props.listing)
const dataTable = computed(() => page.props.dataTable)
const businessMenu = computed(() => page.props.businessMenu || [])

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'company_name', label: 'Empresa / Contacto', sortable: true },
  { key: 'customer_email', label: 'Cliente', sortable: true },
  { key: 'whatsapp', label: 'WhatsApp', sortable: false },
  { key: 'rfc', label: 'RFC', sortable: false },
  { key: 'state', label: 'Estado / Municipio', sortable: false },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Clientes' },
])

const dataTableRef = ref(null)
const selectedIds = ref([])
const deleting = ref(null)

const currentPageIds = computed(() => {
  if (!dataTable.value?.data) return []
  return dataTable.value.data.map(row => row.id)
})

const onDataTableUpdated = () => {
  selectedIds.value = []
}

const onBulkDeleted = () => {
  if (dataTableRef.value) {
    dataTableRef.value.reload()
  }
}

const deleteClient = (row) => {
  if (confirm(`Estas seguro de eliminar a ${row.customer_name}? Esta accion no se puede deshacer.`)) {
    deleting.value = row.id
    router.delete(`/member/listings/${listing.value.id}/clients/${row.id}`, {
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

const cloneClient = (row) => {
  if (!confirm(`¿Clonar "${row.customer_name}"?`)) {
    return
  }
  router.post(`/member/listings/${listing.value.id}/clients/${row.id}/clone`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const deleteSelected = () => {
  if (selectedIds.value.length === 0) return
  const count = selectedIds.value.length
  if (confirm(`Eliminar ${count} cliente${count > 1 ? 's' : ''} seleccionado${count > 1 ? 's' : ''}?`)) {
    deleting.value = true
    router.post(`/member/listings/${listing.value.id}/clients/bulk-delete`, {
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

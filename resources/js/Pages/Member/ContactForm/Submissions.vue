<template>
  <MemberLayout>
    <Head :title="`Envios - ${form?.name || 'Formulario'} - ${business?.name || ''}`" />

    <PageHeader
      :title="`Envios: ${form?.name || 'Formulario'}`"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/contact-forms`"
    >
      <template #actions>
        <a :href="`/member/businesses/${business?.id}/contact-forms/${form?.id}/export`" class="btn btn-success btn-sm">
          <i class="bi bi-download me-1"></i>Exportar CSV
        </a>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/businesses/${business?.id}/contact-forms/${form?.id}/submissions`"
      :columns="columns"
      :initial-data="dataTable"
      search-placeholder="Buscar mensajes..."
      empty-title="No hay mensajes"
      empty-text="Los mensajes de este formulario apareceran aqui."
      @updated="onDataTableUpdated"
    >
      <template #cell-name="{ row }">
        <strong>{{ row.name }}</strong>
      </template>

      <template #cell-email="{ row }">
        <a :href="`mailto:${row.email}`">{{ row.email }}</a>
      </template>

      <template #cell-phone="{ row }">
        {{ row.phone || '-' }}
      </template>

      <template #cell-notes="{ row }">
        <span class="text-muted small">
          {{ row.notes ? row.notes.substring(0, 50) + '...' : '-' }}
        </span>
      </template>

      <template #cell-created_at="{ row }">
        {{ formatDate(row.created_at) }}
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link :href="`/member/businesses/${business?.id}/leads/${row.id}`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-eye"></i>
          </Link>
          <button class="btn btn-sm btn-outline-danger" @click="deleteSubmission(row)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>
    </BaseDataTable>
  </MemberLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'

const page = usePage()
const business = computed(() => page.props.business)
const form = computed(() => page.props.form)
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
        { label: 'Respuestas', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Respuestas', active: true },
  ]
})

const columns = [
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'email', label: 'Email', sortable: false },
  { key: 'phone', label: 'Telefono', sortable: false },
  { key: 'notes', label: 'Notas', sortable: false },
  { key: 'created_at', label: 'Fecha', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)

const onDataTableUpdated = (data) => {
  // Optional: handle data update
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-AR')
}

const deleteSubmission = (row) => {
  if (confirm(`¿Eliminar el mensaje de "${row.name}"?`)) {
    router.delete(`/member/businesses/${business.value?.id}/leads/${row.id}`, {
      preserveScroll: true,
    })
  }
}
</script>

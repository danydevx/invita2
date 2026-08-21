<template>
  <MemberLayout>
    <Head :title="`Horarios - ${location.name}`" />

    <PageHeader
      title="Horarios de Atención"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing.id}/locations/${location.id}/edit`"
    >
      <template #actions>
        <Link
          :href="`/member/listings/${listing.id}/locations/${location.id}/schedules/create`"
          class="btn btn-primary btn-sm"
        >
          <i class="bi bi-plus-lg me-1"></i>
          Nuevo Horario
        </Link>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/locations/${location?.id}/schedules`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      search-placeholder="Buscar horarios..."
      empty-title="No hay horarios"
      empty-text="Comienza creando tu primer horario."
      @updated="onDataTableUpdated"
    >
      <template #cell-name="{ row }">
        <strong>{{ row.name }}</strong>
      </template>

      <template #cell-days_display="{ row }">
        {{ row.days_display }}
      </template>

      <template #cell-time_display="{ row }">
        <small>{{ row.time_display }}</small>
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.is_active ? 'Activo' : 'Inactivo' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button
            class="btn btn-sm btn-outline-secondary"
            @click="cloneSchedule(row.id)"
            :disabled="cloning === row.id"
            title="Clonar"
          >
            <i class="bi bi-copy"></i>
          </button>
          <Link
            :href="`/member/listings/${listing?.id}/locations/${location?.id}/schedules/${row.id}/edit`"
            class="btn btn-sm btn-outline-primary"
          >
            <i class="bi bi-pencil"></i>
          </Link>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deleteSchedule(row)"
            :disabled="deleting === row.id"
          >
            <i class="bi bi-trash"></i>
          </button>
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

const page = usePage()
const listing = computed(() => page.props.listing)
const location = computed(() => page.props.location)
const dataTable = computed(() => page.props.dataTable)

const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const biz = businessMenu.value.find(b => b.id === listing.value.id)
  return [
    { label: biz?.name || '', href: `/member/listings/${listing.value.id}/edit` },
    { label: 'Ubicaciones', href: `/member/listings/${listing.value.id}/locations` },
    { label: location.value.name, href: `/member/listings/${listing.value.id}/locations/${location.value.id}/edit` },
    { label: 'Horarios', active: true },
  ]
})

const columns = [
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'days_display', label: 'Dias', sortable: false },
  { key: 'time_display', label: 'Horario', sortable: false },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const deleting = ref(null)
const cloning = ref(null)
const perPage = ref(10)

const onDataTableUpdated = (data) => {
  perPage.value = data.per_page
}

const cloneSchedule = (scheduleId) => {
  if (confirm('¿Clonar este horario?')) {
    cloning.value = scheduleId
    router.post(`/member/listings/${listing.value.id}/locations/${location.value.id}/schedules/${scheduleId}/clone`, {}, {
      preserveScroll: true,
      onFinish: () => {
        cloning.value = null
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}

const deleteSchedule = (schedule) => {
  if (confirm('¿Eliminar este horario? Esta acción no se puede deshacer.')) {
    deleting.value = schedule.id
    router.delete(`/member/listings/${listing.value.id}/locations/${location.value.id}/schedules/${schedule.id}`, {
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
</script>

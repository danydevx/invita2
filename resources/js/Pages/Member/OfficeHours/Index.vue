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

    <div class="container-fluid py-4">
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
        <span class="schedule-name">{{ row.name }}</span>
      </template>

      <template #cell-days_display="{ row }">
        <span class="badge bg-light text-dark border">{{ row.days_display }}</span>
      </template>

      <template #cell-time_display="{ row }">
        <span class="schedule-time">{{ row.time_display }}</span>
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success-subtle text-success' : 'badge bg-secondary-subtle text-secondary'">
          <i :class="row.is_active ? 'bi bi-check-circle me-1' : 'bi bi-x-circle me-1'"></i>
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
    </div>
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

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Horarios' },
])

const columns = [
  { key: 'name', label: 'Nombre', sortable: true, class: 'fw-semibold' },
  { key: 'days_display', label: 'Días', sortable: false },
  { key: 'time_display', label: 'Horario', sortable: false },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false, class: 'text-end' },
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

<style scoped>
.schedule-name {
  color: var(--bs-dark);
}
.schedule-time {
  font-family: monospace;
  background: var(--bs-light);
  padding: 0.125rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.85em;
}
.actions {
  display: flex;
  gap: 0.375rem;
  justify-content: flex-end;
}
</style>

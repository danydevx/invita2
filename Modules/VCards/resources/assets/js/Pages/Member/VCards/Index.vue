<template>
  <MemberLayout>
    <Head :title="`vCards - ${listing?.name || ''}`" />

    <PageHeader
      title="vCards"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings`"
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
        <Link :href="`/member/listings/${listing?.id}/vcards/teams`" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-people me-1"></i>
          Equipos
        </Link>
        <Link :href="`/member/listings/${listing?.id}/vcards/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>
          Nueva Tarjeta
        </Link>
      </template>
    </PageHeader>

    <div class="row mb-3 align-items-center">
      <div class="col">
        <div class="d-flex gap-2 align-items-center flex-wrap">
          <select v-model="filterTeam" class="form-select form-select-sm" @change="filterVcards" style="max-width: 200px;">
            <option :value="null">Todos los equipos</option>
            <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
          </select>
          <button v-if="filterTeam" type="button" class="btn btn-outline-secondary btn-sm" @click="clearFilter">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
    </div>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/vcards`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      search-placeholder="Buscar tarjetas..."
      empty-title="No hay tarjetas"
      empty-text="Comienza creando tu primera tarjeta de presentación digital."
      @updated="onDataTableUpdated"
    >
      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/listings/${listing?.id}/vcards/bulk-delete`"
          item-name="tarjetas"
          @deleted="onBulkDeleted"
        />
      </template>

      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox
          :id="row.id"
          v-model:selectedIds="selectedIds"
        />
      </template>

      <template #cell-profile_photo="{ row }">
        <img
          v-if="row.profile_photo"
          :src="row.profile_photo"
          class="rounded"
          style="width: 48px; height: 48px; object-fit: cover;"
        />
        <div v-else class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
          <i class="bi bi-person text-muted"></i>
        </div>
      </template>

      <template #cell-name="{ row }">
        <strong>{{ row.name }}</strong>
        <p class="text-muted small mb-0">/{{ row.slug }}</p>
      </template>

      <template #cell-type="{ row }">
        <span :class="row.type === 'team' ? 'badge bg-info' : 'badge bg-secondary'">
          {{ row.type === 'team' ? 'Equipo' : 'Individual' }}
        </span>
      </template>

      <template #cell-team="{ row }">
        <span v-if="row.team">{{ row.team.name }}</span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-active="{ row }">
        <span :class="row.active ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.active ? 'Activa' : 'Inactiva' }}
        </span>
      </template>

      <template #cell-views="{ row }">
        <span>{{ row.views }} visitas</span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link
            :href="`/member/listings/${listing?.id}/vcards/${row.id}/edit`"
            class="btn btn-sm btn-outline-primary"
            title="Editar"
          >
            <i class="bi bi-pencil"></i>
          </Link>
          <a
            :href="`/v/${row.slug}`"
            target="_blank"
            class="btn btn-sm btn-outline-secondary"
            title="Ver tarjeta"
          >
            <i class="bi bi-eye"></i>
          </a>
          <button
            class="btn btn-sm btn-outline-secondary"
            @click="copyLink(row)"
            title="Copiar enlace"
          >
            <i class="bi bi-link"></i>
          </button>
          <button
            class="btn btn-sm btn-outline-secondary"
            @click="downloadVCard(row)"
            title="Descargar vCard"
          >
            <i class="bi bi-download"></i>
          </button>
          <button
            class="btn btn-sm btn-outline-secondary"
            @click="duplicateVCard(row)"
            :disabled="cloning === row.id"
            title="Duplicar"
          >
            <i class="bi bi-copy"></i>
          </button>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deleteVCard(row)"
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
import { toast } from 'vue3-toastify'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import BulkSelect from '@/Components/BulkSelect/BulkSelect.vue'
import BulkSelectRowCheckbox from '@/Components/BulkSelect/BulkSelectRowCheckbox.vue'

const props = defineProps({
  listing: Object,
  dataTable: Object,
  teams: Array,
  filters: Object,
})

const dataTableRef = ref(null)
const selectedIds = ref([])
const deleting = ref(null)
const cloning = ref(null)
const filterTeam = ref(props.filters?.team_id || null)
const currentPageIds = computed(() => {
  return props.dataTable?.data?.map(item => item.id) || []
})

const columns = [
  { key: 'checkbox', label: '', sortable: false },
  { key: 'profile_photo', label: '', sortable: false },
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'type', label: 'Tipo', sortable: true },
  { key: 'team', label: 'Equipo', sortable: false },
  { key: 'active', label: 'Estado', sortable: true },
  { key: 'views', label: 'Visitas', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'vCards' },
])

const perPage = 15

function filterVcards() {
  router.get(
    `/member/listings/${props.listing.id}/vcards`,
    { team_id: filterTeam.value },
    { preserveState: true }
  )
}

function clearFilter() {
  filterTeam.value = null
  router.get(
    `/member/listings/${props.listing.id}/vcards`,
    {},
    { preserveState: true }
  )
}

function copyLink(vcard) {
  navigator.clipboard.writeText(`${window.location.origin}/v/${vcard.slug}`)
  toast.success('Enlace copiado al portapapeles')
}

function downloadVCard(vcard) {
  window.location.href = `/member/listings/${props.listing.id}/vcards/${vcard.id}/download`
}

function duplicateVCard(vcard) {
  cloning.value = vcard.id
  router.post(
    `/member/listings/${props.listing.id}/vcards/${vcard.id}/duplicate`,
    {},
    {
      onSuccess: () => {
        toast.success('Tarjeta duplicada correctamente')
        dataTableRef.value?.reload()
      },
      onFinish: () => {
        cloning.value = null
      },
    }
  )
}

function deleteVCard(vcard) {
  if (!confirm('¿Estás seguro de eliminar esta tarjeta?')) return
  deleting.value = vcard.id
  router.delete(
    `/member/listings/${props.listing.id}/vcards/${vcard.id}`,
    {
      onSuccess: () => {
        toast.success('Tarjeta eliminada correctamente')
        dataTableRef.value?.reload()
      },
      onFinish: () => {
        deleting.value = null
      },
    }
  )
}

function deleteSelected() {
  if (!confirm(`¿Eliminar ${selectedIds.value.length} tarjetas?`)) return
  router.post(
    `/member/listings/${props.listing.id}/vcards/bulk-delete`,
    { ids: selectedIds.value },
    {
      onSuccess: () => {
        toast.success('Tarjetas eliminadas correctamente')
        selectedIds.value = []
        dataTableRef.value?.reload()
      },
    }
  )
}

function onBulkDeleted() {
  selectedIds.value = []
  dataTableRef.value?.reload()
}

function onDataTableUpdated() {
  selectedIds.value = []
}
</script>

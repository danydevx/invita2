<template>
  <MemberLayout>
    <Head :title="`Puestos - ${listing?.name || ''}`" />

    <PageHeader
      title="Puestos"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/team-members`"
    >
      <template #actions>
        <Link :href="`/member/listings/${listing?.id}/team-member-positions/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nuevo Puesto
        </Link>
      </template>
    </PageHeader>

    <div class="mb-3 d-flex gap-2">
      <Link
        :href="`/member/listings/${listing?.id}/team-members`"
        class="btn btn-outline-secondary btn-sm"
      >
        <i class="bi bi-people me-1"></i>Miembros
      </Link>
      <Link
        :href="`/member/listings/${listing?.id}/team-member-positions`"
        class="btn btn-secondary btn-sm"
      >
        <i class="bi bi-folder me-1"></i>Puestos
      </Link>
    </div>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/team-member-positions`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      :reorderable="true"
      :reorder-endpoint="`/member/listings/${listing?.id}/team-member-positions/reorder`"
      search-placeholder="Buscar puestos..."
      empty-title="No hay puestos"
      empty-text="Crea tu primer puesto para organizar a tu equipo."
      @updated="onDataTableUpdated"
    >
      <template #cell-name="{ row }">
        <strong>{{ row.name }}</strong>
        <span v-if="row.parent" class="badge bg-light text-dark ms-2">{{ row.parent.name }}</span>
      </template>

      <template #cell-description="{ row }">
        <span v-if="row.description" class="text-muted small">{{ row.description.substring(0, 50) }}...</span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-members_count="{ row }">
        <span class="text-muted">
          <i class="bi bi-people me-1"></i>{{ row.members_count || 0 }}
        </span>
      </template>

      <template #cell-children_count="{ row }">
        <span v-if="row.children_count > 0" class="text-muted">
          <i class="bi bi-diagram-3 me-1"></i>{{ row.children_count }}
        </span>
        <span v-else class="text-muted">-</span>
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success-subtle text-success' : 'badge bg-secondary-subtle text-secondary'">
          {{ row.is_active ? 'Activo' : 'Inactivo' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <Link :href="`/member/listings/${listing?.id}/team-member-positions/${row.id}/edit`" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil"></i>
          </Link>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deletePosition(row)"
            :disabled="(row.members_count || 0) > 0 || (row.children_count || 0) > 0"
          >
            <i class="bi bi-trash"></i>
          </button>
        </div>
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

const page = usePage()
const listing = computed(() => page.props.listing)
const dataTable = computed(() => page.props.dataTable)
const businessMenu = computed(() => page.props.businessMenu || [])

const dataTableRef = ref(null)
const perPage = ref(10)

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Mi Equipo', href: `/member/listings/${listing.value?.id}/team-members` },
  { label: 'Puestos' },
])

const columns = [
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'description', label: 'Descripción', sortable: false },
  { key: 'members_count', label: 'Miembros', sortable: false, class: 'text-center' },
  { key: 'children_count', label: 'Sub-puestos', sortable: false, class: 'text-center' },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false, class: 'text-end' },
]

const onDataTableUpdated = (data) => {
  perPage.value = data.per_page
}

const deletePosition = (position) => {
  if (!confirm(`¿Estás seguro de eliminar "${position.name}"?`)) {
    return
  }

  router.delete(`/member/listings/${listing.value.id}/team-member-positions/${position.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}
</script>

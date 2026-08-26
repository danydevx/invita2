<template>
  <MemberLayout>
    <Head :title="`Categorias - ${listing?.name || ''}`" />

    <PageHeader
      title="Categorias de Servicios"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/services`"
    >
      <template #actions>
        <button class="btn btn-primary btn-sm" @click="openCreateModal">
          <i class="bi bi-plus-lg me-1"></i>Nueva Categoria
        </button>
      </template>
    </PageHeader>

    <BaseDataTable
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/service-categories`"
      :columns="columns"
      :initial-data="dataTable"
      :initial-per-page="perPage"
      search-placeholder="Buscar categorias..."
      empty-title="No hay categorias"
      empty-text="Comienza creando tu primera categoria."
      @updated="onDataTableUpdated"
    >
      <template #cell-name="{ row }">
        <strong>{{ row.name }}</strong>
        <p v-if="row.description" class="text-muted small mb-0">{{ row.description.substring(0, 60) }}...</p>
      </template>

      <template #cell-services_count="{ row }">
        <span class="badge bg-secondary">{{ row.services_count || 0 }}</span>
      </template>

      <template #cell-is_active="{ row }">
        <span :class="row.is_active ? 'badge bg-success' : 'badge bg-secondary'">
          {{ row.is_active ? 'Activa' : 'Inactiva' }}
        </span>
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button
            class="btn btn-sm btn-outline-primary"
            @click="openEditModal(row)"
          >
            <i class="bi bi-pencil"></i>
          </button>
          <button
            class="btn btn-sm btn-outline-danger"
            @click="deleteCategory(row)"
          >
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>
    </BaseDataTable>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingCategory ? 'Editar Categoria' : 'Nueva Categoria' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="editingCategory ? updateCategory() : createCategory()">
            <div class="modal-body">
              <div class="mb-3">
                <FieldText
                  id="category-name"
                  label="Nombre"
                  v-model="form.name"
                  required
                />
              </div>
              <div class="mb-3">
                <FieldTextarea
                  id="category-description"
                  label="Descripcion"
                  v-model="form.description"
                  :rows="2"
                />
              </div>
              <div class="mb-3">
                <FieldSwitch
                  id="category-active"
                  label="Activa"
                  v-model="form.is_active"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? (editingCategory ? 'Guardando...' : 'Creando...') : (editingCategory ? 'Guardar Cambios' : 'Crear Categoria') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const dataTable = computed(() => page.props.dataTable)
const categories = computed(() => page.props.categories || [])
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Servicios', href: `/member/listings/${listing.value?.id}/services` },
  { label: 'Categorías' },
])

const columns = [
  { key: 'name', label: 'Nombre', sortable: true },
  { key: 'services_count', label: 'Servicios', sortable: true },
  { key: 'is_active', label: 'Estado', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const dataTableRef = ref(null)
const perPage = ref(10)
const modalElement = ref(null)
let categoryModal = null

const sending = ref(false)
const editingCategory = ref(null)

const form = reactive({
  name: '',
  description: '',
  is_active: true,
})

const onDataTableUpdated = (data) => {
  perPage.value = data.per_page
}

const openCreateModal = () => {
  editingCategory.value = null
  form.name = ''
  form.description = ''
  form.is_active = true
  nextTick(() => categoryModal.show())
}

const openEditModal = (category) => {
  editingCategory.value = category
  form.name = category.name
  form.description = category.description || ''
  form.is_active = category.is_active
  nextTick(() => categoryModal.show())
}

const closeModal = () => {
  categoryModal.hide()
}

const createCategory = () => {
  sending.value = true
  router.post(`/member/listings/${listing.value.id}/service-categories`, form, {
    onFinish: () => {
      sending.value = false
      closeModal()
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const updateCategory = () => {
  sending.value = true
  router.put(`/member/listings/${listing.value.id}/service-categories/${editingCategory.value.id}`, form, {
    onFinish: () => {
      sending.value = false
      closeModal()
      if (dataTableRef.value) {
        dataTableRef.value.reload()
      }
    },
  })
}

const deleteCategory = (category) => {
  if (confirm(`Eliminar la categoria "${category.name}"? Los servicios seran desvinculados.`)) {
    router.delete(`/member/listings/${listing.value.id}/service-categories/${category.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        if (dataTableRef.value) {
          dataTableRef.value.reload()
        }
      },
    })
  }
}

onMounted(() => {
  categoryModal = new Modal(modalElement.value)
})
</script>

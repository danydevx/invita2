<template>
  <MemberLayout>
    <Head :title="`Galería - ${currentGallery?.name || ''}`" />

    <PageHeader
      :title="currentGallery?.name || 'Galería'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id || ''}/galleries`"
    >
      <template #description>
        <p class="text-muted mb-0">Gestiona las imagenes de esta galería. Arrastra para reordenar.</p>
      </template>
      <template #actions>
        <div class="dropdown">
          <button
            type="button"
            class="btn btn-outline-primary btn-sm dropdown-toggle"
            data-bs-toggle="dropdown"
          >
            <i class="bi bi-arrow-left-right me-1"></i>
            Cambiar galería
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li v-for="gallery in galleries" :key="gallery.id">
              <Link class="dropdown-item d-flex justify-content-between align-items-center" :href="`/member/listings/${listing?.id}/gallery/${gallery.id}`">
                <span>
                  {{ gallery.name }}
                  <i v-if="gallery.is_primary" class="bi bi-star-fill text-warning ms-1"></i>
                </span>
                <span class="badge bg-secondary ms-2">{{ gallery.images_count || 0 }}</span>
              </Link>
            </li>
          </ul>
        </div>
        <button
          v-if="selectedIds.length > 0"
          class="btn btn-danger btn-sm"
          @click="deleteSelected"
          :disabled="deleting"
        >
          <i class="bi bi-trash me-1"></i>
          Eliminar ({{ selectedIds.length }})
        </button>
        <button class="btn btn-primary btn-sm" @click="openUploadModal">
          <i class="bi bi-plus-lg me-1"></i>
          Subir imagenes
        </button>
      </template>
    </PageHeader>

    <div v-if="!images.data.length" class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <i class="bi bi-images display-1 text-muted"></i>
        <h3 class="h5 mt-3">No hay imagenes en esta galería</h3>
        <p class="text-muted">Sube tu primera imagen para empezar.</p>
        <button class="btn btn-primary" @click="openUploadModal">
          <i class="bi bi-plus-lg me-1"></i>
          Subir imagenes
        </button>
      </div>
    </div>

    <BaseDataTable
      v-else
      ref="dataTableRef"
      :endpoint="`/member/listings/${listing?.id}/gallery/${currentGallery?.id}`"
      :columns="columns"
      :initial-data="dataTable"
      :reorderable="true"
      :reorder-endpoint="`/member/listings/${listing?.id}/gallery/reorder`"
      search-placeholder="Buscar imagenes..."
      empty-title="No hay imagenes"
      empty-text="Sube tu primera imagen para empezar."
      @updated="onDataTableUpdated"
    >
      <template #cell-checkbox="{ row }">
        <BulkSelectRowCheckbox :id="row.id" v-model:selectedIds="selectedIds" />
      </template>

      <template #cell-image="{ row }">
        <img :src="row.path" class="img-thumbnail" style="max-height: 60px; max-width: 80px; object-fit: cover;" :alt="row.title" />
      </template>

      <template #cell-actions="{ row }">
        <div class="actions">
          <button class="btn btn-sm btn-outline-primary" @click="openEditModal(row)">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger" @click="deleteImage(row)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </template>

      <template #header-actions>
        <BulkSelect
          v-model:selectedIds="selectedIds"
          :current-page-ids="currentPageIds"
          :delete-endpoint="`/member/listings/${listing?.id}/gallery/bulk-delete`"
          item-name="imagenes"
          @deleted="onBulkDeleted"
        />
      </template>
    </BaseDataTable>

    <ImageUpload
      ref="imageUploadRef"
      :business-id="listing?.id"
      :gallery-id="currentGallery?.id"
      :locations="locations"
      @uploaded="onImageUploaded"
    />

    <div ref="editModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar imagen</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="saveEdit">
            <div class="modal-body">
              <div class="text-center mb-3">
                <img v-if="editForm.path" :src="editForm.path" class="img-thumbnail" style="max-height: 200px;" :alt="editForm.title" />
              </div>
              <FieldText id="edit-title" label="Titulo" v-model="editForm.title" />
              <FieldTextarea
                id="edit-description"
                label="Descripcion"
                v-model="editForm.description"
                :rows="2"
              />
              <FieldSelect
                id="edit-location"
                label="Ubicacion"
                v-model="editForm.business_location_id"
              >
                <option :value="null">Sin ubicacion</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                  {{ loc.name }}
                </option>
              </FieldSelect>
              <div class="row g-3">
                <div class="col-6">
                  <FieldNumber id="edit-sort" label="Orden" v-model="editForm.sort_order" />
                </div>
                <div class="col-6 d-flex align-items-end">
                  <FieldSwitch id="edit-active" label="Activo" v-model="editForm.is_active" />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, nextTick, onMounted, reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import BaseDataTable from '@/Components/DataTable/BaseDataTable.vue'
import { BulkSelect, BulkSelectRowCheckbox } from '@/Components/BulkSelect'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import ImageUpload from '@/Components/ImageUpload/ImageUpload.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const currentGallery = computed(() => page.props.currentGallery)
const galleries = computed(() => page.props.galleries || [])
const locations = computed(() => page.props.locations || [])
const images = computed(() => page.props.images || { data: [] })
const dataTable = computed(() => page.props.dataTable || { data: [], total: 0 })
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Galería', href: `/member/listings/${listing.value?.id}/galleries` },
  { label: currentGallery.value?.name },
])

const columns = [
  { key: 'checkbox', label: '', sortable: false, width: '40px' },
  { key: 'image', label: 'Imagen', sortable: false },
  { key: 'title', label: 'Titulo', sortable: true },
  { key: 'actions', label: 'Acciones', sortable: false },
]

const imageUploadRef = ref(null)
const editModalElement = ref(null)
let editModal = null

const saving = ref(false)
const deleting = ref(false)
const selectedIds = ref([])

const editForm = reactive({
  id: null,
  path: '',
  title: '',
  description: '',
  business_location_id: null,
  sort_order: 0,
  is_active: true,
})

const openUploadModal = () => {
  imageUploadRef.value?.open()
}

const openEditModal = (img) => {
  editForm.id = img.id
  editForm.path = img.path
  editForm.title = img.title || ''
  editForm.description = img.description || ''
  editForm.business_location_id = img.location?.id ?? null
  editForm.sort_order = img.sort_order || 0
  editForm.is_active = !!img.is_active
  nextTick(() => editModal.show())
}

const closeEditModal = () => {
  editModal.hide()
}

const onImageUploaded = () => {
  if (dataTableRef.value) {
    dataTableRef.value.reload()
  }
}

const saveEdit = () => {
  saving.value = true

  router.put(`/member/listings/${listing.value.id}/gallery/${editForm.id}`, {
    business_gallery_id: currentGallery.value.id,
    title: editForm.title,
    description: editForm.description,
    business_location_id: editForm.business_location_id,
    sort_order: editForm.sort_order,
    is_active: editForm.is_active,
  }, {
    preserveScroll: true,
    onFinish: () => {
      saving.value = false
      closeEditModal()
    },
  })
}

const deleteImage = (img) => {
  if (confirm('Estas seguro de eliminar esta imagen?')) {
    router.delete(`/member/listings/${listing.value.id}/gallery/${img.id}`, {
      preserveScroll: true,
    })
  }
}

const deleteSelected = () => {
  if (selectedIds.value.length === 0) return
  const count = selectedIds.value.length
  if (confirm(`Eliminar ${count} imagen${count > 1 ? 'es' : ''} seleccionada${count > 1 ? 's' : ''}?`)) {
    deleting.value = true
    router.post(`/member/listings/${listing.value.id}/gallery/bulk-delete`, {
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

const currentPageIds = computed(() => {
  if (!dataTable.value?.data) return []
  return dataTable.value.data.map((row) => row.id)
})

const onDataTableUpdated = () => {
  selectedIds.value = []
}

const onBulkDeleted = () => {
  if (dataTableRef.value) {
    dataTableRef.value.reload()
  }
}

onMounted(() => {
  editModal = editModalElement.value ? new Modal(editModalElement.value) : null
})
</script>
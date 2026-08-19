<template>
  <AdminLayout>
    <Head :title="`Galeria - ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin/businesses" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Negocios
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - Galeria</h1>
      </div>
      <div>
        <button class="btn btn-primary btn-sm" @click="openUploadModal">
          <i class="bi bi-plus-lg me-1"></i>
          Subir imagen
        </button>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div v-if="images.data.length === 0" class="text-center text-muted py-5">
          No hay imagenes en la galeria.
        </div>

        <div v-else class="row g-3">
          <div v-for="img in images.data" :key="img.id" class="col-12 col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-img-top ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center overflow-hidden">
                <img v-if="img.path" :src="img.path" :alt="img.title || img.original_name" class="w-100 h-100" style="object-fit: cover;" />
                <i v-else class="bi bi-image text-muted fs-1"></i>
              </div>
              <div class="card-body">
                <h5 class="card-title mb-1">{{ img.title || 'Sin titulo' }}</h5>
                <p v-if="img.description" class="card-text small text-muted">{{ img.description }}</p>
                <p v-if="img.location" class="card-text small text-muted">
                  <i class="bi bi-geo-alt me-1"></i>{{ img.location.name }}
                </p>
                <div>
                  <span v-if="img.is_active" class="badge bg-success">Activa</span>
                  <span v-else class="badge bg-secondary">Inactiva</span>
                </div>
                <div class="d-flex gap-2 mt-2 flex-wrap">
                  <button
                    class="btn btn-sm btn-outline-primary"
                    @click="openEditModal(img)"
                  >
                    <i class="bi bi-pencil me-1"></i>Editar
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger"
                    @click="deleteImage(img)"
                  >
                    <i class="bi bi-trash me-1"></i>Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="images.links" class="d-flex justify-content-center mt-4">
          <Pagination :links="images.links" />
        </div>
      </div>
    </div>

    <!-- Upload Modal -->
    <div v-if="showUploadModal" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Subir imagen</h5>
            <button type="button" class="btn-close" @click="closeUploadModal"></button>
          </div>
          <form @submit.prevent="uploadImage">
            <div class="modal-body">
              <div class="mb-3">
                <label for="image-file" class="form-label">Archivo *</label>
                <input
                  id="image-file"
                  type="file"
                  class="form-control"
                  accept="image/jpeg,image/png,image/webp,image/gif"
                  @change="onFileChange"
                  :class="{ 'is-invalid': formError }"
                  required
                />
                <div v-if="formError" class="invalid-feedback d-block">{{ formError }}</div>
                <small class="text-muted">Max: 5MB. Formatos: JPEG, PNG, WebP, GIF</small>
              </div>
              <div class="mb-3">
                <label for="upload-title" class="form-label">Titulo</label>
                <input
                  id="upload-title"
                  type="text"
                  class="form-control"
                  v-model="uploadForm.title"
                  placeholder="Opcional"
                />
              </div>
              <div class="mb-3">
                <label for="upload-description" class="form-label">Descripcion</label>
                <textarea
                  id="upload-description"
                  class="form-control"
                  rows="2"
                  v-model="uploadForm.description"
                  placeholder="Opcional"
                ></textarea>
              </div>
              <div class="mb-3">
                <label for="upload-location" class="form-label">Ubicacion</label>
                <select id="upload-location" class="form-select" v-model="uploadForm.business_location_id">
                  <option :value="null">Sin ubicacion</option>
                  <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeUploadModal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="uploading || !uploadForm.file">
                {{ uploading ? 'Subiendo...' : 'Subir' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="showEditModal" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar imagen</h5>
            <button type="button" class="btn-close" @click="closeEditModal"></button>
          </div>
          <form @submit.prevent="saveEdit">
            <div class="modal-body">
              <div class="mb-3 text-center">
                <img v-if="editForm.path" :src="editForm.path" class="img-thumbnail" style="max-height: 200px;" :alt="editForm.title" />
              </div>
              <div class="mb-3">
                <label for="edit-title" class="form-label">Titulo</label>
                <input
                  id="edit-title"
                  type="text"
                  class="form-control"
                  v-model="editForm.title"
                />
              </div>
              <div class="mb-3">
                <label for="edit-description" class="form-label">Descripcion</label>
                <textarea
                  id="edit-description"
                  class="form-control"
                  rows="2"
                  v-model="editForm.description"
                ></textarea>
              </div>
              <div class="mb-3">
                <label for="edit-location" class="form-label">Ubicacion</label>
                <select id="edit-location" class="form-select" v-model="editForm.business_location_id">
                  <option :value="null">Sin ubicacion</option>
                  <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                </select>
              </div>
              <div class="row">
                <div class="col-6">
                  <label for="edit-sort" class="form-label">Orden</label>
                  <input
                    id="edit-sort"
                    type="number"
                    class="form-control"
                    v-model="editForm.sort_order"
                    min="0"
                  />
                </div>
                <div class="col-6 d-flex align-items-end">
                  <div class="form-check mb-3 w-100">
                    <input type="checkbox" v-model="editForm.is_active" class="form-check-input" id="edit-active" />
                    <label class="form-check-label" for="edit-active">Activo</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeEditModal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Guardando...' : 'Guardar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const page = usePage()
const business = computed(() => page.props.business)
const images = computed(() => page.props.images || { data: [], links: [] })
const locations = computed(() => page.props.locations || [])

const showUploadModal = ref(false)
const showEditModal = ref(false)
const uploading = ref(false)
const saving = ref(false)
const formError = ref('')

const uploadForm = reactive({
  file: null,
  title: '',
  description: '',
  business_location_id: null,
})

const editForm = reactive({
  id: null,
  path: '',
  title: '',
  description: '',
  business_location_id: null,
  sort_order: 0,
  is_active: true,
})

const resetUploadForm = () => {
  uploadForm.file = null
  uploadForm.title = ''
  uploadForm.description = ''
  uploadForm.business_location_id = null
  formError.value = ''
}

const openUploadModal = () => {
  resetUploadForm()
  showUploadModal.value = true
}

const closeUploadModal = () => {
  showUploadModal.value = false
  resetUploadForm()
}

const openEditModal = (img) => {
  editForm.id = img.id
  editForm.path = img.path
  editForm.title = img.title || ''
  editForm.description = img.description || ''
  editForm.business_location_id = img.business_location_id
  editForm.sort_order = img.sort_order || 0
  editForm.is_active = img.is_active
  showEditModal.value = true
}

const closeEditModal = () => {
  showEditModal.value = false
}

const onFileChange = (e) => {
  formError.value = ''
  const file = e.target.files[0]
  if (!file) return

  const maxSize = 5 * 1024 * 1024
  if (file.size > maxSize) {
    formError.value = 'El archivo supera el tamaño máximo de 5MB.'
    return
  }

  const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
  if (!allowedTypes.includes(file.type)) {
    formError.value = 'Solo se permiten imágenes (JPEG, PNG, WebP, GIF).'
    return
  }

  uploadForm.file = file
}

const uploadImage = () => {
  if (!uploadForm.file) return

  uploading.value = true
  formError.value = ''

  const formData = new FormData()
  formData.append('file', uploadForm.file)
  formData.append('title', uploadForm.title)
  formData.append('description', uploadForm.description)
  if (uploadForm.business_location_id) {
    formData.append('business_location_id', uploadForm.business_location_id)
  }

  router.post(`/admin/businesses/${business.value.id}/gallery`, formData, {
    preserveScroll: true,
    onFinish: () => {
      uploading.value = false
      closeUploadModal()
    },
    onError: (errors) => {
      uploading.value = false
      if (errors.file) {
        formError.value = errors.file
      }
    },
  })
}

const saveEdit = () => {
  saving.value = true

  router.put(`/admin/businesses/${business.value.id}/gallery/${editForm.id}`, {
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
    router.delete(`/admin/businesses/${business.value.id}/gallery/${img.id}`, {
      preserveScroll: true,
    })
  }
}
</script>

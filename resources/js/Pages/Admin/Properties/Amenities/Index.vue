<template>
  <AdminLayout>
    <Head title="Amenidades" />

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <Link href="/admin/modules/properties/types" class="btn btn-outline-secondary btn-sm mb-2">
          <i class="bi bi-arrow-left me-1"></i>
          Volver a Tipos
        </Link>
        <h1 class="h3 mb-0">Amenidades de Propiedades</h1>
      </div>
      <button @click="openCreateModal" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva Amenidad
      </button>
    </div>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body p-0">
        <div v-if="amenities.length === 0" class="text-center py-5 text-muted">
          <i class="bi bi-star" style="font-size: 2rem;"></i>
          <p class="mt-2">No hay amenidades creadas.</p>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th style="width: 40px;"></th>
                <th>Nombre</th>
                <th>Key</th>
                <th>Icono</th>
                <th style="width: 100px;">Estado</th>
                <th style="width: 120px;"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="amenity in amenities" :key="amenity.id">
                <td>
                  <i class="bi bi-grip-vertical text-muted" style="cursor: grab;"></i>
                </td>
                <td>
                  <strong>{{ amenity.name }}</strong>
                </td>
                <td>
                  <code class="small">{{ amenity.key }}</code>
                </td>
                <td>
                  <i :class="amenity.icon || 'bi bi-star'" style="font-size: 1.2rem;"></i>
                </td>
                <td>
                  <span class="badge" :class="amenity.is_active ? 'bg-success' : 'bg-secondary'">
                    {{ amenity.is_active ? 'Activa' : 'Inactiva' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <button @click="openEditModal(amenity)" class="btn btn-outline-secondary">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button @click="deleteAmenity(amenity)" class="btn btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="amenityModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingAmenity ? 'Editar Amenidad' : 'Nueva Amenidad' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="saveAmenity">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input v-model="form.name" type="text" class="form-control" required placeholder="Ej: Alberca">
              </div>
              <div class="mb-3">
                <label class="form-label">Icono (Bootstrap Icons)</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i :class="form.icon || 'bi bi-star'"></i>
                  </span>
                  <input v-model="form.icon" type="text" class="form-control" placeholder="bi bi-water">
                </div>
                <small class="text-muted">Usa clases de Bootstrap Icons, ej: <code>bi-water</code>, <code>bi-house</code></small>
              </div>
              <div class="mb-3">
                <label class="form-check">
                  <input v-model="form.is_active" type="checkbox" class="form-check-input">
                  <span class="form-check-label">Activa</span>
                </label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">
                {{ editingAmenity ? 'Guardar' : 'Crear' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  amenities: { type: Array, default: () => [] },
})

const editingAmenity = ref(null)
const form = reactive({
  name: '',
  icon: '',
  is_active: true,
})

let modalInstance = null

const openCreateModal = () => {
  editingAmenity.value = null
  form.name = ''
  form.icon = ''
  form.is_active = true
  getModal().show()
}

const openEditModal = (amenity) => {
  editingAmenity.value = amenity
  form.name = amenity.name
  form.icon = amenity.icon || ''
  form.is_active = amenity.is_active
  getModal().show()
}

const getModal = () => {
  if (!modalInstance) {
    modalInstance = new Modal(document.getElementById('amenityModal'))
  }
  return modalInstance
}

const saveAmenity = () => {
  if (editingAmenity.value) {
    router.put(`/admin/modules/properties/amenities/${editingAmenity.value.id}`, form, {
      preserveScroll: true,
      onSuccess: () => getModal().hide(),
    })
  } else {
    router.post('/admin/modules/properties/amenities', form, {
      preserveScroll: true,
      onSuccess: () => getModal().hide(),
    })
  }
}

const deleteAmenity = (amenity) => {
  if (!confirm(`¿Eliminar la amenidad "${amenity.name}"?`)) return
  router.delete(`/admin/modules/properties/amenities/${amenity.id}`, {
    preserveScroll: true,
  })
}
</script>

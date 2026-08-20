<template>
  <AdminLayout>
    <Head title="Servicios" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Servicios</h1>
          <p class="text-muted mb-0">{{ listing.name }}</p>
        </div>
        <button class="btn btn-primary" @click="openCreateModal">
          <i class="bi bi-plus me-1"></i>Nuevo servicio
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div v-if="services.length === 0" class="text-center py-5">
            <i class="bi bi-briefcase display-1 text-muted"></i>
            <p class="text-muted mt-3">No hay servicios creados aún.</p>
            <button class="btn btn-primary" @click="openCreateModal">Crear primer servicio</button>
          </div>

          <div v-else class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Precio</th>
                  <th>Duración</th>
                  <th>Reservas online</th>
                  <th>Activo</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="service in services" :key="service.id">
                  <td>
                    <strong>{{ service.name }}</strong>
                    <p v-if="service.description" class="text-muted small mb-0">{{ service.description.substring(0, 80) }}...</p>
                  </td>
                  <td v-format-price="service.price"></td>
                  <td>{{ service.duration_minutes }} min</td>
                  <td>
                    <span v-if="service.allows_online_booking" class="badge bg-success">Sí</span>
                    <span v-else class="badge bg-secondary">No</span>
                  </td>
                  <td>
                    <span v-if="service.is_active" class="badge bg-success">Activo</span>
                    <span v-else class="badge bg-secondary">Inactivo</span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary me-1" @click="openEditModal(service)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" @click="deleteService(service)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div ref="modalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingService ? 'Editar servicio' : 'Nuevo servicio' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitService">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label">Nombre del servicio</label>
                  <input v-model="form.name" type="text" class="form-control" required>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Precio</label>
                  <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input v-model.number="form.price" type="number" step="0.01" min="0" class="form-control">
                  </div>
                </div>

                <div class="col-12">
                  <label class="form-label">Descripción</label>
                  <textarea v-model="form.description" class="form-control" rows="3"></textarea>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Duración (minutos)</label>
                  <input v-model.number="form.duration_minutes" type="number" min="1" max="1440" class="form-control" required>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Depósito requerido</label>
                  <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input v-model.number="form.deposit_amount" type="number" step="0.01" min="0" class="form-control">
                  </div>
                </div>

                <div class="col-md-4">
                  <label class="form-label">WhatsApp</label>
                  <input v-model="form.whatsapp_contact" type="text" class="form-control" placeholder="+54 9 11 1234-5678">
                </div>

                <div class="col-md-6">
                  <div class="form-check form-switch mt-4">
                    <input v-model="form.allows_online_booking" class="form-check-input" type="checkbox" id="allowBooking">
                    <label class="form-check-label" for="allowBooking">Permitir reservas online</label>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-check form-switch mt-4">
                    <input v-model="form.is_active" class="form-check-input" type="checkbox" id="isActive">
                    <label class="form-check-label" for="isActive">Servicio activo</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                <span v-if="sending">Guardando...</span>
                <span v-else>{{ editingService ? 'Actualizar' : 'Crear' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, onMounted, nextTick } from 'vue'
import { Modal } from 'bootstrap'

const props = defineProps({
  listing: Object,
  services: Array,
})

const modalElement = ref(null)
let serviceModal = null
let editingService = ref(null)
let sending = false

const form = useForm({
  name: '',
  description: '',
  duration_minutes: 30,
  price: null,
  deposit_required: false,
  deposit_amount: null,
  allows_online_booking: true,
  whatsapp_contact: '',
  is_active: true,
  sort_order: 0,
})

const openCreateModal = () => {
  editingService.value = null
  form.reset()
  form.duration_minutes = 30
  form.allows_online_booking = true
  form.is_active = true
  nextTick(() => {
    serviceModal.show()
  })
}

const openEditModal = (service) => {
  editingService.value = service
  form.name = service.name
  form.description = service.description || ''
  form.duration_minutes = service.duration_minutes
  form.price = service.price
  form.deposit_required = service.deposit_required
  form.deposit_amount = service.deposit_amount
  form.allows_online_booking = service.allows_online_booking
  form.whatsapp_contact = service.whatsapp_contact || ''
  form.is_active = service.is_active
  form.sort_order = service.sort_order || 0
  nextTick(() => {
    serviceModal.show()
  })
}

const submitService = () => {
  sending = true
  if (editingService.value) {
    form.post(`/admin/listings/${props.listing.id}/services/${editingService.value.id}`, {
      onFinish: () => {
        sending = false
        serviceModal.hide()
      },
    })
  } else {
    form.post(`/admin/listings/${props.listing.id}/services`, {
      onFinish: () => {
        sending = false
        serviceModal.hide()
      },
    })
  }
}

const deleteService = (service) => {
  if (confirm('¿Estás seguro de eliminar este servicio?')) {
    router.delete(`/admin/listings/${props.listing.id}/services/${service.id}`, {
      preserveScroll: true,
    })
  }
}

onMounted(() => {
  serviceModal = new Modal(modalElement.value)
})
</script>

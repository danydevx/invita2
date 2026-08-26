<template>
  <MemberLayout>
    <Head :title="`Paquetes - ${vcard?.name || ''}`" />

    <PageHeader
      :title="`Paquetes - ${vcard?.name || ''}`"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/vcards/${vcard?.id}/edit`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
          {{ $page.props.flash.success }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <p class="text-muted mb-0">Administra los paquetes de tu vCard</p>
          <button class="btn btn-primary btn-sm" @click="showCreateModal = true">
            <i class="bi bi-plus-lg me-1"></i>
            Nuevo Paquete
          </button>
        </div>

        <div v-if="packages.length === 0" class="text-center py-5">
          <i class="bi bi-box-seam display-1 text-muted"></i>
          <p class="text-muted mt-3">No hay paquetes creados</p>
        </div>

        <div v-else class="row g-3">
          <div v-for="pkg in packages" :key="pkg.id" class="col-12 col-md-6 col-lg-4">
            <div class="card h-100">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h6 class="mb-0">{{ pkg.name }}</h6>
                  <span v-if="pkg.active" class="badge bg-success">Activo</span>
                  <span v-else class="badge bg-secondary">Inactivo</span>
                </div>
                <p v-if="pkg.description" class="text-muted small mb-2">{{ pkg.description }}</p>
                <div v-if="pkg.price" class="mb-2">
                  <span class="h5 text-primary mb-0">{{ formatPrice(pkg.price, pkg.currency) }}</span>
                  <span v-if="pkg.duration_days" class="text-muted small"> / {{ pkg.duration_days }} dias</span>
                </div>
                <div class="d-flex gap-2">
                  <button class="btn btn-outline-primary btn-sm" @click="editPackage(pkg)">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-sm" @click="deletePackage(pkg.id)">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showCreateModal || editingPackage" class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" @click.self="closeModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingPackage ? 'Editar Paquete' : 'Nuevo Paquete' }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <form @submit.prevent="savePackage">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Nombre *</label>
                <input v-model="form.name" type="text" class="form-control" required />
                <div v-if="errors.name" class="text-danger small mt-1">{{ errors.name }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <textarea v-model="form.description" class="form-control" rows="2"></textarea>
              </div>
              <div class="row">
                <div class="col-6">
                  <div class="mb-3">
                    <label class="form-label">Precio</label>
                    <input v-model.number="form.price" type="number" step="0.01" min="0" class="form-control" />
                  </div>
                </div>
                <div class="col-6">
                  <div class="mb-3">
                    <label class="form-label">Moneda</label>
                    <select v-model="form.currency" class="form-select">
                      <option value="USD">USD</option>
                      <option value="EUR">EUR</option>
                      <option value="MXN">MXN</option>
                      <option value="COP">COP</option>
                      <option value="ARS">ARS</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Duracion (dias)</label>
                <input v-model.number="form.duration_days" type="number" min="1" class="form-control" placeholder="Ej: 30" />
              </div>
              <div class="mb-3 form-check">
                <input v-model="form.active" class="form-check-input" type="checkbox" id="packageActive" />
                <label class="form-check-label" for="packageActive">Activo</label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal">Cancelar</button>
              <button type="submit" class="btn btn-primary">{{ editingPackage ? 'Actualizar' : 'Crear' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const vcard = computed(() => page.props.vcard)
const packages = computed(() => page.props.packages || [])

const showCreateModal = ref(false)
const editingPackage = ref(null)
const errors = ref({})

const form = reactive({
  name: '',
  description: '',
  price: null,
  currency: 'USD',
  duration_days: null,
  active: true,
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: listing.value?.name || 'Negocio', href: `/member/listings/${listing.value?.id}/modules` },
  { label: 'vCards', href: `/member/listings/${listing.value?.id}/vcards` },
  { label: vcard.value?.name || 'Tarjeta', href: `/member/listings/${listing.value?.id}/vcards/${vcard.value?.id}/edit` },
  { label: 'Paquetes', active: true },
])

function formatPrice(price, currency) {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: currency || 'USD' }).format(price)
}

function editPackage(pkg) {
  editingPackage.value = pkg
  form.name = pkg.name
  form.description = pkg.description || ''
  form.price = pkg.price ? parseFloat(pkg.price) : null
  form.currency = pkg.currency || 'USD'
  form.duration_days = pkg.duration_days
  form.active = pkg.active
}

function closeModal() {
  showCreateModal.value = false
  editingPackage.value = null
  resetForm()
}

function resetForm() {
  form.name = ''
  form.description = ''
  form.price = null
  form.currency = 'USD'
  form.duration_days = null
  form.active = true
  errors.value = {}
}

function savePackage() {
  errors.value = {}

  if (editingPackage.value) {
    router.put(
      `/member/listings/${listing.value.id}/vcards/${vcard.value.id}/packages/${editingPackage.value.id}`,
      form,
      {
        onSuccess: () => {
          closeModal()
          router.reload({ only: ['packages'] })
        },
        onError: (err) => {
          errors.value = err
        },
      }
    )
  } else {
    router.post(
      `/member/listings/${listing.value.id}/vcards/${vcard.value.id}/packages`,
      form,
      {
        onSuccess: () => {
          closeModal()
          router.reload({ only: ['packages'] })
        },
        onError: (err) => {
          errors.value = err
        },
      }
    )
  }
}

function deletePackage(id) {
  if (!confirm('Eliminar este paquete?')) return

  router.delete(
    `/member/listings/${listing.value.id}/vcards/${vcard.value.id}/packages/${id}`,
    {
      onSuccess: () => {
        router.reload({ only: ['packages'] })
      },
    }
  )
}
</script>

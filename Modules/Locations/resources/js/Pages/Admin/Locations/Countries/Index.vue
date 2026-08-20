<template>
  <AdminLayout>
    <Head title="Países - Ubicaciones" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Países</h1>
          <p class="text-muted mb-0">Gestión de países para ubicaciones</p>
        </div>
        <button @click="showCreateModal = true" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nuevo País
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $page.props.flash.success }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Código</th>
                  <th>Nombre</th>
                  <th>Moneda</th>
                  <th>Símbolo</th>
                  <th>Estados</th>
                  <th>Activo</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="country in countries" :key="country.id">
                  <td><span class="badge bg-secondary">{{ country.code }}</span></td>
                  <td>{{ country.name }}</td>
                  <td>{{ country.currency }}</td>
                  <td>{{ country.currency_symbol }}</td>
                  <td>{{ country.states_count || 0 }}</td>
                  <td>
                    <span :class="country.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                      {{ country.is_active ? 'Sí' : 'No' }}
                    </span>
                  </td>
                  <td>
                    <button @click="editCountry = country; showEditModal = true" class="btn btn-outline-secondary btn-sm me-1">
                      <i class="bi bi-pencil"></i>
                    </button>
                  </td>
                </tr>
                <tr v-if="countries.length === 0">
                  <td colspan="7" class="text-center text-muted py-4">No hay países registrados.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showCreateModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nuevo País</h5>
            <button type="button" class="btn-close" @click="showCreateModal = false"></button>
          </div>
          <form @submit.prevent="createCountry">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Código</label>
                <input v-model="form.code" type="text" class="form-control" maxlength="5" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input v-model="form.name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Moneda</label>
                <input v-model="form.currency" type="text" class="form-control" maxlength="10" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Símbolo de Moneda</label>
                <input v-model="form.currency_symbol" type="text" class="form-control" maxlength="10" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showCreateModal = false">Cancelar</button>
              <button type="submit" class="btn btn-primary">Crear</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div v-if="showEditModal && editCountry" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar País</h5>
            <button type="button" class="btn-close" @click="showEditModal = false"></button>
          </div>
          <form @submit.prevent="updateCountry">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Código</label>
                <input v-model="editForm.code" type="text" class="form-control" maxlength="5" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input v-model="editForm.name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Moneda</label>
                <input v-model="editForm.currency" type="text" class="form-control" maxlength="10" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Símbolo de Moneda</label>
                <input v-model="editForm.currency_symbol" type="text" class="form-control" maxlength="10" required>
              </div>
              <div class="mb-3">
                <label class="form-check">
                  <input v-model="editForm.is_active" type="checkbox" class="form-check-input">
                  <span class="form-check-label">Activo</span>
                </label>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showEditModal = false">Cancelar</button>
              <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  countries: Array,
})

const showCreateModal = ref(false)
const showEditModal = ref(false)
const editCountry = ref(null)

const form = reactive({
  code: '',
  name: '',
  currency: 'MXN',
  currency_symbol: '$',
})

const editForm = reactive({
  code: '',
  name: '',
  currency: '',
  currency_symbol: '',
  is_active: true,
})

watch(editCountry, (country) => {
  if (country) {
    editForm.code = country.code
    editForm.name = country.name
    editForm.currency = country.currency
    editForm.currency_symbol = country.currency_symbol
    editForm.is_active = country.is_active
  }
})

const createCountry = () => {
  router.post('/admin/locations/countries', form, {
    onSuccess: () => {
      showCreateModal.value = false
      form.code = ''
      form.name = ''
    },
  })
}

const updateCountry = () => {
  router.put(`/admin/locations/countries/${editCountry.value.id}`, editForm, {
    onSuccess: () => {
      showEditModal.value = false
    },
  })
}
</script>

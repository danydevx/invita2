<template>
  <AdminLayout>
    <Head title="Estados - Ubicaciones" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Estados</h1>
          <p class="text-muted mb-0">Gestión de estados por país</p>
        </div>
        <button @click="showCreateModal = true" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nuevo Estado
        </button>
      </div>

      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Filtrar por país</label>
              <select v-model="selectedCountry" class="form-select" @change="filterByCountry">
                <option value="">Todos los países</option>
                <option v-for="country in countries" :key="country.id" :value="country.id">
                  {{ country.name }}
                </option>
              </select>
            </div>
          </div>
        </div>
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
                  <th>País</th>
                  <th>Municipios</th>
                  <th>Coordenadas</th>
                  <th>Activo</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="state in states" :key="state.id">
                  <td><span class="badge bg-secondary">{{ state.code }}</span></td>
                  <td>{{ state.name }}</td>
                  <td>{{ state.country?.name || 'N/A' }}</td>
                  <td>{{ state.municipalities_count || 0 }}</td>
                  <td>{{ state.lat && state.lng ? `${state.lat}, ${state.lng}` : '-' }}</td>
                  <td>
                    <span :class="state.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                      {{ state.is_active ? 'Sí' : 'No' }}
                    </span>
                  </td>
                  <td>
                    <button @click="editState = state; showEditModal = true" class="btn btn-outline-secondary btn-sm me-1">
                      <i class="bi bi-pencil"></i>
                    </button>
                  </td>
                </tr>
                <tr v-if="states.length === 0">
                  <td colspan="7" class="text-center text-muted py-4">No hay estados registrados.</td>
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
            <h5 class="modal-title">Nuevo Estado</h5>
            <button type="button" class="btn-close" @click="showCreateModal = false"></button>
          </div>
          <form @submit.prevent="createState">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">País</label>
                <select v-model="form.country_id" class="form-select" required>
                  <option value="">Seleccione un país</option>
                  <option v-for="country in countries" :key="country.id" :value="country.id">
                    {{ country.name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Código</label>
                <input v-model="form.code" type="text" class="form-control" maxlength="10" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input v-model="form.name" type="text" class="form-control" required>
              </div>
              <div class="row">
                <div class="col-6">
                  <div class="mb-3">
                    <label class="form-label">Latitud</label>
                    <input v-model="form.lat" type="number" step="any" class="form-control">
                  </div>
                </div>
                <div class="col-6">
                  <div class="mb-3">
                    <label class="form-label">Longitud</label>
                    <input v-model="form.lng" type="number" step="any" class="form-control">
                  </div>
                </div>
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

    <div v-if="showEditModal && editState" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Estado</h5>
            <button type="button" class="btn-close" @click="showEditModal = false"></button>
          </div>
          <form @submit.prevent="updateState">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">País</label>
                <select v-model="editForm.country_id" class="form-select" required>
                  <option v-for="country in countries" :key="country.id" :value="country.id">
                    {{ country.name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Código</label>
                <input v-model="editForm.code" type="text" class="form-control" maxlength="10" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input v-model="editForm.name" type="text" class="form-control" required>
              </div>
              <div class="row">
                <div class="col-6">
                  <div class="mb-3">
                    <label class="form-label">Latitud</label>
                    <input v-model="editForm.lat" type="number" step="any" class="form-control">
                  </div>
                </div>
                <div class="col-6">
                  <div class="mb-3">
                    <label class="form-label">Longitud</label>
                    <input v-model="editForm.lng" type="number" step="any" class="form-control">
                  </div>
                </div>
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
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  states: Array,
  countries: Array,
  selectedCountryId: String,
})

const selectedCountry = ref(props.selectedCountryId || '')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const editState = ref(null)

const form = reactive({
  country_id: '',
  code: '',
  name: '',
  lat: '',
  lng: '',
})

const editForm = reactive({
  country_id: '',
  code: '',
  name: '',
  lat: '',
  lng: '',
  is_active: true,
})

watch(editState, (state) => {
  if (state) {
    editForm.country_id = state.country_id
    editForm.code = state.code
    editForm.name = state.name
    editForm.lat = state.lat || ''
    editForm.lng = state.lng || ''
    editForm.is_active = state.is_active
  }
})

const filterByCountry = () => {
  router.get(route('admin.locations.states.index'), selectedCountry.value ? { country_id: selectedCountry.value } : {})
}

const createState = () => {
  router.post(route('admin.locations.states.store'), form, {
    onSuccess: () => {
      showCreateModal.value = false
      form.code = ''
      form.name = ''
      form.lat = ''
      form.lng = ''
    },
  })
}

const updateState = () => {
  router.put(route('admin.locations.states.update', { state: editState.value.id }), editForm, {
    onSuccess: () => {
      showEditModal.value = false
    },
  })
}
</script>

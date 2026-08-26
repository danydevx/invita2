<template>
  <AdminLayout>
    <Head title="Municipios - Ubicaciones" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Municipios</h1>
          <p class="text-muted mb-0">Gestión de municipios por estado</p>
        </div>
        <button @click="showCreateModal = true" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nuevo Municipio
        </button>
      </div>

      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">País</label>
              <select v-model="filters.country_id" class="form-select" @change="onCountryChange">
                <option value="">Todos</option>
                <option v-for="country in countries" :key="country.id" :value="country.id">
                  {{ country.name }}
                </option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Estado</label>
              <select v-model="filters.state_id" class="form-select" :disabled="!filters.country_id" @change="filterByState">
                <option value="">Todos</option>
                <option v-for="state in filteredStates" :key="state.id" :value="state.id">
                  {{ state.name }}
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
                  <th>Estado</th>
                  <th>País</th>
                  <th>Metropolitano</th>
                  <th>Coordenadas</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="municipality in municipalities" :key="municipality.id">
                  <td><span class="badge bg-secondary">{{ municipality.code }}</span></td>
                  <td>{{ municipality.name }}</td>
                  <td>{{ municipality.state?.name || 'N/A' }}</td>
                  <td>{{ municipality.country?.name || 'N/A' }}</td>
                  <td>
                    <span :class="municipality.is_metropolitan ? 'badge bg-primary' : 'badge bg-secondary'">
                      {{ municipality.is_metropolitan ? 'Sí' : 'No' }}
                    </span>
                  </td>
                  <td>{{ municipality.lat && municipality.lng ? `${municipality.lat}, ${municipality.lng}` : '-' }}</td>
                  <td>
                    <button @click="editMunicipality = municipality; showEditModal = true" class="btn btn-outline-secondary btn-sm me-1">
                      <i class="bi bi-pencil"></i>
                    </button>
                  </td>
                </tr>
                <tr v-if="municipalities.length === 0">
                  <td colspan="7" class="text-center text-muted py-4">No hay municipios registrados.</td>
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
            <h5 class="modal-title">Nuevo Municipio</h5>
            <button type="button" class="btn-close" @click="showCreateModal = false"></button>
          </div>
          <form @submit.prevent="createMunicipality">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">País</label>
                <select v-model="form.country_id" class="form-select" required @change="onFormCountryChange">
                  <option value="">Seleccione un país</option>
                  <option v-for="country in countries" :key="country.id" :value="country.id">
                    {{ country.name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Estado</label>
                <select v-model="form.state_id" class="form-select" :disabled="!form.country_id" required>
                  <option value="">Seleccione un estado</option>
                  <option v-for="state in formStates" :key="state.id" :value="state.id">
                    {{ state.name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Código</label>
                <input v-model="form.code" type="text" class="form-control" maxlength="20" required>
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
              <div class="mb-3">
                <label class="form-check">
                  <input v-model="form.is_metropolitan" type="checkbox" class="form-check-input">
                  <span class="form-check-label">Zona Metropolitana</span>
                </label>
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

    <div v-if="showEditModal && editMunicipality" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar Municipio</h5>
            <button type="button" class="btn-close" @click="showEditModal = false"></button>
          </div>
          <form @submit.prevent="updateMunicipality">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">País</label>
                <select v-model="editForm.country_id" class="form-select" required @change="onEditCountryChange">
                  <option v-for="country in countries" :key="country.id" :value="country.id">
                    {{ country.name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Estado</label>
                <select v-model="editForm.state_id" class="form-select" :disabled="!editForm.country_id" required>
                  <option v-for="state in editFormStates" :key="state.id" :value="state.id">
                    {{ state.name }}
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Código</label>
                <input v-model="editForm.code" type="text" class="form-control" maxlength="20" required>
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
                  <input v-model="editForm.is_metropolitan" type="checkbox" class="form-check-input">
                  <span class="form-check-label">Zona Metropolitana</span>
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
import { ref, reactive, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  municipalities: Array,
  countries: Array,
  states: Array,
  selectedCountryId: String,
  selectedStateId: String,
})

const filters = reactive({
  country_id: props.selectedCountryId || '',
  state_id: props.selectedStateId || '',
})

const showCreateModal = ref(false)
const showEditModal = ref(false)
const editMunicipality = ref(null)

const form = reactive({
  country_id: '',
  state_id: '',
  code: '',
  name: '',
  lat: '',
  lng: '',
  is_metropolitan: false,
})

const editForm = reactive({
  country_id: '',
  state_id: '',
  code: '',
  name: '',
  lat: '',
  lng: '',
  is_metropolitan: false,
})

const formStates = computed(() => {
  if (!form.country_id) return []
  return props.states.filter(s => s.country_id === parseInt(form.country_id))
})

const editFormStates = computed(() => {
  if (!editForm.country_id) return []
  return props.states.filter(s => s.country_id === parseInt(editForm.country_id))
})

const filteredStates = computed(() => {
  if (!filters.country_id) return props.states
  return props.states.filter(s => s.country_id === parseInt(filters.country_id))
})

const onCountryChange = () => {
  filters.state_id = ''
  filterByState()
}

const filterByState = () => {
  const params = {}
  if (filters.country_id) params.country_id = filters.country_id
  if (filters.state_id) params.state_id = filters.state_id
  router.get(route('admin.locations.municipalities.index'), params)
}

const onFormCountryChange = () => {
  form.state_id = ''
}

const onEditCountryChange = () => {
  editForm.state_id = ''
}

watch(editMunicipality, (municipality) => {
  if (municipality) {
    editForm.country_id = municipality.country_id || municipality.country?.id
    editForm.state_id = municipality.state_id || municipality.state?.id
    editForm.code = municipality.code
    editForm.name = municipality.name
    editForm.lat = municipality.lat || ''
    editForm.lng = municipality.lng || ''
    editForm.is_metropolitan = municipality.is_metropolitan
  }
})

const createMunicipality = () => {
  router.post(route('admin.locations.municipalities.store'), form, {
    onSuccess: () => {
      showCreateModal.value = false
      form.code = ''
      form.name = ''
      form.lat = ''
      form.lng = ''
    },
  })
}

const updateMunicipality = () => {
  router.put(route('admin.locations.municipalities.update', { municipality: editMunicipality.value.id }), editForm, {
    onSuccess: () => {
      showEditModal.value = false
    },
  })
}
</script>

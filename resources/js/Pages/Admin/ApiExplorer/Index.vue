<template>
  <AdminLayout>
    <Head title="API Explorer" />

    <PageHeader title="API Explorer" :breadcrumbs="breadcrumbs" backHref="/admin/dashboard" />

    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6 mb-3">Endpoints</h3>
            <div v-for="(group, key) in endpoints" :key="key" class="mb-3">
              <div class="fw-semibold text-primary small mb-2">{{ group.title }}</div>
              <div v-for="ep in group.endpoints" :key="ep.path" class="mb-1">
                <button
                  @click="selectEndpoint(ep, key)"
                  class="btn btn-sm w-100 text-start"
                  :class="selectedEndpoint?.path === ep.path ? 'btn-primary' : 'btn-outline-secondary'"
                >
                  <span class="badge bg-dark me-1">{{ ep.method }}</span>
                  <code class="small">{{ ep.path }}</code>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6 mb-3">Probar Endpoint</h3>

            <div v-if="!selectedEndpoint" class="text-muted">
              Selecciona un endpoint para probarlo.
            </div>

            <div v-else>
              <div class="bg-dark text-light p-3 rounded mb-3">
                <span class="badge bg-light text-dark me-2">{{ selectedEndpoint.method }}</span>
                <code>{{ selectedEndpoint.path }}</code>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Description</label>
                <div class="text-muted">{{ selectedEndpoint.description }}</div>
              </div>

              <div v-if="selectedEndpoint.path.includes('{id}') || selectedEndpoint.path.includes('{business}')" class="mb-3">
                <label class="form-label small fw-semibold">Business ID</label>
                <select v-model="selectedBusinessId" class="form-select">
                  <option value="">Seleccionar negocio...</option>
                  <option v-for="biz in businesses" :key="biz.id" :value="biz.id">
                    {{ biz.name }} (ID: {{ biz.id }})
                  </option>
                </select>
              </div>

              <div v-if="selectedEndpoint.path.includes('{user}')" class="mb-3">
                <label class="form-label small fw-semibold">User ID</label>
                <input v-model="selectedUserId" type="number" class="form-control" placeholder="User ID" />
              </div>

              <button @click="fetchData" class="btn btn-primary" :disabled="loading">
                <span v-if="loading">Cargando...</span>
                <span v-else>Ejecutar</span>
              </button>

              <div v-if="error" class="alert alert-danger mt-3 mb-0">
                {{ error }}
              </div>

              <div v-if="response" class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="small fw-semibold">Response ({{ response.status }})</span>
                  <button @click="copyResponse" class="btn btn-sm btn-outline-secondary">
                    Copiar
                  </button>
                </div>
                <pre class="bg-dark text-light p-3 rounded overflow-auto" style="max-height: 400px;"><code>{{ JSON.stringify(response.body, null, 2) }}</code></pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6 mb-3">Informacion</h3>
            <div class="row g-3">
              <div class="col-12 col-md-4">
                <div class="small text-muted mb-1">Autenticacion</div>
                <code>Authorization: Bearer &#123;api_key&#125;</code>
              </div>
              <div class="col-12 col-md-4">
                <div class="small text-muted mb-1">Solo lectura</div>
                <div>Solo GET requests disponibles</div>
              </div>
              <div class="col-12 col-md-4">
                <div class="small text-muted mb-1">Paginacion</div>
                <div>20 items por pagina (param: per_page)</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  endpoints: {
    type: Object,
    required: true,
  },
  businesses: {
    type: Array,
    default: () => [],
  },
  baseUrl: {
    type: String,
    required: true,
  },
  fetchData: {
    type: Object,
    default: null,
  },
  fetchError: {
    type: String,
    default: null,
  },
})

const breadcrumbs = [
  { label: 'Dashboard', href: '/admin/dashboard' },
  { label: 'API Explorer', active: true },
]

const selectedEndpoint = ref(null)
const selectedGroup = ref(null)
const selectedBusinessId = ref('')
const selectedUserId = ref('')
const loading = ref(false)
const error = ref(null)
const response = ref(null)

const selectEndpoint = (endpoint, group) => {
  selectedEndpoint.value = endpoint
  selectedGroup.value = group
  response.value = null
  error.value = null
}

const fetchData = () => {
  if (!selectedEndpoint.value) return

  loading.value = true
  error.value = null
  response.value = null

  router.post(
    '/admin/api-explorer/fetch',
    {
      path: selectedEndpoint.value.path,
      business_id: selectedBusinessId.value || null,
      user_id: selectedUserId.value || null,
    },
    {
      preserveScroll: true,
      onSuccess: () => {
        loading.value = false
      },
      onError: (errors) => {
        loading.value = false
        error.value = errors.message || 'Error fetching data'
      },
    }
  )
}

watch(
  () => props.fetchData,
  (newVal) => {
    if (newVal) {
      response.value = newVal
      loading.value = false
    }
  }
)

watch(
  () => props.fetchError,
  (newVal) => {
    if (newVal) {
      error.value = newVal
      loading.value = false
    }
  }
)

const copyResponse = () => {
  if (response.value) {
    navigator.clipboard.writeText(JSON.stringify(response.value.body, null, 2))
  }
}
</script>

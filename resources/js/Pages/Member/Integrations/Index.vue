<template>
  <MemberLayout>
    <Head title="Integraciones" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Integraciones</h1>
        <p class="text-muted mb-0">Centraliza API keys, webhooks y documentacion basica.</p>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h2 class="h6">Estado de integraciones</h2>
            <div class="d-flex flex-column gap-2 text-muted">
              <div>API keys activas: <span class="fw-semibold">{{ stats.active_api_keys }}</span></div>
              <div>Webhooks activos: <span class="fw-semibold">{{ stats.active_webhooks }}</span></div>
            </div>
            <div class="mt-3">
              <h3 class="h6">Ultimos eventos</h3>
              <div v-if="recentDeliveries.length === 0" class="text-muted small">Sin entregas recientes.</div>
              <ul v-else class="list-group list-group-flush">
                <li v-for="delivery in recentDeliveries" :key="delivery.id" class="list-group-item px-0">
                  <div class="fw-semibold">{{ delivery.event }}</div>
                  <div class="small text-muted">
                    {{ delivery.response_status || 'Sin respuesta' }} · {{ delivery.delivered_at || delivery.failed_at || '-' }}
                  </div>
                </li>
              </ul>
            </div>
            <div class="mt-3">
              <h3 class="h6">Errores recientes</h3>
              <div v-if="recentErrors.length === 0" class="text-muted small">Sin errores recientes.</div>
              <ul v-else class="list-group list-group-flush">
                <li v-for="error in recentErrors" :key="error.id" class="list-group-item px-0">
                  <div class="fw-semibold">{{ error.type }}</div>
                  <div class="small text-muted">{{ error.message }}</div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
              <div>
                <h2 class="h6 mb-1">API Keys</h2>
                <p class="text-muted mb-0">Gestiona claves para integraciones externas.</p>
              </div>
              <Link href="/member/api-keys" class="btn btn-sm btn-outline-secondary">Administrar</Link>
            </div>

            <div v-if="!canUseApi" class="alert alert-warning mt-3">
              Las integraciones API no estan disponibles en su plan.
            </div>

            <div v-else class="mt-3">
              <form class="row g-2" @submit.prevent="submitApiKey">
                <div class="col-12 col-md-5">
                  <label class="form-label">Nombre</label>
                  <input
                    v-model="apiKeyForm.name"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': apiKeyForm.errors.name }"
                    placeholder="Ej: Integracion CRM"
                    required
                  />
                  <div v-if="apiKeyForm.errors.name" class="invalid-feedback">
                    {{ apiKeyForm.errors.name }}
                  </div>
                </div>
                <div class="col-12 col-md-3">
                  <label class="form-label">Expira (opcional)</label>
                  <input
                    v-model="apiKeyForm.expires_at"
                    type="date"
                    class="form-control"
                    :class="{ 'is-invalid': apiKeyForm.errors.expires_at }"
                  />
                  <div v-if="apiKeyForm.errors.expires_at" class="invalid-feedback">
                    {{ apiKeyForm.errors.expires_at }}
                  </div>
                </div>
                <div class="col-12 col-md-4 d-flex align-items-end">
                  <button class="btn btn-primary w-100" type="submit" :disabled="apiKeyForm.processing">
                    {{ apiKeyForm.processing ? 'Creando...' : 'Crear API key' }}
                  </button>
                </div>
              </form>

              <div class="table-responsive mt-3">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th scope="col">Nombre</th>
                      <th scope="col">Prefijo</th>
                      <th scope="col">Estado</th>
                      <th scope="col">Ultimo uso</th>
                      <th scope="col">Metadata</th>
                      <th scope="col" class="text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="apiKeys.length === 0">
                      <td colspan="6" class="text-center text-muted py-3">No hay API keys creadas.</td>
                    </tr>
                    <tr v-for="key in apiKeys" :key="key.id">
                      <td class="fw-semibold">{{ key.name }}</td>
                      <td class="text-muted">{{ key.key_prefix || '-' }}</td>
                      <td>
                        <span v-if="key.revoked_at" class="badge text-bg-secondary">Revocada</span>
                        <span v-else-if="key.is_active" class="badge text-bg-success">Activa</span>
                        <span v-else class="badge text-bg-warning">Inactiva</span>
                      </td>
                      <td class="text-muted">{{ key.last_used_at || '-' }}</td>
                      <td class="text-muted">
                        <span v-if="key.metadata">{{ JSON.stringify(key.metadata) }}</span>
                        <span v-else>-</span>
                      </td>
                      <td class="text-end">
                        <div class="d-inline-flex gap-2">
                          <button
                            v-if="!key.revoked_at"
                            class="btn btn-sm btn-outline-primary"
                            type="button"
                            @click="toggleKey(key)"
                          >
                            {{ key.is_active ? 'Desactivar' : 'Activar' }}
                          </button>
                          <button
                            class="btn btn-sm btn-outline-danger"
                            type="button"
                            :disabled="!!key.revoked_at"
                            @click="revokeKey(key)"
                          >
                            Revocar
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
              <div>
                <h2 class="h6 mb-1">Webhooks</h2>
                <p class="text-muted mb-0">Recibe eventos en tus sistemas externos.</p>
              </div>
              <Link href="/member/webhooks" class="btn btn-sm btn-outline-secondary">Administrar</Link>
            </div>

            <div v-if="!canUseWebhooks" class="alert alert-warning mt-3">
              Las integraciones de webhooks no estan disponibles en su plan.
            </div>

            <div v-else class="mt-3">
              <form class="row g-2" @submit.prevent="submitWebhook">
                <div class="col-12 col-md-4">
                  <label class="form-label">Nombre</label>
                  <input v-model="webhookForm.name" type="text" class="form-control" :class="{ 'is-invalid': webhookForm.errors.name }" />
                  <div v-if="webhookForm.errors.name" class="invalid-feedback">{{ webhookForm.errors.name }}</div>
                </div>
                <div class="col-12 col-md-5">
                  <label class="form-label">URL</label>
                  <input v-model="webhookForm.url" type="url" class="form-control" :class="{ 'is-invalid': webhookForm.errors.url }" />
                  <div v-if="webhookForm.errors.url" class="invalid-feedback">{{ webhookForm.errors.url }}</div>
                </div>
                <div class="col-12 col-md-3">
                  <label class="form-label">Activo</label>
                  <select v-model="webhookForm.is_active" class="form-select">
                    <option :value="true">Si</option>
                    <option :value="false">No</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Eventos</label>
                  <div class="d-flex flex-wrap gap-2">
                    <label v-for="event in availableEvents" :key="event" class="form-check">
                      <input class="form-check-input" type="checkbox" :value="event" v-model="webhookForm.events" />
                      <span class="form-check-label">{{ event }}</span>
                    </label>
                  </div>
                  <div v-if="webhookForm.errors.events" class="text-danger small mt-1">{{ webhookForm.errors.events }}</div>
                </div>
                <div class="col-12">
                  <button class="btn btn-primary" type="submit" :disabled="webhookForm.processing">
                    {{ webhookForm.processing ? 'Guardando...' : 'Crear webhook' }}
                  </button>
                </div>
              </form>

              <div class="table-responsive mt-3">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th scope="col">Nombre</th>
                      <th scope="col">URL</th>
                      <th scope="col">Eventos</th>
                      <th scope="col">Estado</th>
                      <th scope="col">Ultima entrega</th>
                      <th scope="col" class="text-end">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="webhooks.length === 0">
                      <td colspan="6" class="text-center text-muted py-3">No hay webhooks registrados.</td>
                    </tr>
                    <tr v-for="endpoint in webhooks" :key="endpoint.id">
                      <td class="fw-semibold">{{ endpoint.name }}</td>
                      <td class="text-muted">{{ endpoint.url }}</td>
                      <td class="text-muted">{{ endpoint.events.join(', ') }}</td>
                      <td>
                        <span v-if="endpoint.is_active" class="badge text-bg-success">Activo</span>
                        <span v-else class="badge text-bg-secondary">Inactivo</span>
                      </td>
                      <td class="text-muted">
                        <span v-if="endpoint.last_delivery">
                          {{ endpoint.last_delivery.event }} · {{ endpoint.last_delivery.delivered_at || endpoint.last_delivery.failed_at || '-' }}
                        </span>
                        <span v-else>-</span>
                      </td>
                      <td class="text-end">
                        <div class="d-inline-flex gap-2">
                          <Link :href="`/member/webhooks/${endpoint.id}/deliveries`" class="btn btn-sm btn-outline-secondary">
                            Entregas
                          </Link>
                          <button class="btn btn-sm btn-outline-info" type="button" @click="sendTest(endpoint)">Probar</button>
                          <Link :href="`/member/webhooks`" class="btn btn-sm btn-outline-primary">Editar</Link>
                          <button class="btn btn-sm btn-outline-danger" type="button" @click="removeWebhook(endpoint)">Eliminar</button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h6">Documentacion basica</h2>
            <p class="text-muted">Autenticacion API mediante header Bearer.</p>
            <pre class="bg-light border rounded p-3"><code>curl {{ apiBase }}/api/me \
  -H "Authorization: Bearer YOUR_API_KEY"</code></pre>

            <p class="text-muted">Ejemplo de respuesta:</p>
            <pre class="bg-light border rounded p-3"><code>{
  "id": 10,
  "name": "Juan Perez",
  "email": "juan@email.com"
}</code></pre>

            <p class="text-muted">Eventos webhook:</p>
            <pre class="bg-light border rounded p-3"><code>{
  "event": "payment.succeeded",
  "data": {
    "...": "..."
  }
}</code></pre>

            <p class="text-muted">Firma webhook:</p>
            <pre class="bg-light border rounded p-3"><code>X-Signature: hmac_sha256(secret, payload)</code></pre>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  canUseApi: {
    type: Boolean,
    default: false,
  },
  canUseWebhooks: {
    type: Boolean,
    default: false,
  },
  apiKeys: {
    type: Array,
    default: () => [],
  },
  webhooks: {
    type: Array,
    default: () => [],
  },
  availableEvents: {
    type: Array,
    default: () => [],
  },
  appUrl: {
    type: String,
    default: '',
  },
  stats: {
    type: Object,
    default: () => ({ active_api_keys: 0, active_webhooks: 0 }),
  },
  recentDeliveries: {
    type: Array,
    default: () => [],
  },
  recentErrors: {
    type: Array,
    default: () => [],
  },
})

const apiBase = computed(() => props.appUrl || 'https://api.tusistema.com')

// Formulario para crear API keys desde el portal.
const apiKeyForm = useForm({
  name: '',
  expires_at: '',
})

// Formulario para crear webhooks desde el portal.
const webhookForm = useForm({
  name: '',
  url: '',
  events: [],
  is_active: true,
})

// Envia el formulario para crear una API key usando las rutas existentes.
const submitApiKey = () => {
  apiKeyForm.post('/member/api-keys', {
    preserveScroll: true,
    onSuccess: () => apiKeyForm.reset('name', 'expires_at'),
  })
}

// Cambia el estado de una API key existente.
const toggleKey = (key) => {
  router.put(`/member/api-keys/${key.id}`, {
    name: key.name,
    is_active: !key.is_active,
    expires_at: key.expires_at || null,
  })
}

// Revoca una API key y evita su uso posterior.
const revokeKey = (key) => {
  if (!confirm('Vas a revocar esta API key. Esta accion no se puede deshacer.')) return
  router.delete(`/member/api-keys/${key.id}`)
}

// Envia el formulario para crear un webhook con eventos seleccionados.
const submitWebhook = () => {
  webhookForm.post('/member/webhooks', {
    preserveScroll: true,
    onSuccess: () => webhookForm.reset('name', 'url', 'events'),
  })
}

// Lanza un evento de prueba para validar el endpoint del webhook.
const sendTest = (endpoint) => {
  router.post(`/member/webhooks/${endpoint.id}/test`, {}, { preserveScroll: true })
}

// Elimina un webhook del usuario.
const removeWebhook = (endpoint) => {
  if (!confirm('Eliminar este webhook?')) return
  router.delete(`/member/webhooks/${endpoint.id}`)
}
</script>

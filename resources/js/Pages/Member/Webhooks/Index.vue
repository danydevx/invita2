<template>
  <MemberLayout>
    <Head title="Webhooks" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Webhooks</h1>
        <p class="text-muted mb-0">Recibe notificaciones en tus sistemas externos.</p>
      </div>
    </div>

    <div v-if="secret" class="alert alert-warning">
      <div class="fw-semibold mb-1">Copia este secreto ahora. No volvera a mostrarse.</div>
      <div class="d-flex flex-wrap align-items-center gap-2">
        <code class="bg-light border rounded px-2 py-1">{{ secret }}</code>
        <button class="btn btn-sm btn-outline-secondary" type="button" @click="copySecret">Copiar</button>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <h2 class="h6 mb-3">Nuevo webhook</h2>
        <form class="row g-2" @submit.prevent="submit">
          <div class="col-12 col-md-4">
            <label class="form-label">Nombre</label>
            <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.name }" />
            <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
          </div>
          <div class="col-12 col-md-5">
            <label class="form-label">URL</label>
            <input v-model="form.url" type="url" class="form-control" :class="{ 'is-invalid': form.errors.url }" />
            <div v-if="form.errors.url" class="invalid-feedback">{{ form.errors.url }}</div>
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label">Activo</label>
            <select v-model="form.is_active" class="form-select">
              <option :value="true">Si</option>
              <option :value="false">No</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Eventos</label>
            <div class="d-flex flex-wrap gap-2">
              <label v-for="event in availableEvents" :key="event" class="form-check">
                <input class="form-check-input" type="checkbox" :value="event" v-model="form.events" />
                <span class="form-check-label">{{ event }}</span>
              </label>
            </div>
            <div v-if="form.errors.events" class="text-danger small mt-1">{{ form.errors.events }}</div>
          </div>
          <div class="col-12">
            <button class="btn btn-primary" type="submit" :disabled="form.processing">
              {{ form.processing ? 'Guardando...' : 'Crear webhook' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Nombre</th>
              <th scope="col">URL</th>
              <th scope="col">Eventos</th>
              <th scope="col">Estado</th>
              <th scope="col">Ultimo uso</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="endpoints.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No hay webhooks registrados.</td>
            </tr>
            <tr v-for="endpoint in endpoints" :key="endpoint.id">
              <td class="fw-semibold">{{ endpoint.name }}</td>
              <td class="text-muted">{{ endpoint.url }}</td>
              <td class="text-muted">{{ endpoint.events.join(', ') }}</td>
              <td>
                <span v-if="endpoint.is_active" class="badge text-bg-success">Activo</span>
                <span v-else class="badge text-bg-secondary">Inactivo</span>
              </td>
              <td class="text-muted">{{ endpoint.last_used_at || '-' }}</td>
              <td class="text-end">
                <div class="d-inline-flex gap-2">
                  <Link :href="`/member/webhooks/${endpoint.id}/deliveries`" class="btn btn-sm btn-outline-secondary">
                    Entregas
                  </Link>
                  <button class="btn btn-sm btn-outline-primary" type="button" @click="openEdit(endpoint)">
                    Editar
                  </button>
                  <button class="btn btn-sm btn-outline-info" type="button" @click="sendTest(endpoint)">
                    Probar
                  </button>
                  <button class="btn btn-sm btn-outline-warning" type="button" @click="regenerate(endpoint)">
                    Regenerar secreto
                  </button>
                  <button class="btn btn-sm btn-outline-danger" type="button" @click="remove(endpoint)">
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal fade" id="editWebhook" tabindex="-1" aria-hidden="true" ref="editModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Editar webhook</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form class="row g-2" @submit.prevent="submitEdit">
              <div class="col-12 col-md-4">
                <label class="form-label">Nombre</label>
                <input v-model="editForm.name" type="text" class="form-control" />
              </div>
              <div class="col-12 col-md-5">
                <label class="form-label">URL</label>
                <input v-model="editForm.url" type="url" class="form-control" />
              </div>
              <div class="col-12 col-md-3">
                <label class="form-label">Activo</label>
                <select v-model="editForm.is_active" class="form-select">
                  <option :value="true">Si</option>
                  <option :value="false">No</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Eventos</label>
                <div class="d-flex flex-wrap gap-2">
                  <label v-for="event in availableEvents" :key="event" class="form-check">
                    <input class="form-check-input" type="checkbox" :value="event" v-model="editForm.events" />
                    <span class="form-check-label">{{ event }}</span>
                  </label>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="submitEdit" :disabled="editForm.processing">
              Guardar cambios
            </button>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  endpoints: {
    type: Array,
    default: () => [],
  },
  availableEvents: {
    type: Array,
    default: () => [],
  },
})

const page = usePage()
const secret = computed(() => page.props.flash?.webhook_secret || '')
const editModal = ref(null)

const form = useForm({
  name: '',
  url: '',
  events: [],
  is_active: true,
})

const editForm = useForm({
  id: null,
  name: '',
  url: '',
  events: [],
  is_active: true,
})

const submit = () => {
  form.post('/member/webhooks', {
    preserveScroll: true,
    onSuccess: () => form.reset('name', 'url', 'events'),
  })
}

const openEdit = (endpoint) => {
  editForm.id = endpoint.id
  editForm.name = endpoint.name
  editForm.url = endpoint.url
  editForm.events = [...endpoint.events]
  editForm.is_active = endpoint.is_active

  const modal = new bootstrap.Modal(editModal.value)
  modal.show()
}

const submitEdit = () => {
  if (!editForm.id) return
  editForm.put(`/member/webhooks/${editForm.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      const modal = bootstrap.Modal.getInstance(editModal.value)
      modal?.hide()
    },
  })
}

const sendTest = (endpoint) => {
  router.post(`/member/webhooks/${endpoint.id}/test`, {}, { preserveScroll: true })
}

const regenerate = (endpoint) => {
  if (!confirm('Se regenerara el secreto. El anterior dejara de ser valido.')) return
  router.post(`/member/webhooks/${endpoint.id}/regenerate-secret`, {}, { preserveScroll: true })
}

const remove = (endpoint) => {
  if (!confirm('Eliminar este webhook?')) return
  router.delete(`/member/webhooks/${endpoint.id}`)
}

const copySecret = async () => {
  if (!secret.value) return
  try {
    await navigator.clipboard.writeText(secret.value)
  } catch (error) {
    // ignore
  }
}
</script>

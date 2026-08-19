<template>
  <MemberLayout>
    <Head title="API Keys" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">API Keys</h1>
        <p class="text-muted mb-0">Gestiona claves para integraciones externas.</p>
      </div>
    </div>

    <div v-if="plainKey" class="alert alert-warning">
      <div class="fw-semibold mb-1">Copia esta API key ahora. No volvera a mostrarse.</div>
      <div class="d-flex flex-wrap align-items-center gap-2">
        <code class="bg-light border rounded px-2 py-1">{{ plainKey }}</code>
        <button class="btn btn-sm btn-outline-secondary" type="button" @click="copyKey">Copiar</button>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <h2 class="h6 mb-3">Crear API key</h2>
        <form class="row g-2" @submit.prevent="submit">
          <div class="col-12 col-md-5">
            <label class="form-label">Nombre</label>
            <input
              v-model="form.name"
              type="text"
              class="form-control"
              :class="{ 'is-invalid': form.errors.name }"
              placeholder="Ej: Integracion CRM"
              required
            />
            <div v-if="form.errors.name" class="invalid-feedback">
              {{ form.errors.name }}
            </div>
          </div>
          <div class="col-12 col-md-3">
            <label class="form-label">Expira (opcional)</label>
            <input
              v-model="form.expires_at"
              type="date"
              class="form-control"
              :class="{ 'is-invalid': form.errors.expires_at }"
            />
            <div v-if="form.errors.expires_at" class="invalid-feedback">
              {{ form.errors.expires_at }}
            </div>
          </div>
          <div class="col-12 col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100" type="submit" :disabled="form.processing">
              {{ form.processing ? 'Creando...' : 'Crear API key' }}
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
              <th scope="col">Prefijo</th>
              <th scope="col">Estado</th>
              <th scope="col">Ultimo uso</th>
              <th scope="col">Expira</th>
              <th scope="col">Creada</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="apiKeys.length === 0">
              <td colspan="7" class="text-center text-muted py-4">No hay API keys creadas.</td>
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
              <td class="text-muted">{{ key.expires_at || '-' }}</td>
              <td class="text-muted">{{ key.created_at }}</td>
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
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  apiKeys: {
    type: Array,
    default: () => [],
  },
})

const page = usePage()
const plainKey = computed(() => page.props.flash?.api_key_plain || '')

const form = useForm({
  name: '',
  expires_at: '',
})

const submit = () => {
  form.post('/member/api-keys', {
    preserveScroll: true,
    onSuccess: () => form.reset('name', 'expires_at'),
  })
}

const toggleKey = (key) => {
  router.put(`/member/api-keys/${key.id}`, {
    name: key.name,
    is_active: !key.is_active,
    expires_at: key.expires_at || null,
  })
}

const revokeKey = (key) => {
  if (!confirm('Vas a revocar esta API key. Esta accion no se puede deshacer.')) return
  router.delete(`/member/api-keys/${key.id}`)
}

const copyKey = async () => {
  if (!plainKey.value) return
  try {
    await navigator.clipboard.writeText(plainKey.value)
  } catch (error) {
    // ignore
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="Redes Sociales" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-1">Redes Sociales</h1>
          <p class="text-muted mb-0">{{ business.name }}</p>
        </div>
        <button class="btn btn-primary" @click="openCreateModal">
          <i class="bi bi-plus me-1"></i>Agregar red social
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div v-if="socialNetworks.length === 0" class="text-center py-5">
            <i class="bi bi-share display-1 text-muted"></i>
            <p class="text-muted mt-3">No hay redes sociales configuradas aún.</p>
            <button class="btn btn-primary" @click="openCreateModal">Agregar primera red social</button>
          </div>

          <div v-else class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Plataforma</th>
                  <th>Usuario</th>
                  <th>URL</th>
                  <th>Hero</th>
                  <th>Footer</th>
                  <th>Contacto</th>
                  <th>Activo</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="network in socialNetworks" :key="network.id">
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <i :class="getPlatformIcon(network.platform)" :style="{ color: getPlatformColor(network.platform) }"></i>
                      <strong>{{ getPlatformName(network.platform) }}</strong>
                    </div>
                  </td>
                  <td>{{ network.username || '—' }}</td>
                  <td>
                    <a :href="network.url" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">
                      {{ network.url }}
                    </a>
                  </td>
                  <td>
                    <span v-if="network.show_on_hero" class="badge bg-success">Sí</span>
                    <span v-else class="badge bg-secondary">No</span>
                  </td>
                  <td>
                    <span v-if="network.show_on_footer" class="badge bg-success">Sí</span>
                    <span v-else class="badge bg-secondary">No</span>
                  </td>
                  <td>
                    <span v-if="network.show_on_contact" class="badge bg-success">Sí</span>
                    <span v-else class="badge bg-secondary">No</span>
                  </td>
                  <td>
                    <span v-if="network.is_active" class="badge bg-success">Activo</span>
                    <span v-else class="badge bg-secondary">Inactivo</span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary me-1" @click="openEditModal(network)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" @click="deleteNetwork(network)">
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
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingNetwork ? 'Editar red social' : 'Agregar red social' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="submitNetwork">
            <div class="modal-body">
              <div class="mb-3" v-if="!editingNetwork">
                <label class="form-label">Plataforma</label>
                <select v-model="form.platform" class="form-select" required>
                  <option value="">Seleccionar plataforma</option>
                  <option v-for="(info, key) in platforms" :key="key" :value="key">
                    {{ info.name }}
                  </option>
                </select>
              </div>

              <div class="mb-3" v-else>
                <label class="form-label">Plataforma</label>
                <input type="text" class="form-control" :value="getPlatformName(editingNetwork.platform)" disabled>
              </div>

              <div class="mb-3">
                <label class="form-label">URL</label>
                <input v-model="form.url" type="url" class="form-control" placeholder="https://..." required>
              </div>

              <div class="mb-3">
                <label class="form-label">Usuario (opcional)</label>
                <input v-model="form.username" type="text" class="form-control" placeholder="@usuario">
              </div>

              <div class="row g-3">
                <div class="col-6">
                  <div class="form-check form-switch">
                    <input v-model="form.show_on_hero" class="form-check-input" type="checkbox" id="showHero">
                    <label class="form-check-label" for="showHero">Mostrar en Hero</label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check form-switch">
                    <input v-model="form.show_on_footer" class="form-check-input" type="checkbox" id="showFooter">
                    <label class="form-check-label" for="showFooter">Mostrar en Footer</label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check form-switch">
                    <input v-model="form.show_on_contact" class="form-check-input" type="checkbox" id="showContact">
                    <label class="form-check-label" for="showContact">Mostrar en Contacto</label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check form-switch">
                    <input v-model="form.is_active" class="form-check-input" type="checkbox" id="isActive">
                    <label class="form-check-label" for="isActive">Activo</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="sending">
                <span v-if="sending">Guardando...</span>
                <span v-else>{{ editingNetwork ? 'Actualizar' : 'Crear' }}</span>
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
  business: Object,
  socialNetworks: Array,
  platforms: Object,
})

const modalElement = ref(null)
let networkModal = null
let editingNetwork = ref(null)
let sending = false

const form = useForm({
  platform: '',
  url: '',
  username: '',
  is_active: true,
  show_on_hero: false,
  show_on_footer: true,
  show_on_contact: true,
  sort_order: 0,
})

const platforms = props.platforms

const getPlatformIcon = (platform) => {
  return platforms[platform]?.icon || 'bi bi-globe'
}

const getPlatformName = (platform) => {
  return platforms[platform]?.name || platform
}

const getPlatformColor = (platform) => {
  return platforms[platform]?.color || '#666666'
}

const openCreateModal = () => {
  editingNetwork.value = null
  form.reset()
  form.platform = ''
  form.is_active = true
  form.show_on_hero = false
  form.show_on_footer = true
  form.show_on_contact = true
  nextTick(() => {
    networkModal.show()
  })
}

const openEditModal = (network) => {
  editingNetwork.value = network
  form.url = network.url
  form.username = network.username || ''
  form.is_active = network.is_active
  form.show_on_hero = network.show_on_hero
  form.show_on_footer = network.show_on_footer
  form.show_on_contact = network.show_on_contact
  form.sort_order = network.sort_order || 0
  nextTick(() => {
    networkModal.show()
  })
}

const submitNetwork = () => {
  sending = true
  if (editingNetwork.value) {
    form.post(`/admin/businesses/${props.business.id}/social-networks/${editingNetwork.value.id}`, {
      onFinish: () => {
        sending = false
        networkModal.hide()
      },
    })
  } else {
    form.post(`/admin/businesses/${props.business.id}/social-networks`, {
      onFinish: () => {
        sending = false
        networkModal.hide()
      },
    })
  }
}

const deleteNetwork = (network) => {
  if (confirm('¿Estás seguro de eliminar esta red social?')) {
    router.delete(`/admin/businesses/${props.business.id}/social-networks/${network.id}`, {
      preserveScroll: true,
    })
  }
}

onMounted(() => {
  networkModal = new Modal(modalElement.value)
})
</script>

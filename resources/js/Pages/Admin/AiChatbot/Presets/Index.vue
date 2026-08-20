<template>
  <AdminLayout>
    <Head title="Presets de Chatbot" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-0">Presets de Chatbot</h1>
          <small class="text-muted">Plantillas configurables para el AI Chatbot</small>
        </div>
        <Link href="/admin/modules/ai_chatbot/presets/create" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i>Nuevo Preset
        </Link>
      </div>

      <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
          <div class="row g-3">
            <div class="col-md-4">
              <input
                v-model="search"
                type="search"
                class="form-control"
                placeholder="Buscar presets..."
                @keyup.enter="filterSearch"
              />
            </div>
            <div class="col-md-3">
              <select v-model="filterActive" class="form-select" @change="filterSearch">
                <option :value="null">Todos</option>
                <option :value="true">Activos</option>
                <option :value="false">Inactivos</option>
              </select>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Tipo de Negocio</th>
                <th scope="col">Personalidad</th>
                <th scope="col">Idioma</th>
                <th scope="col">Estado</th>
                <th scope="col" class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="presets.data.length === 0">
                <td colspan="6" class="text-center text-muted py-4">No hay presets registrados.</td>
              </tr>
              <tr v-for="preset in presets.data" :key="preset.id">
                <td>
                  <div class="fw-semibold">{{ preset.name }}</div>
                  <small class="text-muted">{{ preset.description?.substring(0, 60) || '' }}...</small>
                </td>
                <td>
                  <span v-if="preset.listing_type" class="badge text-bg-secondary">
                    {{ preset.listing_type }}
                  </span>
                  <span v-else class="text-muted">Todos</span>
                </td>
                <td>
                  <span class="badge text-bg-info">{{ preset.personality }}</span>
                </td>
                <td>
                  <span class="badge text-bg-secondary">{{ preset.language?.toUpperCase() }}</span>
                </td>
                <td>
                  <span :class="preset.is_active ? 'badge bg-success' : 'badge bg-secondary'">
                    {{ preset.is_active ? 'Activo' : 'Inactivo' }}
                  </span>
                  <span v-if="preset.is_system" class="badge bg-warning ms-1">Sistema</span>
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <Link
                      :href="`/admin/modules/ai_chatbot/presets/${preset.id}/edit`"
                      class="btn btn-outline-primary"
                      :class="{ disabled: preset.is_system }"
                    >
                      <i class="bi bi-pencil"></i>
                    </Link>
                    <button
                      type="button"
                      class="btn btn-outline-secondary"
                      @click="duplicatePreset(preset)"
                    >
                      <i class="bi bi-copy"></i>
                    </button>
                    <button
                      type="button"
                      class="btn"
                      :class="preset.is_active ? 'btn-outline-warning' : 'btn-outline-success'"
                      @click="togglePreset(preset)"
                    >
                      <i :class="preset.is_active ? 'bi bi-x-lg' : 'bi bi-check-lg'"></i>
                    </button>
                    <button
                      v-if="!preset.is_system"
                      type="button"
                      class="btn btn-outline-danger"
                      @click="deletePreset(preset)"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="card-footer bg-white">
          <Pagination :data="presets" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Admin/Pagination.vue'

const page = usePage()
const presets = computed(() => page.props.presets || { data: [] })

const search = ref(page.props.filters?.search || '')
const filterActive = ref(page.props.filters?.is_active ?? null)

const filterSearch = () => {
  const params = {}
  if (search.value) params.search = search.value
  if (filterActive.value !== null) params.is_active = filterActive.value
  router.get('/admin/modules/ai_chatbot/presets', params, { preserveScroll: true })
}

const togglePreset = (preset) => {
  router.post(`/admin/modules/ai_chatbot/presets/${preset.id}/toggle`, {}, {
    preserveScroll: true,
  })
}

const duplicatePreset = (preset) => {
  router.post(`/admin/modules/ai_chatbot/presets/${preset.id}/duplicate`, {}, {
    preserveScroll: true,
  })
}

const deletePreset = (preset) => {
  if (confirm(`¿Eliminar el preset "${preset.name}"?`)) {
    router.delete(`/admin/modules/ai_chatbot/presets/${preset.id}`, {
      preserveScroll: true,
    })
  }
}
</script>

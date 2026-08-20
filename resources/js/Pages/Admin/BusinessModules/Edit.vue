<template>
  <AdminLayout>
    <Head title="Modulos del Negocio" />

    <div class="container-fluid py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-0">{{ listing?.name || 'Cargando...' }}</h1>
          <small class="text-muted">Modulos del minisite</small>
        </div>
        <Link href="/admin/listings" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
      </div>

      <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $page.props.flash.success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>

      <div class="row g-4">
        <div v-for="module in form.modules" :key="module.id" class="col-md-6 col-lg-4">
          <div class="card h-100 border-0 shadow-sm" :class="{ 'border-success border-2': module.is_enabled }">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center gap-2">
                  <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i :class="module.module_icon || 'bi bi-box'"></i>
                  </div>
                  <div>
                    <strong>{{ module.module_name }}</strong>
                    <br>
                    <small class="text-muted">{{ module.module_key }}</small>
                  </div>
                </div>
                <span :class="module.is_enabled ? 'badge bg-success' : 'badge bg-secondary'">
                  {{ module.is_enabled ? 'Activo' : 'Inactivo' }}
                </span>
              </div>

              <div v-if="!module.is_active_globally" class="alert alert-secondary py-2 small mb-3">
                <i class="bi bi-exclamation-triangle me-1"></i>Modulo desactivado globalmente
              </div>

              <div class="d-flex gap-2 flex-wrap">
                <button
                  v-if="module.is_enabled && module.has_settings && module.settings_url"
                  type="button"
                  class="btn btn-sm btn-outline-warning"
                  @click="goToConfig(module)"
                >
                  <i class="bi bi-gear me-1"></i>Configurar
                </button>

                <button
                  type="button"
                  class="btn btn-sm"
                  :class="module.is_enabled ? 'btn-outline-danger' : 'btn-success'"
                  @click="toggleModule(module)"
                >
                  <i :class="module.is_enabled ? 'bi bi-x-lg me-1' : 'bi bi-check-lg me-1'"></i>
                  {{ module.is_enabled ? 'Desinstalar' : 'Instalar' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="form.modules.length === 0" class="text-center py-5">
        <i class="bi bi-grid display-1 text-muted"></i>
        <p class="text-muted mt-3">No hay modulos disponibles para este negocio.</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const page = usePage()
const listing = computed(() => page.props.listing)

const form = reactive({
  modules: listing.value?.modules?.map(m => ({
    id: m.id,
    module_key: m.module_key,
    module_name: m.module_name,
    module_icon: m.module_icon,
    is_enabled: m.is_enabled,
    is_active_globally: m.is_active_globally,
    has_settings: m.has_settings,
    settings_url: m.settings_url,
  })) || [],
})

const toggleModule = (module) => {
  router.put(`/admin/listings/${listing.value.id}/modules`, {
    modules: [{
      id: module.id,
      is_enabled: !module.is_enabled,
    }],
  }, {
    preserveScroll: true,
    preserveState: false,
  })
}

const goToConfig = (module) => {
  if (module.settings_url) {
    let url = module.settings_url
    if (url.includes('{business}')) {
      url = url.replace('{business}', listing.value.id)
    }
    window.location.href = url
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="Themes de Minisite" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/admin" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Admin
        </Link>
        <h1 class="h4 mb-1 mt-1">Themes de Minisite</h1>
      </div>
      <Link href="/admin/minisite-themes/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Theme
      </Link>
    </div>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ $page.props.flash.error }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="row g-4">
      <div v-for="theme in themes" :key="theme.id" class="col-md-6 col-lg-4">
        <div class="card h-100" :class="{ 'border-primary': theme.is_active }">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h5 class="card-title mb-1">{{ theme.name }}</h5>
                <span class="badge" :class="theme.is_active ? 'bg-success' : 'bg-secondary'">
                  {{ theme.is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
              <div class="btn-group btn-group-sm">
                <Link :href="`/admin/minisite-themes/${theme.id}/edit`" class="btn btn-outline-primary">
                  <i class="bi bi-pencil"></i>
                </Link>
                <button @click="deleteTheme(theme)" class="btn btn-outline-danger" :disabled="theme.businesses_count > 0">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>

            <p class="text-muted small mb-3">{{ theme.description }}</p>

            <div class="theme-preview rounded p-3 mb-3" :style="getPreviewStyle(theme)">
              <div class="d-flex gap-2 mb-2">
                <div class="preview-btn" style="width: 30px; height: 20px; border-radius: 4px; background: var(--preview-primary);"></div>
                <div class="preview-btn" style="width: 30px; height: 20px; border-radius: 20px; background: var(--preview-secondary);"></div>
                <div class="preview-btn" style="width: 30px; height: 20px; border-radius: 0; background: var(--preview-accent);"></div>
              </div>
              <div class="bg-white rounded p-2" style="font-size: 10px;">
                <div style="height: 8px; width: 60%; background: #eee; border-radius: 2px; margin-bottom: 4px;"></div>
                <div style="height: 6px; width: 80%; background: #f5f5f5; border-radius: 2px; margin-bottom: 2px;"></div>
                <div style="height: 6px; width: 40%; background: #f5f5f5; border-radius: 2px;"></div>
              </div>
            </div>

            <div class="small text-muted">
              <span class="me-3">
                <i class="bi bi-building me-1"></i>{{ theme.businesses_count || 0 }} negocios
              </span>
              <span>
                <i class="bi bi-grid me-1"></i>{{ theme.layout_config?.page_style || 'N/A' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

<div v-if="themes.length === 0" class="alert alert-info mt-4">
      No hay themes creados. Crea tu primer theme para empezar.
    </div>
  </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  themes: Array,
})

const deleteTheme = (theme) => {
  if (theme.businesses_count > 0) return
  if (!confirm(`¿Eliminar el theme "${theme.name}"?`)) return

  router.delete(`/admin/minisite-themes/${theme.id}`)
}

const getPreviewStyle = (theme) => {
  if (!theme.css_variables?.colors) return {}
  const { brand_primary, brand_secondary, brand_accent, brand_background } = theme.css_variables.colors
  return {
    '--preview-primary': brand_primary || '#1a1a2e',
    '--preview-secondary': brand_secondary || '#6B7280',
    '--preview-accent': brand_accent || '#3B82F6',
    backgroundColor: brand_background || '#f8f9fa',
  }
}
</script>

<style scoped>
.theme-preview {
  border: 1px solid #dee2e6;
}

.preview-btn {
  display: inline-block;
  transition: transform 0.2s;
}

.preview-btn:hover {
  transform: scale(1.1);
}
</style>

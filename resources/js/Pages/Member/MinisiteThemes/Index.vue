<template>
  <MemberLayout>
    <Head :title="`Theme de ${business.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <Link href="/member/business-modules" class="text-decoration-none text-muted small">
          <i class="bi bi-arrow-left me-1"></i>Mis Negocios
        </Link>
        <h1 class="h4 mb-1 mt-1">{{ business.name }} - Theme del Minisite</h1>
      </div>
      <div>
        <a :href="`/b/${business.slug}`" target="_blank" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-eye me-1"></i>Ver minisite
        </a>
      </div>
    </div>

    <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ $page.props.flash.success }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <div class="alert alert-info mb-4">
      <i class="bi bi-info-circle me-2"></i>
      El theme define el estilo visual de tu minisite público. Actualmente tienes asignado: <strong>{{ business.theme?.name || 'Ninguno (se asignará por defecto)' }}</strong>
    </div>

    <div class="row g-4">
      <div v-for="theme in themes" :key="theme.id" class="col-md-6 col-lg-4">
        <div
          class="card h-100 theme-card"
          :class="{
            'border-primary': business.theme?.id === theme.id,
            'border-success': business.theme?.id === theme.id,
            'selected': business.theme?.id === theme.id
          }"
        >
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h5 class="card-title mb-1">{{ theme.name }}</h5>
                <span v-if="business.theme?.id === theme.id" class="badge bg-success">
                  <i class="bi bi-check-circle me-1"></i>Seleccionado
                </span>
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

            <div class="small text-muted mb-3">
              <span class="me-3">
                <i class="bi bi-grid me-1"></i>{{ theme.layout_config?.page_style || 'N/A' }}
              </span>
              <span>
                <i class="bi bi-type me-1"></i>{{ theme.css_variables?.fonts?.headings?.split(',')[0] || 'N/A' }}
              </span>
            </div>

            <button
              v-if="business.theme?.id !== theme.id"
              @click="selectTheme(theme)"
              class="btn btn-outline-primary w-100"
              :disabled="selecting"
            >
              <i class="bi bi-check2 me-1"></i>Seleccionar este theme
            </button>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref } from 'vue'
import { usePage, Head, Link, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  business: Object,
  themes: Array,
})

const selecting = ref(false)

const selectTheme = (theme) => {
  if (!confirm(`¿Cambiar el theme de tu minisite a "${theme.name}"?`)) return

  selecting.value = true

  router.put(
    `/member/businesses/${props.business.id}/minisite-theme/${theme.id}`,
    {},
    {
      onFinish: () => {
        selecting.value = false
      },
    }
  )
}

const getPreviewStyle = (theme) => {
  if (!theme.css_variables?.colors) return {}
  const { primary, secondary, accent } = theme.css_variables.colors
  return {
    '--preview-primary': primary,
    '--preview-secondary': secondary,
    '--preview-accent': accent,
    backgroundColor: theme.css_variables.colors.background || '#f8f9fa',
  }
}
</script>

<style scoped>
.theme-card {
  transition: all 0.3s ease;
}

.theme-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.theme-card.selected {
  box-shadow: 0 0 0 2px var(--bs-success);
}

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

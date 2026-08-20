<template>
  <MemberLayout>
    <Head :title="`Módulos - ${listing?.name || ''}`" />

    <PageHeader
      :title="listing?.name || 'Módulos'"
      :breadcrumbs="breadcrumbs"
      backHref="/member/dashboard"
    >
      <template #description>
        <p v-if="listing?.industry_name" class="text-muted mb-0">
          Industria: <strong>{{ listing.industry_name }}</strong>
        </p>
        <p v-else class="text-muted mb-0">Selecciona un módulo para ver y gestionar su contenido.</p>
      </template>
    </PageHeader>

    <div v-if="moduleSummary.length === 0" class="alert alert-info">
      <i class="bi bi-info-circle me-2"></i>
      No hay módulos disponibles para esta industria. Contacta al administrador.
    </div>

    <div class="row g-3">
      <div v-for="mod in moduleSummary" :key="mod.key" class="col-12 col-md-6 col-lg-4">
        <Link :href="mod.url" class="text-decoration-none">
          <div 
            class="card border-0 shadow-sm h-100 module-card"
            :class="{ 'opacity-50': mod.is_enabled === false }"
          >
            <div class="card-body d-flex flex-column">
              <div class="d-flex align-items-center mb-2">
                <div class="module-icon me-3">
                  <i :class="`bi ${mod.icon}`"></i>
                </div>
                <h3 class="h6 mb-0 text-dark">{{ mod.name }}</h3>
                <span v-if="mod.is_premium" class="badge bg-warning text-dark ms-auto">Premium</span>
                <span v-if="mod.is_enabled === false" class="badge bg-secondary ms-auto">Inactivo</span>
              </div>
              <p class="text-muted small mb-3">{{ mod.description }}</p>
              <div class="mt-auto">
                <span v-if="mod.count > 0" class="badge bg-primary me-2">
                  {{ mod.count }} {{ mod.count === 1 ? 'item' : 'items' }}
                </span>
                <span class="text-primary small fw-medium">
                  Ver contenido <i class="bi bi-arrow-right ms-1"></i>
                </span>
              </div>
            </div>
          </div>
        </Link>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const moduleSummary = computed(() => page.props.moduleSummary || [])

const breadcrumbs = computed(() => [
  { label: 'Dashboard', href: '/member/dashboard' },
  { label: listing.value?.name || 'Módulos', active: true },
])
</script>

<style scoped>
.module-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.module-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}

.module-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bs-primary-bg-subtle);
  border-radius: 8px;
  font-size: 1.25rem;
  color: var(--bs-primary);
}
</style>

<template>
  <MemberLayout>
    <Head :title="`Modulos - ${listing.name}`" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">{{ listing.name }}</h1>
        <p class="text-muted mb-0">Gestiona el contenido de cada modulo.</p>
      </div>
      <div class="d-flex gap-2">
        <Link :href="`/member/listings/${listing.id}/edit`" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-pencil me-1"></i>Editar negocio
        </Link>
        <Link href="/member/listings" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left me-1"></i>Volver
        </Link>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-6 col-md-4 col-lg-3" v-for="mod in localModules" :key="mod.id">
        <div
          class="card border-0 shadow-sm h-100"
          :class="{
            'opacity-50': !mod.is_enabled,
          }"
        >
          <div v-if="mod.module_image" class="card-img-top overflow-hidden" style="height: 100px;">
            <img :src="mod.module_image" class="w-100 h-100 object-fit-cover" :alt="mod.module_name" />
          </div>
          <div class="card-body py-3 px-3">
            <div class="d-flex align-items-center gap-2 mb-2">
              <div
                class="rounded bg-light d-flex align-items-center justify-content-center flex-shrink-0"
                style="width: 36px; height: 36px;"
              >
                <i :class="getModuleIcon(mod.module_key)" style="font-size: 1rem;"></i>
              </div>
              <div class="flex-grow-1 min-width-0">
                <h3 class="h6 mb-0 small fw-semibold text-truncate d-flex align-items-center gap-2">
                  {{ mod.module_name }}
                </h3>
                <span v-if="!mod.is_enabled" class="badge bg-secondary">Inactivo</span>
                <span v-else class="badge bg-success">Activo</span>
              </div>
            </div>
            <p v-if="mod.module_description" class="text-muted small mb-3 text-truncate">{{ mod.module_description }}</p>
            <div class="d-flex flex-column gap-2">
              <button
                class="btn btn-primary btn-sm"
                :class="{ 'disabled': !mod.is_enabled }"
                @click="mod.is_enabled && goToModule(mod)"
              >
                <i class="bi bi-eye me-1"></i>Ver Contenido
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="localModules.length === 0" class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <i class="bi bi-grid display-1 text-muted"></i>
        <h3 class="h5 mt-3">No hay modulos activos</h3>
        <p class="text-muted">Este negocio no tiene modulos activos disponibles.</p>
        <Link href="/member/listings" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Volver a negocios
        </Link>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  listing: {
    type: Object,
  },
  planName: {
    type: String,
    default: '',
  },
})

const localModules = ref([...props.listing.modules])

const moduleIcons = {
  hero: 'bi bi-house',
  locations: 'bi bi-geo-alt',
  services: 'bi bi-scissors',
  products: 'bi bi-bag',
  gallery: 'bi bi-images',
  appointments: 'bi bi-calendar-check',
  slots: 'bi bi-clock',
  leads: 'bi bi-people',
  contact_form: 'bi bi-envelope',
  reviews: 'bi bi-star',
  promotions: 'bi bi-tag',
  restaurant_menu: 'bi bi-list-ul',
  socialmedia: 'bi bi-share',
  about: 'bi bi-info-circle',
  features: 'bi bi-check-circle',
  ai_chatbot: 'bi bi-robot',
  faqs: 'bi bi-question-circle',
  seo: 'bi bi-graph-up',
  branding: 'bi bi-palette',
  clients: 'bi bi-people',
  properties: 'bi bi-building',
}

const moduleUrls = {
  hero: 'hero',
  locations: 'locations',
  services: 'services',
  products: 'menu-products',
  gallery: 'galleries',
  appointments: 'appointments',
  slots: 'slots',
  leads: 'leads',
  contact_form: 'contact-forms',
  reviews: 'reviews',
  promotions: 'promotions',
  restaurant_menu: 'menu-categories',
  socialmedia: 'social-networks',
  about: 'about',
  features: 'features',
  ai_chatbot: 'ai-chatbot',
  faqs: 'faqs',
  seo: 'seo',
  branding: 'branding',
  clients: 'clients',
  properties: 'properties',
}

const getModuleIcon = (key) => moduleIcons[key] || 'bi bi-box'

const getModuleUrl = (key) => {
  const path = moduleUrls[key] || key
  return `/member/listings/${props.listing.id}/${path}`
}

const goToModule = (mod) => {
  window.location.href = getModuleUrl(mod.module_key)
}


</script>

<template>
  <MemberLayout>
    <Head title="Mi dashboard" />

    <div class="mb-4">
      <h1 class="h4 mb-1">Hola, {{ userName }}</h1>
      <p class="text-muted mb-0">Gestiona tu negocio desde un solo lugar.</p>
    </div>

    <div v-if="hasPendingBusiness" class="alert alert-warning d-flex align-items-center gap-2 mb-4">
      <i class="bi bi-clock-history fs-4"></i>
      <div>
        <strong>Tu negocio está pendiente de activación.</strong>
        <span v-if="!emailVerified"> Verifica tu email para activarlo.</span>
        <span v-else> El administrador debe aprobarlo.</span>
      </div>
    </div>

    <div v-if="moduleStats.length > 0" class="mb-4">
      <h2 class="h6 text-muted mb-3">Módulos de contenido</h2>
      <div class="row g-2">
        <div v-for="stat in moduleStats" :key="stat.key" class="col-6 col-md-4 col-lg-3">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-2 px-3">
              <div class="d-flex align-items-center gap-2">
                <div :class="`rounded bg-${stat.tone}-subtle text-${stat.tone} d-flex align-items-center justify-content-center`" style="width: 32px; height: 32px;">
                  <i :class="`bi ${stat.icon}`" style="font-size: 1rem;"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <div class="small text-muted text-truncate">{{ stat.label }}</div>
                  <div class="fw-semibold">{{ stat.count }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex flex-column">
            <h2 class="h6 mb-1">Mi cuenta</h2>
            <p class="text-muted small mb-3">Plan, suscripcion y datos personales.</p>
            <Link href="/member/account" class="btn btn-outline-primary mt-auto">
              <i class="bi bi-wallet2 me-1"></i>Ver cuenta
            </Link>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body d-flex flex-column">
            <h2 class="h6 mb-1">Soporte</h2>
            <p class="text-muted small mb-3">Crea un ticket si necesitas ayuda.</p>
            <Link href="/member/support" class="btn btn-outline-secondary mt-auto">
              <i class="bi bi-headset me-1"></i>Abrir ticket
            </Link>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const page = usePage()
const userName = computed(() => page.props.auth?.user?.name || 'Usuario')
const listings = computed(() => page.props.listings || [])
const moduleStats = computed(() => page.props.moduleStats || [])

const toneMap = {
  'bi-briefcase': 'primary',
  'bi-cart': 'success',
  'bi-images': 'info',
  'bi-people': 'warning',
  'bi-calendar-event': 'danger',
  'bi-megaphone': 'dark',
  'bi-star': 'warning',
  'bi-person-badge': 'primary',
  'bi-box-seam': 'secondary',
  'bi-cup-hot': 'danger',
  'bi-house': 'info',
}

const normalizedStats = computed(() => {
  return moduleStats.value.map(stat => ({
    ...stat,
    tone: toneMap[stat.icon] || 'primary',
  }))
})

const hasPendingBusiness = computed(() => {
  return listings.value.some(biz => !biz.is_published)
})

const emailVerified = computed(() => page.props.auth?.user?.email_verified_at !== null)
</script>

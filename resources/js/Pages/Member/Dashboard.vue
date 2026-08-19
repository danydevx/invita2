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

    <div class="row g-3 mb-4">
      <div v-for="stat in statCards" :key="stat.key" class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <div :class="`d-inline-flex align-items-center justify-content-center rounded-circle mb-2 bg-${stat.tone}-subtle text-${stat.tone}`" style="width: 48px; height: 48px;">
              <i :class="`bi ${stat.icon}`" style="font-size: 1.5rem;"></i>
            </div>
            <h2 class="h6 text-muted text-uppercase small mb-1">{{ stat.label }}</h2>
            <p class="display-6 fw-bold mb-0">{{ stat.count }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-md-6 col-lg-4">
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

      <div class="col-12 col-md-6 col-lg-4">
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
const stats = computed(() => page.props.stats || {})
const businesses = computed(() => page.props.businesses || [])

const hasPendingBusiness = computed(() => {
  return businesses.value.some(biz => !biz.is_published)
})

const emailVerified = computed(() => page.props.auth?.user?.email_verified_at !== null)

const statCards = computed(() => [
  { key: 'leads', label: 'Leads', count: Number(stats.value.leads ?? 0), icon: 'bi-people', tone: 'primary' },
  { key: 'appointments', label: 'Citas', count: Number(stats.value.appointments ?? 0), icon: 'bi-calendar-event', tone: 'success' },
  { key: 'promotions', label: 'Promociones', count: Number(stats.value.promotions ?? 0), icon: 'bi-megaphone', tone: 'warning' },
  { key: 'reviews', label: 'Reseñas', count: Number(stats.value.reviews ?? 0), icon: 'bi-star', tone: 'info' },
])
</script>
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
      <h2 class="h6 text-muted mb-3 text-uppercase small tracking-wide">Módulos de contenido</h2>
      <div class="row g-3">
        <div v-for="stat in normalizedStats" :key="stat.key" class="col-6 col-md-4 col-lg-3">
          <div class="card border-0 shadow-sm h-100 module-card">
            <div class="card-body p-3">
              <div class="d-flex align-items-center gap-3">
                <div :class="`module-icon bg-${stat.tone}-subtle text-${stat.tone}`">
                  <i :class="`bi ${stat.icon}`"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <div class="small text-muted text-truncate">{{ stat.label }}</div>
                  <div class="fw-bold fs-5">{{ stat.count }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 action-card action-card-primary">
          <div class="card-body d-flex flex-column p-4">
            <div class="action-icon mb-3">
              <i class="bi bi-wallet2"></i>
            </div>
            <h2 class="h6 mb-1 fw-bold">Mi cuenta</h2>
            <p class="text-muted small mb-3">Plan, suscripción y datos personales.</p>
            <Link href="/member/account" class="btn btn-primary mt-auto rounded-pill">
              Ver cuenta <i class="bi bi-arrow-right ms-1"></i>
            </Link>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100 action-card action-card-secondary">
          <div class="card-body d-flex flex-column p-4">
            <div class="action-icon mb-3">
              <i class="bi bi-headset"></i>
            </div>
            <h2 class="h6 mb-1 fw-bold">Soporte</h2>
            <p class="text-muted small mb-3">Crea un ticket si necesitas ayuda.</p>
            <Link href="/member/support" class="btn btn-outline-dark mt-auto rounded-pill">
              Abrir ticket <i class="bi bi-arrow-right ms-1"></i>
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

<style scoped>
.module-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-radius: 0.75rem;
}
.module-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}
.module-icon {
  width: 48px;
  height: 48px;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  flex-shrink: 0;
}
.action-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  border-radius: 1rem;
  overflow: hidden;
  position: relative;
}
.action-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
}
.action-card-primary::before {
  background: linear-gradient(90deg, #0d6efd, #0dcaf0);
}
.action-card-secondary::before {
  background: linear-gradient(90deg, #6c757d, #adb5bd);
}
.action-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.12) !important;
}
.action-icon {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}
.action-card-primary .action-icon {
  background: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
}
.action-card-secondary .action-icon {
  background: rgba(108, 117, 125, 0.1);
  color: #6c757d;
}
.tracking-wide {
  letter-spacing: 0.05em;
}
</style>

<template>
  <MemberLayout>
    <Head :title="`Contacto - ${business.name}`" />

    <PageHeader
      title="Detalle del Contacto"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/leads`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <h6 class="text-muted mb-1">Nombre</h6>
            <p class="mb-0">{{ lead.name }}</p>
          </div>

          <div class="col-md-6">
            <h6 class="text-muted mb-1">Email</h6>
            <p class="mb-0">{{ lead.email }}</p>
          </div>

          <div class="col-md-6">
            <h6 class="text-muted mb-1">Telefono</h6>
            <p class="mb-0">{{ lead.phone || '-' }}</p>
          </div>

          <div class="col-md-6">
            <h6 class="text-muted mb-1">Ubicacion</h6>
            <p class="mb-0">{{ lead.location?.name || '-' }}</p>
          </div>

          <div class="col-md-6">
            <h6 class="text-muted mb-1">Estado</h6>
            <span :class="statusClass(lead.status)" class="badge">
              {{ lead.status_label }}
            </span>
          </div>

          <div class="col-md-6">
            <h6 class="text-muted mb-1">Fuente</h6>
            <p class="mb-0">{{ lead.source_label }}</p>
          </div>

          <div class="col-md-6">
            <h6 class="text-muted mb-1">Fecha de registro</h6>
            <p class="mb-0">{{ formatDate(lead.created_at) }}</p>
          </div>

          <div class="col-12" v-if="lead.notes">
            <h6 class="text-muted mb-1">Notas</h6>
            <p class="mb-0">{{ lead.notes }}</p>
          </div>
        </div>

        <div class="mt-4 d-flex gap-2">
          <Link :href="`/member/businesses/${business.id}/leads/${lead.id}/edit`" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>Editar
          </Link>
          <button
            class="btn btn-outline-danger"
            @click="deleteLead"
          >
            <i class="bi bi-trash me-1"></i>Eliminar
          </button>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const page = usePage()
const business = computed(() => page.props.business)
const lead = computed(() => page.props.lead)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Leads', href: `/member/businesses/${biz.id}/leads` },
        { label: lead.value.name, active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Detalle del Contacto', active: true },
  ]
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-AR', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const statusClass = (status) => {
  const classes = {
    new: 'bg-info',
    contacted: 'bg-primary',
    qualified: 'bg-success',
    converted: 'bg-dark',
    lost: 'bg-secondary',
  }
  return classes[status] || 'bg-secondary'
}

const deleteLead = () => {
  if (confirm('¿Estás seguro de eliminar este contacto?')) {
    router.delete(`/member/businesses/${business.value.id}/leads/${lead.value.id}`, {
      preserveScroll: true,
    })
  }
}
</script>

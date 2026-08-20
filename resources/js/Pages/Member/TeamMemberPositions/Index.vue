<template>
  <MemberLayout>
    <Head :title="`Puestos - ${business?.name || ''}`" />

    <PageHeader
      title="Puestos"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${business?.id}/team-members`"
    >
      <template #actions>
        <Link :href="`/member/listings/${business?.id}/team-member-positions/create`" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg me-1"></i>Nuevo Puesto
        </Link>
      </template>
    </PageHeader>

    <div class="mb-3 d-flex gap-2">
      <Link
        :href="`/member/listings/${business?.id}/team-members`"
        class="btn btn-outline-secondary btn-sm"
      >
        <i class="bi bi-people me-1"></i>Miembros
      </Link>
      <Link
        :href="`/member/listings/${business?.id}/team-member-positions`"
        class="btn btn-secondary btn-sm"
      >
        <i class="bi bi-folder me-1"></i>Puestos
      </Link>
    </div>

    <div v-if="positions.length === 0" class="alert alert-info">
      <i class="bi bi-info-circle me-2"></i>
      No hay puestos creados. Crea tu primer puesto para organizar a tu equipo.
    </div>

    <div v-else class="row g-3">
      <div v-for="position in positions" :key="position.id" class="col-12 col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between mb-2">
              <div>
                <h3 class="h6 mb-1">{{ position.name }}</h3>
                <span v-if="position.parent" class="badge bg-light text-dark mb-2">
                  {{ position.parent.name }}
                </span>
              </div>
              <span v-if="position.is_active" class="badge bg-success">Activo</span>
              <span v-else class="badge bg-secondary">Inactivo</span>
            </div>
            <p v-if="position.description" class="text-muted small mb-3">{{ position.description }}</p>
            <div class="d-flex gap-2 mb-3">
              <span class="text-muted small">
                <i class="bi bi-people me-1"></i>{{ position.members_count }} miembros
              </span>
              <span v-if="position.children_count > 0" class="text-muted small">
                <i class="bi bi-diagram-3 me-1"></i>{{ position.children_count }} sub-puestos
              </span>
            </div>
            <div class="d-flex gap-2">
              <Link :href="`/member/listings/${business?.id}/team-member-positions/${position.id}/edit`" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil"></i>
              </Link>
              <button 
                class="btn btn-outline-danger btn-sm" 
                @click="deletePosition(position)"
                :disabled="position.members_count > 0 || position.children_count > 0"
              >
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
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
const positions = computed(() => page.props.positions || [])
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Dashboard', href: '/member/dashboard' },
        { label: biz.name, href: `/member/listings/${biz.id}/modules` },
        { label: 'Equipo', href: `/member/listings/${biz.id}/team-members` },
        { label: 'Puestos', active: true },
      ]
    }
  }
  return [
    { label: 'Dashboard', href: '/member/dashboard' },
    { label: 'Equipo', href: `/member/listings/${business.value?.id}/team-members` },
    { label: 'Puestos', active: true },
  ]
})

const deletePosition = (position) => {
  if (!confirm(`¿Estás seguro de eliminar "${position.name}"?`)) {
    return
  }

  router.delete(`/member/listings/${business.value.id}/team-member-positions/${position.id}`, {
    preserveScroll: true,
  })
}
</script>

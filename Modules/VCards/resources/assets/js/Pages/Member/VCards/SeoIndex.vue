<template>
  <MemberLayout>
    <Head :title="`SEO vCards - ${listing?.name || ''}`" />

    <PageHeader
      title="SEO vCards"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/modules`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div v-if="vcards.length === 0" class="text-center py-5">
          <i class="bi bi-person-vcard display-1 text-muted"></i>
          <p class="text-muted mt-3">No hay vCards creadas.</p>
          <Link :href="`/member/listings/${listing?.id}/vcards/create`" class="btn btn-primary">
            Crear primera vCard
          </Link>
        </div>

        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th style="width: 50px;"></th>
                <th>Nombre</th>
                <th>URL</th>
                <th>SEO Title</th>
                <th>Indexación</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="vcard in vcards" :key="vcard.id">
                <td>
                  <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-person-vcard text-muted"></i>
                  </div>
                </td>
                <td>
                  <strong>{{ vcard.name }}</strong>
                  <br>
                  <small class="text-muted">/{{ vcard.slug }}</small>
                </td>
                <td>
                  <small>{{ vcardPublicUrl(vcard.slug) }}</small>
                </td>
                <td>
                  <span v-if="vcard.seo_setting?.seo_title">{{ vcard.seo_setting.seo_title }}</span>
                  <span v-else class="text-muted">-</span>
                </td>
                <td>
                  <span v-if="vcard.seo_setting?.allow_indexing !== false" class="badge bg-success">
                    <i class="bi bi-check-circle me-1"></i>Activa
                  </span>
                  <span v-else class="badge bg-secondary">
                    <i class="bi bi-x-circle me-1"></i>Inactiva
                  </span>
                </td>
                <td class="text-end">
                  <Link
                    :href="`/member/listings/${listing?.id}/vcards/${vcard.id}/seo`"
                    class="btn btn-outline-primary btn-sm"
                  >
                    <i class="bi bi-graph-up me-1"></i>
                    Configurar SEO
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
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
const vcards = computed(() => page.props.vcards || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: listing.value?.name || 'Negocio', href: `/member/listings/${listing.value?.id}/modules` },
  { label: 'SEO vCards', active: true },
])

function vcardPublicUrl(slug) {
  return `${window.location.origin}/v/${slug}`
}
</script>

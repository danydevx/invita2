<template>
  <AdminLayout>
    <Head title="Detalle invitacion" />

    <PageHeader :title="'Detalle invitacion'" :breadcrumbs="breadcrumbs" backHref="/admin/invitations">
      <template #actions>
        <button class="btn btn-outline-primary" type="button" @click="resend">Reenviar</button>
        <button class="btn btn-outline-danger" type="button" @click="revoke">Revocar</button>
      </template>
    </PageHeader>

    <div class="row g-3">
      <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h2 class="h5 mb-2">{{ invitation.email }}</h2>
            <div class="text-muted">Estado: {{ invitation.status }}</div>
            <div class="text-muted">Expira: {{ invitation.expires_at || '-' }}</div>
          </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
          <div class="card-body">
            <h3 class="h6">Metadata</h3>
            <div v-if="formattedMetadata" class="bg-light border rounded p-3" style="white-space: pre-wrap">
              {{ formattedMetadata }}
            </div>
            <div v-else class="text-muted">Sin metadata.</div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h3 class="h6">Detalle</h3>
            <dl class="row mb-0">
              <dt class="col-5 text-muted">Rol</dt>
              <dd class="col-7">{{ invitation.role_name || '-' }}</dd>
              <dt class="col-5 text-muted">Aceptada</dt>
              <dd class="col-7">{{ invitation.accepted_at || '-' }}</dd>
              <dt class="col-5 text-muted">Revocada</dt>
              <dd class="col-7">{{ invitation.revoked_at || '-' }}</dd>
              <dt class="col-5 text-muted">Creada</dt>
              <dd class="col-7">{{ invitation.created_at }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  invitation: {
    type: Object,
    required: true,
  },
})

const breadcrumbs = [
  { label: 'Invitaciones', href: '/admin/invitations' },
  { label: `#${props.invitation.id}`, active: true },
]

const formattedMetadata = computed(() => {
  if (!props.invitation.metadata || Object.keys(props.invitation.metadata).length === 0) {
    return ''
  }
  return JSON.stringify(props.invitation.metadata, null, 2)
})

const revoke = () => {
  if (!confirm('Revocar esta invitacion?')) return
  router.put(`/admin/invitations/${props.invitation.id}/revoke`)
}

const resend = () => {
  router.post(`/admin/invitations/${props.invitation.id}/resend`)
}
</script>

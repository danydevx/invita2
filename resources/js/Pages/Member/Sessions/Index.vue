<template>
  <MemberLayout>
    <Head title="Sesiones" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Sesiones activas</h1>
        <p class="text-muted mb-0">Gestiona las sesiones abiertas en tu cuenta.</p>
      </div>
      <button class="btn btn-outline-danger btn-sm" type="button" @click="closeOthers">
        Cerrar otras sesiones
      </button>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Estado</th>
              <th scope="col">IP</th>
              <th scope="col">Dispositivo</th>
              <th scope="col">Ultima actividad</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="sessions.length === 0">
              <td colspan="5" class="text-center text-muted py-4">No hay sesiones activas.</td>
            </tr>
            <tr v-for="session in sessions" :key="session.id">
              <td>
                <span v-if="session.is_current" class="badge text-bg-success">Sesion actual</span>
                <span v-else class="badge text-bg-secondary">Activa</span>
              </td>
              <td class="text-muted">{{ session.ip_address || '-' }}</td>
              <td class="text-muted">{{ session.user_agent }}</td>
              <td class="text-muted">{{ session.last_activity }}</td>
              <td class="text-end">
                <button
                  class="btn btn-sm btn-outline-danger"
                  type="button"
                  :disabled="session.is_current"
                  @click="closeSession(session)"
                >
                  Cerrar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'

const props = defineProps({
  sessions: {
    type: Array,
    default: () => [],
  },
})

const closeSession = (session) => {
  if (session.is_current) return
  if (!confirm('Cerrar esta sesion?')) return
  router.delete(`/member/sessions/${session.id}`, { preserveScroll: true })
}

const closeOthers = () => {
  if (!confirm('Cerrar todas las otras sesiones?')) return
  router.delete('/member/sessions/others', { preserveScroll: true })
}
</script>

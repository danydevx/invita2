<template>
  <MemberLayout>
    <Head title="Soporte" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Soporte</h1>
        <p class="text-muted mb-0">Consulta y crea solicitudes de ayuda.</p>
      </div>
      <Link href="/member/support/create" class="btn btn-primary btn-sm">Nuevo ticket</Link>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Asunto</th>
              <th scope="col">Estado</th>
              <th scope="col">Prioridad</th>
              <th scope="col">Ultima respuesta</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="tickets.data.length === 0">
              <td colspan="5" class="text-center text-muted py-4">No tienes tickets abiertos.</td>
            </tr>
            <tr v-for="ticket in tickets.data" :key="ticket.id">
              <td class="fw-semibold">{{ ticket.subject }}</td>
              <td>
                <span class="badge" :class="statusClass(ticket.status)">{{ ticket.status }}</span>
              </td>
              <td class="text-muted">{{ ticket.priority || '-' }}</td>
              <td class="text-muted">{{ ticket.last_reply_at || ticket.created_at }}</td>
              <td class="text-end">
                <Link :href="`/member/support/${ticket.id}`" class="btn btn-sm btn-outline-primary">
                  Ver
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ tickets.data.length }} de {{ tickets.total }} registros
        </div>
        <Pagination :links="tickets.links" />
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import Pagination from '@/Components/Member/Pagination.vue'

const props = defineProps({
  tickets: {
    type: Object,
    required: true,
  },
})

const statusClass = (value) => {
  if (value === 'open') return 'text-bg-success'
  if (value === 'pending') return 'text-bg-warning'
  if (value === 'answered') return 'text-bg-primary'
  if (value === 'closed') return 'text-bg-secondary'
  return 'text-bg-secondary'
}
</script>

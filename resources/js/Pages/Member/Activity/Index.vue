<template>
  <MemberLayout>
    <Head title="Actividad" />

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
      <div>
        <h1 class="h4 mb-1">Actividad</h1>
        <p class="text-muted mb-0">Historial de eventos de tu cuenta.</p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <Link href="/member/account" class="btn btn-outline-secondary btn-sm">Ver cuenta</Link>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th scope="col">Fecha</th>
              <th scope="col">Tipo</th>
              <th scope="col">Descripcion</th>
              <th scope="col">Entidad</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="activities.data.length === 0">
              <td colspan="4" class="text-center text-muted py-4">No hay actividad registrada.</td>
            </tr>
            <tr v-for="activity in activities.data" :key="activity.id">
              <td class="text-muted">{{ activity.created_at }}</td>
              <td class="fw-semibold">{{ activity.type }}</td>
              <td class="text-muted">{{ activity.description || '-' }}</td>
              <td class="text-muted">
                <span v-if="activity.subject_type">{{ activity.subject_type }} #{{ activity.subject_id }}</span>
                <span v-else>-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="text-muted small">
          Mostrando {{ activities.data.length }} de {{ activities.total }} registros
        </div>
        <Pagination :links="activities.links" />
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import Pagination from '@/Components/Member/Pagination.vue'

const props = defineProps({
  activities: {
    type: Object,
    required: true,
  },
})
</script>

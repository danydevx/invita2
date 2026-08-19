<template>
  <div class="exception-dates-list">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Excepciones Configuradas</h5>
      <button type="button" class="btn btn-primary btn-sm" @click="$emit('create')">
        <i class="bi bi-plus-lg me-1"></i>
        Agregar Excepción
      </button>
    </div>

    <div v-if="exceptions.length === 0" class="text-center text-muted py-4">
      <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
      No hay excepciones configuradas.
    </div>

    <div v-else class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Razón</th>
            <th>Estado</th>
            <th>Horario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="exception in sortedExceptions" :key="exception.id">
            <td>
              <strong>{{ formatDate(exception.exception_date) }}</strong>
              <br>
              <small class="text-muted">{{ getDayName(exception.exception_date) }}</small>
            </td>
            <td>{{ exception.reason || '-' }}</td>
            <td>
              <span v-if="exception.is_available" class="badge bg-success">
                <i class="bi bi-check-circle me-1"></i>Abierto
              </span>
              <span v-else class="badge bg-danger">
                <i class="bi bi-x-circle me-1"></i>Cerrado
              </span>
            </td>
            <td>
              <span v-if="exception.is_available && exception.start_time && exception.end_time">
                {{ exception.start_time }} - {{ exception.end_time }}
              </span>
              <span v-else class="text-muted">-</span>
            </td>
            <td>
              <button
                type="button"
                class="btn btn-sm btn-outline-primary me-1"
                @click="$emit('edit', exception)"
                title="Editar"
              >
                <i class="bi bi-pencil"></i>
              </button>
              <button
                type="button"
                class="btn btn-sm btn-outline-danger"
                :disabled="deletingId === exception.id"
                @click="deleteException(exception)"
                title="Eliminar"
              >
                <i :class="deletingId === exception.id ? 'bi bi-hourglass-split' : 'bi bi-trash'"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  exceptions: {
    type: Array,
    required: true,
  },
  businessId: {
    type: [Number, String],
    required: true,
  },
})

const emit = defineEmits(['create', 'edit', 'saved', 'deleted'])

const deletingId = ref(null)

const sortedExceptions = computed(() => {
  return [...props.exceptions].sort((a, b) =>
    new Date(a.exception_date) - new Date(b.exception_date)
  )
})

const deleteException = (exception) => {
  if (!confirm(`¿Eliminar la excepción del ${formatDate(exception.exception_date)}?\n\nEsta acción no se puede deshacer.`)) {
    return
  }

  deletingId.value = exception.id

  router.delete(
    `/member/businesses/${props.businessId}/appointments/availability/exceptions/${exception.id}`,
    {
      preserveScroll: true,
      onSuccess: () => {
        deletingId.value = null
        emit('deleted')
      },
      onError: () => {
        deletingId.value = null
      },
      onFinish: () => {
        deletingId.value = null
      },
    }
  )
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr + 'T00:00:00')
  return date.toLocaleDateString('es-AR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  })
}

const getDayName = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr + 'T00:00:00')
  const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
  return days[date.getDay()]
}
</script>

<style scoped>
.exception-dates-list {
  background: white;
  padding: 1rem;
  border-radius: 0.5rem;
}
</style>
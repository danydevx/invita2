<template>
  <div
    class="modal fade"
    :class="{ show: isOpen }"
    :style="{ display: isOpen ? 'block' : 'none' }"
    tabindex="-1"
    aria-labelledby="exceptionModalLabel"
    :aria-hidden="!isOpen"
    ref="modalRef"
  >
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exceptionModalLabel">
            {{ isEditing ? 'Editar Excepción' : 'Nueva Excepción' }}
          </h5>
          <button type="button" class="btn-close" @click="close" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form @submit.prevent="submit" class="exception-form">
            <FieldDate
              id="exception_date"
              label="Fecha"
              v-model="form.exception_date"
              :formError="errors.exception_date"
              :min="today"
              required
            />

            <div class="form-check form-switch mt-3 mb-3">
              <input
                class="form-check-input"
                type="checkbox"
                role="switch"
                id="is_available"
                v-model="form.is_available"
              />
              <label class="form-check-label" for="is_available">
                Disponible (desmarcar para día cerrado)
              </label>
            </div>

            <div v-if="form.is_available" class="row g-2">
              <div class="col-6">
                <FieldTime
                  id="start_time"
                  label="Hora inicio"
                  v-model="form.start_time"
                  :formError="errors.start_time"
                />
              </div>
              <div class="col-6">
                <FieldTime
                  id="end_time"
                  label="Hora fin"
                  v-model="form.end_time"
                  :formError="errors.end_time"
                />
              </div>
              <div class="col-12 mt-2">
                <FieldNumber
                  id="slots_per_slot"
                  label="Cupos por slot"
                  v-model="form.slots_per_slot"
                  :min="1"
                  :max="100"
                  :step="1"
                  placeholder="Usar valor del horario semanal"
                />
                <small class="text-muted d-block">Dejar vacío para usar el valor del horario semanal.</small>
              </div>
            </div>

            <FieldTextarea
              id="reason"
              label="Razón"
              v-model="form.reason"
              :rows="2"
              placeholder="Ej: Navidad, Vacaciones, Feriado..."
              class="mt-3"
            />

            <div v-if="serverError" class="alert alert-danger mt-3">
              {{ serverError }}
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="close">Cancelar</button>
          <button type="button" class="btn btn-primary" :disabled="saving" @click="submit">
            {{ saving ? 'Guardando...' : 'Guardar' }}
          </button>
        </div>
      </div>
    </div>
  </div>
  <div
    v-if="isOpen"
    class="modal-backdrop fade show"
    @click="close"
  ></div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldTime from '@/Components/Fields/FieldTime.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const props = defineProps({
  businessId: {
    type: [Number, String],
    required: true,
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
})

const emit = defineEmits(['saved'])

const modalRef = ref(null)
const isOpen = ref(false)
const isEditing = ref(false)
const saving = ref(false)
const serverError = ref('')

const today = computed(() => new Date().toISOString().split('T')[0])

const defaultForm = {
  exception_date: '',
  is_available: false,
  start_time: '',
  end_time: '',
  reason: '',
  slots_per_slot: null,
}

const form = reactive({ ...defaultForm })

let currentException = null

const open = (exception = null) => {
  currentException = exception
  isEditing.value = !!exception

  if (exception) {
    Object.assign(form, {
      exception_date: exception.exception_date,
      is_available: exception.is_available,
      start_time: exception.start_time || '',
      end_time: exception.end_time || '',
      reason: exception.reason || '',
      slots_per_slot: exception.slots_per_slot ?? null,
    })
  } else {
    Object.assign(form, defaultForm)
  }

  serverError.value = ''
  isOpen.value = true
  document.body.classList.add('modal-open')

  setTimeout(() => {
    const firstInput = modalRef.value?.querySelector('input, textarea')
    if (firstInput) firstInput.focus()
  }, 100)
}

const close = () => {
  isOpen.value = false
  document.body.classList.remove('modal-open')
  currentException = null
}

const submit = () => {
  saving.value = true
  serverError.value = ''

  router.post(
    `/member/businesses/${props.businessId}/appointments/availability/exceptions`,
    { ...form },
    {
      preserveScroll: true,
      onSuccess: () => {
        saving.value = false
        close()
        emit('saved')
      },
      onError: (errs) => {
        saving.value = false
        serverError.value = Object.values(errs).flat().join(', ')
      },
      onFinish: () => {
        saving.value = false
      },
    }
  )
}

defineExpose({ open, close })
</script>

<style scoped>
.exception-form :deep(.form-floating) {
  width: 100%;
}
</style>
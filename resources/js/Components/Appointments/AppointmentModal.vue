<template>
  <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true" ref="modalRef">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="appointmentModalLabel">
            {{ mode === 'create' ? 'Nueva Cita' : mode === 'edit' ? 'Editar Cita' : 'Detalle de Cita' }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div v-if="mode === 'view'" class="appointment-detail">
            <div class="row g-3">
              <div class="col-md-6">
                <h6 class="text-muted mb-1">Cliente</h6>
                <p class="mb-0">{{ form.customer_name }}</p>
              </div>
              <div class="col-md-6">
                <h6 class="text-muted mb-1">Email</h6>
                <p class="mb-0">{{ form.customer_email || '-' }}</p>
              </div>
              <div class="col-md-6">
                <h6 class="text-muted mb-1">Telefono</h6>
                <p class="mb-0">{{ form.customer_phone || '-' }}</p>
              </div>
              <div class="col-md-6">
                <h6 class="text-muted mb-1">Servicio</h6>
                <p class="mb-0">{{ selectedServiceName }}</p>
              </div>
              <div class="col-md-6">
                <h6 class="text-muted mb-1">Ubicacion</h6>
                <p class="mb-0">{{ selectedLocationName }}</p>
              </div>
              <div class="col-md-6">
                <h6 class="text-muted mb-1">Fecha</h6>
                <p class="mb-0">{{ formatDate(form.appointment_date) }}</p>
              </div>
              <div class="col-md-6">
                <h6 class="text-muted mb-1">Hora</h6>
                <p class="mb-0">{{ form.start_time }}</p>
              </div>
              <div class="col-md-6">
                <h6 class="text-muted mb-1">Estado</h6>
                <span :class="statusClass(form.status)" class="badge">
                  {{ statusLabel }}
                </span>
              </div>
              <div class="col-12" v-if="form.notes">
                <h6 class="text-muted mb-1">Notas</h6>
                <p class="mb-0">{{ form.notes }}</p>
              </div>
            </div>
          </div>

          <form v-else @submit.prevent="submit" class="appointment-form">
            <div class="row g-3">
              <div class="col-md-6">
                <FieldText
                  id="customer_name"
                  label="Nombre del cliente"
                  v-model="form.customer_name"
                  :formError="errors.customer_name"
                  required
                />
              </div>

              <div class="col-md-6">
                <FieldEmail
                  id="customer_email"
                  label="Email del cliente"
                  v-model="form.customer_email"
                  :formError="errors.customer_email"
                />
              </div>

              <div class="col-md-6">
                <FieldPhone
                  id="customer_phone"
                  label="Telefono"
                  v-model="form.customer_phone"
                />
              </div>

              <div class="col-md-6">
                <FieldSelect
                  id="business_service_id"
                  label="Servicio"
                  v-model="form.business_service_id"
                  :formError="errors.business_service_id"
                  required
                >
                  <option :value="null" disabled>Seleccionar servicio</option>
                  <option v-for="svc in services" :key="svc.id" :value="svc.id">
                    {{ svc.name }} ({{ svc.duration_minutes }} min)
                  </option>
                </FieldSelect>
              </div>

              <div class="col-md-6">
                <FieldSelect
                  id="business_location_id"
                  label="Ubicacion"
                  v-model="form.business_location_id"
                  :formError="errors.business_location_id"
                >
                  <option :value="null">Sin ubicacion</option>
                  <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                    {{ loc.name }}
                  </option>
                </FieldSelect>
              </div>

              <div class="col-md-4">
                <FieldDate
                  id="appointment_date"
                  label="Fecha"
                  v-model="form.appointment_date"
                  :formError="errors.appointment_date"
                  :min="today"
                  required
                />
              </div>

              <div class="col-md-4">
                <FieldTime
                  id="start_time"
                  label="Hora"
                  v-model="form.start_time"
                  :formError="errors.start_time"
                  required
                />
              </div>

              <div class="col-md-4" v-if="mode === 'edit'">
                <FieldSelect
                  id="status"
                  label="Estado"
                  v-model="form.status"
                  :formError="errors.status"
                >
                  <option value="pending">Pendiente</option>
                  <option value="confirmed">Confirmada</option>
                  <option value="completed">Completada</option>
                  <option value="cancelled">Cancelada</option>
                  <option value="no_show">No asistio</option>
                </FieldSelect>
              </div>

              <div class="col-12">
                <FieldTextarea
                  id="notes"
                  label="Notas"
                  v-model="form.notes"
                  :rows="2"
                  placeholder="Notas adicionales..."
                />
              </div>
            </div>

            <div v-if="serverError" class="alert alert-danger mt-3">
              {{ serverError }}
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button v-if="mode === 'view'" type="button" class="btn btn-primary" @click="switchToEdit">
            <i class="bi bi-pencil me-1"></i>Editar
          </button>

          <template v-else>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" :disabled="sending" @click="submit">
              {{ sending ? 'Guardando...' : (mode === 'create' ? 'Crear Cita' : 'Guardar Cambios') }}
            </button>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldTime from '@/Components/Fields/FieldTime.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const props = defineProps({
  services: {
    type: Array,
    default: () => [],
  },
  locations: {
    type: Array,
    default: () => [],
  },
  businessId: {
    type: [Number, String],
    required: true,
  },
})

const emit = defineEmits(['saved', 'cancelled'])

const modalRef = ref(null)
const mode = ref('view')
const sending = ref(false)
const serverError = ref('')
const errors = ref({})

const today = computed(() => new Date().toISOString().split('T')[0])

const defaultForm = {
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  business_service_id: null,
  business_location_id: null,
  appointment_date: '',
  start_time: '',
  notes: '',
  status: 'pending',
}

const form = reactive({ ...defaultForm })

let modalInstance = null
let appointmentId = null

const selectedServiceName = computed(() => {
  const svc = props.services.find(s => s.id === form.business_service_id)
  return svc ? svc.name : '-'
})

const selectedLocationName = computed(() => {
  const loc = props.locations.find(l => l.id === form.business_location_id)
  return loc ? loc.name : '-'
})

const statusLabel = computed(() => {
  const labels = {
    pending: 'Pendiente',
    confirmed: 'Confirmada',
    completed: 'Completada',
    cancelled: 'Cancelada',
    no_show: 'No asistio',
  }
  return labels[form.status] || form.status
})

const statusClass = (status) => {
  const classes = {
    pending: 'bg-warning text-dark',
    confirmed: 'bg-success',
    cancelled: 'bg-secondary',
    completed: 'bg-primary',
    no_show: 'bg-danger',
  }
  return classes[status] || 'bg-secondary'
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('es-AR', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const openCreate = (data = {}) => {
  mode.value = 'create'
  appointmentId = null
  resetForm()
  if (data.date) form.appointment_date = data.date
  if (data.time) form.start_time = data.time
  serverError.value = ''
  errors.value = {}
  initModal()
}

const normalizeTime = (time) => {
  if (!time) return ''
  const trimmed = time.toString().trim()
  const match = trimmed.match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?/)
  if (match) {
    const hours = match[1].padStart(2, '0')
    const minutes = match[2]
    const result = `${hours}:${minutes}`
    console.log('normalizeTime:', time, '→', result)
    return result
  }
  if (trimmed.includes('T')) {
    try {
      const d = new Date(trimmed)
      if (!isNaN(d.getTime())) {
        const h = String(d.getHours()).padStart(2, '0')
        const m = String(d.getMinutes()).padStart(2, '0')
        const result = `${h}:${m}`
        console.log('normalizeTime (from Date):', time, '→', result)
        return result
      }
    } catch (e) {
      // ignore
    }
  }
  console.log('normalizeTime (no match):', time)
  return trimmed
}

const normalizeDate = (date) => {
  if (!date) return ''
  const trimmed = date.toString().trim()
  const match = trimmed.match(/^(\d{4})-(\d{2})-(\d{2})/)
  if (match) {
    return `${match[1]}-${match[2]}-${match[3]}`
  }
  return trimmed
}

const openEdit = (appointment) => {
  console.log('openEdit - raw appointment:', JSON.stringify(appointment))
  console.log('openEdit - raw start_time:', appointment.start_time, 'type:', typeof appointment.start_time)
  mode.value = 'edit'
  appointmentId = appointment.id
  form.customer_name = appointment.customer_name || ''
  form.customer_email = appointment.customer_email || ''
  form.customer_phone = appointment.customer_phone || ''
  form.business_service_id = appointment.business_service_id || null
  form.business_location_id = appointment.business_location_id || null
  form.appointment_date = appointment.appointment_date || ''
  form.start_time = normalizeTime(appointment.start_time)
  console.log('openEdit - form.start_time set to:', form.start_time)
  form.notes = appointment.notes || ''
  form.status = appointment.status || 'pending'
  serverError.value = ''
  errors.value = {}
  initModal()
}

const openView = (appointment) => {
  console.log('openView called:', appointment)
  mode.value = 'view'
  appointmentId = appointment.id
  form.customer_name = appointment.customer_name || ''
  form.customer_email = appointment.customer_email || ''
  form.customer_phone = appointment.customer_phone || ''
  form.business_service_id = appointment.business_service_id || null
  form.business_location_id = appointment.business_location_id || null
  form.appointment_date = appointment.appointment_date || ''
  form.start_time = normalizeTime(appointment.start_time)
  form.notes = appointment.notes || ''
  form.status = appointment.status || 'pending'
  serverError.value = ''
  errors.value = {}
  initModal()
}

const switchToEdit = () => {
  mode.value = 'edit'
}

const resetForm = () => {
  Object.assign(form, defaultForm)
}

const initModal = () => {
  console.log('initModal called, modalRef:', modalRef.value)
  if (modalRef.value) {
    if (typeof bootstrap !== 'undefined') {
      modalInstance = new bootstrap.Modal(modalRef.value)
      modalInstance.show()
      console.log('Modal shown')
    } else {
      console.error('Bootstrap not available')
    }
  } else {
    console.error('modalRef is null')
  }
}

const hideModal = () => {
  if (modalInstance) {
    modalInstance.hide()
  }
}

const submit = () => {
  console.log('=== SUBMIT STARTED ===')
  console.log('form.start_time before normalize:', JSON.stringify(form.start_time), 'type:', typeof form.start_time)
  console.log('form.start_time length:', form.start_time ? form.start_time.length : 'null')

  sending.value = true
  serverError.value = ''
  errors.value = {}

  const url = mode.value === 'create'
    ? `/member/businesses/${props.businessId}/appointments`
    : `/member/businesses/${props.businessId}/appointments/${appointmentId}`

  const method = mode.value === 'create' ? 'post' : 'put'

  const formData = {
    ...form,
    start_time: normalizeTime(form.start_time),
    appointment_date: normalizeDate(form.appointment_date),
  }

  console.log('formData.start_time:', JSON.stringify(formData.start_time), 'length:', formData.start_time.length)
  console.log('Full formData:', JSON.stringify(formData))

  console.log('Submitting appointment:', formData)

  router[method](url, formData, {
    onSuccess: () => {
      sending.value = false
      hideModal()
      emit('saved')
    },
    onError: (errs) => {
      sending.value = false
      errors.value = errs
      serverError.value = Object.values(errs).flat().join(', ')
    },
  })
}

defineExpose({
  openCreate,
  openEdit,
  openView,
})
</script>

<style scoped>
.appointment-detail h6 {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.appointment-detail p {
  font-size: 0.95rem;
}

.modal-footer .btn {
  min-width: 120px;
}
</style>

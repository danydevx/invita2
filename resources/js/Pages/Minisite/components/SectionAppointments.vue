<template>
  <section class="section-appointments">
    <div class="section-appointments__inner">
      <h2 v-if="title" class="section-appointments__title">{{ title }}</h2>
      <h3 v-if="subtitle" class="section-appointments__subtitle">{{ subtitle }}</h3>
      <p v-if="description" class="section-appointments__description-text">{{ description }}</p>

      <form @submit.prevent="submitForm" class="section-appointments__form">
        <div class="row g-3">
          <div class="col-md-6" v-if="showServiceSelector && services.length">
            <label for="service_id" class="form-label">
              Servicio <span class="text-danger">*</span>
            </label>
            <select
              id="service_id"
              v-model="form.service_id"
              class="form-select"
              required
              @change="onServiceChange"
            >
              <option value="">Selecciona un servicio...</option>
              <option v-for="svc in services" :key="svc.id" :value="svc.id">
                {{ svc.name }} ({{ svc.duration_minutes }} min)
                <template v-if="svc.price"> - ${{ svc.price }}</template>
              </option>
            </select>
          </div>

          <div class="col-md-6" v-if="showLocationSelector && locations.length">
            <label for="location_id" class="form-label">
              Ubicación
            </label>
            <select
              id="location_id"
              v-model="form.location_id"
              class="form-select"
            >
              <option value="">Cualquier ubicación</option>
              <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                {{ loc.name }}
              </option>
            </select>
          </div>

          <div class="col-md-6" v-if="!showServiceSelector || !services.length">
            <label for="service_simple" class="form-label">
              Servicio requerido <span class="text-danger">*</span>
            </label>
            <input
              id="service_simple"
              type="text"
              v-model="form.service_name"
              class="form-control"
              placeholder="¿Qué servicio necesitas?"
              required
            />
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-6">
            <label for="appointment_date" class="form-label">
              Fecha <span class="text-danger">*</span>
            </label>
            <input
              id="appointment_date"
              type="date"
              v-model="form.appointment_date"
              class="form-control"
              :min="minDate"
              required
              @change="onDateChange"
            />
          </div>

          <div class="col-md-6">
            <label for="start_time" class="form-label">
              Hora <span class="text-danger">*</span>
            </label>
            <select
              id="start_time"
              v-model="form.start_time"
              class="form-select"
              required
              :disabled="!form.appointment_date || loadingSlots"
            >
              <option value="">
                {{ loadingSlots ? 'Cargando...' : 'Selecciona una hora...' }}
              </option>
              <option
                v-for="slot in availableSlots"
                :key="slot.start_time"
                :value="slot.start_time"
                :disabled="!slot.available"
              >
                {{ slot.start_time }} - {{ slot.end_time }}
                <template v-if="!slot.available"> (No disponible)</template>
              </option>
            </select>
            <div v-if="form.appointment_date && !loadingSlots && availableSlots.length === 0" class="form-text text-danger">
              No hay horarios disponibles para esta fecha.
            </div>
          </div>
        </div>

        <hr class="my-4" />

        <div class="row g-3">
          <div class="col-md-6">
            <label for="customer_name" class="form-label">
              Nombre <span class="text-danger">*</span>
            </label>
            <input
              id="customer_name"
              type="text"
              v-model="form.customer_name"
              class="form-control"
              placeholder="Tu nombre completo"
              required
            />
          </div>

          <div class="col-md-6">
            <label for="customer_email" class="form-label">
              Correo electrónico <span class="text-danger">*</span>
            </label>
            <input
              id="customer_email"
              type="email"
              v-model="form.customer_email"
              class="form-control"
              placeholder="tu@email.com"
              required
            />
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-md-6">
            <label for="customer_phone" class="form-label">
              Teléfono
            </label>
            <input
              id="customer_phone"
              type="tel"
              v-model="form.customer_phone"
              class="form-control"
              placeholder="(555) 123-4567"
            />
          </div>

          <div class="col-md-6">
            <label for="notes" class="form-label">
              Notas adicionales
            </label>
            <textarea
              id="notes"
              v-model="form.notes"
              class="form-control"
              rows="2"
              placeholder="Comentarios o instrucciones especiales"
            ></textarea>
          </div>
        </div>

        <div class="mt-4 text-center">
          <button
            type="submit"
            class="btn btn-primary btn-lg px-5"
            :disabled="sending || !isFormValid"
          >
            {{ sending ? 'Reservando...' : 'Reservar cita' }}
          </button>
        </div>

        <div v-if="successMessage" class="alert alert-success mt-3">
          <i class="bi bi-check-circle me-2"></i>
          {{ successMessage }}
        </div>

        <div v-if="errorMessage" class="alert alert-danger mt-3">
          <i class="bi bi-exclamation-triangle me-2"></i>
          {{ errorMessage }}
        </div>
      </form>
    </div>
  </section>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'

const props = defineProps({
  title: String,
  subtitle: String,
  services: {
    type: Array,
    default: () => [],
  },
  locations: {
    type: Array,
    default: () => [],
  },
  availableDays: {
    type: Array,
    default: () => [],
  },
  config: Object,
  description: String,
  buttons: {
    type: Array,
    default: () => [],
  },
  businessSlug: {
    type: String,
    required: true,
  },
})

const showServiceSelector = computed(() => {
  return props.config?.show_service_selector !== false
})

const showLocationSelector = computed(() => {
  return props.config?.show_location_selector !== false && props.locations.length > 0
})

const minDate = computed(() => {
  const today = new Date()
  return today.toISOString().split('T')[0]
})

const form = reactive({
  service_id: '',
  service_name: '',
  location_id: '',
  appointment_date: '',
  start_time: '',
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  notes: '',
})

const availableSlots = ref([])
const loadingSlots = ref(false)
const sending = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const isFormValid = computed(() => {
  const hasService = (showServiceSelector.value && props.services.length)
    ? form.service_id
    : form.service_name
  return (
    hasService &&
    form.appointment_date &&
    form.start_time &&
    form.customer_name &&
    form.customer_email
  )
})

const onServiceChange = () => {
  form.start_time = ''
  availableSlots.value = []
  if (form.appointment_date) {
    fetchSlots()
  }
}

const onDateChange = () => {
  form.start_time = ''
  availableSlots.value = []
  if (form.appointment_date) {
    fetchSlots()
  }
}

const fetchSlots = async () => {
  if (!form.appointment_date) return

  loadingSlots.value = true
  errorMessage.value = ''

  let url = `/api/book/business/${props.businessSlug}/slots?date=${form.appointment_date}`
  if (form.service_id) {
    url += `&service_id=${form.service_id}`
  }

  try {
    const response = await fetch(url)
    const data = await response.json()

    if (response.ok) {
      availableSlots.value = data.slots || []
    } else {
      errorMessage.value = data.error || 'Error al cargar horarios disponibles.'
      availableSlots.value = []
    }
  } catch (error) {
    console.error('Error fetching slots:', error)
    errorMessage.value = 'Error de conexión. Intenta de nuevo.'
    availableSlots.value = []
  } finally {
    loadingSlots.value = false
  }
}

const submitForm = async () => {
  if (!isFormValid.value) return

  sending.value = true
  successMessage.value = ''
  errorMessage.value = ''

  if (!form.service_id && !form.service_name) {
    errorMessage.value = 'Por favor completa la información del servicio.'
    sending.value = false
    return
  }

  const payload = {
    location_id: form.location_id ? parseInt(form.location_id) : null,
    appointment_date: form.appointment_date,
    start_time: form.start_time,
    customer_name: form.customer_name,
    customer_email: form.customer_email,
    customer_phone: form.customer_phone || null,
    notes: form.service_name ? `Servicio solicitado: ${form.service_name}\n${form.notes || ''}` : form.notes,
  }

  if (form.service_id) {
    payload.service_id = parseInt(form.service_id)
  }

  try {
    const response = await fetch(`/api/book/business/${props.businessSlug}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
    })

    const data = await response.json()

    if (response.ok) {
      successMessage.value = data.message || '¡Cita reservada correctamente! Te contactaremos pronto.'
      resetForm()
    } else {
      if (data.errors) {
        const firstError = Object.values(data.errors).flat()[0]
        errorMessage.value = firstError
      } else {
        errorMessage.value = data.error || 'Error al reservar la cita.'
      }
    }
  } catch (error) {
    console.error('Error submitting form:', error)
    errorMessage.value = 'Error de conexión. Intenta de nuevo.'
  } finally {
    sending.value = false
  }
}

const resetForm = () => {
  form.service_id = ''
  form.service_name = ''
  form.location_id = ''
  form.appointment_date = ''
  form.start_time = ''
  form.customer_name = ''
  form.customer_email = ''
  form.customer_phone = ''
  form.notes = ''
  availableSlots.value = []
}
</script>

<style lang="less">
.section-appointments {
  padding: 48px 16px;
  background: #f8f9fa;

  &__inner {
    max-width: 1024px;
    margin: 0 auto;
  }

  &__title {
    font-weight: 700;
    margin: 0 0 8px;
    text-align: center;
    color: #212529;
  }

  &__subtitle {
    font-weight: 600;
    color: #495057;
    text-align: center;
    margin: 0 0 16px;
  }

  &__description-text {
    font-size: 1rem;
    color: #6c757d;
    text-align: center;
    margin: 0 0 16px;
  }

  &__form {
    background: #fff;
    padding: 32px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }
}
</style>

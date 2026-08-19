<template>
    <div class="booking-widget" :class="{ 'booking-widget--compact': compact }">
        <div v-if="!compact && !hideTitle" class="booking-widget__header mb-4">
            <h3 class="mb-1">
                <i class="bi bi-calendar-check me-2"></i>Reservar cita
            </h3>
            <p class="text-muted mb-0">Selecciona negocio, servicio y horario disponible.</p>
        </div>

        <div v-if="successMessage" class="alert alert-success" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ successMessage }}
        </div>

        <div v-if="errorMessage" class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ errorMessage }}
        </div>

        <div v-if="!hideBusinessSelect" class="mb-3">
            <label class="form-label fw-bold">Negocio *</label>
            <select
                v-model="state.businessId"
                class="form-select"
                :disabled="loadingBusinesses"
                @change="onBusinessChange"
                required
            >
                <option value="">{{ loadingBusinesses ? 'Cargando negocios...' : 'Seleccionar negocio...' }}</option>
                <option v-for="b in businesses" :key="b.id" :value="b.id">
                    {{ b.name }}
                </option>
            </select>
        </div>

        <div v-if="state.businessId" class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Servicio *</label>
                <select
                    v-model="state.serviceId"
                    class="form-select"
                    :disabled="loadingServices"
                    @change="onServiceChange"
                    required
                >
                    <option value="">{{ loadingServices ? 'Cargando servicios...' : 'Seleccionar servicio...' }}</option>
                    <option v-for="s in services" :key="s.id" :value="s.id">
                        {{ s.name }} - {{ formatPrice(s.price) }} ({{ s.duration_minutes }} min)
                    </option>
                </select>
            </div>

            <div class="col-md-6" v-if="locations.length > 0">
                <label class="form-label fw-bold">Ubicación</label>
                <select v-model="state.locationId" class="form-select">
                    <option value="">Sin ubicación específica</option>
                    <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                        {{ loc.name }}
                    </option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Fecha *</label>
                <input
                    type="date"
                    v-model="state.appointmentDate"
                    class="form-control"
                    :min="minDate"
                    @change="onDateChange"
                    required
                />
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Horario *</label>
                <select
                    v-model="state.startTime"
                    class="form-select"
                    :disabled="loadingSlots || slots.length === 0"
                    required
                >
                    <option value="">
                        {{ loadingSlots ? 'Cargando horarios...' : (slots.length === 0 ? 'No hay horarios disponibles' : 'Seleccionar horario...') }}
                    </option>
                    <option v-for="slot in slots" :key="slot.start_time" :value="slot.start_time" :disabled="!slot.available">
                        {{ slot.start_time }} - {{ slot.end_time }}
                        <template v-if="slot.available && slot.remaining_capacity < 999">({{ slot.remaining_capacity }} cupo(s))</template>
                        <template v-if="!slot.available">(ocupado)</template>
                    </option>
                </select>
            </div>

            <div class="col-12"><hr class="my-2"></div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Tu nombre *</label>
                <input v-model="state.customerName" type="text" class="form-control" required maxlength="150" />
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Tu email *</label>
                <input v-model="state.customerEmail" type="email" class="form-control" required maxlength="150" />
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">Tu teléfono</label>
                <input v-model="state.customerPhone" type="tel" class="form-control" maxlength="50" />
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Notas adicionales</label>
                <textarea v-model="state.notes" class="form-control" rows="2" maxlength="2000"></textarea>
            </div>

            <div v-if="fieldErrors.start_time" class="col-12">
                <div class="alert alert-danger mb-0 py-2">{{ fieldErrors.start_time }}</div>
            </div>

            <div class="col-12">
                <button
                    type="button"
                    class="btn btn-primary btn-lg w-100"
                    :disabled="!canSubmit || submitting"
                    @click="submit"
                >
                    <span v-if="submitting">
                        <i class="bi bi-hourglass-split me-2"></i>Reservando...
                    </span>
                    <span v-else>
                        <i class="bi bi-check-lg me-2"></i>Confirmar reserva
                    </span>
                </button>
            </div>
        </div>

        <div v-if="compact && selectedService" class="booking-widget__summary mt-3 p-3 bg-light rounded">
            <h5 class="mb-2">Tu reserva</h5>
            <ul class="list-unstyled mb-0 small">
                <li><strong>Servicio:</strong> {{ selectedService.name }}</li>
                <li><strong>Duración:</strong> {{ selectedService.duration_minutes }} minutos</li>
                <li v-if="selectedService.price"><strong>Precio:</strong> {{ formatPrice(selectedService.price) }}</li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'

const props = defineProps({
    initialBusinessSlug: { type: String, default: '' },
    apiBase: { type: String, default: '/api/book' },
    compact: { type: Boolean, default: false },
    hideTitle: { type: Boolean, default: false },
    hideBusinessSelect: { type: Boolean, default: false },
})

const emit = defineEmits(['booked'])

const state = reactive({
    businessId: '',
    serviceId: '',
    locationId: '',
    appointmentDate: '',
    startTime: '',
    customerName: '',
    customerEmail: '',
    customerPhone: '',
    notes: '',
})

const businesses = ref([])
const services = ref([])
const locations = ref([])
const slots = ref([])

const loadingBusinesses = ref(false)
const loadingServices = ref(false)
const loadingSlots = ref(false)
const submitting = ref(false)

const successMessage = ref('')
const errorMessage = ref('')
const fieldErrors = ref({})

const minDate = computed(() => new Date().toISOString().split('T')[0])

const selectedService = computed(() =>
    services.value.find(s => s.id === Number(state.serviceId)) || null
)

const canSubmit = computed(() => {
    return state.businessId
        && state.serviceId
        && state.appointmentDate
        && state.startTime
        && state.customerName.trim()
        && /\S+@\S+\.\S+/.test(state.customerEmail)
})

const formatPrice = (price) => {
    if (price === null || price === undefined) return '-'
    return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(price)
}

const clearMessages = () => {
    successMessage.value = ''
    errorMessage.value = ''
    fieldErrors.value = {}
}

const fetchJson = async (url, options = {}) => {
    const response = await fetch(url, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(options.headers || {}),
        },
        ...options,
    })

    let data = null
    try {
        data = await response.json()
    } catch (e) {
        data = null
    }

    return { ok: response.ok, status: response.status, data }
}

const loadBusinesses = async () => {
    loadingBusinesses.value = true
    try {
        const { ok, data } = await fetchJson(`${props.apiBase}/businesses/active`)
        if (ok && data?.businesses) {
            businesses.value = data.businesses
            if (props.initialBusinessSlug) {
                const found = businesses.value.find(b => b.slug === props.initialBusinessSlug)
                if (found) {
                    state.businessId = found.id
                    await loadServices()
                }
            }
        }
    } catch (e) {
        errorMessage.value = 'No se pudieron cargar los negocios.'
    } finally {
        loadingBusinesses.value = false
    }
}

const loadServices = async () => {
    if (!state.businessId) return
    loadingServices.value = true
    services.value = []
    locations.value = []
    slots.value = []
    state.serviceId = ''
    state.locationId = ''
    state.startTime = ''

    const business = businesses.value.find(b => b.id === Number(state.businessId))
    if (!business) return

    try {
        const { ok, data } = await fetchJson(`${props.apiBase}/business/${business.slug}/services`)
        if (ok) {
            services.value = data.services || []
            locations.value = data.locations || []
        }
    } catch (e) {
        errorMessage.value = 'No se pudieron cargar los servicios.'
    } finally {
        loadingServices.value = false
    }
}

const loadSlots = async () => {
    if (!state.businessId || !state.serviceId || !state.appointmentDate) return
    loadingSlots.value = true
    slots.value = []
    state.startTime = ''

    const business = businesses.value.find(b => b.id === Number(state.businessId))
    if (!business) return

    try {
        const params = new URLSearchParams({
            date: state.appointmentDate,
            service_id: String(state.serviceId),
        })
        const { ok, data } = await fetchJson(
            `${props.apiBase}/business/${business.slug}/slots?${params}`
        )
        if (ok) {
            slots.value = data.slots || []
        }
    } catch (e) {
        errorMessage.value = 'No se pudieron cargar los horarios.'
    } finally {
        loadingSlots.value = false
    }
}

const onBusinessChange = async () => {
    clearMessages()
    await loadServices()
}

const onServiceChange = () => {
    clearMessages()
    state.startTime = ''
    if (state.appointmentDate) loadSlots()
}

const onDateChange = () => {
    clearMessages()
    state.startTime = ''
    if (state.serviceId) loadSlots()
}

watch(() => state.serviceId, () => {
    if (state.serviceId && state.appointmentDate) loadSlots()
})

watch(() => state.appointmentDate, () => {
    if (state.serviceId && state.appointmentDate) loadSlots()
})

const submit = async () => {
    if (!canSubmit.value) return
    clearMessages()
    submitting.value = true

    const business = businesses.value.find(b => b.id === Number(state.businessId))
    if (!business) {
        submitting.value = false
        return
    }

    const payload = {
        service_id: Number(state.serviceId),
        appointment_date: state.appointmentDate,
        start_time: state.startTime,
        customer_name: state.customerName.trim(),
        customer_email: state.customerEmail.trim(),
        customer_phone: state.customerPhone.trim() || null,
        notes: state.notes.trim() || null,
    }

    if (state.locationId) {
        payload.location_id = Number(state.locationId)
    }

    try {
        const { ok, status, data } = await fetchJson(
            `${props.apiBase}/business/${business.slug}`,
            {
                method: 'POST',
                body: JSON.stringify(payload),
            }
        )

        if (ok) {
            successMessage.value = data?.message || 'Cita reservada correctamente.'
            emit('booked', data)

            state.startTime = ''
            state.customerName = ''
            state.customerEmail = ''
            state.customerPhone = ''
            state.notes = ''
            await loadSlots()
        } else if (status === 422) {
            fieldErrors.value = data?.errors || {}
            errorMessage.value = data?.error || 'Por favor revisa los datos ingresados.'
        } else {
            errorMessage.value = data?.error || data?.message || 'No se pudo crear la cita.'
        }
    } catch (e) {
        errorMessage.value = 'Error de red. Intentá nuevamente.'
    } finally {
        submitting.value = false
    }
}

onMounted(() => {
    if (!props.hideBusinessSelect) {
        loadBusinesses()
    }
})
</script>

<style scoped>
.booking-widget {
    background: #fff;
    padding: 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.booking-widget--compact {
    padding: 1rem;
}

.booking-widget__header h3 {
    font-size: 1.5rem;
    color: var(--brand-primary, #0d6efd);
}
</style>
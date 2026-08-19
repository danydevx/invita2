<template>
    <div
        class="public-site"
        :style="{
            '--primary': theme?.primary_color || '#111827',
            '--secondary': theme?.secondary_color || '#6B7280',
            '--bg': theme?.background_color || '#FFFFFF',
            '--text': theme?.text_color || '#111827',
            '--accent': theme?.accent_color || theme?.primary_color || '#3B82F6',
        }"
    >
        <Head :title="`Reservar en ${business.name}`" />

        <header class="site-header">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <img
                            v-if="business.logo"
                            :src="business.logo"
                            :alt="business.name"
                            class="business-logo"
                        />
                        <h1 class="business-name mb-0">{{ business.name }}</h1>
                    </div>
                    <a :href="route('public.business.site', { slug: business.slug })" class="btn btn-light btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </header>

        <div class="container py-4">
            <div class="booking-flow">
                <div class="step-indicator">
                    <div class="step" :class="{ active: step >= 1, completed: step > 1 }"></div>
                    <div class="step" :class="{ active: step >= 2, completed: step > 2 }"></div>
                    <div class="step" :class="{ active: step >= 3, completed: step > 3 }"></div>
                </div>

                <div v-if="errors.error" class="alert alert-danger">
                    {{ errors.error }}
                </div>

                <div v-if="step === 1">
                    <h4 class="mb-4 text-center">Selecciona un Servicio</h4>
                    <div
                        v-for="service in services"
                        :key="service.id"
                        class="service-option"
                        :class="{ selected: selectedService?.id === service.id }"
                        @click="selectService(service)"
                    >
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ service.name }}</strong>
                                <div class="text-muted small">
                                    {{ service.duration_minutes }} min
                                </div>
                            </div>
                            <span class="price">${{ service.price }}</span>
                        </div>
                    </div>
                    <button
                        class="btn btn-primary w-100 mt-4"
                        :disabled="!selectedService"
                        @click="step = 2"
                    >
                        Siguiente
                    </button>
                </div>

                <div v-if="step === 2">
                    <h4 class="mb-4 text-center">Selecciona Fecha y Hora</h4>

                    <div class="mb-4">
                        <label class="form-label">Fecha</label>
                        <select v-model="selectedDate" class="form-select" @change="fetchSlots">
                            <option value="">Seleccionar fecha</option>
                            <option v-for="day in availableDays" :key="day.date" :value="day.date">
                                {{ day.day_name }} {{ day.day_short }}
                            </option>
                        </select>
                    </div>

                    <div v-if="selectedDate && loadingSlots" class="text-center py-3">
                        <div class="spinner-border" role="status"></div>
                    </div>

                    <div v-if="selectedDate && !loadingSlots && timeSlots.length === 0" class="alert alert-warning">
                        No hay horarios disponibles para este día.
                    </div>

                    <div v-if="selectedDate && !loadingSlots && timeSlots.length > 0" class="mb-4">
                        <label class="form-label">Hora</label>
                        <div class="d-flex flex-wrap gap-2">
                            <div
                                v-for="slot in timeSlots"
                                :key="slot.time"
                                class="time-slot"
                                :class="{ selected: selectedTime === slot.time }"
                                @click="selectedTime = slot.time"
                            >
                                {{ slot.time_formatted }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" @click="step = 1">Atrás</button>
                        <button
                            class="btn btn-primary flex-grow-1"
                            :disabled="!selectedDate || !selectedTime"
                            @click="step = 3"
                        >
                            Siguiente
                        </button>
                    </div>
                </div>

                <div v-if="step === 3">
                    <h4 class="mb-4 text-center">Tus Datos</h4>

                    <form @submit.prevent="submitBooking">
                        <div class="mb-3">
                            <label class="form-label">Nombre *</label>
                            <input
                                v-model="form.customer_name"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.customer_name }"
                                required
                            />
                            <div class="invalid-feedback" v-if="form.errors.customer_name">
                                {{ form.errors.customer_name }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Teléfono *</label>
                            <input
                                v-model="form.customer_phone"
                                type="tel"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.customer_phone }"
                                required
                            />
                            <div class="invalid-feedback" v-if="form.errors.customer_phone">
                                {{ form.errors.customer_phone }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email (opcional)</label>
                            <input
                                v-model="form.customer_email"
                                type="email"
                                class="form-control"
                            />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notas (opcional)</label>
                            <textarea
                                v-model="form.notes"
                                class="form-control"
                                rows="2"
                            ></textarea>
                        </div>

                        <div class="card mb-3">
                            <div class="card-body">
                                <h6>Resumen de la Cita</h6>
                                <p class="mb-1"><strong>Servicio:</strong> {{ selectedService?.name }}</p>
                                <p class="mb-1"><strong>Fecha:</strong> {{ formatDate(selectedDate) }}</p>
                                <p class="mb-0"><strong>Hora:</strong> {{ formatTime(selectedTime) }}</p>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" @click="step = 2">Atrás</button>
                            <button type="submit" class="btn btn-primary flex-grow-1" :disabled="form.processing">
                                Confirmar Reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
    business: Object,
    theme: Object,
    services: Array,
    availableDays: Array,
})

const step = ref(1)
const selectedService = ref(null)
const selectedDate = ref('')
const selectedTime = ref('')
const timeSlots = ref([])
const loadingSlots = ref(false)
const errors = ref({})

const form = useForm({
    service_id: '',
    date: '',
    time: '',
    customer_name: '',
    customer_phone: '',
    customer_email: '',
    notes: '',
})

function selectService(service) {
    selectedService.value = service
    form.service_id = service.id
}

async function fetchSlots() {
    if (!selectedDate.value || !selectedService.value) return

    loadingSlots.value = true
    selectedTime.value = ''

    try {
        const response = await fetch(
            `${route('public.booking.slots', { slug: props.business.slug })}?service_id=${selectedService.value.id}&date=${selectedDate.value}`,
            {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            }
        )
        const data = await response.json()
        timeSlots.value = data.slots || []
    } catch (e) {
        timeSlots.value = []
    }

    loadingSlots.value = false
}

watch(selectedService, () => {
    selectedDate.value = ''
    selectedTime.value = ''
    timeSlots.value = []
})

function formatDate(dateStr) {
    if (!dateStr) return ''
    const date = new Date(dateStr + 'T00:00:00')
    return date.toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

function formatTime(timeStr) {
    if (!timeStr) return ''
    const [hours, minutes] = timeStr.split(':')
    const h = parseInt(hours)
    const ampm = h >= 12 ? 'PM' : 'AM'
    const hour12 = h % 12 || 12
    return `${hour12}:${minutes} ${ampm}`
}

function submitBooking() {
    form.date = selectedDate.value
    form.time = selectedTime.value

    form.post(route('public.booking.store', { slug: props.business.slug }), {
        preserveScroll: true,
        onError: (err) => {
            if (err.error) {
                errors.value = { error: err.error }
            }
        },
    })
}
</script>

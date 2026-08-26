<template>
  <MemberLayout>
    <Head :title="`Nuevo Horario - ${location.name}`" />

    <PageHeader
      :title="'Nuevo Horario'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing.id}/locations/${location.id}/schedules`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="text"
                    id="schedule-name"
                    v-model="form.name"
                    class="form-control"
                    :class="{ 'is-invalid': errors.name }"
                    placeholder=" "
                  />
                  <label for="schedule-name">Nombre del horario <strong class="text-danger">*</strong></label>
                  <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
                </div>
                <small class="text-muted">Ej: Horario Regular, Matutino, Nocturno, Diciembre</small>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Días de la semana <strong class="text-danger">*</strong></label>
              <div class="d-flex flex-wrap gap-2">
                <div
                  v-for="day in daysOfWeek"
                  :key="day.value"
                  class="form-check"
                >
                  <input
                    type="checkbox"
                    :id="`day-${day.value}`"
                    :value="day.value"
                    v-model="form.days_of_week"
                    class="form-check-input"
                  />
                  <label :for="`day-${day.value}`" class="form-check-label">{{ day.label }}</label>
                </div>
              </div>
              <div v-if="errors.days_of_week" class="text-danger small mt-1">{{ errors.days_of_week }}</div>
              <small class="text-muted">Selecciona los días que aplica este horario. Déjalos todos vacíos para todos los días.</small>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="time"
                    id="opening-time"
                    v-model="form.opening_time"
                    class="form-control"
                    :class="{ 'is-invalid': errors.opening_time }"
                  />
                  <label for="opening-time">Hora de apertura <strong class="text-danger">*</strong></label>
                  <div v-if="errors.opening_time" class="invalid-feedback">{{ errors.opening_time }}</div>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="time"
                    id="closing-time"
                    v-model="form.closing_time"
                    class="form-control"
                    :class="{ 'is-invalid': errors.closing_time }"
                  />
                  <label for="closing-time">Hora de cierre <strong class="text-danger">*</strong></label>
                  <div v-if="errors.closing_time" class="invalid-feedback">{{ errors.closing_time }}</div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <hr />
              <h6 class="mb-3">Horario de almuerzo (opcional)</h6>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="time"
                    id="lunch-start-time"
                    v-model="form.lunch_start_time"
                    class="form-control"
                  />
                  <label for="lunch-start-time">Inicio del almuerzo</label>
                </div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-group">
                <div class="form-floating">
                  <input
                    type="time"
                    id="lunch-end-time"
                    v-model="form.lunch_end_time"
                    class="form-control"
                  />
                  <label for="lunch-end-time">Fin del almuerzo</label>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check form-switch">
                <input
                  type="checkbox"
                  id="is-active"
                  v-model="form.is_active"
                  class="form-check-input"
                />
                <label class="form-check-label" for="is-active">Horario activo</label>
              </div>
              <small class="text-muted">Los horarios inactivos no se mostrarán en el minisite público.</small>
            </div>
          </div>

          <FormActions :submitText="'Guardar'" :submittingText="'Guardando...'" :cancelHref="`/member/listings/${listing.id}/locations/${location.id}/schedules`" :sending="sending" />
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const location = computed(() => page.props.location)

const daysOfWeek = [
  { value: 0, label: 'Domingo' },
  { value: 1, label: 'Lunes' },
  { value: 2, label: 'Martes' },
  { value: 3, label: 'Miércoles' },
  { value: 4, label: 'Jueves' },
  { value: 5, label: 'Viernes' },
  { value: 6, label: 'Sábado' },
]

const errors = reactive({
  name: '',
  days_of_week: '',
  opening_time: '',
  closing_time: '',
})

const sending = ref(false)

const form = reactive({
  name: '',
  days_of_week: [],
  opening_time: '09:00',
  closing_time: '18:00',
  lunch_start_time: '',
  lunch_end_time: '',
  is_active: true,
})

const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Horarios', href: `/member/listings/${listing.value?.id}/office-hours` },
  { label: 'Nuevo' },
])

const validateForm = () => {
  let isValid = true

  errors.name = ''
  errors.days_of_week = ''
  errors.opening_time = ''
  errors.closing_time = ''

  if (!form.name || form.name.trim() === '') {
    errors.name = 'El nombre es obligatorio.'
    isValid = false
  } else if (form.name.length > 100) {
    errors.name = 'El nombre no puede tener más de 100 caracteres.'
    isValid = false
  }

  if (!form.opening_time) {
    errors.opening_time = 'La hora de apertura es obligatoria.'
    isValid = false
  }

  if (!form.closing_time) {
    errors.closing_time = 'La hora de cierre es obligatoria.'
    isValid = false
  }

  if (form.opening_time && form.closing_time && form.opening_time >= form.closing_time) {
    errors.closing_time = 'La hora de cierre debe ser posterior a la hora de apertura.'
    isValid = false
  }

  return isValid
}

const submit = () => {
  if (!validateForm()) {
    return
  }

  sending.value = true

  const formData = new FormData()
  formData.append('name', form.name)
  form.days_of_week.forEach(day => {
    formData.append('days_of_week[]', day)
  })
  formData.append('opening_time', form.opening_time)
  formData.append('closing_time', form.closing_time)
  if (form.lunch_start_time) {
    formData.append('lunch_start_time', form.lunch_start_time)
  }
  if (form.lunch_end_time) {
    formData.append('lunch_end_time', form.lunch_end_time)
  }
  formData.append('is_active', form.is_active ? '1' : '0')

  router.post(`/member/listings/${listing.value.id}/locations/${location.value.id}/schedules`, formData, {
    preserveScroll: true,
    onError: (serverErrors) => {
      sending.value = false
      if (serverErrors.name) errors.name = serverErrors.name
      if (serverErrors.days_of_week) errors.days_of_week = serverErrors.days_of_week
      if (serverErrors.opening_time) errors.opening_time = serverErrors.opening_time
      if (serverErrors.closing_time) errors.closing_time = serverErrors.closing_time
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>

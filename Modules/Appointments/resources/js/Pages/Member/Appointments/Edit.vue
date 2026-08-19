<template>
  <MemberLayout>
    <Head :title="`Editar Cita - ${business.name}`" />

    <PageHeader
      title="Editar Cita"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/appointments`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3 mb-3">
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
                required
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

            <div class="col-md-3">
              <FieldDate
                id="appointment_date"
                label="Fecha"
                v-model="form.appointment_date"
                :formError="errors.appointment_date"
                :min="today"
                required
                :validateFunction="validateDate"
                :showValidation="showDateValidation"
              />
            </div>

            <div class="col-md-3">
              <FieldTime
                id="start_time"
                label="Hora"
                v-model="form.start_time"
                :formError="errors.start_time"
                required
              />
            </div>

            <div class="col-md-4">
              <FieldSelect
                id="status"
                label="Estado"
                v-model="form.status"
                required
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
                :rows="3"
                placeholder="Notas adicionales..."
              />
            </div>

            <div class="col-12 d-flex gap-2">
              <button type="submit" class="btn btn-primary" :disabled="sending">
                {{ sending ? 'Guardando...' : 'Guardar Cambios' }}
              </button>
              <Link :href="`/member/businesses/${business.id}/appointments`" class="btn btn-outline-secondary">
                Cancelar
              </Link>
            </div>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldDate from '@/Components/Fields/FieldDate.vue'
import FieldTime from '@/Components/Fields/FieldTime.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const page = usePage()
const business = computed(() => page.props.business)
const appointment = computed(() => page.props.appointment)
const services = computed(() => page.props.services || [])
const locations = computed(() => page.props.locations || [])
const errors = computed(() => {
  const errs = page.props.errors || {}
  const normalized = {}
  for (const [key, value] of Object.entries(errs)) {
    normalized[key] = Array.isArray(value) ? value.join(', ') : value
  }
  return normalized
})

watch(() => errors.value.appointment_date, (val) => {
  if (val) showDateValidation.value = true
})
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Citas', href: `/member/businesses/${biz.id}/appointments` },
        { label: 'Editar Cita', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Editar Cita', active: true },
  ]
})

const sending = ref(false)
const showDateValidation = ref(false)
const today = computed(() => new Date().toISOString().split('T')[0])

const validateDate = () => {
  if (!form.appointment_date) return ''
  const selected = new Date(form.appointment_date)
  const todayDate = new Date()
  todayDate.setHours(0, 0, 0, 0)
  if (selected < todayDate) {
    return 'La fecha debe ser hoy o posterior'
  }
  return ''
}

const form = reactive({
  customer_name: appointment.value.customer_name,
  customer_email: appointment.value.customer_email,
  customer_phone: appointment.value.customer_phone || '',
  business_service_id: appointment.value.business_service_id,
  business_location_id: appointment.value.business_location_id,
  appointment_date: appointment.value.appointment_date,
  start_time: appointment.value.start_time,
  status: appointment.value.status,
  notes: appointment.value.notes || '',
})

const submit = () => {
  sending.value = true
  router.put(`/member/businesses/${business.value.id}/appointments/${appointment.value.id}`, form, {
    preserveScroll: true,
    onError: (errs) => {
      console.error('Validation errors:', errs)
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>

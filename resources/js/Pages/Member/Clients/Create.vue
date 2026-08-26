<template>
  <MemberLayout>
    <Head :title="`Nuevo Cliente - ${listing.name}`" />

    <PageHeader
      title="Nuevo Cliente"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing.id}/clients`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldText
              id="client-customer-name"
              label="Nombre del cliente"
              placeholder="Persona que atendemos"
              v-model="form.customer_name"
              :formError="errors.customer_name"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="client-contact-person"
              label="Persona de contacto"
              placeholder="Encargado o responsable"
              v-model="form.contact_person"
              :formError="errors.contact_person"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="client-company"
              label="Empresa o negocio"
              placeholder="Razon social o nombre comercial"
              v-model="form.company_name"
              :formError="errors.company_name"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldPhone
              id="client-whatsapp"
              label="WhatsApp"
              placeholder="+52 55 1234 5678"
              v-model="form.whatsapp"
              :formError="errors.whatsapp"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldEmail
              id="client-email"
              label="Email"
              placeholder="cliente@empresa.com"
              v-model="form.customer_email"
              :formError="errors.customer_email"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldPhone
              id="client-phone"
              label="Telefono"
              v-model="form.customer_phone"
              :formError="errors.customer_phone"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="client-website"
              label="Sitio web"
              placeholder="https://empresa.com"
              v-model="form.website"
              :formError="errors.website"
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldText
              id="client-rfc"
              label="RFC"
              placeholder="XAXX010101000"
              v-model="form.rfc"
              :formError="errors.rfc"
            />
          </div>

          <div class="col-12">
            <FieldText
              id="client-address-1"
              label="Direccion linea 1"
              placeholder="Av. Reforma 123"
              v-model="form.address_line_1"
              :formError="errors.address_line_1"
            />
          </div>

          <div class="col-12">
            <FieldText
              id="client-address-2"
              label="Direccion linea 2"
              placeholder="Piso 3, Interior B"
              v-model="form.address_line_2"
              :formError="errors.address_line_2"
            />
          </div>

          <div class="col-12 col-md-6">
            <LocationSelector
              v-model="locationData"
              :state-error="errors.state_code"
              :municipality-error="errors.municipality"
              @state-changed="onStateChanged"
              @municipality-changed="onMunicipalityChanged"
            />
          </div>

          <div class="col-12 col-md-3">
            <FieldText
              id="client-neighborhood"
              label="Colonia"
              placeholder="Centro"
              v-model="form.neighborhood"
              :formError="errors.neighborhood"
            />
          </div>

          <div class="col-12 col-md-3">
            <FieldText
              id="client-postal"
              label="Codigo postal"
              placeholder="06000"
              v-model="form.postal_code"
              :formError="errors.postal_code"
            />
          </div>

          <div class="col-12">
            <FieldTextarea
              id="client-notes"
              label="Notas"
              v-model="form.notes"
              :formError="errors.notes"
              :rows="3"
              placeholder="Informacion adicional del cliente o de la cita..."
            />
          </div>

          <FormActions :submitText="'Crear Cliente'" :submittingText="'Guardando...'" :cancelHref="`/member/listings/${listing.id}/clients`" :sending="sending" />
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import LocationSelector from '@/Components/LocationSelector.vue'
import FormActions from '@/Components/FormActions.vue'

const props = defineProps({
  listing: {
    type: Object,
  },
})

const page = usePage()
const listing = computed(() => page.props.listing)

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Clientes', href: `/member/listings/${listing.value.id}/clients` },
  { label: 'Nuevo' },
])

const locationData = ref({ state_code: '', municipality: '' })

const sending = ref(false)

const form = reactive({
  customer_name: '',
  contact_person: '',
  company_name: '',
  whatsapp: '',
  website: '',
  rfc: '',
  address_line_1: '',
  address_line_2: '',
  neighborhood: '',
  postal_code: '',
  state_code: '',
  municipality: '',
  customer_email: '',
  customer_phone: '',
  status: 'pending',
  notes: '',
})

const errors = reactive({
  customer_name: '',
  contact_person: '',
  company_name: '',
  whatsapp: '',
  website: '',
  rfc: '',
  address_line_1: '',
  address_line_2: '',
  neighborhood: '',
  postal_code: '',
  state_code: '',
  municipality: '',
  customer_email: '',
  customer_phone: '',
  notes: '',
})

const validateForm = () => {
  let isValid = true

  errors.customer_name = ''
  errors.contact_person = ''
  errors.company_name = ''
  errors.whatsapp = ''
  errors.website = ''
  errors.rfc = ''
  errors.address_line_1 = ''
  errors.address_line_2 = ''
  errors.neighborhood = ''
  errors.postal_code = ''
  errors.state_code = ''
  errors.municipality = ''
  errors.customer_email = ''
  errors.customer_phone = ''
  errors.notes = ''

  if (!form.customer_name || form.customer_name.trim() === '') {
    errors.customer_name = 'El nombre es obligatorio.'
    isValid = false
  }

  if (form.customer_email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.customer_email)) {
    errors.customer_email = 'El email no es valido.'
    isValid = false
  }

  return isValid
}

const onStateChanged = () => {}
const onMunicipalityChanged = () => {}

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true
  form.state_code = locationData.value.state_code
  form.municipality = locationData.value.municipality

  router.post(`/member/listings/${listing.value.id}/clients`, form, {
    preserveScroll: true,
    onSuccess: () => {
      sending.value = false
    },
    onError: (errs) => {
      sending.value = false
      Object.keys(errs).forEach(key => {
        if (key in errors) {
          errors[key] = errs[key]
        }
      })
      toast.warning('Por favor completa los campos requeridos')
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>
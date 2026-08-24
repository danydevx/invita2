<template>
  <MemberLayout>
    <Head title="Crear Formulario" />

    <PageHeader
      title="Nuevo Formulario"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/contact-forms`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="mb-3">
            <FieldText
              id="form-name"
              label="Nombre del Formulario"
              placeholder="Ej: Contacto General, Solicitud de Cotización"
              v-model="form.name"
              :formError="errors.name"
              required
            />
            <small class="text-muted">Nombre interno para identificar el formulario.</small>
          </div>

          <div class="mb-3">
            <FieldTextarea
              id="form-description"
              label="Descripción"
              v-model="form.description"
              :formError="errors.description"
              :rows="2"
              placeholder="Descripción opcional del formulario..."
            />
          </div>

          <div class="mb-3">
            <FieldSwitch
              id="form-active"
              label="Formulario activo"
              v-model="form.is_active"
            />
            <small class="text-muted">Solo un formulario puede estar activo a la vez.</small>
          </div>

          <hr class="my-4" />

          <h5 class="mb-3">Configuración del Formulario</h5>

          <div class="mb-3">
            <FieldTextarea
              id="form-success-message"
              label="Mensaje de éxito"
              v-model="form.success_message"
              :formError="errors.success_message"
              :rows="2"
              placeholder="Mensaje que se muestra al enviar el formulario..."
            />
          </div>

          <div class="mb-3">
            <FieldSwitch
              id="form-show-phone"
              label="Mostrar teléfono del negocio"
              v-model="form.show_phone"
            />
          </div>

          <div class="mb-3">
            <FieldSwitch
              id="form-show-email"
              label="Mostrar email del negocio"
              v-model="form.show_email"
            />
          </div>

          <FormActions :submitText="'Crear Formulario'" :submittingText="'Creando...'" :cancelHref="`/member/listings/${listing?.id}/contact-forms`" :sending="sending" />
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
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FormActions from '@/Components/FormActions.vue'

const props = defineProps({
  listing: Object,
})

const page = usePage()
const listing = computed(() => page.props.listing)
const sending = ref(false)
const businessMenu = computed(() => page.props.businessMenu || [])

const form = reactive({
  name: '',
  description: '',
  is_active: false,
  success_message: 'Mensaje enviado correctamente. Nos pondremos en contacto pronto.',
  show_phone: true,
  show_email: true,
})

const errors = reactive({
  name: '',
  description: '',
  success_message: '',
})

const validateForm = () => {
  let isValid = true

  errors.name = ''
  errors.description = ''
  errors.success_message = ''

  if (!form.name || form.name.trim() === '') {
    errors.name = 'El nombre es obligatorio.'
    isValid = false
  } else if (form.name.length > 150) {
    errors.name = 'El nombre no puede tener más de 150 caracteres.'
    isValid = false
  }

  return isValid
}

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/listings\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: biz.name, href: `/member/listings/${biz.id}/edit` },
        { label: 'Formularios', href: `/member/listings/${biz.id}/contact-forms` },
        { label: 'Nuevo Formulario', active: true },
      ]
    }
  }
  return [
    { label: 'Nuevo Formulario', active: true },
  ]
})

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true
  router.post(`/member/listings/${listing.value.id}/contact-forms`, form, {
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

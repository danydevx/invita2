<template>
  <MemberLayout>
    <Head title="Nueva galería" />

    <PageHeader
      title="Nueva galería"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id || ''}/galleries`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="submit">
          <div class="col-12 col-md-6">
            <FieldText
              id="gallery-name"
              label="Nombre"
              placeholder="Galería del local"
              v-model="form.name"
              :formError="errors.name"
              required
            />
          </div>

          <div class="col-12 col-md-6">
            <FieldSwitch
              id="gallery-primary"
              label="Marcar como galería principal"
              v-model="form.is_primary"
            />
            <div class="form-text">Se elegirá automáticamente si no existe ninguna otra.</div>
          </div>

          <div class="col-12">
            <FieldTextarea
              id="gallery-description"
              label="Descripción"
              v-model="form.description"
              :formError="errors.description"
              :rows="2"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldSwitch
              id="gallery-active"
              label="Galería activa"
              v-model="form.is_active"
            />
          </div>

          <div class="col-12 col-md-4">
            <FieldNumber
              id="gallery-sort"
              label="Orden"
              v-model="form.sort_order"
              :formError="errors.sort_order"
              :min="0"
            />
          </div>

          <FormActions :submitText="'Crear galería'" :submittingText="'Guardando...'" :cancelHref="`/member/listings/${listing?.id || ''}/galleries`" :sending="sending" />
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
import FieldNumber from '@/Components/Fields/FieldNumber.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Galería', href: `/member/listings/${listing.value?.id}/galleries` },
  { label: 'Nueva' },
])

const sending = ref(false)

const form = reactive({
  name: '',
  description: '',
  is_primary: false,
  is_active: true,
  sort_order: 0,
})

const errors = reactive({
  name: '',
  description: '',
  sort_order: '',
})

const validateForm = () => {
  let isValid = true

  errors.name = ''
  errors.description = ''
  errors.sort_order = ''

  if (!form.name || form.name.trim() === '') {
    errors.name = 'El nombre es obligatorio.'
    isValid = false
  } else if (form.name.length > 150) {
    errors.name = 'El nombre no puede tener más de 150 caracteres.'
    isValid = false
  }

  if (form.sort_order && isNaN(parseInt(form.sort_order))) {
    errors.sort_order = 'El orden debe ser un número.'
    isValid = false
  }

  return isValid
}

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true
  router.post(`/member/listings/${listing.value.id}/galleries`, form, {
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
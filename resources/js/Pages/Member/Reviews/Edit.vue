<template>
  <MemberLayout>
    <Head :title="`Editar Resena - ${listing.name}`" />

    <PageHeader
      title="Editar Resena"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing.id}/reviews`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <FieldText
                id="review-client-name"
                label="Nombre del cliente"
                v-model="form.client_name"
                :formError="errors.client_name"
                required
              />
            </div>

            <div class="col-md-6">
              <FieldText
                id="review-company"
                label="Empresa"
                v-model="form.company"
              />
            </div>

            <div class="col-12">
              <FieldTextarea
                id="review-comment"
                label="Comentario"
                v-model="form.comment"
                :formError="errors.comment"
                :rows="4"
                required
              />
            </div>

            <div class="col-md-6">
              <FieldSelect
                id="review-rating"
                label="Calificacion"
                v-model="form.rating"
                :formError="errors.rating"
                required
              >
                <option value="5">5 Estrellas</option>
                <option value="4">4 Estrellas</option>
                <option value="3">3 Estrellas</option>
                <option value="2">2 Estrellas</option>
                <option value="1">1 Estrella</option>
              </FieldSelect>
            </div>

            <div class="col-md-6">
              <FieldUrl
                id="review-google-link"
                label="Link de Google"
                v-model="form.google_link"
                placeholder="https://..."
              />
            </div>

            <div class="col-12">
              <FieldSwitch
                id="review-active"
                label="Activa"
                v-model="form.is_active"
              />
            </div>

            <FormActions :submitText="'Guardar'" :submittingText="'Guardando...'" :cancelHref="`/member/listings/${listing.id}/reviews`" :sending="sending" />
          </div>
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
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldUrl from '@/Components/Fields/FieldUrl.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const review = computed(() => page.props.review)
const errors = reactive({
  client_name: '',
  company: '',
  comment: '',
  rating: '',
  google_link: '',
})
const sending = ref(false)
const businessMenu = computed(() => page.props.businessMenu || [])

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Reseñas', href: `/member/listings/${listing.value.id}/reviews` },
  { label: review.value?.client_name || 'Editar' },
])

const form = reactive({
  client_name: review.value.client_name,
  company: review.value.company || '',
  comment: review.value.comment,
  rating: review.value.rating,
  google_link: review.value.google_link || '',
  is_active: review.value.is_active || false,
})

const validateForm = () => {
  let isValid = true

  errors.client_name = ''
  errors.company = ''
  errors.comment = ''
  errors.rating = ''
  errors.google_link = ''

  if (!form.client_name || form.client_name.trim() === '') {
    errors.client_name = 'El nombre es obligatorio.'
    isValid = false
  }

  if (!form.comment || form.comment.trim() === '') {
    errors.comment = 'El comentario es obligatorio.'
    isValid = false
  }

  if (!form.rating) {
    errors.rating = 'La calificacion es obligatoria.'
    isValid = false
  }

  if (form.google_link && form.google_link.trim() !== '') {
    const urlPattern = /^https?:\/\/.+/
    if (!urlPattern.test(form.google_link)) {
      errors.google_link = 'El link debe ser una URL valida (https://...).'
      isValid = false
    }
  }

  return isValid
}

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true
  router.put(`/member/listings/${listing.value.id}/reviews/${review.value.id}`, form, {
    preserveScroll: true,
    onError: (errs) => {
      errors.client_name = errs.client_name || ''
      errors.comment = errs.comment || ''
      errors.rating = errs.rating || ''
      errors.google_link = errs.google_link || ''
      const errorMessages = Object.values(errs).flat()
      if (errorMessages.length > 0) {
        toast.warning('Por favor completa los campos requeridos')
      }
    },
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>

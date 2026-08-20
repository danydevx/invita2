<template>
  <MemberLayout>
    <Head :title="`Editar Pregunta - ${business.name}`" />

    <PageHeader
      :title="'Editar Pregunta Frecuente'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${business.id}/faqs`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12">
              <FieldText
                id="faq-question"
                label="Pregunta"
                v-model="form.question"
                :formError="errors.question"
                required
              />
            </div>

            <div class="col-12">
              <FieldTextarea
                id="faq-answer"
                label="Respuesta"
                v-model="form.answer"
                :formError="errors.answer"
                :rows="4"
                required
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldSelect
                id="faq-category"
                label="Categoria"
                v-model="form.category_id"
                :options="categoryOptions"
                :formError="errors.category_id"
              />
            </div>

            <div class="col-12 col-md-6">
              <FieldNumber
                id="faq-sort-order"
                label="Orden"
                v-model="form.sort_order"
                :formError="errors.sort_order"
              />
              <small class="text-muted">Menor numero aparece primero.</small>
            </div>

            <div class="col-12 col-md-6">
              <FieldSwitch
                id="faq-active"
                label="Pregunta activa"
                v-model="form.is_active"
              />
            </div>
          </div>

          <div class="col-12 d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Guardando...' : 'Guardar Cambios' }}
            </button>
            <Link :href="`/member/listings/${business.id}/faqs`" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, ref, reactive } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'

const props = defineProps({
  business: { type: Object, required: true },
  faq: { type: Object, required: true },
  categories: { type: Array, default: () => [] },
})

const page = usePage()
const errors = reactive({
  question: '',
  answer: '',
  category_id: '',
  sort_order: '',
})

const validateForm = () => {
  let isValid = true

  errors.question = ''
  errors.answer = ''
  errors.category_id = ''
  errors.sort_order = ''

  if (!form.value.question || form.value.question.trim() === '') {
    errors.question = 'La pregunta es obligatoria.'
    isValid = false
  }

  if (!form.value.answer || form.value.answer.trim() === '') {
    errors.answer = 'La respuesta es obligatoria.'
    isValid = false
  }

  return isValid
}

const sending = ref(false)
const businessMenu = computed(() => page.props.businessMenu || [])

const categoryOptions = computed(() => [
  { value: '', label: 'Sin categoria' },
  ...props.categories.map(c => ({ value: c.id, label: c.name }))
])

const form = ref({
  question: props.faq.question,
  answer: props.faq.answer,
  category_id: props.faq.category_id || '',
  is_active: props.faq.is_active,
  sort_order: props.faq.sort_order || 0,
})

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Mis Negocios', href: '/member/business-modules' },
        { label: biz.name, href: `/member/listings/${biz.id}/edit` },
        { label: 'Preguntas Frecuentes', href: `/member/listings/${biz.id}/faqs` },
        { label: 'Editar Pregunta', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Editar Pregunta', active: true },
  ]
})

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true
  router.put(`/member/listings/${props.business.id}/faqs/${props.faq.id}`, form.value, {
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

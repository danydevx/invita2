<template>
  <MemberLayout>
    <Head :title="`Editar Pregunta - ${listing.name}`" />

    <PageHeader
      :title="'Editar Pregunta Frecuente'"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing.id}/faqs`"
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
              <div class="d-flex align-items-end gap-2">
                <div class="flex-grow-1">
                  <FieldSelect
                    id="faq-category"
                    label="Categoria"
                    v-model="form.category_id"
                    :options="categoryOptions"
                    :formError="errors.category_id"
                  />
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mb-3" @click="openCategoryModal">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
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
            <Link :href="`/member/listings/${listing.id}/faqs`" class="btn btn-outline-secondary">Cancelar</Link>
          </div>
        </form>
      </div>
    </div>

    <div ref="categoryModalElement" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nueva Categoria</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form @submit.prevent="createCategory">
            <div class="modal-body">
              <div class="mb-3">
                <FieldText
                  id="category-name"
                  label="Nombre"
                  v-model="categoryForm.name"
                  required
                />
              </div>
              <div class="mb-3">
                <FieldTextarea
                  id="category-description"
                  label="Descripcion"
                  v-model="categoryForm.description"
                  :rows="2"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary" :disabled="categorySending">
                {{ categorySending ? 'Creando...' : 'Crear Categoria' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, ref, reactive, onMounted, nextTick } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import { Modal } from 'bootstrap'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FieldNumber from '@/Components/Fields/FieldNumber.vue'

const props = defineProps({
  listing: { type: Object },
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
const categorySending = ref(false)
const categoryModalElement = ref(null)
let categoryModal = null

const categoryForm = reactive({
  name: '',
  description: '',
})

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

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Preguntas Frecuentes', href: `/member/listings/${listing.id}/faqs` },
  { label: faq?.question || 'Editar' },
])

const submit = () => {
  if (!validateForm()) {
    toast.warning('Por favor completa los campos requeridos')
    return
  }

  sending.value = true
  router.put(`/member/listings/${props.listing.id}/faqs/${props.faq.id}`, form.value, {
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

const openCategoryModal = () => {
  categoryForm.name = ''
  categoryForm.description = ''
  nextTick(() => categoryModal.show())
}

const createCategory = () => {
  if (!categoryForm.name.trim()) {
    toast.warning('El nombre de la categoria es requerido')
    return
  }
  categorySending.value = true

  router.post(`/member/listings/${props.listing.id}/faq-categories`, categoryForm, {
    preserveScroll: true,
    onSuccess: () => {
      categoryModal.hide()
      toast.success('Categoria creada exitosamente')
      categoryForm.name = ''
      categoryForm.description = ''
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0]
      if (firstError) {
        toast.error(firstError)
      } else {
        toast.error('Error al crear la categoria')
      }
    },
    onFinish: () => {
      categorySending.value = false
    },
  })
}

onMounted(() => {
  categoryModal = new Modal(categoryModalElement.value)
})
</script>

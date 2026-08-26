<template>
  <MemberLayout>
    <Head :title="`Nueva Tarjeta - ${listing?.name || ''}`" />

    <PageHeader
      title="Nueva Tarjeta"
      :breadcrumbs="breadcrumbs"
      backHref="/member/dashboard"
      backLabel="Regresar"
    />

    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <form @submit.prevent="submit">
              <div class="row g-3">
                <div class="col-md-6">
                  <FieldText
                    id="card-client-name"
                    label="Nombre del cliente"
                    placeholder="Ej: María García"
                    v-model="form.client_name"
                    :formError="errors.client_name"
                    required
                  />
                </div>
                <div class="col-md-6">
                  <FieldEmail
                    id="card-client-email"
                    label="Correo electrónico (opcional)"
                    placeholder="maria@ejemplo.com"
                    v-model="form.client_email"
                    :formError="errors.client_email"
                  />
                </div>
              </div>

              <div class="row g-3 mt-3">
                <div class="col-md-6">
                  <FieldText
                    id="card-client-phone"
                    label="Teléfono (opcional)"
                    placeholder="+52 555 123 4567"
                    v-model="form.client_phone"
                    :formError="errors.client_phone"
                  />
                </div>
                <div class="col-md-6">
                  <FieldSelect
                    id="card-reward"
                    label="Recompensa (opcional)"
                    v-model="form.fidelity_reward_id"
                    :options="rewardOptions"
                    :formError="errors.fidelity_reward_id"
                  />
                </div>
                <div class="col-md-6">
                  <FieldSelect
                    id="card-max-visits"
                    label="Número de visitas"
                    v-model="form.max_visits"
                    :options="visitOptions"
                    :formError="errors.max_visits"
                  />
                </div>
              </div>

              <div class="mb-3 mt-3">
                <FieldTextarea
                  id="card-description"
                  label="Descripción (opcional)"
                  placeholder="Ej: 10% de descuento en la próxima visita"
                  v-model="form.description"
                  :formError="errors.description"
                  rows="3"
                />
              </div>

              <FormActions
                :submitText="'Guardar'"
                :submittingText="'Guardando...'"
                :cancelHref="`/member/listings/${listing?.id}/fidelity-cards`"
                :sending="sending"
              />
            </form>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FormActions from '@/Components/FormActions.vue'

const page = usePage()
const listing = computed(() => page.props.listing)
const businessMenu = computed(() => page.props.businessMenu || [])
const rewards = computed(() => page.props.rewards || [])

const rewardOptions = computed(() => [
  { value: '', label: 'Sin recompensa' },
  ...rewards.value.map(r => ({ value: r.id, label: `${r.title} (${r.max_visits} visitas)` })),
])

const visitOptions = [
  { value: 5, label: '5 visitas' },
  { value: 8, label: '8 visitas' },
  { value: 10, label: '10 visitas' },
  { value: 12, label: '12 visitas' },
  { value: 15, label: '15 visitas' },
  { value: 20, label: '20 visitas' },
]

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'Fidelización', href: `/member/listings/${listing.value?.id}/fidelity-cards` },
  { label: 'Nueva' },
])

const sending = ref(false)

const form = reactive({
  client_name: '',
  client_email: '',
  client_phone: '',
  fidelity_reward_id: '',
  max_visits: 10,
  description: '',
})

watch(() => form.fidelity_reward_id, (newRewardId) => {
  if (newRewardId) {
    const selectedReward = rewards.value.find(r => r.id === newRewardId)
    if (selectedReward) {
      form.max_visits = selectedReward.max_visits
    }
  }
})

const errors = reactive({
  client_name: '',
  client_email: '',
  client_phone: '',
  fidelity_reward_id: '',
  max_visits: '',
  description: '',
})

const validateForm = () => {
  let isValid = true

  errors.client_name = ''
  errors.client_email = ''
  errors.client_phone = ''
  errors.max_visits = ''
  errors.description = ''

  if (!form.client_name || form.client_name.trim() === '') {
    errors.client_name = 'El nombre es obligatorio.'
    isValid = false
  } else if (form.client_name.length > 150) {
    errors.client_name = 'El nombre no puede tener más de 150 caracteres.'
    isValid = false
  }

  if (form.client_email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.client_email)) {
    errors.client_email = 'El email no es válido.'
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
  router.post(`/member/listings/${listing.value.id}/fidelity-cards`, form, {
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

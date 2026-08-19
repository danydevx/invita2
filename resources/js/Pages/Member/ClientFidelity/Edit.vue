<template>
  <MemberLayout>
    <Head :title="`Editar Tarjeta - ${business?.name || ''}`" />

    <PageHeader
      title="Editar Tarjeta"
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
                    :formError="form.errors.client_name"
                    required
                  />
                </div>
                <div class="col-md-6">
                  <FieldEmail
                    id="card-client-email"
                    label="Correo electrónico (opcional)"
                    placeholder="maria@ejemplo.com"
                    v-model="form.client_email"
                    :formError="form.errors.client_email"
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
                    :formError="form.errors.client_phone"
                  />
                </div>
                <div class="col-md-6">
                  <FieldSelect
                    id="card-max-visits"
                    label="Número de visitas"
                    v-model="form.max_visits"
                    :options="visitOptions"
                    :formError="form.errors.max_visits"
                  />
                </div>
              </div>

              <div class="mb-3 mt-3">
                <FieldTextarea
                  id="card-description"
                  label="Descripción (opcional)"
                  placeholder="Ej: 10% de descuento en la próxima visita"
                  v-model="form.description"
                  :formError="form.errors.description"
                  rows="3"
                />
              </div>

              <div class="mb-3">
                <FieldSwitch
                  id="card-active"
                  label="Activa"
                  v-model="form.is_active"
                  :formError="form.errors.is_active"
                />
                <div class="form-text">Las tarjetas inactivas no aparecerán para escaneo.</div>
              </div>

              <div v-if="card?.public_code" class="alert alert-info">
                <strong>Código público:</strong> {{ card.public_code }}
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm" :disabled="form.processing">
                  <i class="bi bi-check me-1"></i>
                  {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>
                <Link :href="`/member/businesses/${business?.id}/fidelity-cards`" class="btn btn-outline-secondary btn-sm">
                  Cancelar
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'

const page = usePage()
const business = computed(() => page.props.business)
const card = computed(() => page.props.card)
const businessMenu = computed(() => page.props.businessMenu || [])

const visitOptions = [
  { value: 5, label: '5 visitas' },
  { value: 8, label: '8 visitas' },
  { value: 10, label: '10 visitas' },
  { value: 12, label: '12 visitas' },
  { value: 15, label: '15 visitas' },
  { value: 20, label: '20 visitas' },
]

const breadcrumbs = computed(() => {
  const path = window.location.pathname
  const businessMatch = path.match(/^\/member\/businesses\/(\d+)/)
  if (businessMatch) {
    const businessId = parseInt(businessMatch[1])
    const biz = businessMenu.value.find(b => b.id === businessId)
    if (biz) {
      return [
        { label: 'Dashboard', href: '/member/dashboard' },
        { label: biz.name, href: `/member/businesses/${biz.id}/modules` },
        { label: 'Fidelidad', href: `/member/businesses/${biz.id}/fidelity-cards` },
        { label: card.value?.client_name || 'Editar', active: true },
      ]
    }
  }
  return [
    { label: 'Dashboard', href: '/member/dashboard' },
    { label: 'Fidelidad', href: `/member/businesses/${business.value?.id}/fidelity-cards` },
    { label: card.value?.client_name || 'Editar', active: true },
  ]
})

const form = useForm({
  client_name: card.value?.client_name || '',
  client_email: card.value?.client_email || '',
  client_phone: card.value?.client_phone || '',
  max_visits: card.value?.max_visits || 10,
  description: card.value?.description || '',
  is_active: card.value?.is_active ?? true,
})

const submit = () => {
  form.post(`/member/businesses/${business.value.id}/fidelity-cards/${card.value.id}`, {
    preserveScroll: true,
  })
}
</script>

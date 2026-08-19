<template>
  <MemberLayout>
    <Head :title="`Crear Formulario - ${business?.name || ''}`" />

    <PageHeader
      title="Nuevo Formulario"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business?.id}/contact-forms`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="mb-3">
            <label class="form-label">Nombre del Formulario <span class="text-danger">*</span></label>
            <input
              v-model="form.name"
              type="text"
              class="form-control"
              placeholder="Ej: Contacto General, Solicitud de Cotizacion"
              required
            />
            <small class="text-muted">Nombre interno para identificar el formulario.</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Descripcion</label>
            <textarea
              v-model="form.description"
              class="form-control"
              rows="2"
              placeholder="Descripcion opcional del formulario..."
            ></textarea>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="form-check-input"
                id="isActive"
              />
              <label class="form-check-label" for="isActive">
                Formulario activo
              </label>
            </div>
            <small class="text-muted">Solo un formulario puede estar activo a la vez.</small>
          </div>

          <hr class="my-4" />

          <h5 class="mb-3">Configuracion del Formulario</h5>

          <div class="mb-3">
            <label class="form-label">Mensaje de exito</label>
            <textarea
              v-model="form.success_message"
              class="form-control"
              rows="2"
              placeholder="Mensaje que se muestra al enviar el formulario..."
            ></textarea>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input
                v-model="form.show_phone"
                type="checkbox"
                class="form-check-input"
                id="showPhone"
              />
              <label class="form-check-label" for="showPhone">
                Mostrar telefono del negocio
              </label>
            </div>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input
                v-model="form.show_email"
                type="checkbox"
                class="form-check-input"
                id="showEmail"
              />
              <label class="form-check-label" for="showEmail">
                Mostrar email del negocio
              </label>
            </div>
          </div>

          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary" :disabled="sending">
              {{ sending ? 'Creando...' : 'Crear Formulario' }}
            </button>
            <Link :href="`/member/businesses/${business?.id}/contact-forms`" class="btn btn-outline-secondary">
              Cancelar
            </Link>
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'

const props = defineProps({
  business: Object,
})

const page = usePage()
const business = computed(() => page.props.business)
const sending = ref(false)
const businessMenu = computed(() => page.props.businessMenu || [])

const form = ref({
  name: '',
  description: '',
  is_active: false,
  success_message: 'Mensaje enviado correctamente. Nos pondremos en contacto pronto.',
  show_phone: true,
  show_email: true,
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
        { label: biz.name, href: `/member/businesses/${biz.id}/edit` },
        { label: 'Formularios', href: `/member/businesses/${biz.id}/contact-forms` },
        { label: 'Nuevo Formulario', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Nuevo Formulario', active: true },
  ]
})

const submit = () => {
  sending.value = true
  router.post(`/member/businesses/${business.value.id}/contact-forms`, form.value, {
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>

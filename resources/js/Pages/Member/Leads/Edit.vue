<template>
  <MemberLayout>
    <Head :title="`Editar Contacto - ${business.name}`" />

    <PageHeader
      title="Editar Contacto"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/businesses/${business.id}/leads`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <FieldText
                id="name"
                label="Nombre"
                v-model="form.name"
                :formError="errors.name"
                required
              />
            </div>

            <div class="col-md-6">
              <FieldEmail
                id="email"
                label="Email"
                v-model="form.email"
                :formError="errors.email"
                required
              />
            </div>

            <div class="col-md-6">
              <FieldPhone
                id="phone"
                label="Telefono"
                v-model="form.phone"
              />
            </div>

            <div class="col-md-6">
              <FieldSelect
                id="business_location_id"
                label="Ubicacion"
                v-model="form.business_location_id"
              >
                <option :value="null">Sin ubicacion</option>
                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
              </FieldSelect>
            </div>

            <div class="col-md-6">
              <FieldSelect
                id="source"
                label="Fuente"
                v-model="form.source"
              >
                <option value="">Seleccionar...</option>
                <option value="manual">Manual</option>
                <option value="website">Website</option>
                <option value="phone">Telefono</option>
                <option value="walk_in">Visita directa</option>
                <option value="referral">Referido</option>
                <option value="social_media">Redes sociales</option>
                <option value="other">Otro</option>
              </FieldSelect>
            </div>

            <div class="col-md-6">
              <FieldSelect
                id="status"
                label="Estado"
                v-model="form.status"
                required
              >
                <option value="new">Nuevo</option>
                <option value="contacted">Contactado</option>
                <option value="qualified">Calificado</option>
                <option value="converted">Convertido</option>
                <option value="lost">Perdido</option>
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
              <Link :href="`/member/businesses/${business.id}/leads`" class="btn btn-outline-secondary">
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
import { computed, reactive, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldEmail from '@/Components/Fields/FieldEmail.vue'
import FieldPhone from '@/Components/Fields/FieldPhone.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'

const page = usePage()
const business = computed(() => page.props.business)
const lead = computed(() => page.props.lead)
const locations = computed(() => page.props.locations || [])
const errors = computed(() => page.props.errors || {})
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
        { label: 'Leads', href: `/member/businesses/${biz.id}/leads` },
        { label: 'Editar Lead', active: true },
      ]
    }
  }
  return [
    { label: 'Mis Negocios', href: '/member/business-modules' },
    { label: 'Editar Lead', active: true },
  ]
})

const sending = ref(false)

const form = reactive({
  name: lead.value.name,
  email: lead.value.email,
  phone: lead.value.phone || '',
  notes: lead.value.notes || '',
  business_location_id: lead.value.business_location_id,
  source: lead.value.source,
  status: lead.value.status,
})

const submit = () => {
  sending.value = true
  router.put(`/member/businesses/${business.value.id}/leads/${lead.value.id}`, form, {
    preserveScroll: true,
    onFinish: () => {
      sending.value = false
    },
  })
}
</script>

<template>
  <MemberLayout>
    <Head :title="`Nueva Tarjeta - ${listing?.name || ''}`" />

    <PageHeader
      title="Nueva Tarjeta"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/vcards`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form @submit.prevent="submit">
          <div class="row g-3">
            <div class="col-12 col-md-8">
              <FieldText
                id="vcard-name"
                label="Nombre de la tarjeta"
                v-model="form.name"
                :formError="errors.name"
                placeholder="Daniel López"
                required
              />
            </div>
            <div class="col-12 col-md-4">
              <FieldText
                id="vcard-slug"
                label="Slug"
                v-model="form.slug"
                :formError="errors.slug"
                placeholder="daniel-lopez"
                hint="URL: /v/slug"
              />
            </div>
            <div class="col-12 col-md-6">
              <FieldSelect
                id="vcard-type"
                label="Tipo de tarjeta"
                v-model="form.type"
                :formError="errors.type"
              >
                <option value="single">Individual</option>
                <option value="team">Equipo</option>
              </FieldSelect>
            </div>
            <div class="col-12 col-md-6" v-if="teams.length > 0">
              <FieldSelect
                id="vcard-team"
                label="Equipo"
                v-model="form.vcard_team_id"
                :formError="errors.vcard_team_id"
              >
                <option value="">Sin equipo</option>
                <option v-for="team in teams" :key="team.id" :value="team.id">
                  {{ team.name }}
                </option>
              </FieldSelect>
            </div>
            <div class="col-12">
              <FieldSwitch
                id="vcard-active"
                label="Tarjeta activa"
                v-model="form.active"
              />
            </div>
            <div class="col-12">
              <FieldSwitch
                id="vcard-search-engine-indexing"
                label="Indexación en motores de búsqueda"
                v-model="form.search_engine_indexing"
              />
            </div>
            <div class="col-12">
              <FieldSwitch
                id="vcard-renew"
                label="Renovar"
                v-model="form.renew"
              />
            </div>
            <div class="col-12">
              <FieldText
                id="vcard-tracking-code"
                label="Código de seguimiento"
                v-model="trackingCodeInput"
                placeholder="UTM1, UTM2"
              />
            </div>
            <div class="col-12">
              <FieldSwitch
                id="vcard-paused"
                label="Pausar tarjeta"
                v-model="form.paused"
              />
            </div>
          </div>

          <div class="mt-4">
            <FormActions
              :submitText="'Crear Tarjeta'"
              :submittingText="'Creando...'"
              :cancelHref="`/member/listings/${listing?.id}/vcards`"
              :sending="sending"
            />
          </div>
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FormActions from '@/Components/FormActions.vue'

const props = defineProps({
  listing: Object,
  teams: Array,
})

const sending = ref(false)
const errors = ref({})

const form = reactive({
  name: '',
  slug: '',
  type: 'single',
  vcard_team_id: '',
  active: true,
  search_engine_indexing: true,
  renew: true,
  tracking_code: [],
  paused: false,
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'vCards', href: `/member/listings/${props.listing?.id}/vcards` },
  { label: 'Nueva' },
])

const trackingCodeInput = computed({
  get() {
    if (!form.tracking_code || !Array.isArray(form.tracking_code)) return ''
    return form.tracking_code.join(', ')
  },
  set(value) {
    if (!value || value.trim() === '') {
      form.tracking_code = []
      return
    }
    form.tracking_code = value.split(',').map(s => s.trim()).filter(s => s.length > 0)
  }
})

function submit() {
  sending.value = true
  errors.value = {}

  router.post(
    `/member/listings/${props.listing.id}/vcards`,
    form,
    {
      onError: (err) => {
        errors.value = err
        sending.value = false
      },
      onSuccess: () => {
        sending.value = false
      },
    }
  )
}
</script>

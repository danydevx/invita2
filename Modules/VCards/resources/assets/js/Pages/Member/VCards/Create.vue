<template>
  <MemberLayout>
    <Head :title="`Nueva Tarjeta - ${listing?.name || ''}`" />

    <PageHeader
      title="Nueva Tarjeta"
      :breadcrumbs="breadcrumbs"
      :backHref="`/member/listings/${listing?.id}/vcards`"
    />

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'card' }"
              @click="activeTab = 'card'"
              type="button"
            >
              <i class="bi bi-card-text me-1"></i>
              Tarjeta
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'design' }"
              @click="activeTab = 'design'"
              type="button"
            >
              <i class="bi bi-palette me-1"></i>
              Diseño
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'information' }"
              @click="activeTab = 'information'"
              type="button"
            >
              <i class="bi bi-person me-1"></i>
              Información
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'contacts' }"
              @click="activeTab = 'contacts'"
              type="button"
            >
              <i class="bi bi-telephone me-1"></i>
              Contactos
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              :class="{ active: activeTab === 'fields' }"
              @click="activeTab = 'fields'"
              type="button"
            >
              <i class="bi bi-list-ul me-1"></i>
              Campos
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <form @submit.prevent="submit">
          <div v-show="activeTab === 'card'">
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
            </div>
          </div>

          <div v-show="activeTab === 'design'">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Diseño</label>
                <div class="row g-2">
                  <div
                    v-for="design in designs"
                    :key="design.value"
                    class="col-6 col-md-2"
                  >
                    <div
                      class="design-option border rounded p-2 text-center cursor-pointer"
                      :class="{ 'border-primary': form.design === design.value, 'bg-light': form.design !== design.value }"
                      @click="form.design = design.value"
                    >
                      <div class="design-preview mb-2" :style="{ backgroundColor: form.primary_color + '20' }">
                        <div class="design-icon" :style="{ color: form.primary_color }">
                          <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                        </div>
                      </div>
                      <small>{{ design.label }}</small>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Forma de bordes</label>
                <div class="d-flex gap-3">
                  <div
                    class="shape-option cursor-pointer p-3 border rounded text-center"
                    :class="{ 'border-primary bg-light': form.shape === 'square', 'bg-white': form.shape !== 'square' }"
                    @click="form.shape = 'square'"
                  >
                    <div class="shape-preview shape-square mx-auto mb-2"></div>
                    <small>Cuadrado</small>
                  </div>
                  <div
                    class="shape-option cursor-pointer p-3 border rounded text-center"
                    :class="{ 'border-primary bg-light': form.shape === 'rounded', 'bg-white': form.shape !== 'rounded' }"
                    @click="form.shape = 'rounded'"
                  >
                    <div class="shape-preview shape-rounded mx-auto mb-2"></div>
                    <small>Redondeado</small>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Color principal</label>
                <div class="d-flex gap-2 flex-wrap">
                  <div
                    v-for="color in colors"
                    :key="color"
                    class="color-swatch rounded cursor-pointer"
                    :class="{ 'ring-2 ring-primary': form.primary_color === color }"
                    :style="{ backgroundColor: color }"
                    @click="form.primary_color = color"
                  />
                </div>
              </div>

              <div class="col-12 col-md-6">
                <FieldSelect
                  id="vcard-font"
                  label="Fuente"
                  v-model="form.font"
                  :formError="errors.font"
                >
                  <option v-for="font in fonts" :key="font" :value="font">{{ font }}</option>
                </FieldSelect>
              </div>
            </div>
          </div>

          <div v-show="activeTab === 'information'">
            <fieldset class="mb-4">
              <legend class="h6">Información personal</legend>
              <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldText
                    id="vcard-prefix"
                    label="Prefijo"
                    v-model="form.prefix"
                    placeholder="Lic., Ing., Dr."
                  />
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldText
                    id="vcard-first-name"
                    label="Nombre"
                    v-model="form.first_name"
                    placeholder="Su nombre"
                  />
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldText
                    id="vcard-middle-name"
                    label="Segundo nombre"
                    v-model="form.middle_name"
                    placeholder="Segundo nombre"
                  />
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                  <FieldText
                    id="vcard-last-name"
                    label="Apellidos"
                    v-model="form.last_name"
                    placeholder="Sus apellidos"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <FieldText
                    id="vcard-preferred-name"
                    label="Nombre preferido"
                    v-model="form.preferred_name"
                    placeholder="Ejemplo: Dani"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <FieldSelect
                    id="vcard-pronouns"
                    label="Pronombres"
                    v-model="form.pronouns"
                  >
                    <option value="">Seleccionar...</option>
                    <option v-for="p in pronouns" :key="p.value" :value="p.value">
                      {{ p.label }}
                    </option>
                  </FieldSelect>
                </div>
                <div class="col-12">
                  <FieldText
                    id="vcard-accreditations"
                    label="Acreditaciones"
                    v-model="form.accreditations"
                    placeholder="Ejemplo: MBA, PMP, CCNA"
                  />
                </div>
              </div>
            </fieldset>

            <fieldset class="mb-4">
              <legend class="h6">Información profesional</legend>
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <FieldText
                    id="vcard-title"
                    label="Puesto"
                    v-model="form.title"
                    placeholder="Ejemplo: Diseñador web"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <FieldText
                    id="vcard-department"
                    label="Departamento"
                    v-model="form.department"
                    placeholder="Ejemplo: Marketing"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <FieldText
                    id="vcard-company"
                    label="Empresa"
                    v-model="form.company"
                    placeholder="Nombre de la empresa"
                  />
                </div>
                <div class="col-12">
                  <FieldTextarea
                    id="vcard-headline"
                    label="Descripción"
                    v-model="form.headline"
                    placeholder="Breve descripción profesional"
                    :rows="3"
                  />
                </div>
              </div>
            </fieldset>
          </div>

          <div v-show="activeTab === 'contacts'">
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              Los contactos se agregan después de crear la tarjeta. Serás redirigido a la página de edición.
            </div>
          </div>

          <div v-show="activeTab === 'fields'">
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              Los campos se agregan después de crear la tarjeta. Serás redirigido a la página de edición.
            </div>
          </div>

          <FormActions
            :submitText="'Crear Tarjeta'"
            :submittingText="'Creando...'"
            :cancelHref="`/member/listings/${listing?.id}/vcards`"
            :sending="sending"
          />
        </form>
      </div>
    </div>
  </MemberLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import MemberLayout from '@/Layouts/MemberLayout.vue'
import PageHeader from '@/Components/Admin/PageHeader.vue'
import FieldText from '@/Components/Fields/FieldText.vue'
import FieldTextarea from '@/Components/Fields/FieldTextarea.vue'
import FieldSelect from '@/Components/Fields/FieldSelect.vue'
import FieldSwitch from '@/Components/Fields/FieldSwitch.vue'
import FormActions from '@/Components/FormActions.vue'

const props = defineProps({
  listing: Object,
  teams: Array,
  designs: Array,
  fonts: Array,
  colors: Array,
  pronouns: Array,
})

const activeTab = ref('card')
const sending = ref(false)
const errors = ref({})

const form = reactive({
  name: '',
  slug: '',
  type: 'single',
  vcard_team_id: '',
  active: true,
  design: 'classic',
  primary_color: '#2563EB',
  font: 'Inter',
  shape: 'rounded',
  prefix: '',
  first_name: '',
  middle_name: '',
  last_name: '',
  accreditations: '',
  preferred_name: '',
  pronouns: '',
  title: '',
  department: '',
  company: '',
  headline: '',
})

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/member/dashboard' },
  { label: 'vCards', href: `/member/listings/${props.listing?.id}/vcards` },
  { label: 'Nueva' },
])

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

<style scoped>
.design-option {
  cursor: pointer;
  transition: all 0.2s;
}

.design-preview {
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
}

.color-swatch {
  width: 36px;
  height: 36px;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.2s;
}

.color-swatch:hover {
  transform: scale(1.1);
}

.color-swatch.ring-2 {
  border-color: var(--bs-primary);
}

.shape-option {
  min-width: 100px;
}

.shape-preview {
  width: 48px;
  height: 48px;
  background: #e5e7eb;
}

.shape-square {
  border-radius: 0;
}

.shape-rounded {
  border-radius: 8px;
}
</style>
